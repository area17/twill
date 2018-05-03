const getDepth = ({children}) => 1 + (children && children.length > 0 ? Math.max(...children.map(getDepth)) : 0)
/* eslint-disable */
export default {
  props: {
    /**
     * Enable or not a nested listing
     * @type {Boolean}
     */
    nested: {
      type: Boolean,
      default: false
    },
    /**
     * Define the max depth level for a nested listing.
     * User can't add element in a listing if this value is reached.
     * @type {number}
     */
    maxDepth: {
      type: Number,
      default: 1
    },
    /**
     * The current nested depth
     * @type {number}
     */
    depth: {
      type: Number,
      default: 0
    },
    /**
     * Name is used by sortable.js in vue-draggable to drag elements from one list into another
     * @type {string}
     */
    name: {
      type: String,
      default: 'group1'
    },
    /**
     * The id of parent component and must be unique.
     * Parent could be a dataTable component or a tableRowNested component.
     * This is required to save tree of listing in store.
     * @type {number}
     *
     */
    parentId: {
      type: Number,
      default: -1
    }
  },
  data () {
    return {
      currentElDepth: undefined
    }
  },
  computed: {
    updateTracker () {
      return this.$store.state.datatable.updateTracker
    },
    draggableGetComponentData: function () {
      return {
        props: {
          depth: this.depth
        }
      }
    }
  }
}
