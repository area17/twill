<template>
  <a17-inputframe :error="error"
                  :note="note"
                  :label="label"
                  :locale="locale"
                  @localize="updateLocale"
                  :size="size"
                  :name="name"
                  :required="required">
    <div class="wysiwyg__outer">
      <div class="wysiwyg"
           :class="textfieldClasses"
           v-show="!activeSource">
        <input :name="name"
               type="hidden"
               v-model="value"/>
        <div class="wysiwyg__editor"
             ref="editor">
          <editor-menu-bar :editor="editor"
                           v-slot="{ commands, isActive }">
            <div class="wysiwyg__menubar">
              <!--                <button-->
              <!--                  class="wysiwyg__button"-->
              <!--                  @click="commands.undo"-->
              <!--                >-->
              <!--                  <span v-svg symbol="undo"></span>-->
              <!--                </button>-->

              <!--                <button-->
              <!--                  class="wysiwyg__button"-->
              <!--                  @click="commands.redo"-->
              <!--                >-->
              <!--                  <span v-svg symbol="redo"></span>-->
              <!--                </button>-->

              <button
                class="wysiwyg__menubar-button"
                :class="{ 'is-active': isActive.bold() }"
                @click="commands.bold"
              >
                <wysiwyg-icon icon="bold"/>
              </button>

              <button
                class="wysiwyg__menubar-button"
                :class="{ 'is-active': isActive.italic() }"
                @click="commands.italic"
              >
                <wysiwyg-icon icon="italic"/>
              </button>

              <!--                <button-->
              <!--                  class="wysiwyg__menubar-button"-->
              <!--                  :class="{ 'is-active': isActive.strike() }"-->
              <!--                  @click="commands.strike"-->
              <!--                >-->
              <!--                  <wysiwyg-icon icon="strike"/>-->
              <!--                </button>-->

              <button
                class="wysiwyg__menubar-button"
                :class="{ 'is-active': isActive.underline() }"
                @click="commands.underline"
              >
                <wysiwyg-icon icon="underline"/>
              </button>

              <!--                <button-->
              <!--                  class="wysiwyg__menubar-button"-->
              <!--                  :class="{ 'is-active': isActive.code() }"-->
              <!--                  @click="commands.code"-->
              <!--                >-->
              <!--                  <span v-svg symbol="code"></span>-->
              <!--                </button>-->

              <!--                <button-->
              <!--                  class="wysiwyg__menubar-button"-->
              <!--                  :class="{ 'is-active': isActive.paragraph() }"-->
              <!--                  @click="commands.paragraph"-->
              <!--                >-->
              <!--                  <span v-svg symbol="paragraph"></span>-->
              <!--                </button>-->

              <!--                <button-->
              <!--                  class="wysiwyg__menubar-button"-->
              <!--                  :class="{ 'is-active': isActive.heading({ level: 1 }) }"-->
              <!--                  @click="commands.heading({ level: 1 })"-->
              <!--                >-->
              <!--                  H1-->
              <!--                </button>-->

              <!--                <button-->
              <!--                  class="wysiwyg__menubar-button"-->
              <!--                  :class="{ 'is-active': isActive.heading({ level: 2 }) }"-->
              <!--                  @click="commands.heading({ level: 2 })"-->
              <!--                >-->
              <!--                  H2-->
              <!--                </button>-->

              <!--                <button-->
              <!--                  class="wysiwyg__menubar-button"-->
              <!--                  :class="{ 'is-active': isActive.heading({ level: 3 }) }"-->
              <!--                  @click="commands.heading({ level: 3 })"-->
              <!--                >-->
              <!--                  H3-->
              <!--                </button>-->

              <!--                <button-->
              <!--                  class="wysiwyg__menubar-button"-->
              <!--                  :class="{ 'is-active': isActive.bullet_list() }"-->
              <!--                  @click="commands.bullet_list"-->
              <!--                >-->
              <!--                  <span v-svg symbol="ul"></span>-->
              <!--                </button>-->

              <!--                <button-->
              <!--                  class="wysiwyg__menubar-button"-->
              <!--                  :class="{ 'is-active': isActive.ordered_list() }"-->
              <!--                  @click="commands.ordered_list"-->
              <!--                >-->
              <!--                  <span v-svg symbol="ol"></span>-->
              <!--                </button>-->

              <!--                <button-->
              <!--                  class="wysiwyg__menubar-button"-->
              <!--                  :class="{ 'is-active': isActive.blockquote() }"-->
              <!--                  @click="commands.blockquote"-->
              <!--                >-->
              <!--                  <span v-svg symbol="quote"></span>-->
              <!--                </button>-->

              <!--                <button-->
              <!--                  class="wysiwyg__menubar-button"-->
              <!--                  :class="{ 'is-active': isActive.code_block() }"-->
              <!--                  @click="commands.code_block"-->
              <!--                >-->
              <!--                  <span v-svg symbol="code"></span>-->
              <!--                </button>-->

              <!--                <button-->
              <!--                  class="wysiwyg__menubar-button"-->
              <!--                  @click="commands.createTable({rowsCount: 3, colsCount: 3, withHeaderRow: false })"-->
              <!--                >-->
              <!--                  <span v-svg symbol="table"></span>-->
              <!--                </button>-->

              <!--                <span v-if="isActive.table()">-->
              <!--                  <button-->
              <!--                    class="wysiwyg__menubar-button"-->
              <!--                    @click="commands.deleteTable"-->
              <!--                  >-->
              <!--                   <span v-svg symbol="delete_table"></span>-->
              <!--                  </button>-->
              <!--                  <button-->
              <!--                    class="wysiwyg__menubar-button"-->
              <!--                    @click="commands.addColumnBefore"-->
              <!--                  >-->
              <!--                   <span v-svg symbol="add_col_before"></span>-->
              <!--                  </button>-->
              <!--                  <button-->
              <!--                    class="wysiwyg__menubar-button"-->
              <!--                    @click="commands.addColumnAfter"-->
              <!--                  >-->
              <!--                   <span v-svg symbol="add_col_after"></span>-->
              <!--                  </button>-->
              <!--                  <button-->
              <!--                    class="wysiwyg__menubar-button"-->
              <!--                    @click="commands.deleteColumn"-->
              <!--                  >-->
              <!--                   <span v-svg symbol="delete_col"></span>-->
              <!--                  </button>-->
              <!--                  <button-->
              <!--                    class="wysiwyg__menubar-button"-->
              <!--                    @click="commands.addRowBefore"-->
              <!--                  >-->
              <!--                   <span v-svg symbol="add_row_before"></span>-->
              <!--                  </button>-->
              <!--                  <button-->
              <!--                    class="wysiwyg__menubar-button"-->
              <!--                    @click="commands.addRowAfter"-->
              <!--                  >-->
              <!--                   <span v-svg symbol="add_row_after"></span>-->
              <!--                  </button>-->
              <!--                  <button-->
              <!--                    class="wysiwyg__menubar-button"-->
              <!--                    @click="commands.deleteRow"-->
              <!--                  >-->
              <!--                   <span v-svg symbol="delete_row"></span>-->
              <!--                  </button>-->
              <!--                  <button-->
              <!--                    class="wysiwyg__menubar-button"-->
              <!--                    @click="commands.toggleCellMerge"-->
              <!--                  >-->
              <!--                   <span v-svg symbol="combine_cells"></span>-->
              <!--                  </button>-->
              <!--                 </span>-->
            </div>
          </editor-menu-bar>
          <editor-content class="wysiwyg__content"
                          :editor="editor"/>
        </div>
        <span v-if="shouldShowCounter"
              class="input__limit f--tiny"
              :class="limitClasses">{{ counter }}</span>
      </div>
      <template v-if="editSource">
        <div class="form__field form__field--textarea"
             v-show="activeSource">
          <textarea :placeholder="placeholder"
                    :autofocus="autofocus"
                    v-model="value"
                    :style="textareaHeight"></textarea>
        </div>
        <a17-button variant="ghost"
                    @click="toggleSourcecode"
                    class="wysiwyg__button">Source code
        </a17-button>
      </template>
    </div>
  </a17-inputframe>
