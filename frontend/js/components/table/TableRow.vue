<template>
  <tr class="tablerow" :class="rowClasses">
    <td v-for="col in columns" :key="col.name" class="tablecell" :class="cellClasses(col)" :style="nestedStyle(col)">
      <template v-if="isSpecificColumn(col)">
        <!--Drag handle button-->
        <span v-if="col.name === 'draggable'" class="tablecell__handle"></span>
        <!-- Nested -->
        <a17-tableNested v-if="col.name === 'nested'" :depth="nestedDepth" :offset="nestedOffset"></a17-tableNested>
        <a v-if="col.name === 'bulk'" href="#" @click.prevent.stop="toggleBulk(row['id'])">
          <a17-checkbox name="bulkEdit" :value="row['id']" :initialValue="bulkIds"></a17-checkbox>
        </a><!-- Bulk -->
        <span v-if="col.name === 'featured' && row.hasOwnProperty('featured')" class="tablecell__feature" :class="{'tablecell__feature--active': row[col.name] }" @click.prevent="toggleFeatured" :data-tooltip-title="row['featured'] ? 'Unfeature' : 'Feature'" v-tooltip><span v-svg symbol="star-feature_active"></span><span v-svg symbol="star-feature"></span></span>
        <!-- Featured star button -->
        <span v-if="col.name === 'published' && row.hasOwnProperty('published')" class="tablecell__pubstate" :class="{'tablecell__pubstate--live': row[col.name] }" @click.prevent="togglePublish" :data-tooltip-title="row['published'] ? 'Unpublish' : 'Publish'" v-tooltip></span>
        <!-- Published circle icon -->
        <a class="tablecell__thumb" :href="editUrl" @click="editInPlace" v-if="col.name === 'thumbnail' && !row.hasOwnProperty('deleted')"><img :src="row[col.name]"/></a>
        <template v-else>
          <a class="tablecell__thumb" v-if="col.name === 'thumbnail'"><img :src="row[col.name]"/></a>
        </template> <!-- Thumbnail -->
        <a17-tableDates v-if="col.name === 'publish_start_date'" :startDate="row['publish_start_date']" :endDate="row['publish_end_date'] || ''"></a17-tableDates>
        <!-- Published Date -->
        <a17-tableLanguages v-if="col.name === 'languages'" :languages="row['languages']" :editUrl="editUrl"></a17-tableLanguages>
        <!-- Languages -->
      </template>

      <template v-else>
        <a :href="editUrl" class="tablecell__name" v-if="col.name === 'name' && !row.hasOwnProperty('deleted')" @click="editInPlace"><span
          class="f--link-underlined--o">{{ row[col.name] }}</span></a>
        <span v-else-if="col.html" v-html="row[col.name]"></span>
        <template v-else>{{ row[col.name] }}</template>
      </template>
    </td>
    <td class="tablecell tablecell--spacer">&nbsp;</td>
    <td class="tablecell tablecell--sticky">
      <a17-dropdown ref="rowSetupDropdown" position="bottom-right">
        <a17-button variant="icon" @click="$refs.rowSetupDropdown.toggle()"><span v-svg symbol="more-dots"></span>
        </a17-button>
        <div slot="dropdown__content">
          <a v-if="row.hasOwnProperty('permalink')" :href="row['permalink']" target="_blank">View Permalink</a>
          <a v-if="row.hasOwnProperty('edit') && !row.hasOwnProperty('deleted')" :href="editUrl" @click="editInPlace">Edit</a>
          <a v-if="row.hasOwnProperty('published') && !row.hasOwnProperty('deleted')" href="#"
             @click.prevent="togglePublish">{{ row['published'] ? 'Unpublish' : 'Publish' }}</a>
          <a v-if="row.hasOwnProperty('featured') && !row.hasOwnProperty('deleted')" href="#"
             @click.prevent="toggleFeatured">{{ row['featured'] ? 'Unfeature' : 'Feature' }}</a>
          <a v-if="row.hasOwnProperty('deleted')" href="#" @click.prevent="restoreRow">Restore</a>
          <a v-else-if="row.delete" href="#" @click.prevent="deleteRow">Delete</a>
        </div>
      </a17-dropdown>
    </td>
  </tr>
