<template>
  <div class="box activityFeed">
    <header class="box__header">
      <slot></slot>

      <ul class="box__filter">
        <li v-for="(navItem, index) in navFilters" :key="index"><a href="#" :class="{ 's--on' : navActive === index }" @click.prevent="filterStatus(index, navItem.slug)">{{ navItem.name }}</a></li>
      </ul>
    </header>
    <div class="box__body">
      <table class="activityFeed__table" v-if="rows.data.length > 0">
        <template v-for="(row, index) in rows.data">
          <a17-activity-row :row="row" :index="index" :columns="columns" :key="row.id"></a17-activity-row>
        </template>
      </table>
      <template v-else>
        <div class="activityFeed__empty">
          <h4>{{ emptyMessage }}</h4>
        </div>
      </template>
      <a17-paginate :max="rows.last_page" :value="rows.current_page" :offset="20" :availableOffsets="[20]" @changePage="getData"/>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'

  import A17ActivityRow from '@/components/dashboard/activityRow.vue'
  import { DATATABLE } from '@/store/mutations'
  import A17Paginate from "@/components/table/Paginate.vue";

  export default {
    name: 'A17ActivityFeed',
    components: {
      A17Paginate,
      'a17-activity-row': A17ActivityRow
    },
    props: {
      ajaxBaseUrl: {
        type: String
      },
      emptyMessage: {
        type: String,
        default: 'You don\'t have any activity yet.'
      }
    },
    data: function () {
      return {
        navFilters: [
          {
            name: this.$trans('dashboard.all-activity', 'All activity'),
            slug: 'all'
          },
          {
            name: this.$trans('dashboard.my-activity', 'My activity'),
            slug: 'mine'
          }
        ],
        navActive: 0
      }
    },
    computed: {
      rows: {
        get () {
          return this.$store.state.datatable.data
        },
        set (value) {
          this.$store.commit(DATATABLE.UPDATE_DATATABLE_DATA, value)
        }
      },
      ...mapState({
        page: state => state.datatable.page,
        maxPage: state => state.datatable.maxPage,
        columns: state => state.datatable.columns
      })
    },
    methods: {
      getData(pageNumber) {
        this.$http.get(this.ajaxBaseUrl + '?' + this.navFilters[this.navActive].slug + '=' + pageNumber).then(({data}) => {
          this.rows = data;
        })
      },
      filterStatus: function (index, slug) {
        if (this.navActive === index) return

        this.navActive = index
        if (window[process.env.VUE_APP_NAME].STORE.datatable) {
          if (window[process.env.VUE_APP_NAME].STORE.datatable.hasOwnProperty(slug)) this.rows = window[process.env.VUE_APP_NAME].STORE.datatable[slug]
        }
      }
    }
  }
</script>

<style lang="scss" scoped>

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
