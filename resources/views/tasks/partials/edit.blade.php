<div class="modal fade"
     id="editModal{{ $task->id }}"
     tabindex="-1">

    <div class="modal-dialog modal-lg">

        <form method="POST"
              action="{{ route('tasks.update',$task) }}">

            @csrf
            @method('PUT')

            <!-- Same fields as create -->

        </form>

    </div>

</div>