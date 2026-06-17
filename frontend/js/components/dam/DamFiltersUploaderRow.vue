<template>
  <div class="dam-filters-uploader-row">
    <div class="dam-filters-uploader-row__filters">
      <a17-dam-filters
        v-if="filters.length > 0"
        ref="damFilters"
        @resetFilters="$emit('resetFilters')"
      />
    </div>

    <div class="dam-filters-uploader-row__actions" v-if="hasEditPermissions">
      <a17-button variant="validate" size="small" @click="openUploader">
        {{ uploadLabel }}
      </a17-button>
    </div>

    <a17-dam-project-assets-uploader-modal
      ref="projectAssetsUploader"
      :authorized="hasEditPermissions"
      :project-id="projectId"
      :project-key="projectKey"
      :title="uploadModalTitle"
      :metadata-keys="metadataKeys"
      @media-added="$emit('media-added', $event)"
      @uploaded="$emit('uploaded', $event)"
    />
  </div>
</template>

<script>
  import { mapState } from 'vuex'

  import A17DamFilters from '@/components/dam/DamFilters.vue'
  import A17DamProjectAssetsUploaderModal from '@/components/dam/DamProjectAssetsUploaderModal.vue'

  export default {
    name: 'A17DamFiltersUploaderRow',
    components: {
      'a17-dam-filters': A17DamFilters,
      'a17-dam-project-assets-uploader-modal': A17DamProjectAssetsUploaderModal
    },
    props: {
      uploadLabel: {
        type: String,
        default() {
          return this.$trans('dam.add-new', 'Add new')
        }
      },
      uploadModalTitle: {
        type: String,
        default() {
          return this.$trans('dam.add-files', 'Add files')
        }
      },
      projectId: {
        type: [Number, String],
        default: null
      },
      projectKey: {
        type: String,
        default: 'project'
      },
      metadataKeys: {
        type: Object,
        default: () => ({
          pushToArchive: 'push_to_archive',
          showInCmsMediaLibrary: 'show_in_cms_media_library',
          showInDam: 'show_in_dam'
        })
      }
    },
    computed: {
      ...mapState({
        filters: state => state.mediaLibrary.filters,
        hasEditPermissions: state => state.permissions.hasEditPermissions
      })
    },
    methods: {
      applyFilters() {
        this.$refs.damFilters?.applyFilters()
      },
      openUploader() {
        this.$refs.projectAssetsUploader?.open()
      }
    }
  }
</script>

<style lang="scss" scoped>
  .dam-filters-uploader-row {
    display: flex;
    flex-direction: column;
    gap: rem-calc(16);

    @include breakpoint('medium+') {
      flex-direction: row;
      align-items: flex-start;
      justify-content: space-between;
    }
  }

  .dam-filters-uploader-row__filters {
    min-width: 0;
    flex: 1 1 auto;
  }

  .dam-filters-uploader-row__actions {
    flex: 0 0 auto;
    padding: rem-calc(16);
  }
</style>
