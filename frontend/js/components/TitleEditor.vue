<template>
  <div class="titleEditor" :class="titleEditorClasses">
    <div class="titleEditor__preview">
      <h2 class="titleEditor__title" :class="{ 'titleEditor__title-only' : !permalink }">
        <a v-if="editableTitle" @click.prevent="$refs.editModal.open()" href="#" class="titleEditor__title-wrapper">
          <a17-avatar
            v-if="thumbnail"
            :name="title"
            :thumbnail="thumbnail"
          />
          <span class="titleEditor__title">
            <span class="f--underlined--o">{{ title }}</span> <span v-svg symbol="edit"></span>
          </span>
        </a>
        <span v-else class="titleEditor__title-wrapper">
          <a17-avatar
            v-if="thumbnail"
            :name="title"
            :thumbnail="thumbnail"
          />
          <span class="titleEditor__title">
            {{ customTitle ? customTitle : title }}
          </span>
        </span>
      </h2>
      <a v-if="(permalink || customPermalink) && !showModal" :href="fullUrl" target="_blank" class="titleEditor__permalink f--small">
        <span class="f--note f--external f--underlined--o">{{ visibleUrl | prettierUrl }}</span>
      </a>
      <span v-if="showModal" class="titleEditor__permalink f--small f--note f--external f--underlined--o">{{ visibleUrl | prettierUrl }}</span>

      <!-- Editing modal -->
      <a17-modal class="modal--form" ref="editModal" :title="modalTitle" :forceLock="disabled">
        <a17-langmanager :control-publication="controlLanguagesPublication"></a17-langmanager>
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
  import { mapGetters,mapState } from 'vuex'

  import A17Avatar from '@/components/Avatar.vue'
  import langManager from '@/components/LangManager.vue'
  import a17ModalValidationButtons from '@/components/modals/ModalValidationButtons.vue'
  import InputframeMixin from '@/mixins/inputFrame'
  import LocaleMixin from '@/mixins/locale'
  import a17VueFilters from '@/utils/filters.js'

  export default {
    name: 'A17TitleEditor',
    mixins: [InputframeMixin, LocaleMixin],
    components: {
      'a17-avatar': A17Avatar,
      'a17-modal-validation': a17ModalValidationButtons,
      'a17-langmanager': langManager
    },
    props: {
      modalTitle: {
        type: String,
        default: function () {
          return this.$trans('modal.update.title')
        }
      },
      warningMessage: {
        type: String,
        default: 'Missing title'
      },
      thumbnail: {
        type: String,
        default: ''
      },
      showModal: {
        type: Boolean,
        default: false
      },
      name: {
        default: 'title'
      },
      editableTitle: {
        type: Boolean,
        default: true
      },
      controlLanguagesPublication: {
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
      },
      localizedPermalinkbase: {
        type: String,
        default: ''
      },
      localizedCustomPermalink: {
        type: String,
        default: ''
      }
    },
    data: function () {
      return {
        disabled: false
      }
    },
    mounted: function () {
      this.showModal && this.$refs.editModal.open()
    },
    computed: {
      titleEditorClasses: function () {
        return {
          'titleEditor--error': this.error || (this.title === this.warningMessage)
        }
      },
      mode: function () {
        if (this.showModal) return 'done'
        return this.title.length > 0 ? 'update' : 'create'
      },
      fullUrl: function () {
        return this.customlink || this.baseUrl
          .replace('{language}', this.currentLocale.value)
          .replace('{preview}/', this.published ? '' : 'admin-preview/') + this.permalink
      },
      visibleUrl: function () {
        return this.customlink || this.baseUrl
          .replace('{language}', this.currentLocale.value)
          .replace('{preview}/', '') + this.permalink
      },
      title: function () {
        // Get the title from the store
        const title = this.fieldValueByName(this.name) ? this.fieldValueByName(this.name) : ''
        const titleValue = typeof title === 'string' ? title : title[this.currentLocale.value]
        return titleValue || this.warningMessage
      },
      customlink: function () {
        const localizedCustomPermalink = this.localizedCustomPermalink.length > 0 ? JSON.parse(this.localizedCustomPermalink) : {}
        return Object.keys(localizedCustomPermalink).length > 0 ? localizedCustomPermalink[this.currentLocale.value] : (this.customPermalink ? this.customPermalink : false)
      },
      permalink: function () {
        const localizedPermalinkbase = this.localizedPermalinkbase.length > 0 ? JSON.parse(this.localizedPermalinkbase) : {}
        return Object.keys(localizedPermalinkbase).length > 0 ? ((this.currentLocale.value in localizedPermalinkbase) ? localizedPermalinkbase[this.currentLocale.value].concat('/', this.fieldValueByName('slug')[this.currentLocale.value]) : this.fieldValueByName('slug')[this.currentLocale.value]) : this.fieldValueByName('slug')[this.currentLocale.value]
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

  .titleEditor__title-wrapper {
    display: inline-flex;
    align-content: center;
    align-items: center;

    > .avatar {
      margin-right: 10px;
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