</template>

<script>
  import {mapState} from 'vuex'
  import a17TableLanguages from '@/components/tablecell/TableLanguages'
  import a17TableDates from '@/components/tablecell/TableDates'
  import a17TableNested from '@/components/tablecell/TableNested'

  export default {
    name: 'A17Tablerow',
    components: {
      'a17-tableLanguages': a17TableLanguages,
      'a17-tableDates': a17TableDates,
      'a17-tableNested': a17TableNested
    },
    props: {
      index: {
        type: Number,
        default: 0
      },
      row: {
        type: Object,
        default: function () {
          return {}
        }
      },
      columns: {
        type: Array,
        default: function () {
          return []
        }
      },
      draggable: {
        type: Boolean,
        default: false
      },
      nestedDepth: {
        type: Number,
        default: 0
      },
      rowType: {
        type: String,
        default: ''
      }
    },
    computed: {
      editInModal: function () {
        return this.row['editInModal'] ? this.row['editInModal'] : false
      },
      editUrl: function () {
        return this.row['edit'] ? this.row['edit'] : '#'
      },
      rowClasses () {
        return {
          'tablerow--nested': this.rowType === 'nested'
        }
      },
      ...mapState({
        bulkIds: state => state.datatable.bulk
      }),
      nestedOffset () {
        return this.columns.find((col) => col.name === 'draggable') ? 10 : 0
      }
    },
    methods: {
      editInPlace: function (event) {
        let self = this

        if (this.editInModal) {
          const endpoint = this.editInModal
          this.$store.commit('updateModalMode', 'update')
          this.$store.commit('updateModalAction', 'test' + this.row['id'])
          this.$store.commit('updateFormLoading', true)
          this.$store.dispatch('replaceFormData', endpoint)

          setTimeout(function () {
            if (self.$root.$refs.editionModal) self.$root.$refs.editionModal.open()
          }, 500)

          event.preventDefault()
        }
      },
      cellClasses: function (col) {
        return {
          'tablecell--icon': col.name === 'featured' || col.name === 'published',
          'tablecell--bulk': col.name === 'bulk',
          'tablecell--thumb': col.name === 'thumbnail',
          'tablecell--draggable': col.name === 'draggable',
          'tablecell--languages': col.name === 'languages',
          'tablecell--nested': col.name === 'nested',
          'tablecell--nested--parent': col.name === 'nested' && this.nestedDepth === 0,
          'tablecell--name': col.name === 'name'
        }
      },
      nestedStyle (col) {
        return this.columns.find((col) => col.name === 'nested') && col.name === 'draggable' ? {
          'webkit-transform': 'translateX(-' + this.nestedDepth * 80 + 'px)',
          'transform': 'translateX(-' + this.nestedDepth * 80 + 'px)'
        } : ''
      },
      isSpecificColumn: function (col) {
        return col.name === 'draggable' ||
          col.name === 'bulk' ||
          col.name === 'languages' ||
          col.name === 'featured' ||
          col.name === 'published' ||
          col.name === 'thumbnail' ||
          col.name === 'publish_start_date' ||
          col.name === 'nested'
      },
      toggleFeatured: function () {
        if (!this.row.hasOwnProperty('deleted')) {
          this.$store.dispatch('toggleFeaturedData', this.row)
        } else {
          this.$store.commit('setNotification', {
            message: 'You can’t feature/unfeature a deleted item, please restore it first.',
            variant: 'error'
          })
        }
      },
      togglePublish: function () {
        if (!this.row.hasOwnProperty('deleted')) {
          this.$store.dispatch('togglePublishedData', this.row)
        } else {
          this.$store.commit('setNotification', {
            message: 'You can’t publish/unpublish a deleted item, please restore it first.',
            variant: 'error'
          })
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
    position: relative;
    border-bottom: 1px solid $color__border--light;

    &:hover {
      td {
        background-color: $color__f--bg;
      }
    }
  }

  /* Default cell */

  .tablecell {
    overflow: hidden;
    vertical-align: top;
    padding: 20px 10px;
    background-color: $color__background;
  }

  /* Featuring content */
  .tablecell__feature {
    display: block;
    cursor: pointer;
    position: relative;
    top: 2px;

    .icon {
      color: $color__icons;
      display: block;
      top: -2px;
      position: relative;
    }

    .icon--star-feature_active {
      color: $color__error;
    }

    .icon--star-feature {
      display: block;
    }

    .icon--star-feature_active {
      display: none;
    }
  }

  .tablecell__feature--active {
    .icon svg {
      fill: $color__red;
    }

    .icon--star-feature {
      display: none;
    }

    .icon--star-feature_active {
      display: block;
    }
  }

  /* Publish/Unpublish content */
  .tablecell__pubstate {
    cursor: pointer;
    border-radius: 50%;
    height: 10px;
    width: 10px;
    display: block;
    background: $color__fborder;
    position: relative;
    top: 5px;
    transition: background-color 0.3s ease, border-color 0.3s ease;
  }

  .tablecell__pubstate--live {
    background: $color__publish;
  }

  /* Thumbnails */
  .tablecell--thumb {
    width: 1px;

    img {
      display: block;
      width: 80px;
      min-height: 80px;
      background: $color__border--light;
      height: auto;
    }
  }

  .tablecell__thumb {
    display: block;
  }

  /* Name */
  .tablecell__name {
    min-width: 15vw;
    max-width: 33.33vw;
    color: $color__link;
    text-decoration: none;
    display: block;
  }

  /* Icons */
  .tablecell--icon {
    width: 1px;
    padding-left: 10px;
    padding-right: 10px;
  }

  /* Bulk Edit checkboxes */
  .tablecell--bulk {
    width: 1px;
    padding-left: 10px;
    padding-right: 10px;

    a,
    .checkbox {
      display: block;
      width: 15px;
    }

    &:first-child {
      padding-left: 20px;
    }
  }

  /* Spacer */
  .tablecell--spacer {
    width: 1px;
    padding-left: 25px;
    padding-right: 25px;
  }

  /* Sticky */
  .tablecell--sticky {
    position: absolute;
    right: 0;
    top: auto;
    padding: 16px 20px 16px -2px;
    background: linear-gradient(to right, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 1) 25%);
    overflow: visible;
  }

  tr:hover > .tablecell--sticky {
    background: linear-gradient(to right, #{rgba($color__f--bg, 0)} 0%, #{rgba($color__f--bg, 1)} 25%);
  }

  /* Draggable */
  .tablecell.tablecell--draggable {
    width: 10px;
    padding: 0;
    position: relative;

    + td {
      padding-left: 20px - 10px;
    }
  }

  .tablecell__handle {
    display: none;
    position: absolute;
    height: 40px;
    width: 10px;
    left: 50%;
    top: 50%;
    margin-left: -5px;
    margin-top: -20px;
    @include dragGrid($color__drag, $color__f--bg);
  }

  tr:hover > .tablecell--draggable .tablecell__handle {
    display: block;
  }

  /* Nested table cell */
  .tablerow--nested {
    display: table;
    width: 100%;

    .tablecell.tablecell--draggable {
      position: absolute;
      top: 0;
      bottom: 0;
      transform: translateX(-80px);
    }

    .tablecell__handle {
      left: 0;
      margin-left: 0;
    }
  }

  .tablecell.tablecell--nested {
    position: absolute;
    height: calc(100% + 1px);
    padding: 20px 10px;
    border-bottom: 1px solid $color__border--light;
    overflow: auto;
    transform: translateX(-100%);

    &.tablecell--nested--parent {
      width: 0;
      padding: 0;
    }
  }
</style>
