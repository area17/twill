<template>
  <div class="editorSidebar">
    <slot></slot>
    <button class="editorSidebar__button" type="button" v-if="availableBlocks.length" v-for="(availableBlock, dropdownIndex) in availableBlocks" :key="availableBlock.component" @click="addBlock(availableBlock, -1)">
      <span v-svg :symbol="availableBlock.icon"></span> {{ availableBlock.title }}
    </button>

    <a17-button type="button" variant="validate">Save</a17-button>
  </div>
</template>

<script>
  import { mapState } from 'vuex'

  export default {
    name: 'A17editorsidebar',
    data: function () {
      return {
      }
    },
    computed: {
      ...mapState({
        availableBlocks: state => state.content.available
      })
    },
    methods: {
      addBlock: function (block, fromIndex) {
        let newBlock = {
          title: block.title,
          type: block.component,
          icon: block.icon,
          attributes: block.attributes
        }

        this.$store.commit('addBlock', {
          block: newBlock,
          index: fromIndex
        })
      }
    },
    mounted: function () {
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .editorSidebar {
    padding:10px;
  }

  .editorSidebar__button {
    @include btn-reset;
    display:block;
    width:100%;
    text-align:left;
    background:$color__background;
    border-radius: $border-radius;
    margin-bottom: 10px;
    height:60px;
    line-height:60px;
    padding:0 20px;
  }
</style>
