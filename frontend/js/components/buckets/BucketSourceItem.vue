<template>
  <tr class="buckets__item" :class="bucketClasses">
    <td class="buckets__itemThumbnail" v-if="item.thumbnail">
      <img :src="item.thumbnail" :alt="item.name">
    </td>
    <td class="buckets__itemTitle">
      <h4><a :href="item.edit" target="_blank">{{ item.name }}</a></h4>
    </td>
    <td class="buckets__itemDate" v-if="item.publication">
      {{ item.publication }}
    </td>
    <td class="buckets__itemOptions">

      <a17-button v-if="singleBucket && !inBucketById(buckets[0].id)" icon="add" @click="addToBucket(buckets[0].id)">
        <span v-svg symbol="add"></span></a17-button>
      <a17-button v-else-if="singleBucket" icon="add" :disabled="true"><span v-svg symbol="add"></span></a17-button>

      <template v-else="" v-for="(b, index) in buckets">
        <a17-button :key="b.id" v-if="!inBucketById(b.id)" class="bucket__action" :icon="'bucket--'+(index+1)" @click="addToBucket(b.id)">{{ index + 1 }}</a17-button>
        <a17-button :key="b.id" v-else="" class="bucket__action selected" :icon="'bucket--'+(index+1)" :disabled="true">{{ index + 1 }}</a17-button>
      </template>

    </td>
  </tr>
</template>

<script>
  import bucketMixin from '@/mixins/buckets'

  export default {
    mixins: [bucketMixin]
  }
</script>
