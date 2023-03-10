<template>
  <a17-accordion :open="open" @toggleVisibility="notifyOpen">
    <span slot="accordion__title"><slot></slot></span>
    <div slot="accordion__value">
      <template v-if="startDate">
        {{ startDateForDisplay | formatDateWithFormat(localizedDateDisplayFormat) }}
      </template>
      <template v-else>
        {{ defaultStartDate }}
      </template>
    </div>
    <div class="accordion__fields">
      <a17-datepicker
        name="publish_date"
        :place-holder="$trans('publisher.start-date')"
        :time_24hr="date_24h"
        :altFormat="dateFormat"
        :initialValue="startDate"
        :maxDate="endDate"
        :enableTime="true"
        :allowInput="false"
        :staticMode="true"
        @open="openStartCalendar"
        @close="closeCalendar"
        @input="updateStartDate"
        :clear="true"
      ></a17-datepicker>
      <a17-datepicker name="end_date"
        :place-holder="$trans('publisher.end-date')"
        :time_24hr="date_24h"
        :altFormat="dateFormat"
        :initialValue="endDate"
        :minDate="startDate"
        :enableTime="true"
        :allowInput="false"
        :staticMode="true"
        @open="openEndCalendar"
        @close="closeCalendar"
        @input="updateEndDate"
        :clear="true"
      ></a17-datepicker>
    </div>
  </a17-accordion>
</template>

<script>
  import parseJson from 'date-fns/parse'
  import { mapState } from 'vuex'

  import VisibilityMixin from '@/mixins/toggleVisibility'
  import { PUBLICATION } from '@/store/mutations'
  import a17VueFilters from '@/utils/filters.js'
  import { getTimeFormatForCurrentLocale, isCurrentLocale24HrFormatted } from '@/utils/locale'

  import a17Accordion from './Accordion.vue'

  export default {
    name: 'A17Pubaccordion',
    components: {
      'a17-accordion': a17Accordion
    },
    mixins: [VisibilityMixin],
    props: {
      defaultStartDate: {
        type: String,
        default: function () {
          return this.$trans('publisher.immediate')
        }
      },
      defaultEndDate: {
        type: String,
        default: ''
      },
      dateDisplayFormat: {
        type: String,
        default: null
      },
      dateFormat: {
        type: String,
        default: null
      },
      date_24h: {
        type: Boolean,
        default: isCurrentLocale24HrFormatted()
      }
    },
    filters: a17VueFilters,
    computed: {
      ...mapState({
        startDate: state => state.publication.startDate,
        endDate: state => state.publication.endDate
      }),
      startDateForDisplay() {
        return parseJson(this.startDate + 'Z').toISOString()
      },
      localizedDateDisplayFormat() {
        if (this.dateDisplayFormat) {
          return this.dateDisplayFormat
        }
        return 'MMM, DD, YYYY, ' + getTimeFormatForCurrentLocale(this.date_24h)
      }
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
