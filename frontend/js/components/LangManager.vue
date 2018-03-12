<template>
  <div class="languageManager" v-if="languages.length > 1">
    <a17-accordion :open="open">
      <div slot="accordion__value">{{ currentValue.length }} live</div>
      <a17-checkboxgroup name="langManager" :options="languages" @change="changeValue" :selected="currentValue" :min="1"></a17-checkboxgroup>
    </a17-accordion>
    <div class="languageManager__switcher"><a17-langswitcher :in-modal="true"></a17-langswitcher></div>
  </div>
</template>

<script>
  import VisibilityMixin from '@/mixins/toggleVisibility'
  import LocaleMixin from '@/mixins/locale'
  import { mapState, mapGetters } from 'vuex'
  import a17Accordion from './Accordion.vue'
  import a17Langswitcher from './LangSwitcher.vue'
  import { LANGUAGE } from '@/store/mutations'

  export default {
    name: 'A17LangManager',
    mixins: [VisibilityMixin, LocaleMixin],
    components: {
      'a17-accordion': a17Accordion,
      'a17-langswitcher': a17Langswitcher
    },
    props: {
      value: {
        default: function () { return [] }
      }
    },
    computed: {
      currentValue: {
        get () {
          const values = []

          if (this.publishedLanguages.length) {
            this.publishedLanguages.forEach(function (item) {
              values.push(item.value)
            })
          }

          return values
        }
      },
      ...mapState({
        languages: state => state.language.all
      }),
      ...mapGetters([
        'publishedLanguages'
      ])
    },
    methods: {
      changeValue: function (newValue) {
        this.$store.commit(LANGUAGE.PUBLISH_LANG, newValue)
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .languageManager {
    margin: 0 -20px;
    background-color: $color__light;
    position: relative;
  }

  .languageManager {
    .accordion {
      background-color: $color__light;
    }
    /deep/ .accordion__trigger,
    /deep/ .accordion__trigger:hover,
    /deep/ .accordion__trigger:focus {
      background-color: $color__light;
    }
  }

  .languageManager__switcher {
    position:absolute;
    top:11px;
    left:20px;
  }
</style>
