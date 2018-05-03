<template>
  <span>
    <a v-for="language in languages"
       :key="language.value"
       :href="editWithLanguage(language)"
       class="tag tag--disabled"
       :class="{ 'tag--enabled' : language.published }"
       @click="editInPlace($event, language)">
      {{ language.shortlabel }}
    </a>
  </span>
</template>

<script>
  import { TableCellMixin } from '@/mixins'

  export default {
    name: 'A17TableCellLanguages',
    mixins: [TableCellMixin],
    props: {
      languages: {
        type: Array,
        default: function () {
          return []
        }
      }
    },
    methods: {
      editWithLanguage: function (lang) {
        const langQuery = {}
        langQuery['lang'] = lang.value
        return this.editWithQuery(langQuery)
      },
      editWithQuery: function (context) {
        const queries = []
        for (let prop in context) {
          if (context.hasOwnProperty(prop)) {
            queries.push(encodeURIComponent(prop) + '=' + encodeURIComponent(context[prop]))
          }
        }
        const queryString = queries.length ? '?' + queries.join('&') : ''
        return this.editUrl !== '#' ? (this.editUrl + queryString) : this.editUrl
      },
      editInPlace: function (event, lang) {
        this.$emit('editInPlace', event, lang)
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  /* Languages */
  .tag {
    margin: 0 10px 0 0;
  }
</style>
