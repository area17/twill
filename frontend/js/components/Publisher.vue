<template>
  <div class="publisher__wrapper">
      <a17-switcher title="Status" name="publish_state" v-if="withPublicationToggle" :textEnabled="textEnabled" :textDisabled="textDisabled"></a17-switcher>
      <a17-reviewaccordion  v-if="reviewProcess && reviewProcess.length" :options="reviewProcess" name="review_process" :value="reviewProcessCompleteValues" :open="openStates['A17Reviewaccordion']" @open="openCloseAccordion">Review status</a17-reviewaccordion>
      <a17-radioaccordion  v-if="visibility && visibilityOptions && visibilityOptions.length" :radios="visibilityOptions" name="visibility" :value="visibility" :open="openStates['A17Radioaccordion']" @open="openCloseAccordion" @change="updateVisibility">Visibility</a17-radioaccordion>
      <a17-checkboxaccordion  v-if="languages && showLanguages&& languages.length > 1" :options="languages" name="active_languages" :value="publishedLanguagesValues" :open="openStates['A17Checkboxaccordion']" @open="openCloseAccordion">Languages</a17-checkboxaccordion>
      <a17-pubaccordion :open="openStates['A17Pubaccordion']" @open="openCloseAccordion" v-if="withPublicationTimeframe">Published on</a17-pubaccordion>
      <a17-revaccordion v-if="revisions.length" :open="openStates['A17Revisions']" @open="openCloseAccordion" :revisions="revisions">Revisions</a17-revaccordion>
      <a17-parentaccordion v-if="parents.length" :open="openStates['A17Parents']" @open="openCloseAccordion" :parents="parents" :value="parentId">Parent page</a17-parentaccordion>
      <div class="publisher__item" v-if="revisions.length">
        <a href="#" class="publisher__link" @click.prevent="openPreview"><span v-svg symbol="preview"></span><span class="f--link-underlined--o">Preview changes</span></a>
      </div>
      <div class="publisher__item publisher__item--btns">
        <a17-multibutton @button-clicked="buttonClicked" :options="submitOptions" type="submit"></a17-multibutton>
      </div>
  </div>
  <!-- <div class="publisher__trash">
    <a href="#" @click.prevent="openMoveToTrashModal" class="f--small f--note f--underlined">Move to Trash</a>
  </div> -->
</template>

<script>
  import { mapState, mapGetters } from 'vuex'

  import { PUBLICATION } from '@/store/mutations'

  import a17Switcher from '@/components/Switcher.vue'
  import a17RadioAccordion from '@/components/RadioAccordion.vue'
  import a17ReviewAccordion from '@/components/ReviewAccordion.vue'
  import a17CheckboxAccordion from '@/components/CheckboxAccordion.vue'
  import a17RevisionAccordion from '@/components/RevisionAccordion.vue'
  import a17PubAccordion from '@/components/PubAccordion.vue'
  import a17ParentsAccordion from '@/components/ParentsAccordion.vue'
  import a17MultiButton from '@/components/MultiButton.vue'

  import a17VueFilters from '@/utils/filters.js'

  export default {
    name: 'A17Publisher',
    components: {
      'a17-switcher': a17Switcher,
      'a17-radioaccordion': a17RadioAccordion,
      'a17-checkboxaccordion': a17CheckboxAccordion,
      'a17-reviewaccordion': a17ReviewAccordion,
      'a17-revaccordion': a17RevisionAccordion,
      'a17-parentaccordion': a17ParentsAccordion,
      'a17-pubaccordion': a17PubAccordion,
      'a17-multibutton': a17MultiButton
    },
    props: {
      showLanguages: {
        type: Boolean,
        default: true
      }
    },
    data: function () {
      return {
        singleOpen: true,
        openStates: {
          'A17Reviewaccordion': false,
          'A17Radioaccordion': false,
          'A17Checkboxaccordion': false,
          'A17Revisions': false,
          'A17Pubaccordion': false,
          'A17Parents': false
        }
      }
    },
    filters: a17VueFilters,
    computed: {
      reviewProcessCompleteValues: function () {
        const values = []

        if (this.reviewProcessComplete.length) {
          this.reviewProcessComplete.forEach(function (item) {
            values.push(item.value)
          })
        }

        return values
      },
      submitOptions: function () {
        return this.$store.getters.getSubmitOptions
      },
      publishedLanguagesValues: function () {
        const values = []

        if (this.publishedLanguages.length) {
          this.publishedLanguages.forEach(function (item) {
            values.push(item.value)
          })
        }

        return values
      },
      ...mapState({
        languages: state => state.language.all,
        revisions: state => state.revision.all,
        parentId: state => state.parents.active,
        parents: state => state.parents.all,
        published: state => state.publication.published,
        publishSubmit: state => state.publication.publishSubmit,
        textEnabled: state => state.publication.publishedLabel,
        textDisabled: state => state.publication.draftLabel,
        withPublicationToggle: state => state.publication.withPublicationToggle,
        withPublicationTimeframe: state => state.publication.withPublicationTimeframe,
        visibility: state => state.publication.visibility,
        visibilityOptions: state => state.publication.visibilityOptions,
        reviewProcess: state => state.publication.reviewProcess
      }),
      ...mapGetters([
        'publishedLanguages',
        'reviewProcessComplete'
      ])
    },
    methods: {
      buttonClicked: function (buttonName) {
        this.$store.commit(PUBLICATION.UPDATE_SAVE_TYPE, buttonName)
      },
      openCloseAccordion: function (isOpen, componentname) {
        if (!this.singleOpen) return

        if (isOpen) {
          for (let prop in this.openStates) {
            this.openStates[prop] = prop === componentname
          }
        } else {
          this.openStates[componentname] = false
        }
      },
      openPreview: function () {
        if (this.$root.$refs.preview) this.$root.$refs.preview.open(0)
      },
      updateVisibility: function (newValue) {
        this.$store.commit(PUBLICATION.UPDATE_PUBLISH_VISIBILITY, newValue)
      },
      openMoveToTrashModal: function () {
        this.$parent.$refs.moveToTrashModal.open() // Goes back to parent Form.vue componenent
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  $trigger_height:55px;

  .publisher {
  }

  .publisher__wrapper {
    border-radius:2px;
    border:1px solid $color__border;
    background:$color__background;
    margin-bottom:20px;
  }

  .publisher__trash {
    padding:0 10px;
    margin-bottom:20px;
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

      // &:hover {
      //   text-decoration: underline;
      // }
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
