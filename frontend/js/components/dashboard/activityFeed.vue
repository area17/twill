<template>
  <div class="box activityFeed">
    <header class="box__header">
      <slot></slot>

      <ul class="box__filter">
        <li v-for="(navItem, index) in navFilters"><a href="#" :class="{ 's--on' : navActive === index }" @click.prevent="filterStatus(index, navItem.slug)">{{ navItem.name }}</a></li>
      </ul>
    </header>
    <div class="box__body">
      <table class="activityFeed__table" v-if="rows.length > 0">
        <template v-for="(row, index) in rows">
          <a17-activity-row :row="row" :index="index" :columns="columns" :key="row.id"></a17-activity-row>
        </template>
      </table>
      <template v-else="">
        <div class="activityFeed__empty">
          <h4>{{ emptyMessage }}</h4>
        </div>
      </template>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import { DATATABLE } from '@/store/mutations'
  import A17ActivityRow from '@/components/dashboard/activityRow.vue'

  export default {
    name: 'A17ActivityFeed',
    components: {
      'a17-activity-row': A17ActivityRow
    },
    props: {
      emptyMessage: {
        type: String,
        default: 'You don\'t have any activity yet.'
      }
    },
    data: function () {
      return {
        navFilters: [
          {
            name: 'All activity',
            slug: 'all'
          },
          {
            name: 'My activity',
            slug: 'mine'
          }
        ],
        navActive: 0
      }
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
      filterStatus: function (index, slug) {
        if (this.navActive === index) return
        this.navActive = index
        this.$store.commit(DATATABLE.UPDATE_DATATABLE_PAGE, 1)
        this.$store.commit(DATATABLE.UPDATE_DATATABLE_FILTER_STATUS, slug)
        this.reloadDatas()
      },
      reloadDatas: function () {
        // reload datas
        this.$store.dispatch('getDatatableDatas')
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .activityFeed {
  }

  .activityFeed__table {
    // overflow: hidden;
    width: 100%;
    border-collapse: collapse;
    border-spacing: 0;
  }

  .activityFeed__empty {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 200px;
    padding: 15px 20px;

    h4 {
      @include font-medium();
      color: $color__f--text;
    }
  }
</style>
