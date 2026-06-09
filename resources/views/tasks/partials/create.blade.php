<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">

            <form method="POST" action="{{ route('tasks.store') }}">
                @csrf

                {{-- Header --}}
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="createModalLabel">
                        <i class="fas fa-plus-circle me-2"></i> New Task
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">

                    {{-- Title --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Title <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="title"
                               class="form-control form-control-lg"
                               placeholder="What needs to be done?"
                               required autofocus>
                    </div>

                    {{-- Description --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold d-flex justify-content-between">
                            Description
                            <span class="text-muted fw-normal small" id="createDescCount">0 / 500</span>
                        </label>
                        <textarea name="description" rows="3"
                                  class="form-control"
                                  placeholder="Add more details…"
                                  maxlength="500"
                                  id="createDesc"></textarea>
                    </div>

                    <hr class="my-3">

                    {{-- Category & Assigned To --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Category <span class="text-danger">*</span>
                            </label>
                            <select name="category_id" class="form-select ts-select"
                                    data-placeholder="Search category…" required>
                                <option value=""></option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Assign To</label>
                            <select name="assigned_to" class="form-select ts-select"
                                    data-placeholder="Search user…">
                                <option value=""></option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Priority --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Priority</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="priority" id="pri_low_new" value="low">
                            <label class="btn btn-outline-success" for="pri_low_new">
                                <i class="fas fa-arrow-down me-1"></i> Low
                            </label>

                            <input type="radio" class="btn-check" name="priority" id="pri_med_new" value="medium" checked>
                            <label class="btn btn-outline-warning" for="pri_med_new">
                                <i class="fas fa-equals me-1"></i> Medium
                            </label>

                            <input type="radio" class="btn-check" name="priority" id="pri_hi_new" value="high">
                            <label class="btn btn-outline-danger" for="pri_hi_new">
                                <i class="fas fa-arrow-up me-1"></i> High
                            </label>
                        </div>
                    </div>

                    {{-- Status & Due Date --}}
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select ts-select ts-status"
                                    data-placeholder="Select status…">
                                <option value="pending"     data-icon="⏳">Pending</option>
                                <option value="in_progress" data-icon="🔄">In Progress</option>
                                <option value="completed"   data-icon="✅">Completed</option>
                                <option value="cancelled"   data-icon="🚫">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Due Date</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="fas fa-calendar text-muted"></i>
                                </span>
                                <input type="date" name="due_date" class="form-control">
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> Save Task
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
