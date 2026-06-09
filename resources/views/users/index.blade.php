@extends('layouts.admin')

@section('title', 'Users')

@section('content')

<div class="container-fluid">

    <div class="row mb-3">

        <div class="col-md-6">
            <h3>
                <i class="fas fa-users"></i>
                User Management
            </h3>
        </div>

        <div class="col-md-6 text-end">

            <button type="button"
                    class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#createModal">

                <i class="fas fa-plus"></i>
                Add User

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

    <div class="card">

        <div class="card-header">

            <h3 class="card-title">

                System Users

            </h3>

        </div>

        <div class="card-body">

            <table class="table table-bordered table-striped">

                <thead>

                    <tr>

                        <th width="60">#</th>

                        <th>Name</th>

                        <th>Email</th>

                        <th>Created</th>

                        <th width="180">Actions</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($users as $user)

                        <tr>

                            <td>
                                {{ $loop->iteration }}
                            </td>

                            <td>

                                <strong>
                                    {{ $user->name }}
                                </strong>

                            </td>

                            <td>
                                {{ $user->email }}
                            </td>

                            <td>
                                {{ $user->created_at->format('d M Y') }}
                            </td>

                            <td>

                                <button type="button"
                                        class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal{{ $user->id }}">

                                    <i class="fas fa-edit"></i>

                                </button>

                                <button type="button"
                                        class="btn btn-danger btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal{{ $user->id }}">

                                    <i class="fas fa-trash"></i>

                                </button>

                            </td>

                        </tr>

                        @include('users.partials.edit')

                        @include('users.partials.delete')

                    @empty

                        <tr>

                            <td colspan="5" class="text-center">

                                No Users Found

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@include('users.partials.create')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@endsection