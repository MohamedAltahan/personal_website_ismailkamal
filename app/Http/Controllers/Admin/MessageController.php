<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['q', 'filter']);

        $messages = Message::query()
            ->when($filters['q'] ?? null, fn ($q, $term) => $q->where(fn ($w) => $w
                ->where('name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%")->orWhere('message', 'like', "%{$term}%")))
            ->when(($filters['filter'] ?? null) === 'unread', fn ($q) => $q->unread())
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.messages.index', compact('messages', 'filters'));
    }

    public function show(Message $message): View
    {
        if (! $message->read_at) {
            $message->update(['read_at' => now()]);
        }

        return view('admin.messages.show', [
            'message' => $message,
            'previous' => Message::where('id', '>', $message->id)->orderBy('id')->value('id'),
            'next' => Message::where('id', '<', $message->id)->orderByDesc('id')->value('id'),
        ]);
    }

    public function unread(Message $message): JsonResponse
    {
        $message->update(['read_at' => null]);

        return response()->json(['message' => __('Marked as unread.')]);
    }

    public function destroy(Message $message): JsonResponse
    {
        $message->delete();

        return response()->json(['message' => __('Message deleted.')]);
    }
}
