<template>
  <div class="bulkEditor">
    <div class="bulkEditor__inner" v-if="bulkIds.length">
      <div class="container">
        <p class="bulkEditor__infos">{{ bulkIds.length }} item{{ bulkIds.length > 1 ? 's' : '' }} selected</p>
        <div class="bulkEditor__dropdown">
          <a17-dropdown ref="bulkActionsDown" position="bottom-left" width="full" :offset="0">
            <a17-button variant="dropdown" size="small" @click="$refs.bulkActionsDown.toggle()">Bulk actions</a17-button>

            <div slot="dropdown__content">
              <ul>
                <li>
                  <button v-if="bulkStatus.canPublish && !bulkStatus.published && !bulkStatus.deleted" @click="bulkPublish">Publish</button>
                  <button v-if="bulkStatus.canPublish && bulkStatus.published && !bulkStatus.deleted" @click="bulkUnpublish">Unpublish</button>
                  <button v-if="bulkStatus.canFeature && !bulkStatus.featured && !bulkStatus.deleted" @click="bulkFeature">Feature</button>
                  <button v-if="bulkStatus.canFeature && bulkStatus.featured && !bulkStatus.deleted" @click="bulkUnFeature">Unfeature</button>
                  <!-- <button @click="bulkExport">Export</button> -->
                  <button v-if="!bulkStatus.deleted" @click="bulkDelete">Delete</button>
                  <button v-if="bulkStatus.deleted" @click="bulkRestore">Restore</button>
                </li>
              </ul>
            </div>
          </a17-dropdown>
        </div>
        <a17-button variant="ghost" @click="clearBulkSelect">Clear</a17-button>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'

  export default {
    name: 'A17BulkEditor',
    computed: {
      ...mapState({
        bulkIds: state => state.datatable.bulk,
        bulkStatus: state => state.datatable.data.filter((row) => {
          return state.datatable.bulk.includes(row.id)
        }).reduce((status, row) => {
          return {
            featured: status.featured && (row.featured || false),
            canFeature: status.canFeature && row.hasOwnProperty('featured'),
            published: status.published && (row.published || false),
            canPublish: status.canPublish && row.hasOwnProperty('published'),
            deleted: status.deleted && (row.deleted || false)
          }
        }, {
          featured: true,
          canFeature: true,
          published: true,
          canPublish: true,
          deleted: true
        })
      })
    },
    methods: {
      clearBulkSelect: function () {
        this.$store.commit('replaceDatableBulk', [])
      },
      bulkPublish: function () {
        this.$store.dispatch('bulkPublishData', { toPublish: true })
      },
      bulkUnpublish: function () {
        this.$store.dispatch('bulkPublishData', { toPublish: false })
      },
      bulkFeature: function () {
        this.$store.dispatch('bulkFeatureData', { toFeature: true })
      },
      bulkUnFeature: function () {
        this.$store.dispatch('bulkFeatureData', { toFeature: false })
      },
      bulkExport: function () {
        // Todo : not sure what should be done here
        this.$store.dispatch('bulkExportData')
      },
      bulkDelete: function () {
        this.$store.dispatch('bulkDeleteData')
      },
      bulkRestore: function () {
        this.$store.dispatch('bulkRestoreData')
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .bulkEditor {
  }

  .bulkEditor__infos {
    display:inline-block;
  }

  .bulkEditor__dropdown {
    display:inline-block;
    min-width:150px;
  }

  .bulkEditor__infos,
  .bulkEditor__dropdown {
    margin-right:20px;
  }

  .bulkEditor__inner {
    position:absolute;
    top:0;
    left:0;
    bottom:0;
    width:100%;
    z-index:2;
    padding:20px 0;
    background:$color__bulk--background;
  }
</style>
