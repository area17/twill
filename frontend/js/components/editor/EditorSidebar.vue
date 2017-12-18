<template>
  <div class="editorSidebar">
    <div class="editorSidebar__item" v-for="(block, index) in blocks" :key="block.id" v-show="isBlockActive(block.id)">
      {{ activeBlock.title }} <button type="button" @click="">Delete</button>
      <component v-bind:is="`${block.type}`" :name="componentName(block.id)" v-bind="block.attributes"><!-- dynamic components --></component>
    </div>
    <template v-if="!hasBlockActive">
      <h4 class="editorSidebar__title"><slot></slot></h4>
        <button class="editorSidebar__button" type="button" v-if="availableBlocks.length" v-for="(availableBlock, dropdownIndex) in availableBlocks" :key="availableBlock.component" @click="addBlock(availableBlock, -1)">
        <span v-svg :symbol="availableBlock.icon"></span> {{ availableBlock.title }}
      </button>
    </template>
    <template v-else>
      <a17-button type="button" variant="ok">Done</a17-button>
      <a17-button type="button" variant="ok">Cancel</a17-button>
    </template>
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
      hasBlockActive: function () {
        return Object.keys(this.activeBlock).length > 0
      },
      ...mapState({
        activeBlock: state => state.content.active,
        availableBlocks: state => state.content.available,
        blocks: state => state.content.blocks
      })
    },
    methods: {
      isBlockActive: function (id) {
        if (!this.hasBlockActive) return false

        return id === this.activeBlock.id
      },
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
      },
      componentName: function (id) {
        return 'blocks[' + id + ']'
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

  .editorSidebar__title {
    padding:25px 0 10px 0;
    font-weight:600;
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
    border:1px solid $color__border;
    color:$color__text--light;

    .icon {
      margin-right:20px;
      color:$color__icons;
    }

    &:hover {
      color:$color__text;
    }
  }
</style>
