<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.categories.index', [
            'categories' => Category::with(['subCategories' => fn ($q) => $q->withCount('projects')])->withCount('projects')->ordered()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        Category::create($data + ['sort_order' => (int) Category::max('sort_order') + 1]);
        site_changed();

        return back()->with('success', __('Category added.'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $category->update($this->validated($request, $category));
        site_changed();

        return back()->with('success', __('Category updated.'));
    }

    private function validated(Request $request, ?Category $category = null): array
    {
        $data = $request->validate([
            'name.ar' => ['nullable', 'string', 'max:120', 'required_without:name.en'],
            'name.en' => ['nullable', 'string', 'max:120', 'required_without:name.ar'],
            'description.*' => ['nullable', 'string', 'max:400'],
            'slug' => ['nullable', 'string', 'max:120', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('categories', 'slug')->ignore($category?->id)],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $name = array_map(fn ($v) => (string) $v, $data['name']);
        $data['slug'] = $data['slug'] ?? null ?: $this->uniqueSlug($name['en'] ?: $name['ar'], $category?->id);
        $data['name'] = $name;
        $data['description'] = array_map(fn ($v) => (string) $v, $data['description'] ?? []);

        return $data;
    }

    private function uniqueSlug(string $name, ?int $ignore): string
    {
        $base = Str::slug($name) ?: 'category';
        $slug = $base;
        $i = 2;
        while (Category::where('slug', $slug)->when($ignore, fn ($q) => $q->where('id', '!=', $ignore))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    public function destroy(Category $category): JsonResponse
    {
        if ($category->projects()->exists()) {
            return response()->json(['message' => __('Move or delete the projects in this category first.')], 422);
        }

        $category->subCategories()->delete();
        $category->delete();
        site_changed();

        return response()->json(['message' => __('Category deleted.')]);
    }

    public function toggle(Category $category): JsonResponse
    {
        $category->update(['status' => $category->status === 'active' ? 'inactive' : 'active']);
        site_changed();

        return response()->json(['value' => $category->status === 'active', 'message' => __('Updated.')]);
    }

    public function reorder(Request $request): JsonResponse
    {
        $ids = $request->validate(['ids' => ['required', 'array'], 'ids.*' => ['integer']])['ids'];
        DB::transaction(fn () => collect($ids)->each(fn ($id, $i) => Category::whereKey($id)->update(['sort_order' => $i + 1])));
        site_changed();

        return response()->json(['message' => __('Order saved.')]);
    }

    public function storeSub(Request $request, Category $category): RedirectResponse
    {
        $name = $this->subName($request);
        $category->subCategories()->create([
            'name' => $name,
            'slug' => Str::slug($name['en'] ?: $name['ar']) ?: Str::random(6),
            'status' => 'active',
        ]);

        return back()->with('success', __('Subcategory added.'));
    }

    public function updateSub(Request $request, SubCategory $subCategory): RedirectResponse
    {
        $subCategory->update(['name' => $this->subName($request), 'status' => $request->boolean('active') ? 'active' : 'inactive']);

        return back()->with('success', __('Subcategory updated.'));
    }

    public function destroySub(SubCategory $subCategory): JsonResponse
    {
        $subCategory->projects()->update(['sub_category_id' => null]);
        $subCategory->delete();

        return response()->json(['message' => __('Subcategory deleted.')]);
    }

    private function subName(Request $request): array
    {
        $data = $request->validate([
            'name.ar' => ['nullable', 'string', 'max:120', 'required_without:name.en'],
            'name.en' => ['nullable', 'string', 'max:120', 'required_without:name.ar'],
        ]);

        return array_map(fn ($v) => (string) $v, $data['name']);
    }
}
