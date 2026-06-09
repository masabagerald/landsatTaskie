<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Task;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::with([
            'category',
            'assignedUser'
        ])->latest()->get();

        $users = User::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view(
            'tasks.index',
            compact(
                'tasks',
                'users',
                'categories'
            )
        );
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Task::create($request->all());

        return back()
            ->with('success','Task created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
     public function update( UpdateTaskRequest $request,Task $task ) {
        $task->update($request->validated());

        return back()
            ->with('success','Task updated successfully');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return back()
            ->with('success','Task deleted successfully');
    }
}
