<template>
  <tr class="browserItem">
    <td v-if="draggable && max > 1" class="browserItem__cell browserItem__cell--drag">
      <div :class="dragClasses"></div>
    </td>
    <td :class="thumbnailClasses" v-if="hasThumbnail">
      <template v-if="isUser">
        <a17-avatar
          :name="currentItem.name"
          :thumbnail="currentItem.thumbnail"
        />
      </template>
      <template v-else>
        <a href="#" target="_blank"><img :src="currentItem.thumbnail" /></a>
      </template>
    </td>
    <td class="browserItem__cell browserItem__cell--name">
      <a :href="currentItem.edit" target="_blank">
        <span class="f--link-underlined--o" v-if="currentItem.hasOwnProperty('renderHtml')" v-html="currentItem.name"></span>
        <span class="f--link-underlined--o" v-else>{{ currentItem.name }}</span>
      </a>
      <input type="hidden" :name="name" :value="currentItem.id"/>
    </td>
    <td class="browserItem__cell browserItem__cell--type" v-if="currentItem.hasOwnProperty('endpointType') && showType">
      <span>{{ currentItem.endpointType }}</span>
    </td>
    <td class="browserItem__cell browserItem__cell--icon" v-if="deletable">
      <a17-button class="bucket__action" v-if="!disabled" icon="close" @click="deleteItem()"><span v-svg symbol="close_icon"></span></a17-button>
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
      max: {
        type: Number,
        default: 10
      },
      showType: {
        type: Boolean,
        default: false
      },
      disabled: {
        type: Boolean,
        default: false
      }
    },
    data: function () {
      return {
        handle: '.item__handle' // Drag handle override
      }
    },
    computed: {
      hasThumbnail: function () {
        return Boolean(this.currentItem.hasOwnProperty('thumbnail'))
      },
      hasLargeThumbnail: function () {
        return this.hasThumbnail && !this.isUser
      },
      isUser: function () {
        return Boolean(this.currentItem.endpointType === 'users')
      },
      dragClasses: function () {
        return [
          'drag__handle--drag',
          !this.hasLargeThumbnail ? 'drag__handle--drag-small' : ''
        ]
      },
      thumbnailClasses: function () {
        return [
          'browserItem__cell',
          'browserItem__cell--thumb',
          this.isUser ? 'browserItem__cell--thumb-avatar' : ''
        ]
      },
      currentItem: function () {
        return this.item
      },
      deletable: function () {
        return !this.currentItem.hasOwnProperty('deletable') || this.currentItem.deletable === true
      }
    },
    methods: {
      deleteItem: function () {
        this.$emit('delete')
      }
    }
  }
</script>

<style lang="scss" scoped>

  .browserItem {
    width:100%;
    border-bottom: 1px solid $color__border--light;

    &:hover {
      .browserItem__cell {
        background-color: $color__f--bg;
      }
    }

    &:last-child {
      border-bottom: 0 none;
    }
  }

  .browserItem__cell {
    padding: 14px 15px 14px 0; // 59px = 14 + 14 + 31
    vertical-align: middle;
  }

  .browserItem__cell--name {
    a {
      color:$color__link;
      text-decoration: none;
    }
  }

  .browserItem__cell--thumb {
    padding-top: 16px;
    padding-bottom: 16px;
    padding-left:15px;
    width:50px;

    a {
      color:$color__link;
      text-decoration: none;
      display:block;
    }

    img {
      display:block;
      width: 50px;
      min-height: 50px;
      background: $color__border--light;
      height: auto;
    }
  }

  .browserItem__cell--thumb-avatar {
    padding-top: 12px;
    padding-bottom: 12px;
    width:36px;

    img {
      width: 36px;
      min-height: 36px;
    }
  }

  .browserItem__cell--type {
    text-transform: capitalize;
    width: #{150px + 15px};

    span {
      display: inline-block;
      width: 150px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
  }

  .browserItem__cell--name:first-child,
  .browserItem__cell--drag + .browserItem__cell--name {
    padding-left: 15px;

    @include breakpoint('small+') {
      padding-left: 29px;
    }
  }

  .browserItem__cell--drag {
    padding:0;
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
    background: dragGrid__bg($color__drag_bg--hover);
  }

  .browserItem__cell--icon {
    width:1px;

    button {
      display: block;
    }
  }

  .drag__handle--drag {
    position: relative;
    width: 10px;
    height: 42px;
    margin-left:auto;
    margin-right:auto;
    transition: background 250ms ease;
    @include dragGrid($color__drag, $color__drag_bg);
  }

  .drag__handle--drag-small {
    height: 22px;
  }
</style>
