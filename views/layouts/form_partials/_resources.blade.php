@php
    $with_multiple = $with_multiple ?? false;
    $media_max = $with_multiple ? ($max ?? 1) :1;
@endphp

<script>
    var resources_options_{{$role_relationship}} = {
      "role": "{{ $role_relationship }}",
      "type": "{{ $with_multiple ? 'generic_multiple' : 'generic_single' }}",
      "url": "{{ route('admin.' . $role_relationship . '.browser') }}",
      "title": "Attach {{ $role_relationship_name or $role_relationship }}",
      "max": {{ $media_max }}
    }
</script>

<section class="box bucket-medias" id="generic_library_multiple" data-behavior="media_library" data-options="resources_options_{{$role_relationship}}">
<header class="header_small">
<h3>
    <b>{{ isset($role_relationship_name) ? ucfirst($role_relationship_name) : ucfirst($role_relationship) }}
    </b>
    @if (isset($hint))
        <ul>
            <li><span class="icon icon-label icon-bang">{{ $hint }}</span></li>
        </ul>
    @endif
</h3>
</header>

<input type="hidden" name="case_studies" value="1,2">
<div class="table_container">
  <table data-behavior="sortable" data-hidden-field="case_studies">
    <thead>
      <tr>
        @if($with_multiple)
            <th class="tool"></th>
        @endif
        <th>Image</th>
        <th>Title</th>
        <th class="tool"></th>
      </tr>
    </thead>

    <tbody data-media-bucket="case_studies" data-media-template="{{ moduleRoute($role_relationship, $routePrefix, 'generic_resources', ['with_multiple' => $with_multiple]) }}" data-media-item=".media-row">

    @if(isset($item))
        @foreach($item->case_studies as $single_resource)
            <tr class="media-row media-row-new" id="media-box-{{ $single_resource->id }}" data-id="{{ $single_resource->id }}">
                @if($with_multiple)
                    <td><span class="icon icon-handle"></span></td>
                @endif
                <td class="thumb">
                    @if($single_resource->has('medias'))
                        <img src="{{ $single_resource->cmsImage(
                            head(array_keys($single_resource->mediasParams)),
                            head(array_keys(head($single_resource->mediasParams))),
                            ['w' => 80, 'h' => 80]) }}" width="80" height="80">
                    @endif
                </td>
                <td>{{$single_resource->title}}</td>
                <td><a class="icon icon-trash" href="#" data-media-remove-trigger rel="nofollow">Destroy</a></td>
            </tr>
        @endforeach
    @endif

    </tbody>
  </table>
</div>

<footer data-media-bt>
    <button type="button" class="btn btn-small btn-border" data-media-bt-trigger>Attach {{ $role_relationship_name or $role_relationship }}</button>
    </footer>
</section>
