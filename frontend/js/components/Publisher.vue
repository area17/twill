<template>
  <div class="publisher">
    <div class="publisher__wrapper">
      <a17-switcher title="Status" name="publish_state"></a17-switcher>
      <a17-radioaccordion  v-if="visibilityOptions && visibilityOptions.length" :radios="visibilityOptions" name="visibility" :value="visibility" :open="openStates['A17Radioaccordion']" @open="openCloseAccordion" @change="updateVisibility">Visibility</a17-radioaccordion>
      <a17-checkboxaccordion  v-if="languages && languages.length" :options="languages" name="active_languages" :value="publishedLanguagesValues" :open="openStates['A17Checkboxaccordion']" @open="openCloseAccordion">Languages</a17-checkboxaccordion>
      <a17-pubaccordion :open="openStates['A17Pubaccordion']" @open="openCloseAccordion">Published on</a17-pubaccordion>
      <a17-revaccordion v-if="revisions.length" :open="openStates['A17Revisions']" @open="openCloseAccordion" :revisions="revisions">Revisions</a17-revaccordion>
      <div class="publisher__item">
        <a href="#" class="publisher__link" @click.prevent="openPreview"><span v-svg symbol="preview"></span>Preview changes</a>
      </div>
      <div class="publisher__item publisher__item--btns">
        <a17-multibutton :options="submitOptions" type="submit"></a17-multibutton>
      </div>
    </div>
    <div class="publisher__trash">
      <a href="#" @click.prevent="opentMoveToTrashModal" class="f--small f--note f--underlined">Move to Trash</a>
    </div>
  </div>

</template>

<script>
  import { mapState, mapGetters } from 'vuex'

  import a17Switcher from '@/components/Switcher.vue'
  import a17RadioAccordion from '@/components/RadioAccordion.vue'
  import a17CheckboxAccordion from '@/components/CheckboxAccordion.vue'
  import a17RevisionAccordion from '@/components/RevisionAccordion.vue'
  import a17PubAccordion from '@/components/PubAccordion.vue'
  import a17MultiButton from '@/components/MultiButton.vue'

  import a17VueFilters from '@/utils/filters.js'

  export default {
    name: 'A17Publisher',
    components: {
      'a17-switcher': a17Switcher,
      'a17-radioaccordion': a17RadioAccordion,
      'a17-checkboxaccordion': a17CheckboxAccordion,
      'a17-revaccordion': a17RevisionAccordion,
      'a17-pubaccordion': a17PubAccordion,
      'a17-multibutton': a17MultiButton
    },
    data: function () {
      return {
        singleOpen: true,
        publishSubmit: 'live',
        openStates: {
          'A17Radioaccordion': false,
          'A17Checkboxaccordion': false,
          'A17Revisions': false,
          'A17Pubaccordion': false
        }
      }
    },
    filters: a17VueFilters,
    computed: {
      publishedLanguagesValues: function () {
        const publishedLanguagesValues = []

        if (this.publishedLanguages.length) {
          this.publishedLanguages.forEach(function (language) {
            publishedLanguagesValues.push(language.value)
          })
        }

        return publishedLanguagesValues
      },
      submitOptions: function () {
        return this.published ? this.defaultSubmitOptions[this.publishSubmit] : this.defaultSubmitOptions['draft']
      },
      ...mapState({
        languages: state => state.language.all,
        revisions: state => state.revision.all,
        published: state => state.publication.published,
        visibility: state => state.publication.visibility,
        visibilityOptions: state => state.publication.visibilityOptions,
        defaultSubmitOptions: state => state.publication.submitOptions
      }),
      ...mapGetters([
        'publishedLanguages'
      ])
    },
    methods: {
      openCloseAccordion: function (isOpen, componentname) {
        if (!this.singleOpen) return

        if (isOpen) {
          for (var prop in this.openStates) {
            if (prop !== componentname) this.openStates[prop] = false // close other accordion
            else this.openStates[prop] = true
          }
        }
      },
      openPreview: function () {
        this.$store.commit('updateRevision', 0)
        this.$root.$refs.preview.open()
      },
      updateVisibility: function (newValue) {
        this.$store.commit('updatePublishVisibility', newValue)
      },
      opentMoveToTrashModal: function () {
        this.$parent.$refs.moveToTrashModal.open() // Goes back to parent Form.vue componenent
      }
    },
    beforeMount: function () {
      if (this.published) this.publishSubmit = 'update'
    }
  }
</script>

<style lang="scss" scoped>
  @import "../../scss/setup/variables.scss";
  @import "../../scss/setup/colors.scss";
  @import "../../scss/setup/mixins.scss";

  $trigger_height:55px;

  .publisher {
    margin-bottom:20px;

    @include breakpoint('medium+') {
      margin-bottom:0;
    }
  }

  .publisher__wrapper {
    border-radius:2px;
    border:1px solid $color__border;
    background:$color__background;
  }

  .publisher__trash {
    padding:15px 10px;
  }

  .publisher__item {
    border-bottom:1px solid $color__border--light;

    &:last-child {
      border-bottom:0 none;
    }
  }

  .publisher__item {
    color:$color__text--light;

    a {
      color:$color__link;
      text-decoration: none;

      &:hover {
        text-decoration: underline;
      }
    }
  }

  .revisionaccordion__list {
    padding:20px;
  }

  .publisher__link {
    height:$trigger_height;
    line-height:$trigger_height;
    padding:0 20px;
    display:block;

    .icon {
      margin-right:10px;
      color:$color__link;
    }
  }

  .publisher__item--btns {
    padding:10px;
  }


</style>
