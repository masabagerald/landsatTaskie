<!DOCTYPE html>

<html lang="en">

<head>


<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>
    Register | Landsat Task Performance System
</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet">

<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">


</head>

<body class="bg-light">

<div class="container">

<div class="row justify-content-center align-items-center min-vh-100">

    <div class="col-md-6">

        <div class="card shadow border-0">

            <div class="card-body p-5">

                <div class="text-center mb-4">

                    <i class="fas fa-user-plus text-primary fa-4x mb-3"></i>

                    <h2 class="fw-bold">

                        Landsat ICT Solutions

                    </h2>

                    <p class="text-muted">

                        Task Performance & Productivity Management System

                    </p>

                    <hr>

                    <h5>

                        Create Account

                    </h5>

                </div>

                <form method="POST"
                      action="{{ route('register') }}">

                    @csrf

                    <div class="mb-3">

                        <label class="form-label">
                            Full Name
                        </label>

                        <input type="text"
                               name="name"
                               value="{{ old('name') }}"
                               class="form-control @error('name') is-invalid @enderror"
                               required
                               autofocus>

                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Email Address
                        </label>

                        <input type="email"
                               name="email"
                               value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror"
                               required>

                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Password
                        </label>

                        <input type="password"
                               name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               required>

                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <div class="mb-4">

                        <label class="form-label">
                            Confirm Password
                        </label>

                        <input type="password"
                               name="password_confirmation"
                               class="form-control"
                               required>

                    </div>

                    <div class="d-grid">

                        <button type="submit"
                                class="btn btn-success btn-lg">

                            <i class="fas fa-user-plus me-2"></i>

                            Create Account

                        </button>

                    </div>

                    <div class="text-center mt-4">

                        <span class="text-muted">
                            Already have an account?
                        </span>

                        <a href="{{ route('login') }}"
                           class="text-decoration-none fw-bold">

                            Login Here

                        </a>

                    </div>

                </form>

            </div>

        </div>

        <div class="text-center mt-3">

            <small class="text-muted">

                © {{ date('Y') }}

                Landsat ICT Solutions

            </small>

        </div>

    </div>

</div>php artisan tinker

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
