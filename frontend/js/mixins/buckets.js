import { mapState } from 'vuex'

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
    ...mapState({
      dataSources: state => state.buckets.dataSources.content_types
    }),
    bucketClasses() {
      return {
        selected: this.type !== 'bucket' && this.inBuckets,
        single: this.singleBucket
      }
    }
  },
  methods: {
    addToBucket(bucketId = this.bucket) {
      this.$emit('add-to-bucket', this.item, bucketId)
    },
    inBucketById(id) {
      const index = this.buckets.findIndex(b => b.id === id)

      if (index === -1) return

      return this.buckets[index].children.some((c) => {
        return c.id === this.item.id && c.type === this.item.type
      })
    },
    restrictedBySource(id) {
      const bucket = this.buckets.find((b) => b.id === id)
      if (!bucket) return false

      // In this case all sources are accepted by the bucket
      if (!bucket.hasOwnProperty('acceptedSources')) return true
      if (bucket.acceptedSources.length === 0) return true

      const currentSource = this.dataSources.find((source) => source.type === this.item.type);
      return bucket.acceptedSources.findIndex((source) => source === currentSource.value) !== -1
    }
  }
}
