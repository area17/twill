import { mapState } from 'vuex'

export default {
  props: {
    items: {
      type: Array,
      default: () => []
    },
    selectedItems: {
      type: Array,
      default: () => []
    },
    usedItems: {
      type: Array,
      default: () => []
    }
  },
  computed: {
    ...mapState({
      itemsLoading: state => state.mediaLibrary.loading
    }),
    replacingMediaIds: function () {
      return this.itemsLoading.reduce((agg, curr) => {
        if (curr.isReplacement) {
          agg[curr.replacementId] = curr.id
        }
        return agg
      }, {})
    }
  },
  methods: {
    isSelected: function (item, keys = ['id']) {
      return Boolean(this.selectedItems.find(sItem => keys.every(key => sItem[key] === item[key])))
    },
    isUsed: function (item, keys = ['id']) {
      return Boolean(this.usedItems.find(uItem => keys.every(key => uItem[key] === item[key])))
    },
    toggleSelection: function (item) {
      this.$emit('change', item)
    },
    shiftToggleSelection: function (item) {
      this.$emit('shiftChange', item, true)
    },
    isImage: function(extension) {
      if (!extension) return false
      const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp']
      return imageExtensions.includes(extension)
    },
    getFileIcon: function(extension) {
      const ext = extension.toLowerCase()
      if (!ext) return 'txt'
      switch(ext) {
        case 'pdf':
          return 'pdf'
        case 'psd':
          return 'psd'
        case 'zip':
          return 'zip'
        case 'ppt':
        case 'pptx':
          return 'ppt'
        case 'doc':
        case 'docx':
          return 'doc'
        case 'txt':
          return 'txt'
        case 'mp3':
        case 'wav':
          return 'b-audio'
        case 'mp4':
        case 'mov':
          return 'video'
        default:
          return 'txt'
      }
    },
  }
}
