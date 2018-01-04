<template>
  <div class="modal--titleEditor">
    <a17-textfield :label="titleLabel" :name="titleName" :required="true" :initialValue="savedTitle" :autofocus="true" @change="updateTitle" :maxlength="100" autocomplete="off" :inStore="mode === 'update' ? 'value' : ''"></a17-textfield>
    <slot></slot>
    <!-- <a17-textfield v-if="withPermalink" label="Permalink" name="slug" :prefix="baseUrl | prettierUrl" :initialValue="inputPermalink" @change="updatePermalink" @blur="formatPermalink" :maxlength="100" autocomplete="off" :inStore="mode === 'update' ? 'value' : ''"></a17-textfield> -->
  </div>
</template>

<script>
  import a17VueFilters from '@/utils/filters.js'

  export default {
    name: 'A17ModalTitleEditor',
    props: {
      titleLabel: {
        type: String,
        default: 'Title'
      },
      titleName: {
        type: String,
        default: 'title'
      },
      title: {
        type: String,
        default: ''
      },
      baseUrl: {
        type: String,
        default: ''
      },
      permalink: {
        type: String,
        default: ''
      },
      withPermalink: {
        type: Boolean,
        default: true
      },
      mode: {
        type: String, // create / update
        default: 'create'
      }
    },
    data: function () {
      return {
        savedTitle: this.title,
        inputPermalink: this.permalink,
        savedPermalink: this.permalink
      }
    },
    filters: a17VueFilters,
    methods: {
      updateTitle: function (value) {
        this.savedTitle = value
        // update input permalink too (need watch in textfield)
        this.formatPermalink(value)
      },
      formatPermalink: function (newValue) {
        const slug = this.$options.filters.slugify(newValue)
        this.savedPermalink = slug

        this.syncInputPermalink()
      },
      syncInputPermalink: function () {
        this.inputPermalink = this.savedPermalink
      },
      updatePermalink: function (value) {
        this.savedPermalink = value
      },
      update: function () {
        return {
          title: this.savedTitle,
          permalink: this.savedPermalink
        }
      }
    }
  }
</script>
