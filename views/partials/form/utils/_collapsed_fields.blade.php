<a17-inputframe v-show="!opened">
    <a href='#' @click.prevent='open' class="f--small f--note f--underlined">{{ $label }}</a>
</a17-inputframe>
<div v-show="opened">
  {{ $slot }}
</div>