</template>

<script>
  import { Editor, EditorContent, EditorMenuBar } from 'tiptap'
  import {
    Blockquote,
    CodeBlock,
    HardBreak,
    Heading,
    OrderedList,
    BulletList,
    ListItem,
    TodoItem,
    TodoList,
    Bold,
    Code,
    Italic,
    Link,
    Placeholder,
    Table,
    TableHeader,
    TableCell,
    TableRow,
    Strike,
    Underline,
    History
  } from 'tiptap-extensions'
  import WysiwygIcon from '@/components/WysiwygIcon'

  import { mapState } from 'vuex'
  import debounce from 'lodash/debounce'

  import InputMixin from '@/mixins/input'
  import FormStoreMixin from '@/mixins/formStore'
  import InputframeMixin from '@/mixins/inputFrame'
  import LocaleMixin from '@/mixins/locale'

  // Todo: load highligth depending of needs
  // import { loadScript } from '@/utils/loader'
  //
  // const HIGHLIGHT = '//cdn.jsdelivr.net/gh/highlightjs/cdn-release@9.12.0/build/highlight.min.js'

  export default {
    name: 'A17Wysiwyg',
    mixins: [InputMixin, InputframeMixin, LocaleMixin, FormStoreMixin],
    props: {
      editSource: {
        type: Boolean,
        default: false
      },
      showCounter: {
        type: Boolean,
        default: true
      },
      type: {
        type: String,
        default: 'text'
      },
      prefix: {
        type: String,
        default: ''
      },
      maxlength: {
        type: Number,
        default: 0
      },
      initialValue: {
        default: ''
      },
      options: {
        type: Object,
        required: false,
        default: function () {
          return {}
        }
      }
    },
    computed: {
      textareaHeight: function () {
        return {
          height: this.editorHeight
        }
      },
      textfieldClasses: function () {
        return {
          's--disabled': this.disabled,
          's--focus': this.focused
        }
      },
      hasMaxlength: function () {
        return this.maxlength > 0
      },
      shouldShowCounter: function () {
        return this.hasMaxlength && this.showCounter
      },
      limitClasses: function () {
        return {
          'input__limit--red': this.counter < 10
        }
      },
      ...mapState({
        baseUrl: state => state.form.baseUrl
      })
    },
    components: {
      EditorContent,
      EditorMenuBar,
      'wysiwyg-icon': WysiwygIcon
    },
    data () {
      return {
        value: this.initialValue,
        editorHeight: 50,
        toolbarHeight: 52,
        focused: false,
        activeSource: false,
        counter: 0,
        editor: null
      }
    },
    methods: {
      updateEditor: function (newValue) {
        if (this.editor) {
          this.editor.setContent(newValue)
        }
      },
      updateFromStore: function (newValue) {
        if (typeof newValue === 'undefined') newValue = ''

        if (this.value !== newValue) {
          console.warn('updateFromStore - Update UI value : ' + this.name + ' -> ' + newValue)
          this.value = newValue
          this.updateEditor(newValue)
        }
      },
      textUpdate: debounce(function () {
        this.saveIntoStore() // see formStore mixin
      }, 600),
      toggleSourcecode: function () {
        this.editorHeight = (Math.max(50, this.$refs.editor.clientHeight) + this.toolbarHeight - 1) + 'px'
        this.activeSource = !this.activeSource

        // set editor content
        this.updateEditor(this.value)
        this.saveIntoStore() // see formStore mixin
      },
      updateCounter () {
        if (this.showCounter && this.hasMaxlength) {
          this.counter = this.maxlength - this.getTextLength()
        }
      },
      getTextLength () {
        return this.editor.getHTML().replace(/<[^>]+>/g, '').length
      }
    },
    beforeMount () {
      const placeholder = this.placeholder || ''
      const content = this.value || ''
      this.editor = new Editor({
        extensions: [
          new Blockquote(),
          new BulletList(),
          new CodeBlock(),
          new HardBreak(),
          new Heading({ levels: [1, 2, 3] }),
          new ListItem(),
          new OrderedList(),
          new TodoItem(),
          new TodoList(),
          new Link(),
          new Bold(),
          new Code(),
          new Italic(),
          new Strike(),
          new Underline(),
          new History(),
          new Table({
            resizable: true
          }),
          new TableHeader(),
          new TableCell(),
          new TableRow(),
          new Placeholder({
            emptyNodeClass: 'is-empty',
            emptyNodeText: placeholder,
            showOnlyWhenEditable: true
          })
        ],
        content: content,
        onUpdate: ({ getHTML }) => {
          this.value = getHTML()
          this.textUpdate()
          this.updateCounter()
        }
      })

      this.updateCounter()
    },
    beforeDestroy () {
      this.editor.destroy()
    }
  }
