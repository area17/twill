<template>
  <div class="titleEditor">
    <div class="titleEditor__preview">
      <h2 class="titleEditor__title" :class="{ 'titleEditor__title-only' : !permalink }">
        <a @click.prevent="$refs.editModal.open()" href="#">
          <span class="f--underlined--o">{{ title }}</span> <span v-svg symbol="edit"></span>
        </a>
      </h2>
      <a v-if="permalink" :href="fullUrl" target="_blank" class="titleEditor__permalink f--small">
        <span class="f--note f--external f--underlined--o">{{ fullUrl | prettierUrl }}</span>
      </a>

      <!-- Editing modal -->
      <a17-modal class="modal--form" ref="editModal" :title="modalTitle">
        <form action="#" @submit.prevent="update" ref="modalForm">
          <slot name="modal-form"></slot>
          <a17-modal-validation :mode="mode"></a17-modal-validation>
        </form>
      </a17-modal>
    </div>
    <slot></slot>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import a17VueFilters from '@/utils/filters.js'
  import FormDataAsObj from '@/utils/formDataAsObj.js'
  import a17ModalValidationButtons from '@/components/Modals/ModalValidationButtons.vue'

  export default {
    name: 'A17TitleEditor',
    components: {
      'a17-modal-validation': a17ModalValidationButtons
    },
    props: {
      modalTitle: {
        type: String,
        default: 'Update item'
      }
    },
    computed: {
      mode: function () {
        return this.title.length > 0 ? 'update' : 'create'
      },
      fullUrl: function () {
        return this.baseUrl + this.permalink
      },
      title: function () {
        return this.currentLocale != null
          ? this.$store.state.form.title[this.currentLocale['value']]
          : this.$store.state.form.title
      },
      ...mapState({
        permalink: state => state.form.permalink,
        baseUrl: state => state.form.baseUrl,
        currentLocale: state => state.language.active
      })
    },
    filters: a17VueFilters,
    methods: {
      update: function () {
        let data = FormDataAsObj(this.$refs.modalForm)
        this.$store.commit('updateFormTitle', data[Object.keys(data)[0]])

        // if (this.permalink !== '') {
        //   this.$store.commit('updateFormPermalink', data.permalink)
        // }

        this.$refs.editModal.hide()
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
  }

  .titleEditor__title-only {
    line-height: 35px;
  }

  .titleEditor__permalink {
    text-decoration: none;
    display: inline-block;
    white-space: nowrap;
  }
</style>
