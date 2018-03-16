<template>
    <!-- eslint-disable -->
    <div class="block__body">
        {!! $render !!}
    </div>
</template>

<script>
  import BlockMixin from '@/mixins/block'

  export default {
    mixins: [BlockMixin]
  }
</script>
