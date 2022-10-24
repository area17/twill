@props(['content' => null, 'lang' => 'php'])
<pre class="text-xs">
    @if (isset($content))
        <x-torchlight-code language="{{$lang}}" theme="github-dark"
                           :contents="$content"/>
    @else
        <x-torchlight-code language="{{$lang}}"
                           theme="github-dark">{!! trim(\Illuminate\Support\Str::replace(['<pre>', '</pre>'], '', $slot)) !!}</x-torchlight-code>
    @endif
</pre>
