@extends('layouts.admin')

@section('title', 'Categories')

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
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Search bar --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <div class="row g-2">
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input id="categorySearch" type="text"
                               class="form-control border-start-0"
                               placeholder="Search categories…">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Category table --}}
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

                                <td class="text-muted">
                                    {{ $category->description ?: '—' }}
                                </td>

                                <td class="text-center">
                                    @if($category->tasks_count > 0)
                                        <span class="badge rounded-pill bg-primary">
                                            {{ $category->tasks_count }}
                                        </span>
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

                            {{-- Edit modal --}}
                            <div class="modal fade" id="editModal{{ $category->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('categories.update', $category) }}">
                                            @csrf
                                            @method('PATCH')
                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    <i class="fas fa-pen me-1"></i> Edit Category
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">
                                                        Name <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" name="name"
                                                           value="{{ $category->name }}"
                                                           class="form-control" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Description</label>
                                                    <textarea name="description" rows="3"
                                                              class="form-control">{{ $category->description }}</textarea>
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
                            <div class="modal fade" id="deleteModal{{ $category->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('categories.destroy', $category) }}">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-header border-0">
                                                <h5 class="modal-title text-danger">
                                                    <i class="fas fa-trash me-1"></i> Delete Category
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center py-4">
                                                <p class="mb-1">You are about to delete:</p>
                                                <p class="fw-bold fs-5">{{ $category->name }}</p>
                                                @if($category->tasks_count > 0)
                                                    <div class="alert alert-warning py-2 mt-2 text-start">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                                        This will also delete
                                                        <strong>{{ $category->tasks_count }} task{{ $category->tasks_count !== 1 ? 's' : '' }}</strong>
                                                        assigned to this category.
                                                    </div>
                                                @else
                                                    <p class="text-muted small">This action cannot be undone.</p>
                                                @endif
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

{{-- Create modal --}}
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('categories.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus me-1"></i> Add Category
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" class="form-control"
                               placeholder="e.g. Backend, Design…" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" rows="3"
                                  class="form-control"
                                  placeholder="Optional description…"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    const search = document.getElementById('categorySearch');
    const tbody  = document.querySelector('#categoriesTable tbody');

    search.addEventListener('input', function () {
        const q = this.value.toLowerCase();
        let visible = 0;

        tbody.querySelectorAll('tr.category-row').forEach(row => {
            const show = !q || row.textContent.toLowerCase().includes(q);
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        const existing = document.getElementById('noResultsRow');
        if (existing) existing.remove();

        if (visible === 0 && tbody.querySelectorAll('tr.category-row').length > 0) {
            const tpl = document.getElementById('noResultsTpl');
            tbody.appendChild(tpl.content.cloneNode(true));
        }
    });
})();
</script>

@endsection
