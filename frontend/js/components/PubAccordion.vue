<template>
  <a17-accordion :open="open" @toggleVisibility="notifyOpen">
    <span slot="accordion__title"><slot></slot></span>
    <div slot="accordion__value">
      <template v-if="startDate">
        {{ startDate | formatCalendarDate }}
      </template>
      <template v-else>
        {{ defaultStartDate }}
      </template>
    </div>
    <div class="accordion__fields">
      <a17-datepicker name="publish_date" place-holder="Start Date" :initialValue="startDate" :maxDate="endDate" :enableTime="true" :allowInput="true" :staticMode="true" @open="openStartCalendar" @close="closeCalendar" @input="updateStartDate" :clear="true"></a17-datepicker>
      <a17-datepicker name="end_date" place-holder="End Date" :initialValue="endDate" :minDate="startDate" :enableTime="true" :allowInput="true" :staticMode="true" @open="openEndCalendar" @close="closeCalendar" @input="updateEndDate" :clear="true"></a17-datepicker>
    </div>
  </a17-accordion>
</template>

<script>
  import { mapState } from 'vuex'
  import { PUBLICATION } from '@/store/mutations'
  import a17VueFilters from '@/utils/filters.js'

  import a17Accordion from './Accordion.vue'
  import VisibilityMixin from '@/mixins/toggleVisibility'

  export default {
    name: 'A17Pubaccordion',
    components: {
      'a17-accordion': a17Accordion
    },
    mixins: [VisibilityMixin],
    props: {
      defaultStartDate: {
        type: String,
        default: 'Immediate'
      },
      defaultEndDate: {
        type: String,
        default: ''
      }
    },
    filters: a17VueFilters,
    computed: {
      ...mapState({
        startDate: state => state.publication.startDate,
        endDate: state => state.publication.endDate
      })
    },
    methods: {
      updateStartDate: function (newValue) {
        this.$store.commit(PUBLICATION.UPDATE_PUBLISH_START_DATE, newValue)
      },
      updateEndDate: function (newValue) {
        this.$store.commit(PUBLICATION.UPDATE_PUBLISH_END_DATE, newValue)
      },
      notifyOpen: function (newValue) {
        this.$emit('open', newValue, this.$options.name)
      },
      openCalendar: function () {
        setTimeout(function () {
          const accordions = document.querySelectorAll('.accordion.s--open, .accordion.s--open .accordion__dropdown')

          accordions.forEach(function (accordion) {
            accordion.style.overflow = 'visible'
          })
        }, 10)
      },
      openStartCalendar: function () {
        this.openCalendar()
      },
      openEndCalendar: function () {
        this.openCalendar()
      },
      closeCalendar: function () {
        const accordions = document.querySelectorAll('.accordion.s--open, .accordion.s--open .accordion__dropdown')

        accordions.forEach(function (accordion) {
          accordion.style.overflow = ''
        })
      }
    }
  }
</script>
