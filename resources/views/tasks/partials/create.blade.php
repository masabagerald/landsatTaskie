<div class="modal fade"
     id="createModal"
     tabindex="-1"
     aria-labelledby="createModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-centered">

        <div class="modal-content">

            <form method="POST"
                  action="{{ route('tasks.store') }}">

                @csrf

                <div class="modal-header">

                    <h5 class="modal-title"
                        id="createModalLabel">
                        Add Task
                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close">
                    </button>

                </div>

                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Title
                            </label>

                            <input type="text"
                                   name="title"
                                   class="form-control"
                                   required>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Category
                            </label>

                            <select name="category_id"
                                    class="form-select"
                                    required>

                                <option value="">
                                    Select Category
                                </option>

                                @foreach($categories as $category)

                                    <option value="{{ $category->id }}">
                                        {{ $category->name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-12 mb-3">

                            <label class="form-label">
                                Description
                            </label>

                            <textarea name="description"
                                      rows="3"
                                      class="form-control"></textarea>

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label">
                                Priority
                            </label>

                            <select name="priority"
                                    class="form-select">

                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>

                            </select>

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label">
                                Status
                            </label>

                            <select name="status"
                                    class="form-select">

                                <option value="pending">
                                    Pending
                                </option>

                                <option value="in_progress">
                                    In Progress
                                </option>

                                <option value="completed">
                                    Completed
                                </option>

                                <option value="cancelled">
                                    Cancelled
                                </option>

                            </select>

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label">
                                Due Date
                            </label>

                            <input type="date"
                                   name="due_date"
                                   class="form-control">

                        </div>

                        <div class="col-md-12 mb-3">

                            <label class="form-label">
                                Assign User
                            </label>

                            <select name="assigned_to"
                                    class="form-select">

                                <option value="">
                                    Select User
                                </option>

                                @foreach($users as $user)

                                    <option value="{{ $user->id }}">
                                        {{ $user->name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit"
                            class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Save Task
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>