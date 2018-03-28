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
                  <button v-if="bulkPublishable()" @click="bulkPublish">Publish</button>
                  <button v-if="bulkPublishable(true)" @click="bulkUnpublish">Unpublish</button>
                  <button v-if="bulkFeaturable()" @click="bulkFeature">Feature</button>
                  <button v-if="bulkFeaturable(true)" @click="bulkUnFeature">Unfeature</button>
                  <button v-if="bulkDeletable()" @click="bulkDelete">Delete</button>
                  <button v-if="bulkRestorable()" @click="bulkRestore">Restore</button>
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
  import { DATATABLE } from '@/store/mutations'
  import ACTIONS from '@/store/actions'

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
            deleted: status.deleted && (row.deleted || false),
            canDelete: status.canDelete && row.delete !== null
          }
        }, {
          featured: true,
          canFeature: true,
          published: true,
          canPublish: true,
          deleted: true,
          canDelete: true
        })
      })
    },
    methods: {
      bulkPublishable: function ($inverse = false) {
        return window.CMS_URLS.bulkPublish !== '' && this.bulkStatus.canPublish && ($inverse ? this.bulkStatus.published : !this.bulkStatus.published) && !this.bulkStatus.deleted
      },
      bulkFeaturable: function ($inverse = false) {
        return window.CMS_URLS.bulkFeature !== '' && this.bulkStatus.canFeature && ($inverse ? this.bulkStatus.featured : !this.bulkStatus.featured) && !this.bulkStatus.deleted
      },
      bulkDeletable: function () {
        return window.CMS_URLS.bulkDelete !== '' && !this.bulkStatus.deleted && this.bulkStatus.canDelete
      },
      bulkRestorable: function () {
        return window.CMS_URLS.bulkRestore !== '' && this.bulkStatus.deleted
      },
      clearBulkSelect: function () {
        this.$store.commit(DATATABLE.REPLACE_DATATABLE_BULK, [])
      },
      bulkPublish: function () {
        this.$store.dispatch(ACTIONS.BULK_PUBLISH, { toPublish: true })
      },
      bulkUnpublish: function () {
        this.$store.dispatch(ACTIONS.BULK_PUBLISH, { toPublish: false })
      },
      bulkFeature: function () {
        this.$store.dispatch(ACTIONS.BULK_FEATURE, { toFeature: true })
      },
      bulkUnFeature: function () {
        this.$store.dispatch(ACTIONS.BULK_FEATURE, { toFeature: false })
      },
      bulkExport: function () {
        // Todo : not sure what should be done here
        this.$store.dispatch(ACTIONS.BULK_EXPORT)
      },
      bulkDelete: function () {
        this.$store.dispatch(ACTIONS.BULK_DELETE)
      },
      bulkRestore: function () {
        this.$store.dispatch(ACTIONS.BULK_RESTORE)
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  // .bulkEditor {
  // }

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
