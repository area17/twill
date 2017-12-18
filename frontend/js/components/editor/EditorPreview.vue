<template>
  <div class="editorPreview">
    <draggable class="editorPreview__content" v-model="blocks" :options="dragOptions" v-if="blocks.length">
      <transition-group name="draggable_list" tag='div'>
        <div class="editorPreview__item" :class="{ 'editorPreview__item--active' : isBlockActive(block.id) }" v-for="(block, index) in blocks" :key="block.id" >
          <div class="editorPreview__frame" tabindex="0" @click="selectBlock(index)">
            <div class="">{{ block.title }}</div>
            <iframe srcdoc="Preview HTML content goes here" @load=""></iframe>
          </div>
          <div class="editorPreview__actions">
            <button type="button" @click="selectBlock(index)">Edit</button>
            <button type="button" class="editorPreview__handle">Drag</button>
            <button type="button" @click="deleteBlock(index)">Delete</button>
          </div>
        </div>
      </transition-group>
    </draggable>
    <div class="editorPreview__empty" v-else>
      <b>Add content</b>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'

  import draggableMixin from '@/mixins/draggable'
  import draggable from 'vuedraggable'

  export default {
    name: 'A17editorpreview',
    components: {
      draggable
    },
    mixins: [draggableMixin],
    data: function () {
      return {
        handle: '.editorPreview__handle' // Drag handle override
      }
    },
    computed: {
      blocks: {
        get () {
          return this.savedBlocks
        },
        set (value) {
          this.$store.commit('reorderBlocks', value)
        }
      },
      hasBlockActive: function () {
        return Object.keys(this.activeBlock).length
      },
      ...mapState({
        activeBlock: state => state.content.active,
        savedBlocks: state => state.content.blocks
      })
    },
    methods: {
      isBlockActive: function (id) {
        if (!this.hasBlockActive) return false

        return id === this.activeBlock.id
      },
      deleteBlock: function (index) {
        this.unselectBlock()
        this.$store.commit('deleteBlock', index)
      },
      unselectBlock: function () {
        this.$store.commit('activateBlock', -1)
      },
      selectBlock: function (index) {
        if (this.isBlockActive(this.blocks[index].id)) this.unselectBlock()
        else this.$store.commit('activateBlock', index)
      }
    },
    mounted: function () {
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .editorPreview {
  }

  .editorPreview__content {
    position:absolute;
    top:0;
    bottom:0;
    right:0;
    left:0;
    padding:20px;
    overflow-y: scroll;
  }

  .editorPreview__empty {
    position:absolute;
    top:0;
    bottom:0;
    right:0;
    left:0;
    display:flex;
    align-items: center;
    justify-content: center;
    color:$color__fborder;

    &after {
      display:block;
      content:'';
      position:absolute;
      top:20px;
      bottom:20px;
      right:20px;
      left:20px;
      border:1px dashed $color__fborder;
    }
  }

  .editorPreview__actions {
    display:none;
  }

  .editorPreview__item {
    min-height:100px;
    border:1px solid $color__background;
    position:relative;
  }

  .editorPreview__item--active {
    border-color:$color__text;
  }

  .editorPreview__item:hover,
  .editorPreview__item--active  {
    .editorPreview__actions {
      display:block;
    }
  }

  .editorPreview__frame {
    cursor:pointer;
  }

  .editorPreview__actions {
    position:absolute;
    right:0;
    top:0;
  }
</style>