</script>

<style scoped lang="scss">
  @import '~styles/setup/_mixins-colors-vars.scss';

  $height_input: 45px;

  .wysiwyg {
    position: relative;

    .input__limit {
      color: $color__text--light;
      user-select: none;
      pointer-events: none;
      position: absolute;
      right: 15px;
      bottom: 15px;

      &.input__limit--red {
        color: $color__error;
      }
    }
  }

  .wysiwyg__editor {
    @include textfield;
    @include defaultState;
    position: relative;

    .input--error & {
      border-color: $color__error;
    }

    &.s--focus {
      @include focusState;
    }

    &:hover {
      @include focusState;
    }

    &.s--disabled {
      @include disabledState;
    }
  }

  .wysiwyg__menubar {
    padding: 13px 8px;
    border-top-left-radius: 2px;
    border-top-right-radius: 2px;
    background-color: $color__f--bg;
    border-bottom: 1px solid $color__border--light;

    .s--focus & {
      border-color: $color__fborder--hover;
      border-bottom-color: $color__border--light;
    }

    .wysiwyg__menubar-button {
      @include btn-reset;
      width: 24px;
      margin-right: 35px - 6px - 6px - 6px - 6px;
      text-align: center;

      &:hover,
      &:focus,
      &.is-active {
        color: $color__link;
      }
    }
  }

  .wysiwyg__button {
    margin-top: 20px;
  }

  .wysiwyg__content {
    padding: 15px;

    min-height: 90px;
  }
