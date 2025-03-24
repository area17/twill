<template>
  <tr class="buckets__item" :class="customClasses">
    <td v-if="draggable" class="drag__handle">
      <div class="drag__handle--drag"></div>
    </td>
    <td class="buckets__itemThumbnail" v-if="item.thumbnail">
      <img :src="item.thumbnail" :alt="item.name">
    </td>
    <td class="buckets__itemStarred" v-if="withToggleFeatured" :class="{'buckets__itemStarred--active': item.starred }">
      <span @click.prevent="toggleFeatured" :data-tooltip-title="item.starred ? toggleFeaturedLabels['unstar'] ? toggleFeaturedLabels['unstar'] : 'Unfeature' : toggleFeaturedLabels['star'] ? toggleFeaturedLabels['star'] : 'Feature'" v-tooltip>
        <span v-svg symbol="star-feature_active"></span>
        <span v-svg symbol="star-feature"></span>
      </span>
    </td>
    <td class="buckets__itemTitle">
      <h4>
        <span v-if="item.edit" class="f--link-underlined--o"><a :href="item.edit" target="_blank">{{ item.name }}</a></span>
        <span v-else>{{ item.name }}</span>
      </h4>
    </td>
    <td class="buckets__itemContentType" v-if="item.type && !singleSource">
      {{ sourceLabels[item.type] }}
    </td>
    <td class="buckets__itemOptions">
      <a17-dropdown v-if="!singleBucket" ref="bucketDropdown" class="item__dropdown bucket__action" position="bottom-right" title="Featured in" :clickable="true">
        <a17-button variant="icon" @click="$refs.bucketDropdown.toggle()"><span v-svg symbol="more-dots"></span>
        </a17-button>
        <div v-if="restricted" slot="dropdown__content" class="item__dropdown__content">
          <a17-radiogroup name="bucketsSelection" radioClass="bucket" :radios="dropDownBuckets" :initialValue="selectedBuckets()[0]" @change="updateBucket"/>
        </div>
        <div v-else slot="dropdown__content" class="item__dropdown__content">
          <a17-checkboxgroup name="bucketsSelection" :options="dropDownBuckets" :selected="selectedBuckets()" @change="updateBucket"/>
        </div>
      </a17-dropdown>
      <a17-button class="bucket__action" icon="close" @click="removeFromBucket()">
        <span v-svg symbol="close_icon"></span>
      </a17-button>
    </td>
  </tr>
</template>

<script>
  import bucketMixin from '@/mixins/buckets'

  import A17Dropdown from '../Dropdown.vue'

  export default {
    components: { A17Dropdown },
    name: 'a17BucketItem',
    props: {
      bucket: {
        type: String
      },
      draggable: {
        type: Boolean,
        default: false
      },
      // Items are restricted to one bucket. If not checkboxes will replace radios buttons in BucketItem component.
      restricted: {
        type: Boolean,
        default: false
      },
      type: {
        type: String,
        default: 'bucket'
      },
      singleSource: {
        type: Boolean,
        default: false
      },
      withToggleFeatured: {
        type: Boolean,
        default: false
      },
      toggleFeaturedLabels: {
        type: Array,
        default: () => []
      },
      sourceLabels: Object,
    },
    mixins: [bucketMixin],
    computed: {
      inBuckets() {
        for(const bucket in this.buckets) {
          if (bucket.children.some((b) => b.id === self.item.id && b.type === self.item.type)) {
            return true
          }
        }
        return false
      },
      customClasses() {
        return {
          ...this.bucketClasses,
          draggable: this.draggable
        }
      },
      dropDownBuckets() {
        const checkboxes = []
        const self = this
        let index = 1
        if (this.buckets.length > 0) {
          this.buckets.forEach(function (bucket) {
            if (self.restrictedBySource(bucket.id)) {
              checkboxes.push({
                value: self.slug(bucket.id),
                label: index + ' ' + bucket.name
              })
            }
            index++
          })
        }
        return checkboxes
      }
    },
    methods: {
      removeFromBucket(bucketId = this.bucket) {
        this.$emit('remove-from-bucket', this.item, bucketId)
      },
      toggleFeatured() {
        this.$emit('toggle-featured-in-bucket', this.item, this.bucket)
      },
      selectedBuckets() {
        const selected = []
        const self = this
        if (this.buckets.length > 0) {
          this.buckets.forEach(function (bucket) {
            if (self.inBucketById(bucket.id)) selected.push(self.slug(bucket.id))
          })
        }
        if (selected.length > 0) {
          return selected
        }
        return []
      },
      slug(id) {
        // Current Bucket ID + item id + bucket id
        return 'bucket-' + this.bucket + '_item-' + this.item.id + '_type-' + this.item.type + '_inb-' + id
      },
      updateBucket(value) {
        const pattern = 'inb-'
        const self = this

        const selected = self.selectedBuckets()

        if (self.restricted) { // when restricted : value is coming from a radio group
          const index = value.split(pattern)[1]
          if (!self.inBucketById(index)) {
            self.$refs.bucketDropdown.toggle()
            self.addToBucket(index)
          }
          return
        }

        selected.forEach(function (select) {
          if (value.indexOf(select) === -1) {
            const index = select.split(pattern)[1]
            self.$refs.bucketDropdown.toggle()
            self.removeFromBucket(index)
          }
        })

        if (Array.isArray(value)) { // when no restricted : values are coming from checkboxes as an array
          value.forEach(function (val) {
            const index = val.split(pattern)[1]
            if (!self.inBucketById(index)) {
              self.$refs.bucketDropdown.toggle()
              self.addToBucket(index)
            }
          })
        }
      }
    }
  }
</script>

<style lang="scss" scoped>

  .drag__handle {
    position: absolute;
    top: 0;
    left: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    width: 12px;
    min-width: 12px;
    height: 100%;
    background-color: $color__drag_bg;
    transition: background 250ms ease;
    cursor: move;

    &:hover {
      background-color: $color__drag_bg--hover;
    }
  }

  .drag__handle:hover .drag__handle--drag:before {
    background: dragGrid__bg($color__drag_bg--hover);
  }

  .drag__handle--drag {
    position: relative;
    width: 10px;
    height: 42px;
    margin-left:auto;
    margin-right:auto;
    transition: background 250ms ease;
    @include dragGrid($color__drag, $color__drag_bg);
  }

  .buckets__itemStarred {
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

  .buckets__itemStarred--active {
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
</style>
