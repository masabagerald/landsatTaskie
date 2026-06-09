<div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content border-0 shadow">

            <form method="POST" action="{{ route('users.destroy', $user) }}">
                @csrf
                @method('DELETE')

                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body text-center px-4 pt-0 pb-4">
                    <div class="rounded-circle bg-danger bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3"
                         style="width:64px;height:64px;">
                        <i class="fas fa-user-times text-danger fa-lg"></i>
                    </div>

                    <h5 class="fw-bold mb-1">Delete User?</h5>
                    <p class="text-muted mb-1">You are about to permanently delete:</p>
                    <p class="fw-semibold mb-1">{{ $user->name }}</p>
                    <p class="text-muted small mb-3">{{ $user->email }}</p>

                    @if($user->tasks_count > 0)
                        <div class="alert alert-warning py-2 text-start small mb-0">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            This user has <strong>{{ $user->tasks_count }} assigned task{{ $user->tasks_count !== 1 ? 's' : '' }}</strong>.
                            They will become <strong>unassigned</strong>, not deleted.
                        </div>
                    @else
                        <p class="text-muted small mb-0">This action cannot be undone.</p>
                    @endif
                </div>

                <div class="modal-footer border-0 pt-0 justify-content-center gap-2">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-danger px-4">
                        <i class="fas fa-user-times me-1"></i> Delete
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
