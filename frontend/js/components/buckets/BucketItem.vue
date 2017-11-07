<template>
  <tr class="buckets__item" :class="customClasses">
    <td v-if="draggable" class="drag__handle">
      <div class="drag__handle--drag"></div>
    </td>
    <td class="buckets__itemThumbnail" v-if="item.thumbnail">
      <img :src="item.thumbnail" :alt="item.name">
    </td>
    <td class="buckets__itemTitle">
      <h4><a :href="item.edit" target="_blank">{{ item.name }}</a></h4>
    </td>
    <td class="buckets__itemContentType" v-if="item.content_type">
      {{ item.content_type.label }}
    </td>
    <td class="buckets__itemOptions">
      <a17-dropdown v-if="!singleBucket" ref="bucketDropdown" class="item__dropdown bucket__action" position="bottom-right" title="Featured in" :clickable="true">
        <a17-button variant="icon" @click="$refs.bucketDropdown.toggle()"><span v-svg symbol="more-dots"></span>
        </a17-button>
        <div v-if="restricted" slot="dropdown__content" class="item__dropdown__content">
          <a17-radiogroup name="bucketsSelection" radioClass="bucket" :radios="dropDownBuckets" :initialValue="selectedBuckets()[0]" @change="updateBucket"></a17-radiogroup>
        </div>
        <div v-else="" slot="dropdown__content" class="item__dropdown__content">
          <a17-checkboxgroup name="bucketsSelection" :options="dropDownBuckets" :selected="selectedBuckets()" @change="updateBucket"></a17-checkboxgroup>
        </div>
      </a17-dropdown>
      <a17-button class="bucket__action" icon="close" @click="removeFromBucket()">
        <span v-svg symbol="close_icon"></span>
      </a17-button>
    </td>
  </tr>
</template>

<script>
  import A17Dropdown from '../Dropdown.vue'
  import bucketMixin from '@/mixins/buckets'

  export default {
    components: {A17Dropdown},
    name: 'a17BucketItem',
    props: {
      bucket: {
        type: Number
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
      }
    },
    mixins: [bucketMixin],
    computed: {
      inBuckets: function () {
        let self = this
        let find = false
        self.buckets.forEach(function (bucket) {
          if (bucket.children.find(function (b) {
            return b.id === self.item.id && b.content_type.value === self.item.content_type.value
          })) {
            find = true
          }
        })
        return find
      },
      customClasses: function () {
        return {
          ...this.bucketClasses,
          'draggable': this.draggable
        }
      },
      dropDownBuckets: function () {
        let checkboxes = []
        let self = this
        let index = 1
        if (this.buckets.length > 0) {
          this.buckets.forEach(function (bucket) {
            checkboxes.push({
              value: self.slug(bucket.id),
              label: index + ' ' + bucket.name
            })
            index++
          })
        }
        return checkboxes
      }
    },
    methods: {
      removeFromBucket: function (bucketId = this.bucket) {
        this.$emit('remove-from-bucket', this.item, bucketId)
      },
      selectedBuckets: function () {
        let selected = []
        let self = this
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
      slug: function (id) {
        // Current Bucket ID + item id + bucket id
        return 'bucket-' + this.bucket + '_item-' + this.item.id + '_type-' + this.item.content_type.value + '_inb-' + id
      },
      updateBucket: function (value) {
        let pattern = 'inb-'
        let self = this

        let selected = self.selectedBuckets()

        selected.forEach(function (select) {
          if (value.indexOf(select) === -1) {
            let index = parseInt(select.split(pattern)[1])
            self.$refs.bucketDropdown.toggle()
            self.removeFromBucket(index)
          }
        })

        if (self.restricted) {
          let index = parseInt(value.split(pattern)[1])
          if (!self.inBucketById(index)) {
            self.$refs.bucketDropdown.toggle()
            self.addToBucket(index)
          }
          return
        }

        value.forEach(function (val) {
          let index = parseInt(val.split(pattern)[1])
          if (!self.inBucketById(index)) {
            self.$refs.bucketDropdown.toggle()
            self.addToBucket(index)
          }
        })
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

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
    background: repeating-linear-gradient(180deg, $color__drag_bg--hover 0, $color__drag_bg--hover 2px, transparent 2px, transparent 4px);
  }

  .drag__handle--drag {
    position: relative;
    width: 6px;
    height: 42px;
    background: repeating-linear-gradient(90deg, $color__drag 0, $color__drag 2px, transparent 2px, transparent 4px);
    transition: background 250ms ease;

    &:before {
      content: '';
      position: absolute;
      width: 100%;
      height: 100%;
      background: repeating-linear-gradient(180deg, $color__drag_bg 0, $color__drag_bg 2px, transparent 2px, transparent 4px);
    }
  }
</style>
