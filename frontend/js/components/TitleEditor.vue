<template>
  <div class="titleEditor" :class="titleEditorClasses">
    <div class="titleEditor__preview">
      <h2 class="titleEditor__title" :class="{ 'titleEditor__title-only' : !permalink }">
        <a v-if="editableTitle" @click.prevent="$refs.editModal.open()" href="#">
          <span class="f--underlined--o">{{ title }}</span> <span v-svg symbol="edit"></span>
        </a>
        <span v-else>{{ customTitle ? customTitle : title }}</span>
      </h2>
      <a v-if="permalink || customPermalink" :href="fullUrl" target="_blank" class="titleEditor__permalink f--small">
        <span class="f--note f--external f--underlined--o">{{ visibleUrl | prettierUrl }}</span>
      </a>

      <!-- Editing modal -->
      <a17-modal class="modal--form" ref="editModal" :title="modalTitle" :forceLock="disabled">
        <a17-langmanager></a17-langmanager>
        <form action="#" @submit.prevent="update" ref="modalForm">
          <slot name="modal-form"></slot>
          <a17-modal-validation :mode="mode" @disable="lockModal"></a17-modal-validation>
        </form>
      </a17-modal>
    </div>
    <slot></slot>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import a17VueFilters from '@/utils/filters.js'
  import a17ModalValidationButtons from '@/components/modals/ModalValidationButtons.vue'
  import langManager from '@/components/LangManager.vue'

  import InputframeMixin from '@/mixins/inputFrame'
  import LocaleMixin from '@/mixins/locale'

  export default {
    name: 'A17TitleEditor',
    mixins: [InputframeMixin, LocaleMixin],
    components: {
      'a17-modal-validation': a17ModalValidationButtons,
      'a17-langmanager': langManager
    },
    props: {
      modalTitle: {
        type: String,
        default: 'Update item'
      },
      warningMessage: {
        type: String,
        default: 'Missing title'
      },
      name: {
        default: 'title'
      },
      editableTitle: {
        type: Boolean,
        default: true
      },
      customTitle: {
        type: String,
        default: ''
      },
      customPermalink: {
        type: String,
        default: ''
      }
    },
    data: function () {
      return {
        disabled: false
      }
    },
    computed: {
      titleEditorClasses: function () {
        return {
          'titleEditor--error': this.error || (this.title === this.warningMessage)
        }
      },
      mode: function () {
        return this.title.length > 0 ? 'update' : 'create'
      },
      fullUrl: function () {
        return this.customPermalink || this.baseUrl
          .replace('{language}', this.currentLocale.value)
          .replace('{preview}/', this.published ? '' : 'admin-preview/') + this.permalink
      },
      visibleUrl: function () {
        return this.customPermalink || this.baseUrl
          .replace('{language}', this.currentLocale.value)
          .replace('{preview}/', '') + this.permalink
      },
      title: function () {
        // Get the title from the store
        const title = this.fieldValueByName(this.name) ? this.fieldValueByName(this.name) : ''
        const titleValue = typeof title === 'string' ? title : title[this.currentLocale['value']]
        return titleValue || this.warningMessage
      },
      permalink: function () {
        return this.fieldValueByName('slug')[this.currentLocale.value]
      },
      ...mapState({
        baseUrl: state => state.form.baseUrl,
        currentLocale: state => state.language.active,
        languages: state => state.language.all,
        fields: state => state.form.fields,
        published: state => state.publication.published
      }),
      ...mapGetters([
        'fieldValueByName'
      ])
    },
    filters: a17VueFilters,
    methods: {
      update: function () {
        this.$refs.editModal.hide()
      },
      lockModal: function (newValue) {
        this.disabled = newValue
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .titleEditor {
    margin-bottom: 20px;

    @include breakpoint('medium+') {
      margin-bottom: 0;
    }
  }

  .titleEditor__title {
    font-weight: 600;

    a {
      text-decoration: none;
    }

    .icon {
      color: $color__icons;
      margin-left: 7px;
    }

    a:hover .icon {
      color: $color__text;
    }

    .stickyNav.sticky__fixedTop & {
      line-height: 35px;
    }
  }

  .titleEditor--error .titleEditor__title {
    .f--underlined--o,
    .icon {
      color:$color__error;
    }

    &:hover {
      .f--underlined--o,
      .icon {
        color: $color__error;
      }

      .f--underlined--o {
        @include bordered($color__error, false);
      }
    }
  }

  .titleEditor__title-only {
    line-height: 35px;
  }

  .titleEditor__permalink {
    text-decoration: none;
    display: inline-block;
    white-space: nowrap;

    .stickyNav.sticky__fixedTop & {
      display: none;
    }
  }
</style>
