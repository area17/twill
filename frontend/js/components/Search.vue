<template>
  <div class="search">
    Search overlay
  </div>
</template>

<script>
  export default {
    name: 'A17Search',
    props: {
    },
    computed: {
    },
    methods: {
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .search {
    position:fixed;
    top:0;
    left:0;
    right:0;
    bottom:0;
    background:red;
    opacity:0.5;
    z-index:$zindex__search;
  }
</style>
