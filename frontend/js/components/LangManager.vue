<template>
  <div class="languageManager" v-if="languages.length > 1">
    <div class="languageManager__switcher">
      <a17-langswitcher :in-modal="true"/>
    </div>
    <a17-dropdown class="languageManager__dropdown"
                  ref="languageManagerDropdown"
                  position="bottom-right"
                  :clickable="true">
      <button class="languageManager__button"
              type="button"
              @click="$refs.languageManagerDropdown.toggle()">
        {{currentValue.length }} Live <span v-svg symbol="dropdown_module"></span>
      </button>
      <div slot="dropdown__content" class="languageManager__dropdown-content">
        <a17-checkboxgroup name="langManager"
                           :options="languages"
                           :selected="currentValue"
                           :min="1"
                           @change="changeValue"
                           />
      </div>
    </a17-dropdown>
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
    display: flex;
    justify-content: space-between;
    flex-wrap: nowrap;
    padding: 20px;
  }

  .languageManager__switcher {
    height: 35px;
    overflow: hidden;
    border: 1px solid #d9d9d9;
    border-radius: 2px;
  }

  .languageManager__button {
    @include btn-reset;
    color: $color--icons;
    padding: 0;
    margin-left: 15px;
    height: 35px;
    line-height: 35px;

    &:focus,
    &:hover {
      color: $color--text;
    }

    .icon {
      position: relative;
      margin-left: 5px;
      top: -1px;
    }
  }

  .languageManager__dropdown-content {
    max-height: 240px;
    overflow-y: scroll;
  }
</style>