</style>

<style lang="scss">
  @import '~styles/setup/_mixins-colors-vars.scss';

  .wysiwyg__content {
    .ProseMirror {
      h1, h2, h3, h4, h5, h6 {
        font-weight: 700;
      }

      p, ul, ol, h1, h2, h3, h4, h5 {
        margin-bottom: 1em;
      }

      ol {
        padding-left: 1em;

        li {
          list-style-type: decimal;
        }
      }

      ul {
        padding-left: 1em;

        li {
          list-style-type: disc;
        }
      }

      h1 {
        font-size: 2em;
        line-height: 1.25em;
      }

      h2 {
        font-size: 1.66em;
        line-height: 1.25em;
      }

      h3 {
        font-size: 1.33em;
        line-height: 1.25em;
      }

      h4 {
        font-size: 1.25em;
        line-height: 1.25em;
      }

      h5 {
        font-size: 1em;
        line-height: 1.25em;
      }

      // default code syntax hightlighting is github
      pre {
        color: $color__wysiwyg-codeText;
        padding: 15px;
        overflow: auto;
        background-color: $color__wysiwyg-codeBg;
        border-radius: 3px;
        font-family: "SFMono-Regular", Consolas, "Liberation Mono", Menlo, Courier, monospace;
      }

      a {
        color: $color__link;
      }

      sup {
        vertical-align: super;
        font-size: smaller;
      }

      sub {
        vertical-align: sub;
        font-size: smaller;
      }
    }
  }
</style>
