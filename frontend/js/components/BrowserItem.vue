<template>
  <tr class="browserItem">
    <td v-if="draggable" class="browserItem__cell browserItem__cell--drag">
      <div class="drag__handle--drag"></div>
    </td>
    <td class="browserItem__cell browserItem__cell--name">
      <a href="#" target="_blank">{{ currentItem.name }}</a>
      <input type="hidden" :name="name" :value="currentItem.id"/>
    </td>
    <td class="browserItem__cell">
      <a17-button class="bucket__action" icon="close" @click="deleteItem()"><span v-svg symbol="close_icon"></span></a17-button>
    </td>
  </tr>
</template>

<script>
  export default {
    name: 'A17BrowserItem',
    props: {
      name: {
        type: String,
        required: true
      },
      draggable: {
        type: Boolean,
        default: false
      },
      item: {
        type: Object,
        default: function () {
          return {}
        }
      },
      itemLabel: {
        type: String,
        default: 'Item'
      },
      endpoint: {
        type: String,
        default: ''
      },
      max: {
        type: Number,
        default: 10
      }
    },
    data: function () {
      return {
        handle: '.item__handle' // Drag handle override
      }
    },
    computed: {
      currentItem: function () {
        return this.item
      }
    },
    methods: {
      deleteItem: function () {
        this.$emit('delete')
      },
      openBrowser: function () {
        this.$store.commit('updateBrowserConnector', this.name)
        this.$store.commit('updateBrowserEndpoint', this.endpoint)
        this.$store.commit('updateBrowserMax', this.max)
        this.$root.$refs.browser.open()
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .browserItem {
    position:relative;
    display:flex;
    width:100%;
    border-bottom: 1px solid $color__border--light;

    &:hover {
      .browserItem__cell {
        background-color: $color__f--bg;
      }
    }
  }

  .browserItem__cell {
    padding:15px;
  }

  .browserItem__cell--name {
    flex-grow: 1;
    padding-left:15px + 12px;

    a {
      color:$color__link;
      text-decoration: none;
      display:block;
      margin:-15px;
      padding:15px;

      &:hover {
        text-decoration: underline;
      }
    }
  }

  .browserItem__cell--drag {
    position: absolute;
    top: 0;
    padding:0;
    height:100%;
    display: flex;
    justify-content: center;
    align-items: center;
    width: 12px;
    min-width: 12px;
    background-color: $color__drag_bg;
    transition: background 250ms ease;
    cursor: move;

    &:hover {
      background-color: $color__drag_bg--hover;
    }
  }

  .drag__handle:hover .drag__handle--drag:before {
    background: repeating-linear-gradient(180deg, $color__drag_bg--hover 0, $color__drag_bg--hover 2px, transparent 2px, transparent 4px);
  }

  .drag__handle--drag {
    position: relative;
    width: 6px;
    height: 42px;
    background: repeating-linear-gradient(90deg, $color__drag 0, $color__drag 2px, transparent 2px, transparent 4px);
    transition: background 250ms ease;

    &:before {
      content: '';
      position: absolute;
      width: 100%;
      height: 100%;
      background: repeating-linear-gradient(180deg, $color__drag_bg 0, $color__drag_bg 2px, transparent 2px, transparent 4px);
    }
  }
</style>
