<template>
    <div>
    <a17-dam-modal title="DAM Media Library" mode="wide" ref="modal" @open="opened">

        <template #header>
            <div class="medialibrary__header">

                <div class="medialibrary__collections">
                    <h3>{{ $trans('dam.add-collection-uploads-text', 'Add files to') }}</h3>
                
                   <div class="collection__dropdown">
                    <v-select
                        :options="collections"
                        :searchable="true"
                        placeholder="Select collections"
                        @input="updateMetadata($event, 'collection')"
                        >
                    </v-select>
                        
                   </div>
                    
                </div>
            
                <div class="medialibrary__actions">
                    <a17-button variant="outline" @click="close" > Cancel</a17-button>
                    <a17-button variant="validate" @click="openMetadataModal" :disabled="disabled">{{ btnLabel }}</a17-button>
                </div>
            </div>
        </template>
        <div class="medialibrary">
            <div class="medialibrary__frame">
            <div class="medialibrary__header" ref="form">
            
            </div>
            <div class="medialibrary__inner">
                <div class="medialibrary__grid">
                <div class="medialibrary__list" ref="list">
                    <a17-uploader ref="uploader" v-if="authorized" @loaded="addSavedMedia"  @added="addMedia" @uploaded="uploadSuccess"
                                :type="currentTypeObject"/>
                    <div class="medialibrary__list-items">
                    <a17-mediagrid :items="renderedMediaItems" :selected-items="selectedMedias"
                                    @deleteMedia="deleteMedia"/>
                    <a17-spinner v-if="loading" class="medialibrary__spinner">Loading&hellip;</a17-spinner>
                    </div>
                </div>
                </div>
            </div>
            </div>
        </div>
       
    </a17-dam-modal>
    <a17-modal ref="metadataModal" :title="'Assign Metadata'">
        <div class="modal__metadata__content">
            <a17-vselect
            label="Tags"
            name="tags"
            :multiple="true"
            :searchable="true"
            :taggable="true"
            :push-tags="true"
            in-store="inputValue"
            @change="updateMetadata($event, 'tags')"
        ></a17-vselect>
        <a17-vselect
            label="Disciplines"
            name="disciplines"
            :options="disciplines"
            :in-modal="true"
            :multiple="true"
            in-store="inputValue"
            @change="updateMetadata($event, 'disciplines')"
        >
        </a17-vselect>
        <a17-vselect
            label="Sectors"
            :options="sectors"
            name="sectors"
            :in-modal="true"
            :multiple="true"
            in-store="inputValue"
            @change="updateMetadata($event, 'sectors')"
        >
        </a17-vselect>
        <a17-inputframe label="Project" name="browsers.projects" note="Note">
            <a17-browserfield
                name="project"
                itemLabel="project"
                browserNote="project"
                :endpoint="endpoint"
                :max="1"
            >Project</a17-browserfield>
        </a17-inputframe>

        <a17-inputframe>
            <a17-button type="submit" name="create" variant="validate" @click="saveFiles">Add files</a17-button>
        </a17-inputframe>
        </div>
        

    </a17-modal>
        </div>
  </template>
  
  <script>
  import { mapState } from 'vuex'


  import a17Spinner from '@/components/Spinner.vue'
  import { MEDIA_LIBRARY,NOTIFICATION } from '@/store/mutations'
  import a17MediaGrid from './MediaGrid.vue'
  import a17Uploader from './Uploader.vue'
  import a17DamModal from './Modal.vue'
  import vSelect from "vue-select";
  export default {
    name: 'A17DAMMedialibrary',
    components: {
      'a17-uploader': a17Uploader,
      'a17-mediagrid': a17MediaGrid,
      'a17-spinner': a17Spinner,
      'a17-dam-modal': a17DamModal,
      'v-select': vSelect
    },
    props: {
      
      initialPage: {
        type: Number,
        default: 1
      },
      authorized: {
        type: Boolean,
        default: false
      },
      showInsert: {
        type: Boolean,
        default: true
      },
      extraMetadatas: {
        type: Array,
        default () {
          return []
        }
      },
      translatableMetadatas: {
        type: Array,
        default () {
          return []
        }
      }
    },
    data: function () {
      return {
        loading: false,
        maxPage: 20,
        mediaItems: [],
        selectedMedias: [],
        gridHeight: 0,
        page: this.initialPage,
        tags: [],
        lastScrollTop: 0,
        gridLoaded: false,
        metadata : {},
      }
    },
    computed: {
      renderedMediaItems: function () {
        return this.mediaItems.map((item) => {
          item.disabled = (this.filesizeMax > 0 && item.filesizeInMb > this.filesizeMax) ||
            (this.widthMin > 0 && item.width < this.widthMin) ||
            (this.heightMin > 0 && item.height < this.heightMin)
          return item
        })
      },
      currentTypeObject: function () {
        return this.types.find((type) => {
          return type.value === this.type
        })
      },
      endpoint: function () {
        return this.currentTypeObject.endpoint
      },
      btnLabel: function () {
        
        if (this.mediaItems.length > 0) {
          return 'Add '+this.mediaItems.length+' files'
        }
        return 'Add files'
      },
      disabled: function() {
        return this.mediaItems.length < 1;
      },
      
      ...mapState({
        connector: state => state.mediaLibrary.connector,
        max: state => state.mediaLibrary.max,
        filesizeMax: state => state.mediaLibrary.filesizeMax,
        widthMin: state => state.mediaLibrary.widthMin,
        heightMin: state => state.mediaLibrary.heightMin,
        type: state => state.mediaLibrary.type, // image, video, file
        types: state => state.mediaLibrary.types,
        strict: state => state.mediaLibrary.strict,
        selected: state => state.mediaLibrary.selected,
        collections: state => state.mediaLibrary.forUploadCollections,
        sectors: state => state.mediaLibrary.forUploadSectors,
        disciplines: state => state.mediaLibrary.forUploadDisciplines,
        endpoint: state => state.mediaLibrary.projectBrowserUrl,
        project: state => state.browser.selected.project

      })
    },
    watch: {
      
    },
    methods: {
      deleteMedia: function(media) {
        const index  = this.mediaItems.findIndex(function(m) {
          return m.id === media.id
        })
        if (index > -1) {
          this.mediaItems.splice(index, 1)
          this.$refs.uploader.removeMedia(media.id)
        }
       
      },

      openMetadataModal: function() {
        this.$refs.metadataModal.open()
      },
      updateMetadata(event, type) { 
        if (type === 'tags') {
          this.metadata[type] = event
        }else {
          const ids = Array.isArray(event) ? event.map(item => item.id) : [event.id]
          this.metadata[type] = ids;
        }
        
      },
      saveFiles(){
        if (this.project && this.project.length > 0) {
          this.metadata.project = this.project[0].id;
        }
        this.$refs.uploader.uploadFiles(this.metadata)
      },
      open: function () {
        this.$refs.modal.open()
      },
      close: function () {
        this.$refs.uploader.cancelAll()
        this.mediaItems = []
        this.$refs.modal.hide()
      },
      uploadSuccess(){
        this.$refs.modal.hide()
        this.$refs.metadataModal.hide()
        this.mediaItems = []
      },
      opened: function () {
      },
      updateType: function (newType) {
       
      },
      addSavedMedia: function(media){
        this.$emit('media-added', media)
      },
      addMedia: function(media){
        const index = this.mediaItems.findIndex(function (item) {
          return item.id === media.id
        })
        if (index > -1) {
          this.$set(this.mediaItems, index, media)
        }else {
          this.mediaItems.push(media)
        }
      },
      saveAndClose: function () {
        this.$store.commit(MEDIA_LIBRARY.SAVE_MEDIAS, this.selectedMedias)
        this.close()
      }
    }
  }
  </script>
  
  <style lang="scss" scoped>

  
    .medialibrary {
      display: block;
      width: 100%;
      min-height: 100%;
      padding: 0;
      position: relative;
    }
  
    .medialibrary__header {
        display: flex;
        flex-flow: row;
        width:100%;
        justify-content: space-between;
        align-items: center;
        padding: 1.25rem;
        color:white;
    }

    .medialibrary__collections, .medialibrary__actions {
        display:flex;
        flex-direction: row;
        gap: 1.25rem;
        align-items: center;
        justify-content: center;
    }
  
    .medialibrary__frame {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      display: flex;
      flex-flow: column nowrap;
    }
  
    .medialibrary__inner {
      position: relative;
      width: 100%;
      overflow: hidden;
      flex-grow: 1;
    }
  

  
    .medialibrary__list {
      margin: 0;
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      overflow: auto;
      padding: 10px;
    }
  
    .medialibrary__list-items {
      position: relative;
      display: block;
      width: 100%;
      min-height: 100%;
    }

    .modal__metadata__content {
        display:flex;
        flex-flow: column;
        padding-bottom: 1rem;
    }
    .collection__dropdown {
        width: 13rem;
       

    div {
        position: relative;
    }
    .icon {
      color: $color__grey--54;
      position: absolute;
      top: rem-calc(8);
      right: rem-calc(8);
    }
    }
  
   
  </style>
  
  <style lang="scss">
  
    .medialibrary__filter-item {
      .vselect {
        min-width: 200px;
      }
    }
  
    .medialibrary__filter-item.checkbox {
      margin-top: 8px;
      margin-right: 45px !important;
    }

  </style>
  