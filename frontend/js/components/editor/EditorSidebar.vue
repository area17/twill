<template>
  <div class="editorSidebar"
       :class="{
          'editorSidebar--multiple-section': multipleSections,
          'editorSidebar--specific-section': specificSection
         }">
    <template v-show="hasBlockActive">
      <a17-block-list>
        <div class="editorSidebar__edit-list" slot-scope="{ savedBlocks }">
          <a17-block-model
            :block="savedBlock"
            :section="section"
            v-for="savedBlock in savedBlocks"
            :key="savedBlock.id">
            <div class="editorSidebar__edit-block"
                 slot-scope="{ block, isActive, blockIndex, move, remove, unEdit }">
              <a17-sidebar-block-item :block="block"
                                      v-show="isActive"
                                      :blockIndex="blockIndex"
                                      :blocksLength="savedBlocksLength"
                                      @block:move="move"
                                      @block:delete="deleteBlock(remove)"/>
              <div class="editorSidebar__actions">
                <a17-button variant="action"
                            @click="saveBlock(unEdit, blockIndex)">
                  Done
                </a17-button>
                <a17-button variant="secondary"
                            @click="cancelBlock(unEdit, blockIndex)">
                  Cancel
                </a17-button>
              </div>
            </div>
          </a17-block-model>
        </div>
      </a17-block-list>
    </template>

    <template v-if="!hasBlockActive">
      <div v-if="multipleSections"
           class="editorSidebar__toggle-section">
        <a17-vselect v-if="!specificSection" label="Currently editing section:"
                     selected="default"
                     size="small"
                     @change="updateSection"
                     :options="sections"/>
        <template v-else>
          Currently editing section <b>{{ getCurrentSectionLabel(sections) }}</b>.
        </template>
      </div>
      <div class="editorSidebar__list">
        <a17-sidebar-block-list :blocks="blocks"/>
      </div>

      <div class="editorSidebar__actions">
        <a17-button :name="submitOptions[0].name"
                    variant="validate"
                    @click="saveForm(submitOptions[0].name)">
          {{ submitOptions[0].text }}
        </a17-button>
      </div>
    </template>
  </div>
</template>

<script>
  import { PUBLICATION } from '@/store/mutations'
  import { BlockEditorMixin } from '@/mixins'
  import A17EditorSidebarBlockItem from '@/components/editor/EditorSidebarBlockItem'
  import A17EditorSidebarBlockList from '@/components/editor/EditorSidebarBlockList'
  import A17BlockList from '@/components/blocks/BlocksList'
  import A17BlockModel from '@/components/blocks/BlockModel'

  export default {
    name: 'A17editorsidebar',
    props: {
      hasBlockActive: {
        type: Boolean,
        default: false
      },
      activeBlock: {
        type: Object,
        default: () => {}
      },
      multipleSections: {
        type: Boolean,
        default: false
      },
      specificSection: {
        type: Boolean,
        default: false
      },
      sections: {
        type: Array,
        default: () => []
      }
    },
    components: {
      'a17-sidebar-block-item': A17EditorSidebarBlockItem,
      'a17-sidebar-block-list': A17EditorSidebarBlockList,
      'a17-block-list': A17BlockList,
      'a17-block-model': A17BlockModel
    },
    mixins: [BlockEditorMixin],
    computed: {
      submitOptions () {
        return this.$store.getters.getSubmitOptions
      }
    },
    methods: {
      getCurrentSectionLabel (sections) {
        return sections.find(section => section.value === this.section).label || ''
      },
      updateSection (section) {
        this.$emit('section:update', section.value)
      },
      saveForm (buttonName) {
        this.$store.commit(PUBLICATION.UPDATE_SAVE_TYPE, buttonName)
        if (this.$root.submitForm) this.$root.submitForm()
      }
    }
  }
</script>

<style lang="scss" scoped>

  .editorSidebar {
    margin: 20px 0 20px 0;
    // height:100%;
    position: relative;
    overflow: hidden;
    height: calc(100% - 40px);
  }

  .editorSidebar__list {
    overflow-y: auto;
    /*height: calc(100% + 20px - 80px);*/
    /*height: 100%;*/
    padding: 0 10px 0 20px;
    position: absolute;
    top: 0;
    bottom: 60px;
    left: 0;
    right: 0;

    .editorSidebar--multiple-section & {
      top: 110px;
    }

    .editorSidebar--multiple-section.editorSidebar--specific-section & {
      top: 70px;
    }
  }

  .editorSidebar__toggle-section {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    width: 100%;
    padding: 0 10px 15px 20px;
    //border-bottom: 1px solid $color__border--focus;

    .editorSidebar--multiple-section.editorSidebar--specific-section & {
      margin-top: 16px;
    }

    &:after {
      content: '';
      position: absolute;
      left: 20px;
      right: 10px;
      bottom: 0;
      height: 1px;
      background-color: $color__border--focus;
    }
  }

  .editorSidebar__actions {
    position: absolute;
    width: 100%;
    left: 0;
    bottom: 0;
    padding: 20px 10px 0 20px;
    background: $color__border--light;
    display: flex;

    button {
      width: calc(50% - 10px);
    }

    button + button {
      margin-left: 20px;
    }

    button.button--validate:last-child {
      width: 100%;
      margin-left: 0;
    }
  }
</style>

<style lang="scss">
  .editorSidebar__body {
    .block__body {
      > .media,
      > .slideshow,
      > .browserField {
        margin-left: -15px;
        margin-right: -15px;
        border: 0 none;
      }

      > .media:last-child,
      > .slideshow:last-child,
      > .browserField:last-child {
        margin-bottom: -15px;
      }
    }
  }
</style>
