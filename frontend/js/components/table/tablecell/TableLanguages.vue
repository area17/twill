<template>
  <span>
    <a v-for="language in languages" :key="language.value" :href="editWithLanguage(language)" @click="editInPlace($event, language)" class="tag tag--disabled" :class="{ 'tag--enabled' : language.published }">{{ language.shortlabel }}</a>
  </span>
</template>

<script>
  import { LANGUAGE } from '@/store/mutations'
  export default {
    name: 'A17TableLanguages',
    props: {
      languages: {
        type: Array,
        default: function () {
          return []
        }
      },
      editUrl: {
        type: String,
        default: '#'
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
        for (var prop in context) {
          if (context.hasOwnProperty(prop)) {
            queries.push(encodeURIComponent(prop) + '=' + encodeURIComponent(context[prop]))
          }
        }

        const queryString = queries.length ? '?' + queries.join('&') : ''
        return this.editUrl !== '#' ? (this.editUrl + queryString) : this.editUrl
      },
      editInPlace: function (event, lang) {
        this.$store.commit(LANGUAGE.UPDATE_LANG, lang.value)
        this.$emit('editInPlaceWithLang', event)
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  /* Languages */
  .tag {
    margin:0 10px 0 0;
  }
</style>
