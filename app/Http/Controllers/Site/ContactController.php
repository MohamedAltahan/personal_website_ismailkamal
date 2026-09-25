<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Mail\NewContactMessage;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        return view('site.contact');
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        // Honeypot: bots fill the hidden "website" field.
        if ($request->filled('website')) {
            return $request->expectsJson() ? response()->json(['ok' => true]) : back()->with('sent', true);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180'],
            'phone' => ['nullable', 'string', 'max:40'],
            'subject' => ['nullable', 'string', 'max:180'],
            'message' => ['required', 'string', 'min:5', 'max:5000'],
        ]);

        $message = Message::create($data + ['locale' => app()->getLocale(), 'ip' => $request->ip()]);

        if (setting('contact.notify') && ($to = setting('contact.notify_email') ?: setting('general.contact_email'))) {
            rescue(fn () => Mail::to($to)->send(new NewContactMessage($message)));
        }

        return $request->expectsJson()
            ? response()->json(['ok' => true, 'message' => __('Thanks! Your message has been sent.')])
            : back()->with('sent', true);
    }
}
