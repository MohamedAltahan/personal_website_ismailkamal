<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Social;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SocialController extends Controller
{
    public function index(): View
    {
        return view('admin.socials.index', [
            'socials' => Social::orderBy('sort_order')->orderBy('id')->get(),
            'platforms' => Social::PLATFORMS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Social::create($this->validated($request) + ['status' => 'active', 'sort_order' => (int) Social::max('sort_order') + 1]);
        site_changed();

        return back()->with('success', __('Link added.'));
    }

    public function update(Request $request, Social $social): RedirectResponse
    {
        $social->update($this->validated($request));
        site_changed();

        return back()->with('success', __('Link updated.'));
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', Rule::in(Social::PLATFORMS)],
            'link' => ['required', 'string', 'max:500'],
        ]);

        if ($data['name'] === 'email' && ! str_starts_with($data['link'], 'mailto:')) {
            $data['link'] = 'mailto:'.$data['link'];
        } elseif ($data['name'] !== 'email' && ! preg_match('#^https?://#', $data['link'])) {
            $data['link'] = 'https://'.ltrim($data['link'], '/');
        }

        return $data;
    }

    public function destroy(Social $social): JsonResponse
    {
        $social->delete();
        site_changed();

        return response()->json(['message' => __('Link deleted.')]);
    }

    public function toggle(Social $social): JsonResponse
    {
        $social->update(['status' => $social->status === 'active' ? 'inactive' : 'active']);
        site_changed();

        return response()->json(['value' => $social->status === 'active', 'message' => __('Updated.')]);
    }

    public function reorder(Request $request): JsonResponse
    {
        $ids = $request->validate(['ids' => ['required', 'array'], 'ids.*' => ['integer']])['ids'];
        DB::transaction(fn () => collect($ids)->each(fn ($id, $i) => Social::whereKey($id)->update(['sort_order' => $i + 1])));
        site_changed();

        return response()->json(['message' => __('Order saved.')]);
    }
}
