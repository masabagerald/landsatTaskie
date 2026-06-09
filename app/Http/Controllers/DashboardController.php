<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
{
    $totalTasks = Task::count();

    $completedTasks = Task::where(
        'status',
        'completed'
    )->count();

    $pendingTasks = Task::where(
        'status',
        'pending'
    )->count();

    // Intentionally excludes only 'completed'; cancelled tasks still count as overdue
    // so managers can see abandoned work that missed its deadline
    $overdueTasks = Task::whereDate(
            'due_date',
            '<',
            now()
        )
        ->where('status', '!=', 'completed')
        ->count();

    $totalUsers = User::count();

    // Only active statuses — completed/cancelled tasks are excluded from the personal list
    // Ordered by due_date ascending so the most urgent tasks surface first; capped at 10
    $myTasks = Task::with('category')
        ->where('assigned_to', auth()->id())
        ->whereIn('status', ['pending', 'in_progress'])
        ->orderBy('due_date')
        ->take(10)
        ->get();

    return view(
        'dashboard',
        compact(
            'totalTasks',
            'completedTasks',
            'pendingTasks',
            'overdueTasks',
            'totalUsers',
            'myTasks'
        )
    );
}
}