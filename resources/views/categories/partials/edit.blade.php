<div class="modal fade" id="editModal{{ $category->id }}">
    <div class="modal-dialog">
        <form method="POST"
              action="{{ route('categories.update',$category->id) }}">

            @csrf
            @method('PUT')

            <div class="modal-content">

                <div class="modal-header">
                    <h5>Edit Category</h5>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text"
                               name="name"
                               value="{{ $category->name }}"
                               class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Description</label>
                        <textarea name="description"
                                  class="form-control">{{ $category->description }}</textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-warning">
                        Update
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>