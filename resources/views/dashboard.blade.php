@extends('layouts.admin')

@section('title', 'Dashboard')

@section('styles')
<style>
/* ── Donut ring ────────────────────────────────────────────────────────────── */
.donut-ring { position: relative; width: 160px; height: 160px; margin: 0 auto; }
.donut-ring svg { transform: rotate(-90deg); }
.donut-ring .donut-label {
    position: absolute; inset: 0;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    font-size: 1.75rem; font-weight: 700; line-height: 1.1;
}
.donut-ring .donut-label small { font-size: .7rem; font-weight: 400; color: #6c757d; }

/* ── KPI cards ─────────────────────────────────────────────────────────────── */
.kpi-card { border: none; border-radius: .75rem; transition: transform .15s; }
.kpi-card:hover { transform: translateY(-3px); }
.kpi-icon {
    width: 48px; height: 48px; border-radius: .5rem;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem; flex-shrink: 0;
}

/* ── Priority bars ─────────────────────────────────────────────────────────── */
.priority-bar { height: 10px; border-radius: 5px; transition: width .6s ease; }

/* ── Overdue badge ─────────────────────────────────────────────────────────── */
.overdue-days {
    font-size: .7rem; font-weight: 600;
    background: #fee2e2; color: #dc3545;
    padding: 2px 8px; border-radius: 20px;
    white-space: nowrap;
}

/* ── Due-soon chip ─────────────────────────────────────────────────────────── */
.due-chip {
    font-size: .7rem; padding: 2px 8px; border-radius: 20px;
    font-weight: 600; white-space: nowrap;
}
.due-chip.overdue   { background:#fee2e2; color:#dc3545; }
.due-chip.today     { background:#fff3cd; color:#856404; }
.due-chip.soon      { background:#d1ecf1; color:#0c5460; }
.due-chip.future    { background:#e2e8f0; color:#475569; }
</style>
@endsection

@section('content')

@php
    $greeting = match(true) {
        now()->hour < 12 => 'Good morning',
        now()->hour < 17 => 'Good afternoon',
        default          => 'Good evening',
    };
    $userName = Auth::user()->name ?? 'there';
@endphp

<div class="container-fluid pb-4">

    {{-- ── Page header ────────────────────────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0 fw-bold">{{ $greeting }}, {{ explode(' ', $userName)[0] }} 👋</h3>
            <small class="text-muted">{{ now()->format('l, d F Y') }}</small>
        </div>
        <a href="{{ route('tasks.index') }}" class="btn btn-primary">
            <i class="fas fa-tasks me-1"></i> View All Tasks
        </a>
    </div>

    {{-- ── KPI cards ──────────────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">

        @php
            $kpis = [
                ['label'=>'Total Tasks',    'value'=>$totalTasks,      'icon'=>'fa-layer-group',            'bg'=>'bg-primary bg-opacity-10',   'text'=>'text-primary'],
                ['label'=>'Pending',        'value'=>$pendingTasks,    'icon'=>'fa-hourglass-half',         'bg'=>'bg-secondary bg-opacity-10', 'text'=>'text-secondary'],
                ['label'=>'In Progress',    'value'=>$inProgressTasks, 'icon'=>'fa-spinner',                'bg'=>'bg-info bg-opacity-10',      'text'=>'text-info'],
                ['label'=>'Completed',      'value'=>$completedTasks,  'icon'=>'fa-check-circle',           'bg'=>'bg-success bg-opacity-10',   'text'=>'text-success'],
                ['label'=>'Overdue',        'value'=>$overdueTasks,    'icon'=>'fa-exclamation-triangle',   'bg'=>'bg-danger bg-opacity-10',    'text'=>'text-danger'],
                ['label'=>'Team Members',   'value'=>$totalUsers,      'icon'=>'fa-users',                  'bg'=>'bg-warning bg-opacity-10',   'text'=>'text-warning'],
            ];
        @endphp

        @foreach($kpis as $kpi)
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card kpi-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="kpi-icon {{ $kpi['bg'] }} {{ $kpi['text'] }}">
                        <i class="fas {{ $kpi['icon'] }}"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1">{{ $kpi['value'] }}</div>
                        <div class="small text-muted mt-1">{{ $kpi['label'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

    </div>

    {{-- ── Row 2: Donut + Priority + Category distribution ───────────────── --}}
    <div class="row g-3 mb-4">

        {{-- Donut: Completion rate --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <p class="fw-semibold text-muted small text-uppercase letter-spacing mb-3">
                        Completion Rate
                    </p>
                    <div class="donut-ring mb-3">
                        <svg viewBox="0 0 36 36" width="160" height="160">
                            {{-- Track --}}
                            <circle cx="18" cy="18" r="15.9155"
                                    fill="none" stroke="#e9ecef" stroke-width="3.5"/>
                            {{-- Progress --}}
                            <circle cx="18" cy="18" r="15.9155"
                                    fill="none"
                                    stroke="{{ $productivity >= 80 ? '#198754' : ($productivity >= 50 ? '#0dcaf0' : '#ffc107') }}"
                                    stroke-width="3.5"
                                    stroke-linecap="round"
                                    stroke-dasharray="{{ $productivity }}, 100"/>
                        </svg>
                        <div class="donut-label">
                            {{ $productivity }}%
                            <small>complete</small>
                        </div>
                    </div>
                    <div class="row text-center g-0 border-top pt-3 mt-1">
                        <div class="col">
                            <div class="fw-bold text-success">{{ $completedTasks }}</div>
                            <div class="small text-muted">Done</div>
                        </div>
                        <div class="col border-start border-end">
                            <div class="fw-bold text-info">{{ $inProgressTasks }}</div>
                            <div class="small text-muted">Active</div>
                        </div>
                        <div class="col">
                            <div class="fw-bold text-secondary">{{ $pendingTasks }}</div>
                            <div class="small text-muted">Pending</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Priority breakdown --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body py-4">
                    <p class="fw-semibold text-muted small text-uppercase mb-3">
                        Priority Breakdown
                    </p>

                    @php
                        $pTotal = max($totalTasks, 1);
                        $priorities = [
                            ['label'=>'High',   'count'=>$highPriorityCount,   'color'=>'bg-danger',  'text'=>'text-danger'],
                            ['label'=>'Medium', 'count'=>$mediumPriorityCount, 'color'=>'bg-warning', 'text'=>'text-warning'],
                            ['label'=>'Low',    'count'=>$lowPriorityCount,    'color'=>'bg-success', 'text'=>'text-success'],
                        ];
                    @endphp

                    @foreach($priorities as $p)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small fw-semibold {{ $p['text'] }}">
                                <i class="fas fa-circle me-1" style="font-size:.5rem;vertical-align:middle;"></i>
                                {{ $p['label'] }}
                            </span>
                            <span class="small text-muted">
                                {{ $p['count'] }} / {{ $totalTasks }}
                            </span>
                        </div>
                        <div class="progress" style="height:10px;border-radius:5px;">
                            <div class="progress-bar {{ $p['color'] }}"
                                 role="progressbar"
                                 style="width:{{ $totalTasks > 0 ? round($p['count']/$pTotal*100) : 0 }}%">
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <hr class="my-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small text-muted">Cancelled tasks</span>
                        <span class="badge bg-danger bg-opacity-10 text-danger fw-semibold">
                            {{ $cancelledTasks }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Category distribution --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body py-4">
                    <p class="fw-semibold text-muted small text-uppercase mb-3">
                        Tasks by Category
                    </p>

                    @if($categoryStats->isEmpty())
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-tags fa-2x mb-2 d-block opacity-25"></i>
                            No categories yet
                        </div>
                    @else
                        @php
                            $palette = ['bg-primary','bg-info','bg-success','bg-warning','bg-danger','bg-secondary'];
                            $maxCat  = $categoryStats->max('tasks_count') ?: 1;
                        @endphp
                        @foreach($categoryStats as $i => $cat)
                        <div class="mb-2">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small fw-semibold text-truncate" style="max-width:140px;" title="{{ $cat->name }}">
                                    {{ $cat->name }}
                                </span>
                                <span class="small text-muted">{{ $cat->tasks_count }}</span>
                            </div>
                            <div class="progress" style="height:8px;border-radius:4px;">
                                <div class="progress-bar {{ $palette[$i % count($palette)] }}"
                                     role="progressbar"
                                     style="width:{{ round($cat->tasks_count/$maxCat*100) }}%">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @endif

                    <div class="text-end mt-3">
                        <a href="{{ route('categories.index') }}" class="small text-primary text-decoration-none">
                            Manage categories <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Overdue tasks ───────────────────────────────────────────────────── --}}
    @if($overdueTasksList->isNotEmpty())
    <div class="card border-0 shadow-sm border-start border-danger border-4 mb-4">
        <div class="card-header bg-white d-flex align-items-center gap-2 py-3">
            <span class="rounded-circle bg-danger d-inline-flex align-items-center justify-content-center"
                  style="width:28px;height:28px;">
                <i class="fas fa-exclamation text-white" style="font-size:.75rem;"></i>
            </span>
            <span class="fw-semibold">Overdue Tasks</span>
            <span class="badge bg-danger ms-1">{{ $overdueTasks }}</span>
            <a href="{{ route('tasks.index') }}" class="ms-auto small text-decoration-none text-muted">
                View all <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <tbody>
                        @foreach($overdueTasksList as $task)
                        <tr>
                            <td class="ps-4" style="width:36%;">
                                <div class="fw-semibold">{{ $task->title }}</div>
                                @if($task->category)
                                    <span class="badge rounded-pill bg-light text-dark border small">
                                        {{ $task->category->name }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($task->assignedUser)
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center"
                                             style="width:26px;height:26px;font-size:10px;flex-shrink:0;">
                                            {{ strtoupper(substr($task->assignedUser->name,0,1)) }}
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
                                    <span class="badge bg-warning text-dark">Medium</span>
                                @else
                                    <span class="badge bg-success">Low</span>
                                @endif
                            </td>
                            <td>
                                <span class="overdue-days">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ now()->diffInDays($task->due_date) }} day{{ now()->diffInDays($task->due_date) !== 1 ? 's' : '' }} overdue
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                @if($task->status === 'pending')
                                    <a href="{{ route('tasks.start', $task) }}"
                                       class="btn btn-sm btn-outline-info me-1">
                                        <i class="fas fa-play me-1"></i> Start
                                    </a>
                                @endif
                                <a href="{{ route('tasks.complete', $task) }}"
                                   class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-check me-1"></i> Finish
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- ── My Tasks ────────────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex align-items-center gap-2 py-3">
            <span class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center"
                  style="width:28px;height:28px;">
                <i class="fas fa-user text-white" style="font-size:.75rem;"></i>
            </span>
            <span class="fw-semibold">My Tasks</span>
            @if($myTasks->isNotEmpty())
                <span class="badge bg-primary ms-1">{{ $myTasks->count() }}</span>
            @endif
            <a href="{{ route('tasks.index') }}" class="ms-auto small text-decoration-none text-muted">
                View all <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="card-body p-0">
            @if($myTasks->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-check-double fa-3x text-success mb-3 d-block opacity-50"></i>
                    <p class="fw-semibold mb-1">You're all caught up!</p>
                    <p class="text-muted small mb-0">No pending or in-progress tasks assigned to you.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Task</th>
                                <th width="120">Priority</th>
                                <th width="120">Status</th>
                                <th width="140">Due Date</th>
                                <th width="160" class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($myTasks as $task)
                            @php
                                $daysUntil = $task->due_date ? now()->startOfDay()->diffInDays($task->due_date->startOfDay(), false) : null;
                                $chipClass = match(true) {
                                    $daysUntil === null       => '',
                                    $daysUntil < 0            => 'overdue',
                                    $daysUntil === 0          => 'today',
                                    $daysUntil <= 3           => 'soon',
                                    default                   => 'future',
                                };
                                $chipLabel = match(true) {
                                    $daysUntil === null       => null,
                                    $daysUntil < 0            => abs($daysUntil).'d overdue',
                                    $daysUntil === 0          => 'Due today',
                                    $daysUntil === 1          => 'Due tomorrow',
                                    $daysUntil <= 3           => 'Due in '.$daysUntil.'d',
                                    default                   => $task->due_date->format('d M'),
                                };
                            @endphp
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-semibold">{{ $task->title }}</div>
                                    @if($task->category)
                                        <span class="badge rounded-pill bg-light text-dark border small">
                                            {{ $task->category->name }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($task->priority === 'high')
                                        <span class="badge bg-danger"><i class="fas fa-arrow-up me-1"></i>High</span>
                                    @elseif($task->priority === 'medium')
                                        <span class="badge bg-warning text-dark">Medium</span>
                                    @else
                                        <span class="badge bg-success">Low</span>
                                    @endif
                                </td>
                                <td>
                                    @if($task->status === 'pending')
                                        <span class="badge bg-secondary">⏳ Pending</span>
                                    @else
                                        <span class="badge bg-info text-dark">🔄 In Progress</span>
                                    @endif
                                </td>
                                <td>
                                    @if($chipLabel)
                                        <span class="due-chip {{ $chipClass }}">
                                            <i class="fas fa-calendar-alt me-1"></i>{{ $chipLabel }}
                                        </span>
                                    @else
                                        <span class="text-muted small">No due date</span>
                                    @endif
                                </td>
                                <td class="pe-4 text-end">
                                    @if($task->status === 'pending')
                                        <a href="{{ route('tasks.start', $task) }}"
                                           class="btn btn-sm btn-outline-info me-1">
                                            <i class="fas fa-play me-1"></i> Start
                                        </a>
                                    @endif
                                    <a href="{{ route('tasks.complete', $task) }}"
                                       class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-check me-1"></i> Finish
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection
