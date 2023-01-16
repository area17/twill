<template>
  <a17-accordion :open="open" @toggleVisibility="notifyOpen">
    <span slot="accordion__title"><slot></slot></span>
    <div slot="accordion__value" v-html="currentLabel"></div>
    <div class="accordion__fields">
      <a17-select name="parent_id" :options="options" :selected="currentValue" size="small" @change="updateSelected"></a17-select>
    </div>
  </a17-accordion>
</template>

<script>
  import { mapState } from 'vuex'

  import a17Accordion from '@/components/Accordion.vue'
  import VisibilityMixin from '@/mixins/toggleVisibility'
  import { PARENTS } from '@/store/mutations'
  import parentTreeToOptions from '@/utils/parentTreeToOptions.js'

  export default {
    name: 'A17Parents',
    components: {
      'a17-accordion': a17Accordion
    },
    mixins: [VisibilityMixin],
    props: {
      value: {
        default: 0
      },
      parents: {
        default: function () {
          return []
        }
      }
    },
    computed: {
      currentLabel: function () {
        const selectedOption = this.options.filter(this.isSameValue)
        if (selectedOption.length) return selectedOption[0].label
        else return ''
      },
      options: function () {
        const options = parentTreeToOptions(this.parents, '&nbsp;&nbsp;&nbsp;')
        const noParent = { value: 0, label: '(No parent)' }
        options.unshift(noParent)

        return options
      },
      ...mapState({
        currentValue: state => state.parents.active
      })
    },
    methods: {
      isSameValue: function (option) {
        return option.value === this.currentValue
      },
      updateSelected: function (newValue) {
        this.$store.commit(PARENTS.UPDATE_PARENT, newValue)
      },
      notifyOpen: function (newValue) {
        this.$emit('open', newValue, this.$options.name)
      }
    }
  }
</script>

<style lang="scss" scoped>

  .revaccordion__scroller {
    height:100%;
    overflow:hidden;
    overflow-y: auto;
    max-height:165px;
    margin:-12px -20px;
  }

  .revaccordion__list {
    padding:12px 20px;
  }

  .revaccordion__item {
    display: flex;
    flex-direction: row;
    flex-wrap:no-wrap;
    color:$color__text--light;
    padding:7.5px 20px;
    cursor:pointer;
    margin-left:-20px;
    margin-right:-20px;

    &:hover {
      color:$color__text;
      background:$color__light;
    }
  }

  .revaccordion__author {
    flex-grow: 1;
  }

  .revaccordion__datetime {
    color:$color__link;
    white-space: nowrap;
    overflow:hidden;
  }

</style>
