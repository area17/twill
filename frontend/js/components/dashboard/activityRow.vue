<template>
  <tr class="activityRow">
    <td v-for="col in columns" :key="col.name" class="activityCell" :class="cellClasses(col)">
      <template v-if="isSpecificColumn(col)">
        <span v-if="col.name === 'published'" class="activityCell__pubstate" :class="{'activityCell__pubstate--live': row[col.name] }" :data-tooltip-title="row[col.name] ? 'Published' : 'Draft'" v-tooltip ></span> <!-- Published circle icon @click.prevent="togglePublish"  -->
        <a v-if="col.name === 'thumbnail'" class="activityCell__thumb" :href="row['edit']" ><img :src="row[col.name]" /></a> <!-- Thumbnail -->
      </template>
      <template v-else>
        <template v-if="col.name === 'name'">
          <a :href="row['edit']" class="activityCell__link">{{ row[col.name] }}</a>
          <p class="activityCell__meta f--note">
            {{ row['activity'] }} <timeago :auto-update="1" :since="new Date(row['date'])"></timeago> by {{ row['author'] }}
            <span class="activityCell__type">{{ row['type'] }}</span>
          </p>
        </template>
      </template>
    </td>
    <td class="activityCell activityCell--icon">
      <a17-dropdown ref="activityRowSetupDropdown" position="bottom-right">
        <a17-button variant="icon" @click="$refs.activityRowSetupDropdown.toggle()"><span v-svg symbol="more-dots"></span></a17-button>
        <div slot="dropdown__content">
          <a v-if="row.hasOwnProperty('permalink')" :href="row['permalink']" target="_blank">View Permalink</a>
          <a v-if="row.hasOwnProperty('edit')" :href="row['edit']">Edit</a>
          <!-- <a v-if="row.hasOwnProperty('published')" href="#" @click.prevent="togglePublish">{{ row['published'] ? 'Unpublish' : 'Publish' }}</a> -->
          <!-- <a href="#" @click.prevent="deleteRow">Delete</a> -->
        </div>
      </a17-dropdown>
    </td>
  </tr>
</template>

<script>
  // import ACTIONS from '@/store/actions'

  export default {
    name: 'A17ActivityRow',
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
    },
    methods: {
      cellClasses: function (col) {
        return {
          'activityCell--icon': col.name === 'featured' || col.name === 'published',
          'activityCell--pub hide--xsmall': col.name === 'published',
          'activityCell--thumb hide--xsmall': col.name === 'thumbnail'
        }
      },
      isSpecificColumn: function (col) {
        return col.name === 'featured' ||
          col.name === 'published' ||
          col.name === 'thumbnail'
      }
      // toggleFeatured: function () {
      //   this.$store.dispatch(ACTIONS.TOGGLE_FEATURE, this.row.id)
      // },
      // togglePublish: function () {
      //   this.$store.dispatch(ACTIONS.TOGGLE_PUBLISH, this.row)
      // },
      // deleteRow: function () {
      //   this.$store.dispatch(ACTIONS.DELETE_ROW, this.row.id)
      // }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .activityRow {
    border-bottom:1px solid $color__border--light;

    &:hover {
      td {
        background-color: $color__f--bg;
      }
    }

    &:last-child {
      border-bottom:0 none;
    }
  }

  .activityCell {
    vertical-align: top;
    padding:20px 15px;
    background-color: $color__background;
  }

  .activityCell__link {
    color:$color__link;
    text-decoration:none;

    &:hover {
      @include bordered($color__link, false);
    }
  }

  .activityCell__meta {
    margin-top: 5px;
  }

  .activityCell__type {
    &::before {
      content:"•";
      color:$color__text--light;
      display:inline;
      padding:0 8px 0 5px;
      font-size:11px;
      position:relative;
      top:-2px;
    }
  }

  .activityCell--thumb {
    width:1px;

    img {
      display:block;
      width:50px;
      min-height:50px;
      background:$color__border--light;
      height:auto;
    }
  }

  .activityCell__feature {
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

  .activityCell__feature--active {
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

  .activityCell__pubstate {
    // cursor:pointer;
    border-radius:50%;
    height:9px;
    width:9px;
    display:block;
    background:$color__fborder;
    position:relative;
    top:3px;
    // transition: background-color 0.3s ease, border-color 0.3s ease;
  }

  .activityCell__pubstate--live {
    background:$color__publish;
    // animation: pulse 0.3s normal forwards;
  }

  .activityCell--icon {
    width:1px;
  }

  .activityCell--pub {
    padding-left:0;
    padding-right:0;
  }
</style>
