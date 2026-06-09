<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:500px;">
        <div class="modal-content border-0 shadow">

            <form method="POST" action="{{ route('users.store') }}">
                @csrf

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="createModalLabel">
                        <i class="fas fa-user-plus me-2"></i> New User
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Full Name <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-user text-muted"></i>
                            </span>
                            <input type="text" name="name"
                                   class="form-control"
                                   placeholder="e.g. Jane Smith"
                                   value="{{ old('name') }}"
                                   required autofocus>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Email Address <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-envelope text-muted"></i>
                            </span>
                            <input type="email" name="email"
                                   class="form-control"
                                   placeholder="jane@example.com"
                                   value="{{ old('email') }}"
                                   required>
                        </div>
                    </div>

                    <hr class="my-3">

                    <p class="small text-muted mb-3">
                        <i class="fas fa-info-circle me-1 text-info"></i>
                        Leave the password fields blank to use the default:
                        <code>password123</code>
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-lock text-muted"></i>
                            </span>
                            <input type="password" name="password"
                                   id="createPassword"
                                   class="form-control"
                                   placeholder="Min. 8 characters"
                                   autocomplete="new-password">
                            <button type="button" class="btn btn-outline-secondary toggle-pw" data-target="createPassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-lock text-muted"></i>
                            </span>
                            <input type="password" name="password_confirmation"
                                   id="createPasswordConfirm"
                                   class="form-control"
                                   placeholder="Repeat password"
                                   autocomplete="new-password">
                            <button type="button" class="btn btn-outline-secondary toggle-pw" data-target="createPasswordConfirm">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> Create User
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
