<template>
  <a17-accordion :open="open" @toggleVisibility="notifyOpen">
    <span slot="accordion__title"><slot></slot></span>
    <div slot="accordion__value" v-html="currentLabel"></div>
    <a17-select name="parent_id" :options="options" :selected="currentValue" size="large" @change="updateSelected"></a17-select>
  </a17-accordion>
</template>

<script>
  import { mapState } from 'vuex'

  import a17Accordion from '@/components/Accordion.vue'
  import VisibilityMixin from '@/mixins/toggleVisibility'

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
        var selectedOption = this.options.filter(this.isSameValue)
        if (selectedOption.length) return selectedOption[0].label
        else return ''
      },
      options: function () {
        const options = []
        const spacing = '&nbsp;&nbsp;&nbsp;'

        // No parents default value
        const noParent = { value: 0, label: '(No parent)' }
        options.push(noParent)

        function setSpacing (level) {
          return Array(level + 1).join(spacing) + ''
        }

        function getOptionsFromArray (parents, level) {
          parents.forEach(function (parent) {
            const option = {}
            option.value = parent.id
            option.label = setSpacing(level) + parent.name
            options.push(option)

            if (parent.children && parent.children.length) {
              const newLevel = level + 1
              getOptionsFromArray(parent.children, newLevel)
            }
          })
        }

        getOptionsFromArray(this.parents, 0)

        return options
      },
      ...mapState({
        currentValue: state => state.parents.active
      })
    },
    methods: {
      isSameValue: function (option) {
        if (option.value === this.currentValue) return true
        return false
      },
      updateSelected: function (newValue) {
        this.$store.commit('updateParent', newValue)
      },
      notifyOpen: function (newValue) {
        this.$emit('open', newValue, this.$options.name)
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

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
