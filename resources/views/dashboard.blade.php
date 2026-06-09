@extends('layouts.admin')

@section('content')

<div class="content">

    <div class="container-fluid">

        <div class="row mb-3">

            <div class="col-sm-6">
                <h1 class="m-0">
                    Dashboard
                </h1>
            </div>

        </div>

        <!-- KPI CARDS -->

        <div class="row">

            <div class="col-lg-3 col-6">

                <div class="small-box bg-primary">

                    <div class="inner">

                        <h3>{{ $totalTasks }}</h3>

                        <p>Total Tasks</p>

                    </div>

                    <div class="icon">
                        <i class="fas fa-tasks"></i>
                    </div>

                </div>

            </div>

            <div class="col-lg-3 col-6">

                <div class="small-box bg-success">

                    <div class="inner">

                        <h3>{{ $completedTasks }}</h3>

                        <p>Completed Tasks</p>

                    </div>

                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>

                </div>

            </div>

            <div class="col-lg-3 col-6">

                <div class="small-box bg-warning">

                    <div class="inner">

                        <h3>{{ $pendingTasks }}</h3>

                        <p>Pending Tasks</p>

                    </div>

                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>

                </div>

            </div>

            <div class="col-lg-3 col-6">

                <div class="small-box bg-danger">

                    <div class="inner">

                        <h3>{{ $overdueTasks }}</h3>

                        <p>Overdue Tasks</p>

                    </div>

                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>

                </div>

            </div>

        </div>

        <!-- SECOND ROW -->

        <div class="row">

            <div class="col-md-6">

                <div class="card">

                    <div class="card-header">
                        <h3 class="card-title">
                            System Summary
                        </h3>
                    </div>

                    <div class="card-body">

                        <table class="table table-bordered">

                            <tr>
                                <th>Total Users</th>
                                <td>{{ $totalUsers }}</td>
                            </tr>

                            <tr>
                                <th>Total Tasks</th>
                                <td>{{ $totalTasks }}</td>
                            </tr>

                            <tr>
                                <th>Completed Tasks</th>
                                <td>{{ $completedTasks }}</td>
                            </tr>

                            <tr>
                                <th>Pending Tasks</th>
                                <td>{{ $pendingTasks }}</td>
                            </tr>

                            <tr>
                                <th>Overdue Tasks</th>
                                <td>{{ $overdueTasks }}</td>
                            </tr>

                        </table>

                    </div>

                </div>

            </div>

            <div class="col-md-6">

                <div class="card">

                    <div class="card-header">
                        <h3 class="card-title">
                            Productivity Rate
                        </h3>
                    </div>

                    <div class="card-body">

                        @php

                            $productivity =
                            $totalTasks > 0
                                ? round(($completedTasks / $totalTasks) * 100)
                                : 0;

                        @endphp

                        <div class="progress progress-lg">

                            <div class="progress-bar bg-success"
                                 style="width: {{ $productivity }}%">

                                {{ $productivity }}%

                            </div>

                        </div>

                        <br>

                        <h4 class="text-center">

                            {{ $productivity }}% Tasks Completed

                        </h4>

                    </div>

                </div>

            </div>

            <div class="row">

    <div class="col-md-12">

        <div class="card">

            <div class="card-header">

                <h3 class="card-title">
                    <i class="fas fa-user-check"></i>
                    My Tasks
                </h3>

            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover">

                        <thead>

                            <tr>

                                <th>Task</th>

                                <th>Category</th>

                                <th>Priority</th>

                                <th>Status</th>

                                <th>Due Date</th>

                                <th width="250">
                                    Actions
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($myTasks as $task)

                                <tr>

                                    <td>
                                        {{ $task->title }}
                                    </td>

                                    <td>
                                        {{ $task->category->name ?? '-' }}
                                    </td>

                                    <td>

                                        @if($task->priority == 'high')

                                            <span class="badge bg-danger">
                                                High
                                            </span>

                                        @elseif($task->priority == 'medium')

                                            <span class="badge bg-warning">
                                                Medium
                                            </span>

                                        @else

                                            <span class="badge bg-success">
                                                Low
                                            </span>

                                        @endif

                                    </td>

                                    <td>

                                        @if($task->status == 'pending')

                                            <span class="badge bg-secondary">
                                                Pending
                                            </span>

                                        @elseif($task->status == 'in_progress')

                                            <span class="badge bg-info">
                                                In Progress
                                            </span>

                                        @elseif($task->status == 'completed')

                                            <span class="badge bg-success">
                                                Completed
                                            </span>

                                        @endif

                                    </td>

                                    <td>

                                        {{ $task->due_date?->format('d M Y') }}

                                    </td>

                                    <td>

                                        @if($task->status == 'pending')

                                            <a href="{{ route('tasks.start',$task) }}"
                                               class="btn btn-info btn-sm">

                                                <i class="fas fa-play"></i>

                                                Start

                                            </a>

                                        @endif

                                        @if($task->status != 'completed')

                                            <a href="{{ route('tasks.complete',$task) }}"
                                               class="btn btn-success btn-sm">

                                                <i class="fas fa-check"></i>

                                                Finish

                                            </a>

                                        @endif

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="6"
                                        class="text-center">

                                        No assigned tasks

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>

        </div>

    </div>

</div>

@endsection