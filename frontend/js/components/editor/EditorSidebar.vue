<template>
  <div class="editorSidebar">
    <div class="editorSidebar__item" v-for="(block, index) in blocks" :key="block.id" v-show="isBlockActive(block.id)">
      <div class="editorSidebar__title">
        <span class="editorSidebar__blockTitle">{{ activeBlock.title }}</span><a href="#" @click.prevent="" class="f--small f--note f--underlined">Delete</a>
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
        <a17-button variant="action" @click="unselectBlock">Done</a17-button>
        <a17-button variant="secondary" @click="unselectBlock">Cancel</a17-button>
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
      unselectBlock: function () {
        this.$store.commit('activateBlock', -1)
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
    overflow-y: scroll;
  }

  .editorSidebar__title {
    padding:15px 0 10px 0;
    display:flex;
  }

  h4,
  .editorSidebar__blockTitle {
    font-weight:600;
  }

  .editorSidebar__blockTitle {
    flex-grow:1;
  }

  .editorSidebar__actions {
    position:fixed;
    bottom:20px;
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
