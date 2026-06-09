<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // ── KPI counts ───────────────────────────────────────────────────────
        $totalTasks     = Task::count();
        $completedTasks = Task::where('status', 'completed')->count();
        $pendingTasks   = Task::where('status', 'pending')->count();
        $inProgressTasks = Task::where('status', 'in_progress')->count();
        $cancelledTasks  = Task::where('status', 'cancelled')->count();

        // Intentionally excludes only 'completed'; cancelled tasks still count as overdue
        // so managers can see abandoned work that missed its deadline
        $overdueTasks = Task::whereDate('due_date', '<', now())
            ->where('status', '!=', 'completed')
            ->count();

        $totalUsers = User::count();

        $productivity = $totalTasks > 0
            ? round(($completedTasks / $totalTasks) * 100)
            : 0;

        // ── Priority breakdown ────────────────────────────────────────────────
        $highPriorityCount   = Task::where('priority', 'high')->count();
        $mediumPriorityCount = Task::where('priority', 'medium')->count();
        $lowPriorityCount    = Task::where('priority', 'low')->count();

        // ── Overdue tasks list ────────────────────────────────────────────────
        $overdueTasksList = Task::with(['category', 'assignedUser'])
            ->whereDate('due_date', '<', now())
            ->where('status', '!=', 'completed')
            ->orderBy('due_date')
            ->take(5)
            ->get();

        // ── Category task distribution ────────────────────────────────────────
        $categoryStats = Category::withCount('tasks')
            ->orderByDesc('tasks_count')
            ->take(6)
            ->get();

        // Only active statuses — completed/cancelled tasks are excluded from the personal list
        // Ordered by due_date ascending so the most urgent tasks surface first; capped at 10
        $myTasks = Task::with('category')
            ->where('assigned_to', auth()->id())
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderBy('due_date')
            ->take(10)
            ->get();

        return view('dashboard', compact(
            'totalTasks',
            'completedTasks',
            'pendingTasks',
            'inProgressTasks',
            'cancelledTasks',
            'overdueTasks',
            'totalUsers',
            'productivity',
            'highPriorityCount',
            'mediumPriorityCount',
            'lowPriorityCount',
            'overdueTasksList',
            'categoryStats',
            'myTasks'
        ));
    }
}
