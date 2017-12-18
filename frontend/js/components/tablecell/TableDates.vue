<template>
  <span>
    <span v-if="formatDateLabel" class="tablecell__datePub" :class="{ 's--expired' : formatDateLabel === textExpired }">
      {{ startDate | formatDatatableDate }}<br /><span>{{ formatDateLabel }}</span>
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
  import a17VueFilters from '@/utils/filters.js'
  import compareAsc from 'date-fns/compare_asc'

  export default {
    name: 'A17TableDates',
    props: {
      startDate: {
        type: String,
        default: ''
      },
      endDate: {
        type: String,
        default: ''
      },
      textExpired: {
        type: String,
        default: 'Expired'
      },
      textScheduled: {
        type: String,
        default: 'Scheduled'
      }
    },
    computed: {
      formatDateLabel: function () {
        let label = ''
        let scoreStart = compareAsc(this.startDate, new Date())
        let scoreEnd = this.endDate ? compareAsc(this.endDate, new Date()) : 1

        if (this.startDate && scoreEnd < 0) label = this.textExpired
        else if (scoreStart > 0) label = this.textScheduled

        return label
      }
    },
    filters: a17VueFilters
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  /* Publication dates */
  .tablecell__datePub {
    color:$color__text--light;

    span {
      color:$color__ok;
    }

    &.s--expired {
      span {
        color:$color__error;
      }
    }
  }
</style>
