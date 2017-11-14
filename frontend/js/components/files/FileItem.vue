<template>
  <tr class="fileItem">
    <td v-if="draggable" class="fileItem__cell fileItem__cell--drag">
      <div class="drag__handle--drag"></div>
    </td>
    <td class="fileItem__cell fileItem__cell--extension" v-if="currentItem.hasOwnProperty('extension')">
      <span v-svg :symbol="getSvgIconName()"></span>
    </td>
    <td class="fileItem__cell fileItem__cell--name">
      <span v-if="currentItem.hasOwnProperty('thumbnail')"><img :src="currentItem.thumbnail"/></span>
      <a href="#" target="_blank"><span class="f--link-underlined--o">{{ currentItem.name }}</span></a>
      <input type="hidden" :name="name" :value="currentItem.id"/>
    </td>
    <td class=" fileItem__cell fileItem__cell--size" v-if="currentItem.hasOwnProperty('size')">{{ currentItem.size }}</td>
    <td class="fileItem__cell">
      <a17-button class="bucket__action" icon="close" @click="deleteItem()"><span v-svg symbol="close_icon"></span>
      </a17-button>
    </td>
  </tr>
</template>

<script>
  import Extensions from './Extensions'

  export default {
    name: 'a17FileItem',
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
      getSvgIconName: function () {
        const itemExt = this.currentItem.extension

        // Look on extensions key
        if (Extensions.hasOwnProperty(itemExt)) {
          return Extensions[itemExt].icon
        }

        // Look into second extensions level by key
        for (let ext in Extensions) {
          const index = Extensions[ext].extensions.findIndex((e) => e === itemExt)
          if (index > -1) {
            return Extensions[ext].icon
          }
        }

        // Default
        return 'gen'
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .fileItem {
    position: relative;
    display: flex;
    width: 100%;
    border-bottom: 1px solid $color__border--light;

    &:hover {
      .fileItem__cell {
        background-color: $color__f--bg;
      }
    }
  }

  .fileItem__cell {
    display: flex;
    align-items: center;
    padding: 15px;

    &:nth-child(2) {
      padding-left: 15px + 25px;
    }
  }

  .fileItem__cell--extension {
    padding-right: 5px;
  }

  .fileItem__cell--name {
    flex-grow: 1;

    a {
      color: $color__link;
      text-decoration: none;
      display: block;
      margin: -15px;
      padding: 15px;

      // &:hover {
      //   text-decoration: underline;
      // }
    }
  }

  .fileItem__cell--size {
    color: $color__text--light;
    text-transform: uppercase;
  }

  .fileItem__cell--drag {
    position: absolute;
    top: 0;
    padding: 0;
    height: 100%;
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
