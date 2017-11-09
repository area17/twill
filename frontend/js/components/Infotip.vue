<template>
  <a17-button variant="icon" size="smallIcon" :data-tooltip-title="text" data-tooltip-theme="large" data-tooltip-placement="right" v-tooltip><span v-svg symbol="info"></span></a17-button>
</template>

<script>
  export default {
    name: 'A17Infotip',
    props: {
      text: {
        default: ''
      }
    },
    computed: {
    },
    methods: {
      onClick: function () {
        this.$emit('click')
      }
    }
  }
</script>

<style lang="scss" scoped>
  button.button--icon {
    vertical-align: middle;
    display: inline-block;
    margin-left: 7px;
    margin-right: 7px;

    .icon {
      display:block;
      margin:-1px;
    }
  }

</style>
