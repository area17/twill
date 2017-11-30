<template>
  <tr class="tablerow">
    <td v-for="col in columns" :key="col.name" class="tablecell" :class="cellClasses(col)">
      <template v-if="isSpecificColumn(col)">
        <span v-if="col.name === 'draggable'" class="tablecell__handle"></span> <!-- Drag handle button -->
        <a v-if="col.name === 'bulk'" href="#" @click.prevent.stop="toggleBulk(row['id'])"><a17-checkbox name="bulkEdit" :value="row['id']" :initialValue="bulkIds" ></a17-checkbox></a><!-- Bulk -->
        <span v-if="col.name === 'featured'" class="tablecell__feature" :class="{'tablecell__feature--active': row[col.name] }" @click.prevent="toggleFeatured" :data-tooltip-title="row['featured'] ? 'Unfeature' : 'Feature'" v-tooltip><span v-svg symbol="star-feature_active"></span><span v-svg symbol="star-feature"></span></span> <!-- Featured star button -->
        <span v-if="col.name === 'published'" class="tablecell__pubstate" :class="{'tablecell__pubstate--live': row[col.name] }"  @click.prevent="togglePublish" :data-tooltip-title="row['published'] ? 'Unpublish' : 'Publish'" v-tooltip ></span> <!-- Published circle icon -->
        <a class="tablerow__thumb" :href="row['edit']" v-if="col.name === 'thumbnail'"><img :src="row[col.name]" /></a> <!-- Thumbnail -->
        <template v-if="col.name === 'publish_start_date'">
          <span v-if="formatDateLabel" class="tablecell__datePub" :class="{ 's--expired' : formatDateLabel === textExpired }">
            {{ row['publish_start_date'] | formatDatatableDate }}<br /><span>{{ formatDateLabel }}</span>
          </span>
          <span v-else>
            {{ row['publish_start_date'] | formatDatatableDate }}
          </span>
        </template> <!-- Published Date -->
      </template>
      <template v-else>
        <a :href="row['edit']" v-if="col.name === 'name'"><span class="f--link-underlined--o">{{ row[col.name] }}</span></a>
        <template v-else>{{ row[col.name] }}</template>
      </template>
    </td>

    <td class="tablecell tablecell--spacer">&nbsp;</td>
    <td class="tablecell tablecell--sticky">
      <a17-dropdown ref="rowSetupDropdown" position="bottom-right">
        <a17-button variant="icon" @click="$refs.rowSetupDropdown.toggle()"><span v-svg symbol="more-dots"></span></a17-button>
        <div slot="dropdown__content">
          <a v-if="row.hasOwnProperty('permalink')" :href="row['permalink']" target="_blank">View Permalink</a>
          <a v-if="row.hasOwnProperty('edit') && !row.hasOwnProperty('deleted')" :href="row['edit']">Edit</a>
          <a v-if="row.hasOwnProperty('published') && !row.hasOwnProperty('deleted')" href="#" @click.prevent="togglePublish">{{ row['published'] ? 'Unpublish' : 'Publish' }}</a>
          <a v-if="row.hasOwnProperty('featured') && !row.hasOwnProperty('deleted')" href="#" @click.prevent="toggleFeatured">{{ row['featured'] ? 'Unfeature' : 'Feature' }}</a>
          <a v-if="row.hasOwnProperty('deleted')" href="#" @click.prevent="restoreRow">Restore</a>
          <a v-else href="#" @click.prevent="deleteRow">Delete</a>
        </div>
      </a17-dropdown>
    </td>
  </tr>
</template>

