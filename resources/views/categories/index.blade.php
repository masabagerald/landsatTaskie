@extends('layouts.admin')

@section('title', 'Categories')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
    .ts-wrapper.single .ts-control { padding: .375rem .75rem; }
    .ts-wrapper .ts-control { border-color: #ced4da; border-radius: .375rem; }
    .ts-wrapper.focus .ts-control { border-color: #86b7fe; box-shadow: 0 0 0 .25rem rgba(13,110,253,.25); }
</style>
@endsection

@section('content')

<div class="container-fluid">

    {{-- Page header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0"><i class="fas fa-tags text-primary me-2"></i> Categories</h3>
            <small class="text-muted">{{ $categories->count() }} categor{{ $categories->count() !== 1 ? 'ies' : 'y' }}</small>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="fas fa-plus me-1"></i> Add Category
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

    {{-- Search bar --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <div class="input-group input-group-sm" style="max-width:360px;">
                <span class="input-group-text bg-white border-end-0">
                    <i class="fas fa-search text-muted"></i>
                </span>
                <input id="categorySearch" type="text"
                       class="form-control border-start-0"
                       placeholder="Search categories…">
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="categoriesTable">
                    <thead class="table-light">
                        <tr>
                            <th width="50" class="ps-3">#</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th width="110" class="text-center">Tasks</th>
                            <th width="130" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr class="category-row">
                                <td class="ps-3 text-muted small">{{ $loop->iteration }}</td>

                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center"
                                             style="width:34px;height:34px;flex-shrink:0;">
                                            <i class="fas fa-tag" style="font-size:13px;"></i>
                                        </div>
                                        <span class="fw-semibold">{{ $category->name }}</span>
                                    </div>
                                </td>

                                <td class="text-muted">{{ $category->description ?: '—' }}</td>

                                <td class="text-center">
                                    @if($category->tasks_count > 0)
                                        <span class="badge rounded-pill bg-primary">{{ $category->tasks_count }}</span>
                                    @else
                                        <span class="badge rounded-pill bg-light text-muted border">0</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-warning me-1"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal{{ $category->id }}"
                                            title="Edit">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteModal{{ $category->id }}"
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>

                            {{-- ── Edit Modal ───────────────────────────────────────── --}}
                            <div class="modal fade" id="editModal{{ $category->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
                                    <div class="modal-content border-0 shadow">
                                        <form method="POST" action="{{ route('categories.update', $category) }}">
                                            @csrf
                                            @method('PATCH')

                                            <div class="modal-header bg-warning">
                                                <h5 class="modal-title fw-semibold">
                                                    <i class="fas fa-pen me-2"></i> Edit Category
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body p-4">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">
                                                        Name <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" name="name"
                                                           value="{{ $category->name }}"
                                                           class="form-control form-control-lg"
                                                           placeholder="Category name"
                                                           required autofocus>
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label fw-semibold d-flex justify-content-between">
                                                        Description
                                                        <span class="text-muted fw-normal small desc-count">
                                                            {{ strlen($category->description ?? '') }} / 300
                                                        </span>
                                                    </label>
                                                    <textarea name="description" rows="4"
                                                              class="form-control desc-textarea"
                                                              placeholder="Describe what this category is for…"
                                                              maxlength="300">{{ $category->description }}</textarea>
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
                            <div class="modal fade" id="deleteModal{{ $category->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
                                    <div class="modal-content border-0 shadow">
                                        <form method="POST" action="{{ route('categories.destroy', $category) }}">
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
                                                <h5 class="fw-bold mb-1">Delete Category?</h5>
                                                <p class="text-muted mb-1">You are about to permanently delete:</p>
                                                <p class="fw-semibold mb-2">{{ $category->name }}</p>
                                                @if($category->tasks_count > 0)
                                                    <div class="alert alert-warning py-2 text-start small mb-0">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                                        This will also delete
                                                        <strong>{{ $category->tasks_count }}
                                                        task{{ $category->tasks_count !== 1 ? 's' : '' }}</strong>
                                                        in this category.
                                                    </div>
                                                @endif
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
                                <td colspan="5" class="text-center py-5">
                                    <i class="fas fa-tags fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted mb-1">No categories yet</p>
                                    <button class="btn btn-sm btn-primary mt-2"
                                            data-bs-toggle="modal" data-bs-target="#createModal">
                                        <i class="fas fa-plus me-1"></i> Add your first category
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
            <td colspan="5" class="text-center py-4 text-muted">
                <i class="fas fa-search me-1"></i> No categories match your search.
            </td>
        </tr>
    </template>

</div>

{{-- ── Create Modal ─────────────────────────────────────────────────────────── --}}
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="{{ route('categories.store') }}">
                @csrf

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle me-2"></i> New Category
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name"
                               class="form-control form-control-lg"
                               placeholder="e.g. Backend, Design, Testing…"
                               required autofocus>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold d-flex justify-content-between">
                            Description
                            <span class="text-muted fw-normal small" id="createCatDescCount">0 / 300</span>
                        </label>
                        <textarea name="description" rows="4"
                                  id="createCatDesc"
                                  class="form-control"
                                  placeholder="Describe what this category is for…"
                                  maxlength="300"></textarea>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ── Character counters ────────────────────────────────────────────────────────
document.addEventListener('input', function (e) {
    if (!e.target.classList.contains('desc-textarea')) return;
    const counter = e.target.closest('.mb-0, .mb-3').querySelector('.desc-count');
    if (counter) counter.textContent = e.target.value.length + ' / 300';
});

const createCatDesc = document.getElementById('createCatDesc');
const createCatDescCount = document.getElementById('createCatDescCount');
if (createCatDesc) {
    createCatDesc.addEventListener('input', function () {
        createCatDescCount.textContent = this.value.length + ' / 300';
    });
}

// ── Table search ──────────────────────────────────────────────────────────────
(function () {
    const search = document.getElementById('categorySearch');
    const tbody  = document.querySelector('#categoriesTable tbody');

    search.addEventListener('input', function () {
        const q = this.value.toLowerCase();
        let visible = 0;

        tbody.querySelectorAll('tr.category-row').forEach(function (row) {
            const show = !q || row.textContent.toLowerCase().includes(q);
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        const existing = document.getElementById('noResultsRow');
        if (existing) existing.remove();

        if (visible === 0 && tbody.querySelectorAll('tr.category-row').length > 0) {
            tbody.appendChild(document.getElementById('noResultsTpl').content.cloneNode(true));
        }
    });
})();
</script>
@endsection
