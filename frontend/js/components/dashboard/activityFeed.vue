<template>
  <div class="box activityFeed">
    <header class="box__header">
      <slot></slot>
    </header>
    <div class="box__body">
      <table class="activityFeed__list">
        <template v-for="(row, index) in rows">
          <a17-activity-row :row="row" :index="index" :columns="columns" :key="row.id"></a17-activity-row>
        </template>
      </table>
    </div>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import A17ActivityRow from '@/components/dashboard/activityRow.vue'

  export default {
    name: 'A17ActivityFeed',
    components: {
      'a17-activity-row': A17ActivityRow,
    },
    computed: {
      ...mapState({
        page: state => state.datatable.page,
        rows: state => state.datatable.data,
        maxPage: state => state.datatable.maxPage,
        columns: state => state.datatable.columns
      })
    },
    methods: {
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .activityFeed {
  }

  .activityFeed__list {
    overflow: hidden;
    width: 100%;
    border-collapse: collapse;
    border-spacing: 0;
  }
</style>
