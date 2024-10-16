<template>
  <div class="buckets">
    <div class="buckets__page-title">
      <div class="container buckets__page-title-content">
        <h2>
          <slot/>
        </h2>
        <div class="buckets__page-title-actions">
          <a17-button variant="validate" @click="save">{{ $trans('buckets.publish') }}</a17-button>
          <a17-button v-for="link in extraActions" :key="link.url" el="a" :href="link.url" :download="link.download || ''" :target="link.target || ''" :rel="link.rel || ''" variant="secondary">{{ link.label }}</a17-button>
        </div>
      </div>
    </div>
    <div class="container">
      <div class=" wrapper">
        <div class="buckets__container col--even">
          <a17-fieldset class="buckets__fieldset" :title="title" :activeToggle="false">
            <div class="buckets__header">
              <div class="buckets__sources">
                <a17-vselect v-if="!singleSource" class="sources__select" name="sources" :selected="currentSource"
                             :options="dataSources" :required="true" @change="changeDataSource"/>
              </div>
              <div class="buckets__filter">
                <a17-filter @submit="filterBucketsData"/>
              </div>
            </div>
            <table v-if="source.items.length > 0" class="buckets__list">
              <tbody>
              <a17-bucket-item-source v-for="item in source.items" :key="item.id" :item="item"
                                      :singleBucket="singleBucket" :buckets="buckets" v-on:add-to-bucket="addToBucket"/>
              </tbody>
            </table>
            <div v-else class="buckets__empty">
              <h4>{{ emptySource }}</h4>
            </div>
            <a17-paginate :max="max" :value="page" :offset="offset" :availableOffsets="availableOffsets"
                          @changePage="updatePage" @changeOffset="updateOffset"/>
          </a17-fieldset>
        </div>
        <div class="buckets__container col--even">
          <a17-fieldset v-for="(bucket, index) in buckets" :class="'buckets__fieldset buckets__fieldset--'+(index+1)"
                        :key="bucket.id" :name="'bucket_'+bucket.id" :activeToggle="false">
            <h3 slot="header" class="buckets__fieldset__header">
              <span><span v-if="buckets.length > 1"
                          class="buckets__number">{{ (index + 1) }}</span> {{ bucket.name }}</span> <span
              class="buckets__size-infos">{{ bucket.children.length }} / {{ bucket.max }}</span>
            </h3>
            <draggable v-if="bucket.children.length > 0" class="buckets__list buckets__draggable" v-bind="dragOptions"
                       @change="sortBucket($event, index)" :value="bucket.children" :tag="'table'">
              <transition-group name="fade_scale_list" tag='tbody'>
                <a17-bucket-item v-for="(child, index) in bucket.children" :key="`${child.id}_${index}`" :item="child"
                                 :restricted="restricted" :draggable="bucket.children.length > 1"
                                 :sourceLabels="sourceLabels"
                                 :singleBucket="singleBucket" :singleSource="singleSource" :bucket="bucket.id"
                                 :buckets="buckets" v-on:add-to-bucket="addToBucket"
                                 v-on:remove-from-bucket="deleteFromBucket"
                                 v-on:toggle-featured-in-bucket="toggleFeaturedInBucket"
                                 :withToggleFeatured="bucket.withToggleFeatured"
                                 :toggleFeaturedLabels="bucket.toggleFeaturedLabels"/>
              </transition-group>
            </draggable>
            <div v-else class="buckets__empty">
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
  import draggable from 'vuedraggable'
  import { mapGetters,mapState } from 'vuex'

  import Fieldset from '@/components/Fieldset.vue'
  import Filter from '@/components/Filter'
  import Paginate from '@/components/table/Paginate'
  import VSelect from '@/components/VSelect.vue'
  import draggableMixin from '@/mixins/draggable'
  import ACTIONS from '@/store/actions'
  import { BUCKETS } from '@/store/mutations'

  import BucketItem from './BucketItem.vue'
  import BucketSourceItem from './BucketSourceItem.vue'

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
        default: 'No items selected.'
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
      },
      // Optionnal additionnal actions showing up after the Publish button
      extraActions: {
        type: Array,
        default() { return [] }
      }
    },
    components: {
      'a17-bucket-item': BucketItem,
      'a17-bucket-item-source': BucketSourceItem,
      'a17-fieldset': Fieldset,
      'a17-paginate': Paginate,
      'a17-filter': Filter,
      'a17-vselect': VSelect,
      draggable
    },
    data() {
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
      sourceLabels() {
        const labels = {};
        this.dataSources.forEach((source) => {
          labels[source.type] = source.label;
        })
        return labels;
      },
      singleBucket() {
        return this.buckets.length === 1
      },
      singleSource() {
        return this.dataSources.length === 1
      },
      overrideBucketText() {
        const bucket = this.buckets.find(b => b.id === this.currentBucketID)
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
      addToBucket(item, bucket) {
        const index = this.buckets.findIndex(b => b.id === bucket)

        if (!item && index === -1) return

        this.currentBucketID = bucket
        this.currentItem = item

        const data = {
          index,
          item
        }

        // Use -1 for an unlimited bucket
        const count = this.buckets[index].children.length

        if (count > -1 && count < this.buckets[index].max) {
          // Commit before dispatch to prevent ui visual effect timeout
          this.checkRestricted(item)
          this.$store.commit(BUCKETS.ADD_TO_BUCKET, data)
        } else if (this.overridableMax || this.overrideItem) {
          this.checkRestricted(item)
          this.$store.commit(BUCKETS.ADD_TO_BUCKET, data)
          this.$store.commit(BUCKETS.DELETE_FROM_BUCKET, { index, itemIndex: 0 })
          this.overrideItem = false
        } else {
          this.$refs.overrideBucket.open()
        }
      },
      deleteFromBucket(item, bucket) {
        const bucketIndex = this.buckets.findIndex(b => b.id === bucket)
        if (bucketIndex === -1) return

        const itemIndex = this.buckets[bucketIndex].children.findIndex(c => c.id === item.id && c.type === item.type)

        if (itemIndex === -1) return

        const data = {
          index: bucketIndex,
          itemIndex
        }
        this.$store.commit(BUCKETS.DELETE_FROM_BUCKET, data)
      },
      toggleFeaturedInBucket(item, bucket) {
        const bucketIndex = this.buckets.findIndex(b => b.id === bucket)
        if (bucketIndex === -1) return

        const itemIndex = this.buckets[bucketIndex].children.findIndex(c => c.id === item.id && c.type === item.type)

        if (itemIndex === -1) return

        const data = {
          index: bucketIndex,
          itemIndex
        }

        this.$store.commit(BUCKETS.TOGGLE_FEATURED_IN_BUCKET, data)
      },
      checkRestricted(item) {
        // Remove item from each bucket if option restricted to one bucket is active
        if (this.restricted) {
          this.buckets.forEach((bucket) => {
            bucket.children.forEach((child) => {
              if (child.id === item.id && child.type === item.type) {
                this.deleteFromBucket(item, bucket.id)
              }
            })
          })
        }
      },
      sortBucket(evt, index) {
        const data = {
          bucketIndex: index,
          oldIndex: evt.moved.oldIndex,
          newIndex: evt.moved.newIndex
        }
        this.$store.commit(BUCKETS.REORDER_BUCKET_LIST, data)
      },
      changeDataSource(value) {
        this.$store.commit(BUCKETS.UPDATE_BUCKETS_DATASOURCE, value)
        this.$store.commit(BUCKETS.UPDATE_BUCKETS_DATA_PAGE, 1)
        this.$store.dispatch(ACTIONS.GET_BUCKETS)
      },
      filterBucketsData(formData) {
        this.$store.commit(BUCKETS.UPDATE_BUCKETS_DATA_PAGE, 1)
        this.$store.commit(BUCKETS.UPDATE_BUCKETS_FILTER, formData || { search: '' })
        // reload datas
        this.$store.dispatch(ACTIONS.GET_BUCKETS)
      },
      updateOffset(value) {
        this.$store.commit(BUCKETS.UPDATE_BUCKETS_DATA_PAGE, 1)
        this.$store.commit(BUCKETS.UPDATE_BUCKETS_DATA_OFFSET, value)

        // reload datas
        this.$store.dispatch(ACTIONS.GET_BUCKETS)
      },
      updatePage(value) {
        this.$store.commit(BUCKETS.UPDATE_BUCKETS_DATA_PAGE, value)
        // reload datas
        this.$store.dispatch(ACTIONS.GET_BUCKETS)
      },
      override() {
        this.overrideItem = true
        this.addToBucket(this.currentItem, this.currentBucketID)
        this.$refs.overrideBucket.close()
      },
      save() {
        this.$store.dispatch(ACTIONS.SAVE_BUCKETS)
      }
    }
  }
