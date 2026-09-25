<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Message;
use App\Models\Page;
use App\Models\Project;
use App\Support\Blocks\BlockRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Page::create(['slug' => 'home', 'title' => ['ar' => 'الرئيسية', 'en' => 'Home'], 'is_system' => true, 'blocks' => [
            BlockRegistry::make('hero', ['title' => ['ar' => 'أهلاً', 'en' => 'Hello there']]),
            BlockRegistry::make('projects', ['source' => 'latest']),
        ]]);
        Page::create(['slug' => 'about', 'title' => ['ar' => 'عني', 'en' => 'About'], 'is_system' => true, 'blocks' => [
            BlockRegistry::make('text', ['html' => ['ar' => '<p>نبذة</p>', 'en' => '<p>Bio</p>']]),
        ]]);
    }

    private function project(array $attributes = []): Project
    {
        $category = Category::create(['name' => ['ar' => 'موشن', 'en' => 'Motion'], 'slug' => 'motion', 'status' => 'active']);

        return Project::create($attributes + [
            'slug' => 'my-project',
            'title' => ['ar' => 'مشروعي', 'en' => 'My project'],
            'category_id' => $category->id,
            'status' => 'active',
            'blocks' => [BlockRegistry::make('heading', ['text' => ['ar' => 'عنوان', 'en' => 'A heading']])],
        ]);
    }

    public function test_root_uses_default_language_then_remembered_choice(): void
    {
        // English by default, whatever the browser says.
        $this->get('/', ['Accept-Language' => 'ar-EG,ar;q=0.9'])->assertRedirect('/en');
        $this->withCookie('site_locale', 'ar')->get('/', ['Accept-Language' => 'en'])->assertRedirect('/ar');

        // Optional browser-language detection.
        setting()->set(['appearance.detect_browser_locale' => true]);
        $this->get('/', ['Accept-Language' => 'ar-EG,ar;q=0.9'])->assertRedirect('/ar');
        $this->withCookie('site_locale', 'en')->get('/', ['Accept-Language' => 'ar'])->assertRedirect('/en');
    }

    public function test_default_themes_for_site_and_dashboard(): void
    {
        $this->get('/en')->assertOk()
            ->assertSee('var key = "theme", fallback = "dark"', false);

        $this->withoutVite()->get('/admin/login')->assertOk()
            ->assertSee('var key = "admin-theme", fallback = "light"', false)
            ->assertSee('lang="ar"', false);
    }

    public function test_pages_render_in_both_languages_with_direction(): void
    {
        $this->get('/ar')->assertOk()->assertSee('dir="rtl"', false)->assertSee('أهلاً');
        $this->get('/en')->assertOk()->assertSee('dir="ltr"', false)->assertSee('Hello there');
        $this->get('/en/about')->assertOk()->assertSee('Bio');
        $this->get('/ar/contact')->assertOk();
        $this->get('/fr')->assertNotFound();
    }

    public function test_project_page_and_listing(): void
    {
        $project = $this->project();

        $this->get('/en/work')->assertOk()->assertSee('My project');
        $this->get('/en/category/motion')->assertOk()->assertSee('My project');
        $this->get('/ar/work/my-project')->assertOk()->assertSee('مشروعي')->assertSee('عنوان');
        $this->assertSame(1, $project->fresh()->views);
    }

    public function test_hidden_projects_are_not_public(): void
    {
        $this->project(['status' => 'inactive']);

        $this->get('/en/work/my-project')->assertNotFound();
        $this->get('/en/work')->assertDontSee('My project');
    }

    public function test_legacy_urls_redirect_permanently(): void
    {
        $project = $this->project();

        $this->get('/design-details/'.$project->id, ['Accept-Language' => 'en'])->assertStatus(301)->assertRedirect('/en/work/my-project');
        $this->get('/about', ['Accept-Language' => 'en'])->assertStatus(301)->assertRedirect('/en/about');
        // A visitor who picked Arabic before is sent to the Arabic page.
        $this->withCookie('site_locale', 'ar')->get('/category/'.$project->category_id)->assertStatus(301)->assertRedirect('/ar/category/motion');
    }

    public function test_contact_form_stores_message_and_blocks_bots(): void
    {
        $this->postJson('/en/contact', ['name' => 'Sara', 'email' => 'sara@example.com', 'message' => 'Hello, I have a project.'])
            ->assertOk()->assertJson(['ok' => true]);
        $this->assertDatabaseHas('email_inboxes', ['email' => 'sara@example.com', 'locale' => 'en']);

        $this->postJson('/en/contact', ['name' => 'Bot', 'email' => 'bot@example.com', 'message' => 'spam spam', 'website' => 'http://spam'])
            ->assertOk();
        $this->assertSame(1, Message::count());

        $this->postJson('/en/contact', ['name' => '', 'email' => 'bad'])->assertStatus(422)->assertJsonValidationErrors(['name', 'email', 'message']);
    }

    public function test_contact_form_is_throttled(): void
    {
        foreach (range(1, 5) as $i) {
            $this->postJson('/ar/contact', ['name' => 'A', 'email' => "a{$i}@example.com", 'message' => 'Message number '.$i]);
        }

        $this->postJson('/ar/contact', ['name' => 'A', 'email' => 'x@example.com', 'message' => 'One too many'])->assertStatus(429);
    }

    public function test_every_hero_effect_renders(): void
    {
        $home = Page::findSlug('home');

        foreach (BlockRegistry::HERO_EFFECTS as $effect) {
            foreach (BlockRegistry::TEXT_EFFECTS as $textEffect) {
                $home->update(['blocks' => [BlockRegistry::make('hero', [
                    'title' => ['ar' => 'أهلاً بك', 'en' => 'Hello there'],
                    'effect' => $effect,
                    'text_effect' => $textEffect,
                    'marquee' => ['ar' => 'موشن، أنيميشن', 'en' => 'Motion, Animation'],
                ])]]);

                $response = $this->get('/en')->assertOk()->assertSee('Motion');
                $textEffect === 'rise'
                    ? $response->assertSee('aria-label="Hello there"', false)
                    : $response->assertSee('Hello there');
            }
        }
    }

    public function test_hero_blocks_saved_before_effects_still_render(): void
    {
        $hero = BlockRegistry::make('hero', ['title' => ['ar' => 'قديم', 'en' => 'Old hero']]);
        foreach (['effect', 'text_effect', 'grain', 'interactive', 'marquee', 'effect_intensity', 'effect_color'] as $key) {
            unset($hero['data'][$key]);
        }
        Page::findSlug('home')->update(['blocks' => [$hero]]);

        $this->get('/en')->assertOk()->assertSee('Old hero');
    }

    public function test_sitemap_and_robots(): void
    {
        $this->project();

        $this->get('/sitemap.xml')->assertOk()->assertSee('/en/work/my-project', false)->assertSee('hreflang="ar"', false);
        $this->get('/robots.txt')->assertOk()->assertSee('Sitemap:');
    }
}
