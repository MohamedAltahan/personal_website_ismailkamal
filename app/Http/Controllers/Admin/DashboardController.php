<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Media;
use App\Models\Message;
use App\Models\Project;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $mediaBytes = (int) Media::all(['size', 'meta'])->sum(fn (Media $m) => $m->totalSize());

        $stats = [
            'projects' => Project::count(),
            'published' => Project::published()->count(),
            'images' => Media::where('type', 'image')->count(),
            'videos' => Media::whereIn('type', ['video', 'embed'])->count(),
            'messages' => Message::count(),
            'unread' => Message::unread()->count(),
            'storage' => $mediaBytes,
            'views' => (int) Project::sum('views'),
        ];

        // Messages per month (last 12 months).
        $months = collect(range(11, 0))->map(fn ($i) => now()->startOfMonth()->subMonths($i));
        $counts = Message::where('created_at', '>=', $months->first())->pluck('created_at')
            ->countBy(fn ($date) => $date->format('Y-m'));

        $messagesChart = [
            'labels' => $months->map(fn ($m) => $m->translatedFormat('M'))->all(),
            'values' => $months->map(fn ($m) => (int) ($counts[$m->format('Y-m')] ?? 0))->all(),
        ];

        $categories = Category::withCount('projects')->ordered()->get();
        $categoriesChart = [
            'labels' => $categories->map(fn ($c) => $c->name)->all(),
            'values' => $categories->pluck('projects_count')->all(),
        ];

        return view('admin.dashboard', [
            'stats' => $stats,
            'messagesChart' => $messagesChart,
            'categoriesChart' => $categoriesChart,
            'latestProjects' => Project::with(['cover', 'category'])->latest('updated_at')->take(6)->get(),
            'latestMessages' => Message::latest()->take(5)->get(),
            'topProjects' => Project::with('cover')->orderByDesc('views')->take(5)->get(),
        ]);
    }
}
