export default {
  props: {
    buckets: {
      type: Array,
      default: () => []
    },
    item: {
      type: Object
    },
    singleBucket: {
      type: Boolean,
      default: true
    }
  },
  computed: {
    bucketClasses: function () {
      return {
        'selected': this.type !== 'bucket' && this.inBuckets,
        'single': this.singleBucket
      }
    }
  },
  methods: {
    addToBucket: function (bucketId = this.bucket) {
      this.$emit('add-to-bucket', this.item, bucketId)
    },
    inBucketById: function (id) {
      let self = this
      let index = self.buckets.findIndex(b => b.id === id)

      if (index === -1) return

      let find = self.buckets[index].children.find(function (c) {
        return c.id === self.item.id && c.content_type.value === self.item.content_type.value
      })

      return !!find
    }
  }
}
