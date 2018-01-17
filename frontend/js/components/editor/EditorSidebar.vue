<template>
  <div class="editorSidebar">
    <div class="editorSidebar__item" v-for="(block, index) in blocks" :key="block.id" v-show="isBlockActive(block.id)">
      <div class="editorSidebar__title">
        <span class="editorSidebar__blockTitle"><span class="editorSidebar__counter f--tiny">{{ index + 1 }}</span> {{ activeBlock.title }}</span><span><a href="#" @click.prevent="deleteBlock(index)" class="f--small f--note f--underlined">Delete</a></span>
      </div>
      <component v-bind:is="`${block.type}`" :name="componentName(block.id)" v-bind="block.attributes"><!-- dynamic components --></component>
    </div>
    <template v-if="!hasBlockActive">
      <h4 class="editorSidebar__title"><slot></slot></h4>
      <draggable v-model="availableBlocks" :options="{ group: { name: 'editorBlocks',  pull: 'clone', put: false }, sort: false }" v-if="availableBlocks.length">
        <button class="editorSidebar__button" type="button" :data-title="availableBlock.title" :data-icon="availableBlock.icon" :data-component="availableBlock.component" v-for="(availableBlock, dropdownIndex) in availableBlocks" :key="availableBlock.component">
          <span v-svg :symbol="availableBlock.icon"></span> {{ availableBlock.title }}
        </button>
      </draggable>
    </template>
    <template v-else>
      <div class="editorSidebar__actions">
        <a17-button variant="action" @click="saveBlock()">Done</a17-button>
        <a17-button variant="secondary" @click="cancelBlock()">Cancel</a17-button>
      </div>
    </template>
  </div>
</template>

<script>
  import { mapState } from 'vuex'

  import draggableMixin from '@/mixins/draggable'
  import draggable from 'vuedraggable'

  export default {
    name: 'A17editorsidebar',
    components: {
      draggable
    },
    mixins: [draggableMixin],
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
      componentName: function (id) {
        return 'blocks[' + id + ']'
      },
      saveBlock: function () {
        this.$emit('save')
      },
      cancelBlock: function () {
        this.$emit('cancel')
      },
      deleteBlock: function (index) {
        this.$emit('delete', index)
      }
    },
    mounted: function () {
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .editorSidebar {
    padding:20px;
    height:100%;
    position:relative;
  }

  .editorSidebar__item {
    max-height: calc(100% - 80px);
    overflow-y: scroll;
  }

  .editorSidebar__title {
    padding:15px 0 10px 0;
    display:flex;
  }

  .editorSidebar__counter {
    border:1px solid $color__border;
    border-radius:50%;
    height:26px;
    width:26px;
    text-align:center;
    display:inline-block;
    line-height:25px;
    margin-right:10px;
    background:$color__background;
    color:$color__text--light;
    @include monospaced-figures('off'); // dont use monospaced figures here
    user-select: none;
    cursor: default;
  }

  h4,
  .editorSidebar__blockTitle {
    font-weight:600;
  }

  .editorSidebar__blockTitle {
    flex-grow:1;
  }

  .editorSidebar__actions {
    position:absolute;
    width:100%;
    left:0;
    bottom:0;
    padding:20px;
    background:$color__border--light;
    display:flex;

    button {
      width:50%;
    }
  }

  .editorSidebar__button {
    @include btn-reset;
    cursor:move;
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
