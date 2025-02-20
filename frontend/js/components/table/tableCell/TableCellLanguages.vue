<template>
  <span>
    <a v-for="language in displayedLanguages"
       :key="language.value"
       :href="editWithLanguage(language)"
       class="tag tag--disabled"
       :class="{ 'tag--enabled' : language.published }"
       @click="editInPlace($event, language)">
      {{ language.shortlabel }}
    </a>
    <a v-if="languages.length > 4" :href="editWithLanguage(languages[0])" @click="editInPlace($event, languages[0])" class="more__languages f--small">
        + {{ languages.length - 4 }} more
    </a>
  </span>
</template>

<script>
  import TableCellMixin from '@/mixins/tableCell'

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
    computed: {
      displayedLanguages: function () {
        return this.languages.slice(0, 4)
      }
    },
    methods: {
      editWithLanguage: function (lang) {
        const langQuery = {}
        langQuery.lang = lang.value
        return this.editWithQuery(langQuery)
      },
      editWithQuery: function (context) {
        const queries = []
        for (const prop in context) {
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

  /* Languages */
  .tag {
    margin: 0 10px 0 0;
  }

  .more__languages {
    color: $color__link-light;
    text-decoration: none;
  }
</style>
