<div class="modal fade" id="deleteModal{{ $category->id }}">
    <div class="modal-dialog">

        <form method="POST"
              action="{{ route('categories.destroy',$category->id) }}">

            @csrf
            @method('DELETE')

            <div class="modal-content">

                <div class="modal-header">
                    <h5>Delete Category</h5>
                </div>

                <div class="modal-body">
                    Are you sure you want to delete
                    <strong>{{ $category->name }}</strong>?
                </div>

                <div class="modal-footer">
                    <button class="btn btn-danger">
                        Delete
                    </button>
                </div>

            </div>

        </form>

    </div>
</div>