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
         @click="preventEditInPlace($event)">{{ $trans('listing.dropdown.edit', 'Edit') }}</a>
      <a v-if="row.hasOwnProperty('published') && !row.hasOwnProperty('deleted')"
         href="#"
         @click.prevent="update('published')"
      >{{ row['published'] ? $trans('listing.dropdown.unpublish', 'Unpublish') : $trans('listing.dropdown.publish', 'Publish') }}</a>
      <a v-if="row.hasOwnProperty('featured') && !row.hasOwnProperty('deleted')"
         href="#"
         @click.prevent="update('featured')">{{ row['featured'] ? $trans('listing.dropdown.unfeature', 'Unfeature') : $trans('listing.dropdown.feature', 'Feature') }}</a>
      <a v-if="row.duplicate && !row.hasOwnProperty('deleted')"
         href="#"
         @click.prevent="duplicateRow">{{ $trans('listing.dropdown.duplicate', 'Duplicate') }}</a>
      <a v-if="row.hasOwnProperty('deleted')"
         href="#"
         @click.prevent="restoreRow">{{ $trans('listing.dropdown.restore', 'Restore') }}</a>
      <a v-if="row.hasOwnProperty('deleted') && row.hasOwnProperty('destroyable')"
         href="#"
         @click.prevent="destroyRow">{{ $trans('listing.dropdown.destroy', 'Destroy') }}</a>
      <a v-else-if="row.delete && !row.hasOwnProperty('deleted')"
         href="#"
         @click.prevent="deleteRow">{{ $trans('listing.dropdown.delete', 'Delete') }}</a>
    </div>
  </a17-dropdown>
</template>

<script>
  import TableCellMixin from '@/mixins/tableCell'

  export default {
    name: 'TableCellActions',
    mixins: [TableCellMixin],
    methods: {
      update: function (colName) {
        this.$emit('update', { row: this.row, col: colName })
      }
    }
  }
</script>

<style scoped>

</style>
