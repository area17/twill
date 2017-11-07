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
                  <button @click="bulkPublish">Publish</button>
                  <button @click="bulkUnpublish">Unpublish</button>
                  <button @click="bulkFeature">Feature</button>
                  <button @click="bulkExport">Export</button>
                  <button @click="bulkDelete">Delete</button>
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
        bulkIds: state => state.datatable.bulk
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
        this.$store.dispatch('bulkFeatureData')
      },
      bulkExport: function () {
        // Todo : not sure what should be done here
        this.$store.dispatch('bulkExportData')
      },
      bulkDelete: function () {
        this.$store.dispatch('bulkDeleteData')
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
    z-index:1;
    padding:20px 0;
    background:$color__bulk--background;
  }
</style>
