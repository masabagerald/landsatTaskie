@extends('layouts.admin')

@section('title', 'Tasks')

@section('content')

<div class="container-fluid">

    {{-- Page header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0"><i class="fas fa-tasks text-primary me-2"></i> Task Management</h3>
            <small class="text-muted">{{ $tasks->count() }} task{{ $tasks->count() !== 1 ? 's' : '' }} total</small>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="fas fa-plus me-1"></i> Add Task
        </button>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Stats strip --}}
    @php
        $countPending    = $tasks->where('status', 'pending')->count();
        $countProgress   = $tasks->where('status', 'in_progress')->count();
        $countCompleted  = $tasks->where('status', 'completed')->count();
        $countCancelled  = $tasks->where('status', 'cancelled')->count();
        $countOverdue    = $tasks->filter(fn($t) => $t->due_date && $t->due_date->isPast() && $t->status !== 'completed')->count();
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm text-center py-3 h-100">
                <div class="fs-4 fw-bold text-dark">{{ $tasks->count() }}</div>
                <div class="small text-muted">All</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm text-center py-3 h-100" style="border-left: 4px solid #6c757d !important;">
                <div class="fs-4 fw-bold text-secondary">{{ $countPending }}</div>
                <div class="small text-muted">Pending</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm text-center py-3 h-100" style="border-left: 4px solid #0dcaf0 !important;">
                <div class="fs-4 fw-bold text-info">{{ $countProgress }}</div>
                <div class="small text-muted">In Progress</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm text-center py-3 h-100" style="border-left: 4px solid #198754 !important;">
                <div class="fs-4 fw-bold text-success">{{ $countCompleted }}</div>
                <div class="small text-muted">Completed</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm text-center py-3 h-100" style="border-left: 4px solid #dc3545 !important;">
                <div class="fs-4 fw-bold text-danger">{{ $countCancelled }}</div>
                <div class="small text-muted">Cancelled</div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm text-center py-3 h-100" style="border-left: 4px solid #fd7e14 !important;">
                <div class="fs-4 fw-bold text-warning">{{ $countOverdue }}</div>
                <div class="small text-muted">Overdue</div>
            </div>
        </div>
    </div>

    {{-- Filter bar --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <div class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input id="taskSearch" type="text" class="form-control border-start-0" placeholder="Search tasks…">
                    </div>
                </div>
                <div class="col-md-2">
                    <select id="filterStatus" class="form-select form-select-sm">
                        <option value="">All statuses</option>
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="filterPriority" class="form-select form-select-sm">
                        <option value="">All priorities</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="filterCategory" class="form-select form-select-sm">
                        <option value="">All categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button id="clearFilters" class="btn btn-sm btn-outline-secondary w-100" title="Clear filters">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Task table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tasksTable">
                    <thead class="table-light">
                        <tr>
                            <th width="46" class="ps-3">#</th>
                            <th>Task</th>
                            <th width="130">Category</th>
                            <th width="140">Assigned To</th>
                            <th width="100">Priority</th>
                            <th width="120">Status</th>
                            <th width="110">Due Date</th>
                            <th width="90" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $task)
                            @php
                                $isOverdue = $task->due_date && $task->due_date->isPast() && $task->status !== 'completed';
                            @endphp
                            <tr class="task-row"
                                data-status="{{ $task->status }}"
                                data-priority="{{ $task->priority }}"
                                data-category="{{ $task->category_id }}">

                                <td class="ps-3 text-muted small">{{ $loop->iteration }}</td>

                                <td>
                                    <div class="fw-semibold">{{ $task->title }}</div>
                                    @if($task->description)
                                        <div class="text-muted small">{{ Str::limit($task->description, 60) }}</div>
                                    @endif
                                </td>

                                <td>
                                    @if($task->category)
                                        <span class="badge rounded-pill bg-light text-dark border">
                                            {{ $task->category->name }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                <td>
                                    @if($task->assignedUser)
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                                 style="width:28px;height:28px;font-size:11px;flex-shrink:0;">
                                                {{ strtoupper(substr($task->assignedUser->name, 0, 1)) }}
                                            </div>
                                            <span class="small">{{ $task->assignedUser->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted small">Unassigned</span>
                                    @endif
                                </td>

                                <td>
                                    @if($task->priority === 'high')
                                        <span class="badge bg-danger">● High</span>
                                    @elseif($task->priority === 'medium')
                                        <span class="badge bg-warning text-dark">● Medium</span>
                                    @else
                                        <span class="badge bg-success">● Low</span>
                                    @endif
                                </td>

                                <td>
                                    @if($task->status === 'pending')
                                        <span class="badge bg-secondary">Pending</span>
                                    @elseif($task->status === 'in_progress')
                                        <span class="badge bg-info text-dark">In Progress</span>
                                    @elseif($task->status === 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @else
                                        <span class="badge bg-danger">Cancelled</span>
                                    @endif
                                </td>

                                <td>
                                    @if($task->due_date)
                                        <span class="{{ $isOverdue ? 'text-danger fw-semibold' : 'text-body' }}">
                                            @if($isOverdue)<i class="fas fa-exclamation-circle me-1"></i>@endif
                                            {{ $task->due_date->format('d M Y') }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-warning me-1"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal{{ $task->id }}"
                                            title="Edit">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteModal{{ $task->id }}"
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>

                            {{-- Edit modal --}}
                            <div class="modal fade" id="editModal{{ $task->id }}" tabindex="-1">
                                <div class="modal-dialog modal-xl modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('tasks.update', $task) }}">
                                            @csrf
                                            @method('PATCH')

                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    <i class="fas fa-pen me-1"></i> Edit Task
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                                                        <input type="text" name="title" value="{{ $task->title }}"
                                                               class="form-control" required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                                                        <select name="category_id" class="form-select" required>
                                                            <option value="">Select Category</option>
                                                            @foreach($categories as $category)
                                                                <option value="{{ $category->id }}"
                                                                    {{ $task->category_id == $category->id ? 'selected' : '' }}>
                                                                    {{ $category->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-12 mb-3">
                                                        <label class="form-label fw-semibold">Description</label>
                                                        <textarea name="description" rows="3"
                                                                  class="form-control">{{ $task->description }}</textarea>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label fw-semibold">Priority</label>
                                                        <select name="priority" class="form-select">
                                                            <option value="low"    {{ $task->priority === 'low'    ? 'selected' : '' }}>Low</option>
                                                            <option value="medium" {{ $task->priority === 'medium' ? 'selected' : '' }}>Medium</option>
                                                            <option value="high"   {{ $task->priority === 'high'   ? 'selected' : '' }}>High</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label fw-semibold">Status</label>
                                                        <select name="status" class="form-select">
                                                            <option value="pending"     {{ $task->status === 'pending'     ? 'selected' : '' }}>Pending</option>
                                                            <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                            <option value="completed"   {{ $task->status === 'completed'   ? 'selected' : '' }}>Completed</option>
                                                            <option value="cancelled"   {{ $task->status === 'cancelled'   ? 'selected' : '' }}>Cancelled</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label fw-semibold">Due Date</label>
                                                        <input type="date" name="due_date"
                                                               value="{{ $task->due_date?->format('Y-m-d') }}"
                                                               class="form-control">
                                                    </div>
                                                    <div class="col-md-12 mb-3">
                                                        <label class="form-label fw-semibold">Assign To</label>
                                                        <select name="assigned_to" class="form-select">
                                                            <option value="">Unassigned</option>
                                                            @foreach($users as $user)
                                                                <option value="{{ $user->id }}"
                                                                    {{ $task->assigned_to == $user->id ? 'selected' : '' }}>
                                                                    {{ $user->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-warning">
                                                    <i class="fas fa-save me-1"></i> Save Changes
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- Delete modal --}}
                            <div class="modal fade" id="deleteModal{{ $task->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('tasks.destroy', $task) }}">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-header border-0">
                                                <h5 class="modal-title text-danger">
                                                    <i class="fas fa-trash me-1"></i> Delete Task
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center py-4">
                                                <p class="mb-1">You are about to delete:</p>
                                                <p class="fw-bold fs-5">{{ $task->title }}</p>
                                                <p class="text-muted small">This action cannot be undone.</p>
                                            </div>
                                            <div class="modal-footer border-0 justify-content-center">
                                                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger px-4">
                                                    <i class="fas fa-trash me-1"></i> Delete
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        @empty
                            <tr id="emptyRow">
                                <td colspan="8" class="text-center py-5">
                                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted mb-1">No tasks yet</p>
                                    <button class="btn btn-sm btn-primary mt-2"
                                            data-bs-toggle="modal" data-bs-target="#createModal">
                                        <i class="fas fa-plus me-1"></i> Add your first task
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- "no results" row shown by JS when filter returns nothing --}}
    <template id="noResultsTpl">
        <tr id="noResultsRow">
            <td colspan="8" class="text-center py-4 text-muted">
                <i class="fas fa-search me-1"></i> No tasks match your filters.
            </td>
        </tr>
    </template>

</div>

@include('tasks.partials.create')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    const search   = document.getElementById('taskSearch');
    const status   = document.getElementById('filterStatus');
    const priority = document.getElementById('filterPriority');
    const category = document.getElementById('filterCategory');
    const clear    = document.getElementById('clearFilters');
    const tbody    = document.querySelector('#tasksTable tbody');

    function filter() {
        const q   = search.value.toLowerCase();
        const st  = status.value;
        const pr  = priority.value;
        const cat = category.value;

        let visible = 0;
        tbody.querySelectorAll('tr.task-row').forEach(row => {
            const matchQ   = !q  || row.textContent.toLowerCase().includes(q);
            const matchSt  = !st || row.dataset.status   === st;
            const matchPr  = !pr || row.dataset.priority === pr;
            const matchCat = !cat || row.dataset.category === cat;
            const show = matchQ && matchSt && matchPr && matchCat;
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        const existing = document.getElementById('noResultsRow');
        if (existing) existing.remove();

        if (visible === 0 && tbody.querySelectorAll('tr.task-row').length > 0) {
            const tpl = document.getElementById('noResultsTpl');
            tbody.appendChild(tpl.content.cloneNode(true));
        }
    }

    [search, status, priority, category].forEach(el => el.addEventListener('input', filter));

    clear.addEventListener('click', () => {
        search.value = '';
        status.value = '';
        priority.value = '';
        category.value = '';
        filter();
    });
})();
</script>

@endsection
