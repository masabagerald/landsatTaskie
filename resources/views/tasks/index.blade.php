@extends('layouts.admin')

@section('title', 'Tasks')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
    .ts-wrapper.single .ts-control { padding: .375rem .75rem; }
    .ts-wrapper .ts-control { border-color: #ced4da; border-radius: .375rem; }
    .ts-wrapper.focus .ts-control { border-color: #86b7fe; box-shadow: 0 0 0 .25rem rgba(13,110,253,.25); }
    .priority-btn-group .btn { border-radius: 0; }
    .priority-btn-group .btn:first-child { border-radius: .375rem 0 0 .375rem; }
    .priority-btn-group .btn:last-child  { border-radius: 0 .375rem .375rem 0; }
</style>
@endsection

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
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Stats strip --}}
    @php
        $countPending   = $tasks->where('status', 'pending')->count();
        $countProgress  = $tasks->where('status', 'in_progress')->count();
        $countCompleted = $tasks->where('status', 'completed')->count();
        $countCancelled = $tasks->where('status', 'cancelled')->count();
        $countOverdue   = $tasks->filter(fn($t) => $t->due_date && $t->due_date->isPast() && $t->status !== 'completed')->count();
    @endphp

    <div class="row g-3 mb-4">
        @foreach([
            ['label'=>'All',         'count'=>$tasks->count(), 'color'=>'dark',      'icon'=>'fa-list'],
            ['label'=>'Pending',     'count'=>$countPending,   'color'=>'secondary', 'icon'=>'fa-clock'],
            ['label'=>'In Progress', 'count'=>$countProgress,  'color'=>'info',      'icon'=>'fa-spinner'],
            ['label'=>'Completed',   'count'=>$countCompleted, 'color'=>'success',   'icon'=>'fa-check-circle'],
            ['label'=>'Cancelled',   'count'=>$countCancelled, 'color'=>'danger',    'icon'=>'fa-ban'],
            ['label'=>'Overdue',     'count'=>$countOverdue,   'color'=>'warning',   'icon'=>'fa-exclamation-triangle'],
        ] as $stat)
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm text-center py-3 h-100">
                <i class="fas {{ $stat['icon'] }} text-{{ $stat['color'] }} mb-1"></i>
                <div class="fs-5 fw-bold text-{{ $stat['color'] }}">{{ $stat['count'] }}</div>
                <div class="small text-muted">{{ $stat['label'] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Filter bar --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <div class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input id="taskSearch" type="text" class="form-control border-start-0"
                               placeholder="Search tasks…">
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
                    <button id="clearFilters" class="btn btn-sm btn-outline-secondary w-100" title="Clear">
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
                            <th width="150">Assigned To</th>
                            <th width="100">Priority</th>
                            <th width="120">Status</th>
                            <th width="115">Due Date</th>
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
                                        <span class="badge bg-danger"><i class="fas fa-arrow-up me-1"></i>High</span>
                                    @elseif($task->priority === 'medium')
                                        <span class="badge bg-warning text-dark"><i class="fas fa-equals me-1"></i>Medium</span>
                                    @else
                                        <span class="badge bg-success"><i class="fas fa-arrow-down me-1"></i>Low</span>
                                    @endif
                                </td>

                                <td>
                                    @if($task->status === 'pending')
                                        <span class="badge bg-secondary">⏳ Pending</span>
                                    @elseif($task->status === 'in_progress')
                                        <span class="badge bg-info text-dark">🔄 In Progress</span>
                                    @elseif($task->status === 'completed')
                                        <span class="badge bg-success">✅ Completed</span>
                                    @else
                                        <span class="badge bg-danger">🚫 Cancelled</span>
                                    @endif
                                </td>

                                <td>
                                    @if($task->due_date)
                                        <span class="{{ $isOverdue ? 'text-danger fw-semibold' : '' }}">
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

                            {{-- ── Edit Modal ───────────────────────────────────────── --}}
                            <div class="modal fade" id="editModal{{ $task->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content border-0 shadow">
                                        <form method="POST" action="{{ route('tasks.update', $task) }}">
                                            @csrf
                                            @method('PATCH')

                                            <div class="modal-header bg-warning">
                                                <h5 class="modal-title fw-semibold">
                                                    <i class="fas fa-pen me-2"></i> Edit Task
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body p-4">

                                                {{-- Title --}}
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">
                                                        Title <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" name="title"
                                                           value="{{ $task->title }}"
                                                           class="form-control form-control-lg" required>
                                                </div>

                                                {{-- Description --}}
                                                <div class="mb-4">
                                                    <label class="form-label fw-semibold d-flex justify-content-between">
                                                        Description
                                                        <span class="text-muted fw-normal small desc-count"
                                                              data-max="500">{{ strlen($task->description ?? '') }} / 500</span>
                                                    </label>
                                                    <textarea name="description" rows="3"
                                                              class="form-control desc-textarea"
                                                              maxlength="500">{{ $task->description }}</textarea>
                                                </div>

                                                <hr class="my-3">

                                                {{-- Category & User --}}
                                                <div class="row g-3 mb-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">
                                                            Category <span class="text-danger">*</span>
                                                        </label>
                                                        <select name="category_id" class="form-select ts-select"
                                                                data-placeholder="Search category…" required>
                                                            <option value=""></option>
                                                            @foreach($categories as $cat)
                                                                <option value="{{ $cat->id }}"
                                                                    {{ $task->category_id == $cat->id ? 'selected' : '' }}>
                                                                    {{ $cat->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Assign To</label>
                                                        <select name="assigned_to" class="form-select ts-select"
                                                                data-placeholder="Search user…">
                                                            <option value=""></option>
                                                            @foreach($users as $user)
                                                                <option value="{{ $user->id }}"
                                                                    {{ $task->assigned_to == $user->id ? 'selected' : '' }}>
                                                                    {{ $user->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                {{-- Priority toggle --}}
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Priority</label>
                                                    <div class="btn-group w-100 priority-btn-group" role="group">
                                                        <input type="radio" class="btn-check"
                                                               name="priority"
                                                               id="pri_low_{{ $task->id }}"
                                                               value="low"
                                                               {{ $task->priority === 'low' ? 'checked' : '' }}>
                                                        <label class="btn btn-outline-success"
                                                               for="pri_low_{{ $task->id }}">
                                                            <i class="fas fa-arrow-down me-1"></i> Low
                                                        </label>

                                                        <input type="radio" class="btn-check"
                                                               name="priority"
                                                               id="pri_med_{{ $task->id }}"
                                                               value="medium"
                                                               {{ $task->priority === 'medium' ? 'checked' : '' }}>
                                                        <label class="btn btn-outline-warning"
                                                               for="pri_med_{{ $task->id }}">
                                                            <i class="fas fa-equals me-1"></i> Medium
                                                        </label>

                                                        <input type="radio" class="btn-check"
                                                               name="priority"
                                                               id="pri_hi_{{ $task->id }}"
                                                               value="high"
                                                               {{ $task->priority === 'high' ? 'checked' : '' }}>
                                                        <label class="btn btn-outline-danger"
                                                               for="pri_hi_{{ $task->id }}">
                                                            <i class="fas fa-arrow-up me-1"></i> High
                                                        </label>
                                                    </div>
                                                </div>

                                                {{-- Status & Due Date --}}
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Status</label>
                                                        <select name="status" class="form-select ts-select ts-status"
                                                                data-placeholder="Select status…">
                                                            <option value="pending"     {{ $task->status === 'pending'     ? 'selected' : '' }}>⏳ Pending</option>
                                                            <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>🔄 In Progress</option>
                                                            <option value="completed"   {{ $task->status === 'completed'   ? 'selected' : '' }}>✅ Completed</option>
                                                            <option value="cancelled"   {{ $task->status === 'cancelled'   ? 'selected' : '' }}>🚫 Cancelled</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Due Date</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text bg-white">
                                                                <i class="fas fa-calendar text-muted"></i>
                                                            </span>
                                                            <input type="date" name="due_date"
                                                                   value="{{ $task->due_date?->format('Y-m-d') }}"
                                                                   class="form-control">
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    Cancel
                                                </button>
                                                <button type="submit" class="btn btn-warning px-4">
                                                    <i class="fas fa-save me-1"></i> Save Changes
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- ── Delete Modal ─────────────────────────────────────── --}}
                            <div class="modal fade" id="deleteModal{{ $task->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
                                    <div class="modal-content border-0 shadow">
                                        <form method="POST" action="{{ route('tasks.destroy', $task) }}">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-header border-0 pb-0">
                                                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center px-4 pt-0 pb-4">
                                                <div class="rounded-circle bg-danger bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3"
                                                     style="width:64px;height:64px;">
                                                    <i class="fas fa-trash text-danger fa-lg"></i>
                                                </div>
                                                <h5 class="fw-bold mb-1">Delete Task?</h5>
                                                <p class="text-muted mb-1">You are about to permanently delete:</p>
                                                <p class="fw-semibold">{{ $task->title }}</p>
                                            </div>
                                            <div class="modal-footer border-0 pt-0 justify-content-center gap-2">
                                                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                                                    Cancel
                                                </button>
                                                <button type="submit" class="btn btn-danger px-4">
                                                    <i class="fas fa-trash me-1"></i> Delete
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        @empty
                            <tr>
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

    <template id="noResultsTpl">
        <tr id="noResultsRow">
            <td colspan="8" class="text-center py-4 text-muted">
                <i class="fas fa-search me-1"></i> No tasks match your filters.
            </td>
        </tr>
    </template>

</div>

@include('tasks.partials.create')

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
// ── Tom Select: init on modal open ────────────────────────────────────────────
document.addEventListener('shown.bs.modal', function (e) {
    e.target.querySelectorAll('select.ts-select').forEach(function (sel) {
        if (sel.tomselect) return; // already initialised
        new TomSelect(sel, {
            placeholder: sel.dataset.placeholder || 'Select…',
            allowEmptyOption: true,
            maxOptions: 50,
        });
    });
});

// ── Description character counters ────────────────────────────────────────────
document.addEventListener('input', function (e) {
    if (!e.target.classList.contains('desc-textarea')) return;
    const counter = e.target.closest('.mb-4').querySelector('.desc-count');
    if (counter) counter.textContent = e.target.value.length + ' / 500';
});

// Create modal description counter
const createDesc = document.getElementById('createDesc');
const createDescCount = document.getElementById('createDescCount');
if (createDesc && createDescCount) {
    createDesc.addEventListener('input', function () {
        createDescCount.textContent = this.value.length + ' / 500';
    });
}

// ── Table filter ─────────────────────────────────────────────────────────────
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

        tbody.querySelectorAll('tr.task-row').forEach(function (row) {
            const show = (!q  || row.textContent.toLowerCase().includes(q))
                      && (!st || row.dataset.status   === st)
                      && (!pr || row.dataset.priority === pr)
                      && (!cat|| row.dataset.category === cat);
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        const existing = document.getElementById('noResultsRow');
        if (existing) existing.remove();

        if (visible === 0 && tbody.querySelectorAll('tr.task-row').length > 0) {
            tbody.appendChild(document.getElementById('noResultsTpl').content.cloneNode(true));
        }
    }

    [search, status, priority, category].forEach(function (el) {
        el.addEventListener('input', filter);
    });

    clear.addEventListener('click', function () {
        search.value = status.value = priority.value = category.value = '';
        filter();
    });
})();
</script>
@endsection
