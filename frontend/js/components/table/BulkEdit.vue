<template>
  <div class="bulkEditor">
    <div class="bulkEditor__inner" v-if="bulkIds.length">
      <div class="container">
        <p class="bulkEditor__infos">
          {{ bulkIds.length }} {{ bulkIds.length > 1 ? $trans('listing.bulk-selected-items') : $trans('listing.bulk-selected-item') }}
        </p>
        <div class="bulkEditor__dropdown">
          <a17-dropdown ref="bulkActionsDown" position="bottom-left" width="full" :offset="0">
            <a17-button variant="dropdown" size="small" @click="$refs.bulkActionsDown.toggle()">{{ $trans('listing.bulk-actions') }}</a17-button>

            <div slot="dropdown__content">
              <ul>
                <li>
                  <button v-if="bulkPublishable()" @click="bulkPublish">{{ $trans('listing.dropdown.publish') }}</button>
                  <button v-if="bulkPublishable(true)" @click="bulkUnpublish">{{ $trans('listing.dropdown.unpublish') }}</button>
                  <button v-if="bulkFeaturable()" @click="bulkFeature">{{ $trans('listing.dropdown.feature') }}</button>
                  <button v-if="bulkFeaturable(true)" @click="bulkUnFeature">{{ $trans('listing.dropdown.unfeature') }}</button>
                  <button v-if="bulkDeletable()" @click="bulkDelete">{{ $trans('listing.dropdown.delete') }}</button>
                  <button v-if="bulkRestorable()" @click="bulkRestore">{{ $trans('listing.dropdown.restore') }}</button>
                  <button v-if="bulkDestroyable()" @click="bulkDestroy">{{ $trans('listing.dropdown.destroy') }}</button>
                </li>
              </ul>
            </div>
          </a17-dropdown>
        </div>
        <a17-button variant="ghost" @click="clearBulkSelect">{{ $trans('listing.bulk-clear') }}</a17-button>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'

  import ACTIONS from '@/store/actions'
  import { DATATABLE } from '@/store/mutations'

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
            canDelete: status.canDelete && row.delete !== null,
            canDestroy: status.canDestroy && row.hasOwnProperty('destroyable')
          }
        }, {
          featured: true,
          canFeature: true,
          published: true,
          canPublish: true,
          deleted: true,
          canDelete: true,
          canDestroy: true
        })
      })
    },
    methods: {
      bulkPublishable: function ($inverse = false) {
        return window[process.env.VUE_APP_NAME].CMS_URLS.bulkPublish !== '' && this.bulkStatus.canPublish && ($inverse ? this.bulkStatus.published : !this.bulkStatus.published) && !this.bulkStatus.deleted
      },
      bulkFeaturable: function ($inverse = false) {
        return window[process.env.VUE_APP_NAME].CMS_URLS.bulkFeature !== '' && this.bulkStatus.canFeature && ($inverse ? this.bulkStatus.featured : !this.bulkStatus.featured) && !this.bulkStatus.deleted
      },
      bulkDeletable: function () {
        return window[process.env.VUE_APP_NAME].CMS_URLS.bulkDelete !== '' && !this.bulkStatus.deleted && this.bulkStatus.canDelete
      },
      bulkRestorable: function () {
        return window[process.env.VUE_APP_NAME].CMS_URLS.bulkRestore !== '' && this.bulkStatus.deleted
      },
      bulkDestroyable: function () {
        return window[process.env.VUE_APP_NAME].CMS_URLS.bulkDestroy !== '' && this.bulkStatus.deleted && this.bulkStatus.canDestroy
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
        if (this.$root.$refs.warningDeleteRow) {
          this.$root.$refs.warningDeleteRow.open(() => {
            this.$store.dispatch(ACTIONS.BULK_DELETE)
          })
        } else {
          this.$store.dispatch(ACTIONS.BULK_DELETE)
        }
      },
      bulkRestore: function () {
        this.$store.dispatch(ACTIONS.BULK_RESTORE)
      },
      bulkDestroy: function () {
        if (this.$root.$refs.warningDestroyRow) {
          this.$root.$refs.warningDestroyRow.open(() => {
            this.$store.dispatch(ACTIONS.BULK_DESTROY)
          })
        } else {
          this.$store.dispatch(ACTIONS.BULK_DESTROY)
        }
      }
    }
  }
</script>

<style lang="scss" scoped>

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
