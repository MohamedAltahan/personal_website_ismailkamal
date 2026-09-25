<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('admin.profile', ['user' => auth()->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180', Rule::unique('users')->ignore($user->id)],
            'image' => ['nullable', 'image', 'max:4096'],
            'locale' => ['nullable', Rule::in(array_keys(locales()))],
        ]);

        if ($request->hasFile('image')) {
            $disk = Storage::disk(config('site.media_disk'));
            if ($user->image) {
                $disk->delete($user->image);
            }
            $data['image'] = $request->file('image')->store('profile', config('site.media_disk'));
        } else {
            unset($data['image']);
        }

        $user->update($data);

        return back()->with('success', __('Profile updated.'));
    }

    public function password(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $request->user()->update(['password' => $request->input('password')]);

        return back()->with('success', __('Password changed.'));
    }
}
