<template>
  <span>
    <!--Todo: check formatDateLabel logic-->
    <span v-if="formatDateLabel.length > 0"
          class="tablecell__datePub"
          :class="{ 's--expired' : formatDateLabel === textExpired }">
      {{ startDate | formatDatatableDate }}
      <template v-if="endDate">- {{ endDate | formatDatatableDate }}</template>
      <br>
      <span>{{ formatDateLabel }}</span>
    </span>
    <span v-else>
      <template v-if="!startDate">
        —
      </template>
      <template v-else>
      {{ startDate | formatDatatableDate }}
      </template>
    </span>
  </span>
</template>

<script>
  import compareAsc from 'date-fns/compare_asc'

  import TableCellMixin from '@/mixins/tableCell'
  import a17VueFilters from '@/utils/filters.js'

  export default {
    name: 'A17TableCellDates',
    mixins: [TableCellMixin],
    data () {
      return {
        textExpired: this.$trans('publisher.expired'),
        textScheduled: this.$trans('publisher.scheduled')
      }
    },
    computed: {
      formatDateLabel: function () {
        let label = ''
        const scoreStart = compareAsc(this.startDate, new Date())
        const scoreEnd = this.endDate ? compareAsc(this.endDate, new Date()) : 1

        if (this.startDate && scoreEnd < 0) label = this.textExpired
        else if (scoreStart > 0) label = this.textScheduled

        return label
      },
      startDate: function () {
        return this.row.hasOwnProperty('publish_start_date') ? this.row.publish_start_date : ''
      },
      endDate: function () {
        return this.row.hasOwnProperty('publish_end_date') ? this.row.publish_end_date : ''
      }
    },
    filters: a17VueFilters
  }
</script>

<style lang="scss" scoped>

  /* Publication dates */
  .tablecell__datePub {
    color: $color__text--light;

    span {
      color: $color__ok;
    }

    &.s--expired {
      span {
        color: $color__error;
      }
    }
  }
</style>
