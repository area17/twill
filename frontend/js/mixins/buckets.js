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
      let index = this.buckets.findIndex(b => b.id === id)

      if (index === -1) return

      let find = this.buckets[index].children.find((c) => {
        return c.id === this.item.id && c.content_type.value === this.item.content_type.value
      })

      return !!find
    }
  }
}
