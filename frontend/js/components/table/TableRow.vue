<template>
  <tr class="tablerow">
    <td v-for="col in columns" :key="col.name" class="tablecell" :class="cellClasses(col)">
      <template v-if="isSpecificColumn(col)">
        <span v-if="col.name === 'draggable'" class="tablecell__handle"></span> <!-- Drag handle button -->
        <a v-if="col.name === 'bulk'" href="#" @click.prevent.stop="toggleBulk(row['id'])"><a17-checkbox name="bulkEdit" :value="row['id']" :initialValue="bulkIds" ></a17-checkbox></a><!-- Bulk -->
        <span v-if="col.name === 'featured'" class="tablecell__feature" :class="{'tablecell__feature--active': row[col.name] }" @click.prevent="toggleFeatured" data-tooltip-title="Feature" v-tooltip><span v-svg symbol="star-feature_active"></span><span v-svg symbol="star-feature"></span></span> <!-- Featured star button -->
        <span v-if="col.name === 'published'" class="tablecell__pubstate" :class="{'tablecell__pubstate--live': row[col.name] }"  @click.prevent="togglePublish" data-tooltip-title="Publish" v-tooltip ></span> <!-- Published circle icon -->
        <a class="tablerow__thumb" :href="row['edit']" v-if="col.name === 'thumbnail'"><img :src="row[col.name]" /></a> <!-- Thumbnail -->
      </template>
      <template v-else>
        <a :href="row['edit']" v-if="col.name === 'name'">{{ row[col.name] }}</a>
        <template v-else>{{ row[col.name] }}</template>
      </template>
    </td>

    <td class="tablecell tablecell--spacer">&nbsp;</td>
    <td class="tablecell tablecell--sticky">
      <a17-dropdown ref="rowSetupDropdown" position="bottom-right">
        <a17-button variant="icon" @click="$refs.rowSetupDropdown.toggle()"><span v-svg symbol="more-dots"></span></a17-button>
        <div slot="dropdown__content">
          <a :href="row['permalink']" target="_blank">View Permalink</a>
          <a :href="row['edit']">Edit</a>
          <a href="#" v-if="row.hasOwnProperty('published')" @click.prevent="togglePublish">{{ row['published'] ? 'Unpublish' : 'Publish' }}</a>
          <a href="#" @click.prevent="deleteRow">Delete</a>
        </div>
      </a17-dropdown>
    </td>
  </tr>
</template>

<script>
  import { mapState } from 'vuex'

  export default {
    name: 'A17Tablerow',
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
        default: function () { return [] }
      }
    },
    computed: {
      ...mapState({
        bulkIds: state => state.datatable.bulk
      })
    },
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
               col.name === 'thumbnail'
      },
      toggleFeatured: function () {
        this.$store.dispatch('toggleFeaturedData', this.row.id)
      },
      togglePublish: function () {
        this.$store.dispatch('togglePublishedData', this.row.id)
      },
      deleteRow: function () {
        this.$store.dispatch('deleteData', this.row.id)
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

      &:hover {
        text-decoration: underline;
      }
    }
  }

  .tablecell__feature {
    display:block;
    cursor:pointer;

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

  // @keyframes pulse {
  //   0% {
  //     transform: scale(1);
  //   }
  //   33% {
  //     transform: scale(1.3);
  //   }
  //   100% {
  //     transform: scale(1);
  //   }
  // }

  .tablecell__pubstate {
    cursor:pointer;
    border-radius:50%;
    height:9px;
    width:9px;
    display:block;
    background:$color__fborder;
    position:relative;
    top:3px;
    transition: background-color 0.3s ease, border-color 0.3s ease;
  }

  .tablecell__pubstate--live {
    background:$color__publish;
    // animation: pulse 0.3s normal forwards;
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
    padding-bottom: 16px;
    overflow:visible;
  }

  tr:hover .tablecell--sticky {
    background: linear-gradient(to right, #{rgba($color__f--bg, 0)} 0%, #{rgba($color__f--bg, 1)} 15%);
  }

  .tablecell__handle {
    display:none;
    cursor: move;
    position:absolute;
    background:repeating-linear-gradient(180deg, $color__drag 0, $color__drag 2px, transparent 2px, transparent 4px);
    height:40px;
    width:10px;
    left:50%;
    top:50%;
    margin-left:-5px;
    margin-top:-20px;

    &:before {
      display:block;
      content:'';
      background:repeating-linear-gradient(90deg, $color__f--bg 0, $color__f--bg 2px, transparent 2px, transparent 4px);
      width:100%;
      height:40px;
    }
  }

  tr:hover .tablecell__handle {
    display:block;
  }
</style>
