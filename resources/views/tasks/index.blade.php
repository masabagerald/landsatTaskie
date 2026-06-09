@extends('layouts.admin')

@section('title', 'Tasks')

@section('content')

<div class="container-fluid">

    <div class="row mb-3">

        <div class="col-md-6">
            <h3>
                <i class="fas fa-tasks"></i>
                Task Management
            </h3>
        </div>

        <div class="col-md-6 text-end">

            <button class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#createModal">

                <i class="fas fa-plus"></i>
                Add Task

            </button>

        </div>

    </div>

    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif

    @if ($errors->any())

        <div class="alert alert-danger">

            <ul class="mb-0">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif

    <div class="card">

        <div class="card-header">

            <h3 class="card-title">

                Task List

            </h3>

        </div>

        <div class="card-body">

            <table class="table table-bordered table-striped">

                <thead>

                    <tr>

                        <th width="60">#</th>

                        <th>Title</th>

                        <th>Category</th>

                        <th>Assigned To</th>

                        <th>Priority</th>

                        <th>Status</th>

                        <th>Due Date</th>

                        <th width="180">Actions</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($tasks as $task)

                        <tr>

                            <td>
                                {{ $loop->iteration }}
                            </td>

                            <td>

                                <strong>
                                    {{ $task->title }}
                                </strong>

                                @if($task->description)

                                    <br>

                                    <small class="text-muted">
                                        {{ Str::limit($task->description,50) }}
                                    </small>

                                @endif

                            </td>

                            <td>

                                {{ $task->category->name ?? '-' }}

                            </td>

                            <td>

                                {{ $task->assignedUser->name ?? 'Unassigned' }}

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

                                @else

                                    <span class="badge bg-danger">

                                        Cancelled

                                    </span>

                                @endif

                            </td>

                            <td>

                                @if($task->due_date)

                                    {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}

                                @else

                                    -

                                @endif

                            </td>

                            <td>

                                <button class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal{{ $task->id }}">

                                    <i class="fas fa-edit"></i>

                                </button>

                                <button class="btn btn-danger btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal{{ $task->id }}">

                                    <i class="fas fa-trash"></i>

                                </button>

                            </td>

                        </tr>

                        @include('tasks.partials.edit')

                        @include('tasks.partials.delete')

                    @empty

                        <tr>

                            <td colspan="8" class="text-center">

                                No Tasks Found

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


<div class="modal fade" id="testModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Test Modal</h5>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body">
                Bootstrap Modal Works
            </div>

        </div>
    </div>
</div>




@include('tasks.partials.create')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@endsection