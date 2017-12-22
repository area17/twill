<template>
  <div class="buckets">
    <div class="buckets__page-title">
      <div class="container">
        <h2><slot></slot></h2>
      </div>
    </div>
    <div class="container">
      <div class=" wrapper">
        <div class="buckets__container col--even">
          <a17-fieldset class="buckets__fieldset" :title="title" :activeToggle="false">
            <div class="buckets__header">
              <div class="buckets__sources">
                <a17-vselect v-if="!singleSource" class="sources__select" name="sources" :selected="currentSource" :options="dataSources" :required="true" @change="changeDataSource"></a17-vselect>
                <h3 v-else>{{ currentSource.label }}</h3>
              </div>
              <div class="buckets__filter">
                <a17-filter @submit="filterBucketsData"></a17-filter>
              </div>
            </div>
            <table v-if="source.items.length > 0" class="buckets__list">
              <tbody>
                <a17-bucket-item-source v-for="item in source.items" :key="item.id" :item="item" :singleBucket="singleBucket" :buckets="buckets" v-on:add-to-bucket="addToBucket"></a17-bucket-item-source>
              </tbody>
            </table>
            <div v-else="" class="buckets__empty">
              <h4>{{ emptySource }}</h4>
            </div>
            <a17-paginate :max="max" :value="page" :offset="offset" :availableOffsets="availableOffsets" @changePage="updatePage" @changeOffset="updateOffset"></a17-paginate>
          </a17-fieldset>
        </div>
        <div class="buckets__container col--even">
          <a17-fieldset v-for="(bucket, index) in buckets" :class="'buckets__fieldset buckets__fieldset--'+(index+1)" :key="bucket.id" :name="'bucket_'+bucket.id" :activeToggle="false">
            <h3 slot="header" class="buckets__fieldset__header">
              <span><span v-if="buckets.length > 1" class="buckets__number">{{ (index + 1) }}</span> {{ bucket.name }}</span> <span class="buckets__size-infos">{{ bucket.children.length }} / {{ bucket.max }}</span>
            </h3>
            <draggable v-if="bucket.children.length > 0" class="buckets__list buckets__draggable" :options="dragOptions" @change="sortBucket($event, index)" :value="bucket.children" :element="'table'" >
              <transition-group name="fade_scale_list" tag='tbody'>
                <a17-bucket-item v-for="(child, index) in bucket.children" :key="index" :item="child" :restricted="restricted" :draggable="bucket.children.length > 1" :singleBucket="singleBucket" :singleSource="singleSource" :bucket="bucket.id" :buckets="buckets" v-on:add-to-bucket="addToBucket" v-on:remove-from-bucket="deleteFromBucket"></a17-bucket-item>
              </transition-group>
            </draggable>
            <div v-else="" class="buckets__empty">
              <h4>{{ emptyBuckets }}</h4>
            </div>
          </a17-fieldset>
        </div>
      </div>
    </div>
    <a17-modal class="modal--tiny modal--form modal--withintro" ref="overrideBucket" title="Override Bucket">
      <p class="modal--tiny-title"><strong>Are you sure ?</strong></p>
      <p v-html="overrideBucketText"></p>
      <a17-inputframe>
        <a17-button variant="validate" @click="override">Override</a17-button>
        <a17-button variant="aslink" @click="$refs.overrideBucket.close()"><span>Cancel</span></a17-button>
      </a17-inputframe>
    </a17-modal>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'

  import BucketItem from './BucketItem.vue'
  import BucketSourceItem from './BucketSourceItem.vue'
  import draggableMixin from '@/mixins/draggable'
  import draggable from 'vuedraggable'
  import Paginate from '@/components/table/Paginate'
  import Fieldset from '@/components/Fieldset.vue'
  import Filter from '@/components/Filter'
  import VSelect from '@/components/VSelect.vue'
  import A17VSelect from '../VSelect.vue'

  export default {
    name: 'A17Buckets',
    mixins: [draggableMixin],
    props: {
      title: {
        type: String,
        default: 'Features'
      },
      emptyBuckets: {
        type: String,
        default: 'No files selected.'
      },
      emptySource: {
        type: String,
        default: 'No items found.'
      },
      // If disabled, this option will block don't delete first element of an bucket and will send and alert message
      overridableMax: {
        type: Boolean,
        default: false
      },
      // Items are restricted to one bucket. If not checkboxes will replace radios buttons in BucketItem component.
      restricted: {
        type: Boolean,
        default: true
      }
    },
    components: {
      A17VSelect,
      'a17-bucket-item': BucketItem,
      'a17-bucket-item-source': BucketSourceItem,
      'a17-fieldset': Fieldset,
      'a17-paginate': Paginate,
      'a17-filter': Filter,
      'a17-vselect': VSelect,
      draggable
    },
    data: function () {
      return {
        currentBucketID: '',
        currentItem: '',
        overrideItem: false
      }
    },
    computed: {
      ...mapState({
        buckets: state => state.buckets.buckets,
        source: state => state.buckets.source,
        dataSources: state => state.buckets.dataSources.content_types,
        page: state => state.buckets.page,
        availableOffsets: state => state.buckets.availableOffsets,
        offset: state => state.buckets.offset,
        max: state => state.buckets.maxPage
      }),
      ...mapGetters([
        'currentSource'
      ]),
      singleBucket: function () {
        return this.buckets.length === 1
      },
      singleSource: function () {
        return this.dataSources.length === 1
      },
      overrideBucketText: function () {
        let bucket = this.buckets.find(b => b.id === this.currentBucketID)
        let bucketName = ''
        let bucketSize = ''
        if (bucket) {
          bucketName = bucket.name
          bucketSize = bucket.max
        }
        return 'Bucket <em>"' + bucketName + '"</em> has a strict limit of ' + bucketSize + ' items. Do you want to override the first item of this bucket ?'
      }
    },
    methods: {
      addToBucket: function (item, bucket) {
        let self = this
        let index = self.buckets.findIndex(b => b.id === bucket)

        if (!item && index === -1) return

        self.currentBucketID = bucket
        self.currentItem = item

        const data = {
          index: index,
          item: item
        }

        // Remove item from each bucket if option restricted to one bucket is active
        if (self.restricted) {
          self.buckets.forEach(function (bucket) {
            bucket.children.forEach(function (child) {
              if (child.id === item.id && child.content_type.value === item.content_type.value) self.deleteFromBucket(item, bucket.id)
            })
          })
        }

        // Use -1 for an unlimited bucket
        let count = self.buckets[index].children.length

        if (count > -1 && count < self.buckets[index].max) {
          // Commit before dispatch to prevent ui visual effect timeout
          self.$store.dispatch('addToBucket', data)
        } else if (self.overridableMax || self.overrideItem) {
          const opts = {
            add: data,
            del: {index: index, itemIndex: 0}
          }
          self.$store.dispatch('overrideBucket', opts)
          self.overrideItem = false
        } else {
          self.$refs.overrideBucket.open()
        }
      },
      deleteFromBucket: function (item, bucket) {
        let bucketIndex = this.buckets.findIndex(b => b.id === bucket)
        if (bucketIndex === -1) return

        let itemIndex = this.buckets[bucketIndex].children.findIndex(c => c.id === item.id && c.content_type.value === item.content_type.value)

        if (itemIndex === -1) return

        const data = {
          index: bucketIndex,
          itemIndex: itemIndex
        }

        this.$store.dispatch('deleteFromBucket', data)
      },
      sortBucket: function (evt, index) {
        const data = {
          bucketIndex: index,
          oldIndex: evt.moved.oldIndex,
          newIndex: evt.moved.newIndex
        }
        this.$store.dispatch('reorderBucket', data)
      },
      changeDataSource: function (value) {
        this.$store.commit('updateBucketsDataSource', value)
        this.$store.commit('updateBucketsDataPage', 1)
        this.$store.dispatch('getBucketsData')
      },
      filterBucketsData: function (formData) {
        this.$store.commit('updateBucketsDataPage', 1)
        this.$store.commit('updateBucketsFilter', formData || {search: ''})
        // reload datas
        this.$store.dispatch('getBucketsData')
      },
      updateOffset: function (value) {
        this.$store.commit('updateBucketsDataPage', 1)
        this.$store.commit('updateBucketsDataOffset', value)

        // reload datas
        this.$store.dispatch('getBucketsData')
      },
      updatePage: function (value) {
        this.$store.commit('updateBucketsDataPage', value)
        // reload datas
        this.$store.dispatch('getBucketsData')
      },
      override: function () {
        this.overrideItem = true
        this.addToBucket(this.currentItem, this.currentBucketID)
        this.$refs.overrideBucket.close()
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .buckets {
    padding-bottom: 80px;
  }

  .buckets__page-title {
    margin-bottom: 20px;
    background-color: $color__border--light;
    border-bottom: 1px solid $color__border;
    overflow: hidden;

    h2 {
      // min-height: 90px;
      padding: 30px 0;
    }
  }

  .buckets__fieldset {
    /* override fieldset style */

    /deep/ .fieldset__content {
      padding: 0;
    }

    @each $current-color in $colors__bucket--list {
      $i: index($colors__bucket--list, $current-color);
      &.buckets__fieldset--#{$i} /deep/ header {
        color: $color__white;
        background-color: $current-color;
      }
    }
  }

  .buckets__fieldset__header {
    display: flex;
    align-items: center;
    justify-content: space-between;

    @include font-smoothing();

    .buckets__number {
      margin-right: 10px;
    }

    .buckets__size-infos {
      @include font-tiny();
      text-align: right;
      float: right;
    }
  }

  .buckets__header {

    display: flex;
    align-items: center;

    padding: 0 15px;
    height: 80px;

    background-color: $color__ultralight;
    border-bottom:1px solid $color__border--light;

    .buckets__sources {
      flex-grow: 2;

      .sources__select {
        margin-top: 0;
      }
    }

    .buckets__filter {
      margin-left: 15px;

      /*Override default search style  */

      /deep/ .filter__search {
        width: 100%;

        input {
          width: 100%;
          min-width: inherit;
        }
      }
    }
  }

  .buckets__list {
    width: 100%;
    display: flex;

    tbody {
      width: 100%;
    }
  }

  .buckets__empty {
    display: flex;
    align-items: center;
    height: 80px;
    padding: 15px 20px;

    h4 {
      color: $color__f--text;
    }
  }
</style>

<style lang="scss">
  @import '~styles/setup/_mixins-colors-vars.scss';

  .buckets__item {
    position: relative;
    display: flex;
    justify-content: space-between;
    align-items: center;
    height: 80px;
    padding: 0 15px;
    border-top: 1px solid $color__border--light;

    td {
      padding-top: 15px;
      padding-bottom: 15px;
      // height: 80px;
    }

    &:hover {
      background-color: $color__f--bg;
    }

    &:first-child {
      border-top: 0 none;
    }

    .buckets__itemThumbnail {

      @include breakpoint(xsmall) {
        display: none;
      }

      img {
        display: block;
        width: 50px;
        min-width: 50px;
        min-height: 50px;
        height:auto;
        background:$color__border--light;
      }
    }

    .buckets__itemTitle {
      flex-grow: 1;
      margin: 0 30px 0 15px;
      overflow: hidden;

      h4 {
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
        color: $color__link;
      }

      @include breakpoint(xsmall) {
        margin-left: 0;
      }

      @media screen and (min-width: 1440px) {
        margin-right: 80px;
      }

      .f--link-underlined--o a {
        color: $color__link;
        text-decoration: none;
      }
    }

    .buckets__itemDate {
      @include breakpoint(medium) {
        display: none;
      }
    }

    .buckets__itemDate,
    .buckets__itemContentType {
      margin-right: 25px;
      color: $color__text--light;

      @include breakpoint(xsmall) {
        display: none;
      }

      @include breakpoint(medium) {
        margin-right: 15px;
      }

      @include breakpoint(large) {
        margin-right: 40px;
      }

      @media screen and (min-width: 1440px) {
        margin-right: 80px;
      }
    }

    .buckets__itemOptions {
      display: flex;

      .item__dropdown {

        .item__dropdown__content {
          min-width: 250px;

          /deep/ .radioGroup__item {
            &:hover {
              background-color: $color__border--light;
            }
          }
        }
      }

      .bucket__action {
        @include font-tiny();

        line-height: 25px;
        margin-right: 15px;

        &:last-child {
          margin-right: 0;
        }

        &.selected {
          opacity: 0.4;
        }

      }

    }

    &.single.selected > * {
      opacity: 0.4;
    }

    &.draggable {
      padding-left: 27px;
    }
  }
</style>