<script>
  import { mapState } from 'vuex'
  import a17VueFilters from '@/utils/filters.js'
  import compareAsc from 'date-fns/compare_asc'

  export default {
    name: 'A17Tablerow',
    props: {
      index: {
        type: Number,
        default: 0
      },
      textExpired: {
        type: String,
        default: 'Expired'
      },
      textScheduled: {
        type: String,
        default: 'Scheduled'
      },
      row: {
        type: Object,
        default: function () {
          return {}
        }
      },
      columns: {
        type: Array,
        default: function () { return [] }
      }
    },
    computed: {
      formatDateLabel: function () {
        let label = ''
        let scoreStart = compareAsc(this.row['publish_start_date'], new Date())
        let scoreEnd = compareAsc(this.row['publish_end_date'], new Date())

        if (this.row['publish_start_date'] && scoreEnd < 0) label = this.textExpired
        else if (this.row['publish_end_date'] && scoreStart > 0) label = this.textScheduled

        return label
      },
      ...mapState({
        bulkIds: state => state.datatable.bulk
      })
    },
    filters: a17VueFilters,
    methods: {
      cellClasses: function (col) {
        return {
          'tablecell--icon': col.name === 'featured' || col.name === 'published',
          'tablecell--bulk': col.name === 'bulk',
          'tablecell--thumb': col.name === 'thumbnail',
          'tablecell--draggable': col.name === 'draggable'
        }
      },
      isSpecificColumn: function (col) {
        return col.name === 'draggable' ||
               col.name === 'bulk' ||
               col.name === 'featured' ||
               col.name === 'published' ||
               col.name === 'thumbnail' ||
               col.name === 'publish_start_date'
      },
      toggleFeatured: function () {
        if (!this.row.hasOwnProperty('deleted')) {
          this.$store.dispatch('toggleFeaturedData', this.row)
        } else {
          this.$store.commit('setNotification', { message: 'You can’t feature/unfeature a deleted item, please restore it first.', variant: 'error' })
        }
      },
      togglePublish: function () {
        if (!this.row.hasOwnProperty('deleted')) {
          this.$store.dispatch('togglePublishedData', this.row)
        } else {
          this.$store.commit('setNotification', { message: 'You can’t publish/unpublish a deleted item, please restore it first.', variant: 'error' })
        }
      },
      restoreRow: function () {
        this.$store.dispatch('restoreData', this.row)
      },
      deleteRow: function () {
        this.$store.dispatch('deleteData', this.row)
      },
      toggleBulk: function (id) {
        // We cant use the vmodel of the a17-checkbox directly because the checkboxes are in separated components (so the model is not shared)
        this.$store.commit('updateDatableBulk', id)
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .tablerow {
    position:relative;
    border-bottom:1px solid $color__border--light;

    &:hover {
      td {
        background-color: $color__f--bg;
      }
    }
  }

  .tablecell {
    overflow: hidden;
    vertical-align: top;
    padding:20px 15px;
    background-color: $color__background;

    > a {
      color:$color__link;
      text-decoration:none;
      display:block;

      // &:hover {
      //   text-decoration: underline;
      // }
    }
  }

  .tablecell__feature {
    display:block;
    cursor:pointer;
    position:relative;
    top:2px;

    .icon {
      color:$color__icons;
      display:block;
      top: -2px;
      position: relative;
    }

    .icon--star-feature_active {
      color:$color__error;
    }

    .icon--star-feature {
      display:block;
    }

    .icon--star-feature_active {
      display:none;
    }
  }

  .tablecell__feature--active {
    .icon svg {
      fill: $color__red;
    }

    .icon--star-feature {
      display:none;
    }

    .icon--star-feature_active {
      display:block;
    }
  }

  .tablecell__pubstate {
    cursor:pointer;
    border-radius:50%;
    height:10px;
    width:10px;
    display:block;
    background:$color__fborder;
    position:relative;
    top:5px;
    transition: background-color 0.3s ease, border-color 0.3s ease;
  }

  .tablecell__pubstate--live {
    background:$color__publish;
  }

  .tablecell__datePub {
    color:$color__text--forms;

    span {
      color:$color__ok;
    }

    &.s--expired {
      span {
        color:$color__error;
      }
    }
  }

  .tablerow__thumb {
    display:block;
  }

  .tablecell--thumb {
    width:1px;

    img {
      display:block;
      width:80px;
      min-height:80px;
      background:$color__border--light;
      height:auto;
    }
  }

  .tablecell--icon {
    width:1px;
    padding-left:10px;
    padding-right:10px;
  }

  .tablecell--bulk {
    width:1px;
    padding-left:10px;
    padding-right:10px;

    a,
    .checkbox {
      display:block;
      width:15px;
    }

    &:first-child {
      padding-left:20px;
    }
  }

  .tablecell--spacer {
    width:1px;
    padding-left:25px;
    padding-right:25px;
  }

  .tablecell.tablecell--draggable {
    width:10px;
    padding:0;
    position:relative;

    + td {
      padding-left:20px - 10px;
    }
  }

  .tablecell--sticky {
    position:absolute;
    right:0;
    top: auto;
    background: linear-gradient(to right, rgba(255,255,255,0) 0%,rgba(255,255,255,1) 15%);
    padding-top: 16px;
    padding-bottom: 16px - 2px;
    overflow:visible;
  }

  tr:hover .tablecell--sticky {
    background: linear-gradient(to right, #{rgba($color__f--bg, 0)} 0%, #{rgba($color__f--bg, 1)} 15%);
  }

  .tablecell__handle {
    display:none;
    position:absolute;
    height:40px;
    width:10px;
    left:50%;
    top:50%;
    margin-left:-5px;
    margin-top:-20px;
    @include dragGrid($color__drag, $color__f--bg);
  }

  tr:hover .tablecell__handle {
    display:block;
  }
</style>
