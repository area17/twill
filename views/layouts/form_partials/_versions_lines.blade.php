@php
    $revisions = isset($item) && $item->revisions ? $item->revisions()->paginate(5, ['*'], 'rev_page') : [];
@endphp

<div class="table_container">
    <div data-behavior="liveago">
        @php
            $indexForPage = method_exists($revisions, 'total') ? $revisions->total() - ($revisions->currentPage() * $revisions->perPage() - $revisions->perPage()) : count($revisions);
        @endphp
        @forelse($revisions as $revision)
            <div class="Versions__line">
                <span class="Versions__action">v{{ $indexForPage }} (<span class="js-timeago" datetime="{{ $revision->created_at->toW3cString() }}">{{ $revision->created_at->diffInSeconds(Carbon\Carbon::now()) < 10 ? 'just now' : $revision->created_at->diffForHumans() }}</span> by {{ $revision->byUser }})</span>

                @if (isset($with_preview) && $with_preview)
                    <script>
                        var modal_options_revision_preview_{{ $loop->index }} = {
                            "type": "iframe",
                            "url": "{{ $item->previewUrl }}",
                            "title": "{{ $item->previewTitle }} Preview (Revision: {{ $revision->created_at }} by {{ $revision->byUser }})"
                        }
                        var modal_options_revision_compare_{{ $loop->index }} = {
                            "type": "iframe",
                            "url": "{{ $item->previewUrl }}",
                            "title": "{{ $item->previewTitle }} Compare previews (Left: Revision: {{ $revision->created_at }} by {{ $revision->byUser }}, Right: your changes)"
                        }
                    </script>

                    <button class="Versions__button" data-behavior="preview" data-preview-revision="{{ $revision->id }}" data-submit-form="{{ $form_options['id'] }}" data-options="modal_options_revision_preview_{{ $loop->index }}" type="button">{{-- <span class="icon" style="background-repeat: no-repeat; background-image: url('https://icon.now.sh/open_in_browser/14/666'); background-position: 25% 25%;"></span> --}}Preview</button>

                    <button class="Versions__button" data-behavior="preview" data-compare data-preview-revision="{{ $revision->id }}" data-submit-form="{{ $form_options['id'] }}" data-options="modal_options_revision_compare_{{ $loop->index }}" type="button">{{-- <span class="icon" style="background-repeat: no-repeat; background-image: url('https://icon.now.sh/compare/14/666'); background-position: 25% 25%;"></span> --}}Compare</button>

                @endif
                @unless($loop->first && $revisions->onFirstPage())
                    {{-- <button class="Versions__button" type="button" data-behavior="restore" data-submit-form="{{ $form_options['id'] }}" data-revision="{{ $revision->id }}"><span class="icon" style="background-repeat: no-repeat; background-image: url('https://icon.now.sh/restore/14/666'); background-position: 25% 25%;"></span> Restore</button> --}}
                @endunless
            </div>
            @unless($loop->last)
                <hr>
            @endunless
            @php
                $indexForPage = $indexForPage - 1;
            @endphp
        @empty
            <div class="message">
                <p>No revisions yet.</p>
                <p>Save your content as hidden, you'll then be able to preview it on your site before publishing.</p>
            </div>
        @endforelse
    </div>
</div>

@if (method_exists($revisions, 'total'))
    @resourceView($moduleName, 'paginator', ['items' => $revisions])
@endif
