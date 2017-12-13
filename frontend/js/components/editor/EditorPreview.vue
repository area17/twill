<template>
  <div class="editorPreview">
    <draggable class="editorPreview__content" v-model="blocks" :options="dragOptions" v-if="blocks.length">
      <transition-group name="draggable_list" tag='div'>
        <div class="editorPreview__item" v-for="(block, index) in blocks" :key="block.id">
          {{ block }}
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

  import draggable from 'vuedraggable'
  import draggableMixin from '@/mixins/draggable'

  export default {
    name: 'A17editorpreview',
    components: {
      draggable
    },
    mixins: [draggableMixin],
    data: function () {
      return {
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
      ...mapState({
        savedBlocks: state => state.content.blocks
      })
    },
    methods: {
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
  }
</style>
