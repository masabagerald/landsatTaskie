@extends('layouts.admin')

@section('title', 'Categories')

@section('content')

<div class="container-fluid">

    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">

            <h3 class="card-title">
                Categories
            </h3>

            <button class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#createModal">
                <i class="fas fa-plus"></i>
                Add Category
            </button>

        </div>

        <div class="card-body">

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

            <table class="table table-bordered table-striped">

                <thead>
                    <tr>
                        <th width="80">#</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th width="180">Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($categories as $category)

                        <tr>

                            <td>
                                {{ $loop->iteration }}
                            </td>

                            <td>
                                {{ $category->name }}
                            </td>

                            <td>
                                {{ $category->description }}
                            </td>

                            <td>

                                <button
                                    class="btn btn-warning btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal{{ $category->id }}">
                                    <i class="fas fa-edit"></i>
                                    Edit
                                </button>

                                <button
                                    class="btn btn-danger btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteModal{{ $category->id }}">
                                    <i class="fas fa-trash"></i>
                                    Delete
                                </button>

                            </td>

                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade"
                             id="editModal{{ $category->id }}"
                             tabindex="-1">

                            <div class="modal-dialog">

                                <form method="POST"
                                      action="{{ route('categories.update', $category->id) }}">

                                    @csrf
                                    @method('PUT')

                                    <div class="modal-content">

                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                Edit Category
                                            </h5>

                                            <button type="button"
                                                    class="btn-close"
                                                    data-bs-dismiss="modal">
                                            </button>
                                        </div>

                                        <div class="modal-body">

                                            <div class="mb-3">

                                                <label class="form-label">
                                                    Name
                                                </label>

                                                <input type="text"
                                                       name="name"
                                                       value="{{ $category->name }}"
                                                       class="form-control"
                                                       required>

                                            </div>

                                            <div class="mb-3">

                                                <label class="form-label">
                                                    Description
                                                </label>

                                                <textarea name="description"
                                                          rows="4"
                                                          class="form-control">{{ $category->description }}</textarea>

                                            </div>

                                        </div>

                                        <div class="modal-footer">

                                            <button type="button"
                                                    class="btn btn-secondary"
                                                    data-bs-dismiss="modal">
                                                Close
                                            </button>

                                            <button type="submit"
                                                    class="btn btn-warning">
                                                Update
                                            </button>

                                        </div>

                                    </div>

                                </form>

                            </div>

                        </div>

                        <!-- Delete Modal -->
                        <div class="modal fade"
                             id="deleteModal{{ $category->id }}"
                             tabindex="-1">

                            <div class="modal-dialog">

                                <form method="POST"
                                      action="{{ route('categories.destroy', $category->id) }}">

                                    @csrf
                                    @method('DELETE')

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <h5 class="modal-title">
                                                Delete Category
                                            </h5>

                                            <button type="button"
                                                    class="btn-close"
                                                    data-bs-dismiss="modal">
                                            </button>

                                        </div>

                                        <div class="modal-body">

                                            Are you sure you want to delete

                                            <strong>
                                                {{ $category->name }}
                                            </strong>?

                                        </div>

                                        <div class="modal-footer">

                                            <button type="button"
                                                    class="btn btn-secondary"
                                                    data-bs-dismiss="modal">
                                                Cancel
                                            </button>

                                            <button type="submit"
                                                    class="btn btn-danger">
                                                Delete
                                            </button>

                                        </div>

                                    </div>

                                </form>

                            </div>

                        </div>

                    @empty

                        <tr>
                            <td colspan="4" class="text-center">
                                No Categories Found
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

<!-- Create Modal -->
<div class="modal fade"
     id="createModal"
     tabindex="-1">

    <div class="modal-dialog">

        <form method="POST"
              action="{{ route('categories.store') }}">

            @csrf

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Add Category
                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <div class="mb-3">

                        <label class="form-label">
                            Name
                        </label>

                        <input type="text"
                               name="name"
                               class="form-control"
                               required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Description
                        </label>

                        <textarea name="description"
                                  rows="4"
                                  class="form-control"></textarea>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Close
                    </button>

                    <button type="submit"
                            class="btn btn-primary">
                        Save
                    </button>

                </div>

            </div>

        </form>

    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@endsection