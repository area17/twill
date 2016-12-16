<style>
.grid .row_item.selected {
    background-color: #fffff3;
    color: #3278b8;
}
</style>

@foreach($items as $file)
    <a class="row_item" data-id="{{ $file->id }}" data-name="{{ $file->filename or $file->id }}" href="#" data-url="{{ route('admin.file-library.files.edit', ['id' => $file->id]) }}">
        <div class="row_item_col">
            {{ $file->filename }}
        </div>
        <div class="row_item_col">
            {{ $file->size }}
        </div>
    </a>
@endforeach
