@extends('layouts.admin')

@section('title', 'Users')

@section('styles')
<style>
    .avatar-circle {
        width: 38px; height: 38px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: .95rem; flex-shrink: 0;
    }
    .kpi-card { border: none; border-radius: .75rem; }
</style>
@endsection

@section('content')

<div class="container-fluid">

    {{-- Page header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0"><i class="fas fa-users text-primary me-2"></i> User Management</h3>
            <small class="text-muted">{{ $users->count() }} registered user{{ $users->count() !== 1 ? 's' : '' }}</small>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="fas fa-user-plus me-1"></i> Add User
        </button>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
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

    {{-- KPI strip --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card kpi-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="avatar-circle bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1">{{ $users->count() }}</div>
                        <div class="small text-muted mt-1">Total Users</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card kpi-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="avatar-circle bg-success bg-opacity-10 text-success">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1">{{ $totalTasks }}</div>
                        <div class="small text-muted mt-1">Assigned Tasks</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card kpi-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="avatar-circle bg-info bg-opacity-10 text-info">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1">{{ $users->where('tasks_count', '>', 0)->count() }}</div>
                        <div class="small text-muted mt-1">Active Assignees</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card kpi-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="avatar-circle bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-user-slash"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1">{{ $users->where('tasks_count', 0)->count() }}</div>
                        <div class="small text-muted mt-1">Unassigned</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Search bar --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <div class="input-group input-group-sm" style="max-width:360px;">
                <span class="input-group-text bg-white border-end-0">
                    <i class="fas fa-search text-muted"></i>
                </span>
                <input id="userSearch" type="text"
                       class="form-control border-start-0"
                       placeholder="Search by name or email…">
            </div>
        </div>
    </div>

    {{-- Users table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="usersTable">
                    <thead class="table-light">
                        <tr>
                            <th width="50" class="ps-4">#</th>
                            <th>User</th>
                            <th>Email</th>
                            <th width="130" class="text-center">Assigned Tasks</th>
                            <th width="130">Joined</th>
                            <th width="100" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            @php
                                $colors = ['bg-primary','bg-success','bg-info','bg-warning','bg-danger','bg-secondary'];
                                $color  = $colors[$user->id % count($colors)];
                                $isMe   = $user->id === auth()->id();
                            @endphp
                            <tr class="user-row">
                                <td class="ps-4 text-muted small">{{ $loop->iteration }}</td>

                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-circle {{ $color }} bg-opacity-80 text-white">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">
                                                {{ $user->name }}
                                                @if($isMe)
                                                    <span class="badge bg-primary ms-1" style="font-size:.65rem;">You</span>
                                                @endif
                                            </div>
                                            <div class="small text-muted">
                                                @if($user->email_verified_at)
                                                    <i class="fas fa-shield-alt text-success me-1" title="Email verified"></i>Verified
                                                @else
                                                    <i class="fas fa-exclamation-circle text-warning me-1" title="Not verified"></i>Unverified
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="text-muted">{{ $user->email }}</td>

                                <td class="text-center">
                                    @if($user->tasks_count > 0)
                                        <a href="{{ route('tasks.index') }}"
                                           class="badge rounded-pill bg-primary text-decoration-none">
                                            {{ $user->tasks_count }} task{{ $user->tasks_count !== 1 ? 's' : '' }}
                                        </a>
                                    @else
                                        <span class="badge rounded-pill bg-light text-muted border">None</span>
                                    @endif
                                </td>

                                <td class="text-muted small">
                                    {{ $user->created_at->format('d M Y') }}
                                </td>

                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-warning me-1"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal{{ $user->id }}"
                                            title="Edit">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteModal{{ $user->id }}"
                                            title="{{ $isMe ? 'Cannot delete your own account' : 'Delete' }}"
                                            {{ $isMe ? 'disabled' : '' }}>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>

                            @include('users.partials.edit')
                            @include('users.partials.delete')

                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-users fa-3x text-muted mb-3 d-block opacity-50"></i>
                                    <p class="text-muted mb-1">No users yet</p>
                                    <button class="btn btn-sm btn-primary mt-2"
                                            data-bs-toggle="modal" data-bs-target="#createModal">
                                        <i class="fas fa-user-plus me-1"></i> Add your first user
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
            <td colspan="6" class="text-center py-4 text-muted">
                <i class="fas fa-search me-1"></i> No users match your search.
            </td>
        </tr>
    </template>

</div>

@include('users.partials.create')

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ── Password show/hide toggle ─────────────────────────────────────────────────
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.toggle-pw');
    if (!btn) return;
    const input = document.getElementById(btn.dataset.target);
    if (!input) return;
    const isText = input.type === 'text';
    input.type = isText ? 'password' : 'text';
    btn.querySelector('i').className = isText ? 'fas fa-eye' : 'fas fa-eye-slash';
});

// ── Table search ──────────────────────────────────────────────────────────────
(function () {
    const search = document.getElementById('userSearch');
    const tbody  = document.querySelector('#usersTable tbody');

    search.addEventListener('input', function () {
        const q = this.value.toLowerCase();
        let visible = 0;

        tbody.querySelectorAll('tr.user-row').forEach(function (row) {
            const show = !q || row.textContent.toLowerCase().includes(q);
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        const existing = document.getElementById('noResultsRow');
        if (existing) existing.remove();

        if (visible === 0 && tbody.querySelectorAll('tr.user-row').length > 0) {
            tbody.appendChild(document.getElementById('noResultsTpl').content.cloneNode(true));
        }
    });
})();
</script>
@endsection
