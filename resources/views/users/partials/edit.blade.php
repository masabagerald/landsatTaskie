<div class="modal fade" id="editModal{{ $user->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:500px;">
        <div class="modal-content border-0 shadow">

            <form method="POST" action="{{ route('users.update', $user) }}">
                @csrf
                @method('PATCH')

                <div class="modal-header bg-warning">
                    <h5 class="modal-title fw-semibold">
                        <i class="fas fa-user-edit me-2"></i> Edit User
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">

                    {{-- Avatar preview --}}
                    <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold"
                             style="width:48px;height:48px;font-size:1.1rem;flex-shrink:0;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-semibold">{{ $user->name }}</div>
                            <div class="small text-muted">{{ $user->email }}</div>
                        </div>
                    </div>

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
                                   value="{{ $user->name }}"
                                   required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            Email Address <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-envelope text-muted"></i>
                            </span>
                            <input type="email" name="email"
                                   class="form-control"
                                   value="{{ $user->email }}"
                                   required>
                        </div>
                    </div>

                    <hr class="my-3">

                    <p class="small text-muted mb-3">
                        <i class="fas fa-lock me-1 text-warning"></i>
                        Leave blank to keep the current password unchanged.
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">New Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-key text-muted"></i>
                            </span>
                            <input type="password"
                                   name="password"
                                   id="editPassword{{ $user->id }}"
                                   class="form-control"
                                   placeholder="Min. 8 characters"
                                   autocomplete="new-password">
                            <button type="button" class="btn btn-outline-secondary toggle-pw"
                                    data-target="editPassword{{ $user->id }}">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold">Confirm New Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-key text-muted"></i>
                            </span>
                            <input type="password"
                                   name="password_confirmation"
                                   id="editPasswordConfirm{{ $user->id }}"
                                   class="form-control"
                                   placeholder="Repeat new password"
                                   autocomplete="new-password">
                            <button type="button" class="btn btn-outline-secondary toggle-pw"
                                    data-target="editPasswordConfirm{{ $user->id }}">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning px-4">
                        <i class="fas fa-save me-1"></i> Save Changes
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
