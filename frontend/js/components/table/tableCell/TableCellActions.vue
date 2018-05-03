<template>
  <a17-dropdown
    ref="rowSetupDropdown"
    position="bottom-right"
    :fixed="true">
    <a17-button
      variant="icon"
      @click="$refs.rowSetupDropdown.toggle()">
      <span v-svg symbol="more-dots"></span>
    </a17-button>
    <div slot="dropdown__content">
      <a v-if="row.hasOwnProperty('permalink')"
         :href="row['permalink']"
         target="_blank">View permalink</a>
      <a v-if="row.hasOwnProperty('edit') && !row.hasOwnProperty('deleted') && row['edit']"
         :href="editUrl"
         @click="preventEditInPlace($event)">Edit</a>
      <a v-if="row.hasOwnProperty('published') && !row.hasOwnProperty('deleted')"
         href="#"
         @click.prevent="update('published')"
      >{{ row['published'] ? 'Unpublish' : 'Publish' }}</a>
      <a v-if="row.hasOwnProperty('featured') && !row.hasOwnProperty('deleted')"
         href="#"
         @click.prevent="update('featured')">{{ row['featured'] ? 'Unfeature' : 'Feature' }}</a>
      <a v-if="row.hasOwnProperty('deleted')"
         href="#"
         @click.prevent="restoreRow">Restore</a>
      <a v-else-if="row.delete"
         href="#"
         @click.prevent="deleteRow">Delete</a>
    </div>
  </a17-dropdown>
</template>

<script>
  import { TableCellMixin } from '@/mixins'

  export default {
    name: 'TableCellActions',
    mixins: [TableCellMixin],
    methods: {
      update: function (colName) {
        this.$emit('update', {row: this.row, col: colName})
      }
    }
  }
</script>

<style scoped>

</style>
