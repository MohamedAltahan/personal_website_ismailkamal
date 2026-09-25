<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Page;
use App\Models\Project;
use App\Models\User;
use App\Support\Blocks\BlockRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->admin = User::factory()->create();
        Page::create(['slug' => 'home', 'title' => ['ar' => 'الرئيسية', 'en' => 'Home'], 'is_system' => true, 'blocks' => []]);
        Page::create(['slug' => 'about', 'title' => ['ar' => 'عني', 'en' => 'About'], 'is_system' => true, 'blocks' => []]);
    }

    public function test_guests_are_sent_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/projects')->assertRedirect('/admin/login');
        $this->get('/admin/login')->assertOk();
    }

    public function test_login_and_logout(): void
    {
        $this->post('/admin/login', ['email' => $this->admin->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->post('/admin/login', ['email' => $this->admin->email, 'password' => 'password'])->assertRedirect('/admin');
        $this->assertAuthenticatedAs($this->admin);
        $this->post('/admin/logout')->assertRedirect('/admin/login');
        $this->assertGuest();
    }

    public function test_every_dashboard_screen_renders(): void
    {
        $project = Project::create(['slug' => 'p', 'title' => ['ar' => 'م', 'en' => 'P'], 'status' => 'active', 'blocks' => []]);

        $this->actingAs($this->admin);

        foreach ([
            '/admin', '/admin/projects', '/admin/projects/create', "/admin/projects/{$project->id}/edit",
            '/admin/pages', '/admin/pages/create', '/admin/pages/1/edit', '/admin/categories', '/admin/media',
            '/admin/media/list', '/admin/messages', '/admin/socials', '/admin/profile',
            '/admin/settings/general', '/admin/settings/branding', '/admin/settings/appearance',
            '/admin/settings/media', '/admin/settings/seo', '/admin/settings/contact',
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_create_and_update_project_with_blocks(): void
    {
        $this->actingAs($this->admin);
        $category = Category::create(['name' => ['ar' => 'فئة', 'en' => 'Cat'], 'slug' => 'cat', 'status' => 'active']);

        $payload = [
            'title' => ['ar' => 'مشروع جديد', 'en' => 'New Project'],
            'excerpt' => ['ar' => '', 'en' => 'Short'],
            'slug' => '',
            'category_id' => $category->id,
            'status' => 'active',
            'is_featured' => true,
            'tags' => ['After Effects'],
            'blocks' => [
                BlockRegistry::make('heading', ['text' => ['ar' => 'عنوان', 'en' => 'Title']]),
                BlockRegistry::make('text', ['html' => ['ar' => '<p>نص<script>x</script></p>', 'en' => '<p>Text</p>']]),
            ],
        ];

        $this->postJson('/admin/projects', $payload)->assertOk()->assertJsonStructure(['redirect']);

        $project = Project::firstWhere('slug', 'new-project');
        $this->assertNotNull($project);
        $this->assertTrue($project->is_featured);
        $this->assertSame(['heading', 'text'], array_column($project->blocks, 'type'));
        $this->assertSame('<p>نص</p>', $project->blocks[1]['data']['html']['ar']);

        $payload['blocks'] = [['type' => 'nope']];
        $this->putJson("/admin/projects/{$project->id}", $payload)->assertStatus(422);

        $payload['blocks'] = [];
        $payload['slug'] = 'renamed';
        $this->putJson("/admin/projects/{$project->id}", $payload)->assertOk();
        $this->assertSame('renamed', $project->fresh()->slug);
    }

    public function test_toggle_and_reorder_projects(): void
    {
        $this->actingAs($this->admin);
        $a = Project::create(['slug' => 'a', 'title' => ['en' => 'A'], 'status' => 'active', 'sort_order' => 1]);
        $b = Project::create(['slug' => 'b', 'title' => ['en' => 'B'], 'status' => 'active', 'sort_order' => 2]);

        $this->patchJson("/admin/projects/{$a->id}/toggle/status")->assertOk()->assertJson(['value' => false]);
        $this->assertSame('inactive', $a->fresh()->status);

        $this->postJson('/admin/projects/reorder', ['ids' => [$b->id, $a->id]])->assertOk();
        $this->assertSame(1, $b->fresh()->sort_order);
        $this->assertSame(2, $a->fresh()->sort_order);
    }

    public function test_builder_preview_renders_unsaved_content(): void
    {
        $this->actingAs($this->admin);

        $this->post('/admin/builder/preview', [
            'mode' => 'project',
            'locale' => 'en',
            'data' => [
                'title' => ['ar' => '', 'en' => 'Preview title'],
                'slug' => '',
                'blocks' => [BlockRegistry::make('quote', ['text' => ['ar' => '', 'en' => 'Great work']])],
            ],
        ])->assertOk()->assertSee('Preview title')->assertSee('Great work')->assertSee('data-preview="on"', false);
    }

    public function test_settings_are_saved_per_tab(): void
    {
        $this->actingAs($this->admin);

        $this->put('/admin/settings/media', ['s' => ['media' => [
            'quality' => 55, 'format' => 'webp', 'widths' => [960, 480], 'max_dimension' => 2400,
            'keep_original' => 1, 'max_upload_mb' => 300, 'watermark_enabled' => 0,
            'watermark' => '', 'watermark_position' => 'center', 'watermark_opacity' => 30, 'watermark_scale' => 10,
        ]]])->assertRedirect('/admin/settings/media');

        $this->assertSame(55, setting('media.quality'));
        $this->assertSame([480, 960], setting('media.widths'));
        $this->assertFalse(setting('media.watermark_enabled'));

        $this->put('/admin/settings/general', ['s' => ['general' => [
            'site_name' => ['ar' => 'اسم', 'en' => 'Name'], 'tagline' => ['ar' => '', 'en' => ''],
            'contact_email' => 'not-an-email', 'available_for_work' => 1,
        ]]])->assertSessionHasErrors('general.contact_email');
    }
}