</script>

<style lang="scss" scoped>

  .buckets {
    padding-bottom: 80px;
  }

  .buckets__page-title {
    margin-bottom: 20px;
    background-color: $color__border--light;
    border-bottom: 1px solid $color__border;
    overflow: hidden;
  }

  .buckets__page-title-content {
    padding-top: 30px;
    padding-bottom: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .buckets__page-title-actions {
    display: flex;
    flex-wrap: nowrap;

    a,
    button {
      margin-left: 20px;
      vertical-align: middle;
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
    border-bottom: 1px solid $color__border--light;

    .buckets__sources {
      flex-grow: 2;

      .sources__select {
        margin-top: -35px;
      }
    }

    .buckets__filter {
      margin-left: 15px;
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
        height: auto;
        background: $color__border--light;
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

        a :not(.tag) {
          color: $color__link;
        }
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

          .radioGroup__item {
            &:hover {
              background-color: $color__border--light;
            }
          }
        }
      }

      .button--add:disabled {
        opacity: 0.3;
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

<style lang="scss">

  .buckets {
    .buckets__fieldset {
      /* override fieldset style */

      .fieldset__content {
        padding: 0;
      }

      @each $current-color in $colors__bucket--list {
        $i: index($colors__bucket--list, $current-color);
        &.buckets__fieldset--#{$i} header {
          color: $color__white;
          background-color: $current-color;
        }
      }
    }

    .filter__search {
      width: 100%;

      input {
        width: 100%;
        min-width: inherit;
      }
    }
  }
</style>
