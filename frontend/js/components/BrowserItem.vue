<template>
  <tr class="browserItem">
    <td v-if="draggable && max > 1" class="browserItem__cell browserItem__cell--drag">
      <div class="drag__handle--drag"></div>
    </td>
    <td class="browserItem__cell browserItem__cell--thumb" v-if="currentItem.hasOwnProperty('thumbnail')">
      <a href="#" target="_blank"><img :src="currentItem.thumbnail" /></a>
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
    <td class="browserItem__cell browserItem__cell--icon">
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
      max: {
        type: Number,
        default: 10
      },
      showType: {
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
      currentItem: function () {
        return this.item
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
  @import '~styles/setup/_mixins-colors-vars.scss';

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
    padding: 26px 15px 26px 0;
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

  .browserItem__cell--type {
    text-transform: capitalize;
    width: 150px;
    span {
      display: inline-block;
      width: 150px;
      white-space: nowrap;
      overflow: hidden;
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
</style>
