<div class="modal fade"
     id="deleteModal{{ $task->id }}"
     tabindex="-1">

    <div class="modal-dialog">

        <form method="POST"
              action="{{ route('tasks.destroy',$task) }}">

            @csrf
            @method('DELETE')

            <div class="modal-content">

                <div class="modal-header">

                    <h5>
                        Delete Task
                    </h5>

                </div>

                <div class="modal-body">

                    Are you sure you want to delete:

                    <strong>
                        {{ $task->title }}
                    </strong> ?

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