<template>
  <tr class="activityRow">
    <td v-for="col in columns" :key="col.name" class="activityCell" :class="cellClasses(col)">
      <template v-if="isSpecificColumn(col)">
        <span v-if="col.name === 'featured'" class="activityCell__feature" :class="{'activityCell__feature--active': row[col.name] }" @click.prevent="toggleFeatured" data-tooltip-title="Feature" v-tooltip><span v-svg symbol="star-feature_active"></span><span v-svg symbol="star-feature"></span></span> <!-- Featured star button -->
        <span v-if="col.name === 'published'" class="activityCell__pubstate" :class="{'activityCell__pubstate--live': row[col.name] }"  @click.prevent="togglePublish" data-tooltip-title="Publish" v-tooltip ></span> <!-- Published circle icon -->
        <a class="activityCell__thumb" :href="row['edit']" v-if="col.name === 'thumbnail'"><img :src="row[col.name]" /></a> <!-- Thumbnail -->
      </template>
      <template v-else>
        <template v-if="col.name === 'name'">
          <a :href="row['edit']" class="activityCell__link">{{ row[col.name] }}</a>
          <p class="activityCell__meta f--note">{{ row['published'] ? 'Unpublished' : 'Published' }} <timeago :auto-update="1" :since="new Date()"></timeago> by George • Projects</p>
        </template>
        <template v-else>{{ row[col.name] }}</template>
      </template>
    </td>
    <td class="activityCell">
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
  import { mapState, mapGetters } from 'vuex'

  export default {
    name: 'A17ActivityFeed',
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
          'activityCell--thumb': col.name === 'thumbnail'
        }
      },
      isSpecificColumn: function (col) {
        return col.name === 'bulk' ||
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
      }
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
  }

  .activityCell {
    vertical-align: top;
    padding:15px 15px;
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

  .activityCell__pubstate--live {
    background:$color__publish;
    // animation: pulse 0.3s normal forwards;
  }

  .activityCell--icon {
    width:1px;
  }
</style>
