<template>
  <a17-blocks-list :section="section">
    <div class="content"
         slot-scope="{ savedBlocks, availableBlocks, reorderBlocks }">
      <draggable class="content__container"
                 :value="savedBlocks"
                 @input="(value) => { reorderBlocks(value) }"
                 :options="dragOptions">
        <transition-group name="draggable_list"
                          tag='div'>
          <div class="content__item"
               v-for="(savedBlock, index) in savedBlocks"
               :key="savedBlock.id">
            <a17-block-model :section="section"
                             :block="savedBlock">
              <a17-block-item slot-scope="{ block, add, move, remove, duplicate }"
                              ref="blockList"
                              :block="block"
                              :index="index"
                              :expand="savedBlocks.length <= 3"
                              @expand="checkExpandBlocks"
                              v-if="availableBlocks.length">
                <button slot="dropdown-add"
                        type="button"
                        v-for="availableBlock in availableBlocks"
                        :key="availableBlock.component"
                        @click="handleBlockAdd(add, availableBlock, index + 1)">
                    <span v-svg
                          :symbol="availableBlock.icon"></span>
                  {{ availableBlock.title }}
                </button>
                <div slot="dropdown-action">
                  <button type="button"
                          v-if="allBlocksExpands"
                          @click="collapseAllBlocks()">Collapse all
                  </button>
                  <button type="button"
                          v-else
                          @click="expandAllBlocks()">Expand all
                  </button>
                  <button type="button"
                          v-if="editor"
                          @click="openEditor(index, section)">Open in editor
                  </button>
                  <button type="button"
                          @click="handleDuplicateBlock(duplicate, index)">Create another
                  </button>
                  <button type="button"
                          @click="handleDeleteBlock(remove, index)">Delete
                  </button>
                </div>
                <button slot="dropdown-numbers"
                        type="button"
                        v-for="n in savedBlocks.length"
                        @click="move(index, n - 1)"
                        :key="n">{{ n }}
                </button>
              </a17-block-item>
            </a17-block-model>
          </div>
        </transition-group>
      </draggable>

      <div class="content__actions">
        <a17-dropdown ref="blocksDropdown"
                      position="top-center"
                      v-if="availableBlocks.length"
                      :arrow="true"
                      :offset="10"
                      :maxHeight="430">

          <a17-button size="small"
                      variant="action"
                      @click="$refs.blocksDropdown.toggle()">{{ title }}
          </a17-button>

          <div slot="dropdown__content">
            <template v-for="availableBlock in availableBlocks">
              <a17-block-model :section="section"
                               :key="availableBlock.component">
                <button type="button"
                        slot-scope="{ add }"
                        :key="availableBlock.component"
                        @click="handleBlockAdd(add, availableBlock)">
                  <span class="content__icon"
                        v-svg
                        :symbol="availableBlock.icon"></span>
                  {{ availableBlock.title }}
                </button>
              </a17-block-model>
            </template>
          </div>
        </a17-dropdown>
        <div class="content__secondaryActions">
          <a href="#"
             class="f--link f--link-underlined--o"
             v-if="editor"
             @click.prevent="openEditor(-1)">Open in editor</a>
        </div>
      </div>
    </div>
  </a17-blocks-list>
</template>

<script>
  import { mapState } from 'vuex'
  import { DraggableMixin, EditorMixin } from '@/mixins/index'
  import draggable from 'vuedraggable'
  import BlockItem from '@/components/blocks/Block.vue'
  import BlocksList from '@/components/blocks/BlocksList'
  import BlockModel from '@/components/blocks/BlockModel'

  export default {
    name: 'A17Blocks',
    components: {
      'a17-block-item': BlockItem,
      'a17-block-model': BlockModel,
      'a17-blocks-list': BlocksList,
      draggable
    },
    mixins: [DraggableMixin, EditorMixin],
    props: {
      title: {
        type: String,
        default: ''
      },
      section: {
        type: String,
        default: 'default'
      }
    },
    data () {
      return {
        allBlocksExpands: false,
        handle: '.block__handle' // drag handle
      }
    },
    computed: {
      ...mapState({
        editor: state => state.content.editor
      })
    },
    methods: {
      checkExpandBlocks () {
        this.allBlocksExpands = this.$refs.blockList.every((blocks) => blocks.visible)
      },
      handleBlockAdd (fn, block, index = -1) {
        fn(block, index)
        this.$nextTick(() => {
          this.checkExpandBlocks()
        })
      },
      handleDuplicateBlock (fn, index) {
        fn(index)
        this.$nextTick(() => {
          this.checkExpandBlocks()
        })
      },
      handleDeleteBlock (fn, index) {
        // open confirm dialog if any
        if (this.$root.$refs.warningContentEditor) {
          this.$root.$refs.warningContentEditor.open(() => {
            fn(index)
            this.$nextTick(() => {
              this.checkExpandBlocks()
            })
          })
        } else {
          fn(index)
          this.$nextTick(() => {
            this.checkExpandBlocks()
          })
        }
      },
      collapseAllBlocks () {
        this.$refs.blockList.forEach(block => {
          block.visible = false
        })
        this.$nextTick(() => {
          this.checkExpandBlocks()
        })
      },
      expandAllBlocks () {
        this.$refs.blockList.forEach(block => {
          block.visible = true
        })
        this.$nextTick(() => {
          this.checkExpandBlocks()
        })
      }
    }
  }
</script>

<style lang="scss" scoped>

  .content {
    margin-top: 20px; // margin-top:35px;
  }

  .content__container {
    margin-bottom: 20px;

    + .dropdown {
      display: inline-block;
    }
  }

  .content__actions {
    display: flex;
  }

  .content__secondaryActions {
    flex-grow: 1;
    text-align: right;
    margin-left: 20px;
    padding-top: 8px;
  }

  .content__item {
    border: 1px solid $color__border;
    border-top: 0 none;

    &.sortable-ghost {
      opacity: 0.5;
    }
  }

  .content__actions button .content__icon {
    margin-right: 0;
    margin-left: -15px;
    min-width: 55px;
    text-align: center;
    height: 40px;
  }

  .content__item:first-child {
    border-top: 1px solid $color__border;
  }
</style>
