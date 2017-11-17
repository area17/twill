<template>
  <div class="itemlist">
    <div class="itemlist__row" v-for="(item, index) in listItemsLoading" :key="item.id" >
      <span class="itemlist__cell itemlist__cell--loading">
        <span class="itemlist__progress" v-if="!item.error" ><span class="itemlist__progressBar" :style="loadingProgress(index)"></span></span>
        <span class="itemlist__progressError" v-else>Upload Error</span>
      </span>
    </div>
    <div class="itemlist__row" v-for="(item, index) in listItems" :key="item.id" :class="{ 's--picked': isSelected(item.id) }" @click.prevent="toggleSelection(item.id)">
      <span class="itemlist__cell itemlist__cell--btn">
        <a17-checkbox name="item_list" :value="item.id" :initialValue="checkedItems" theme="bold"></a17-checkbox>
      </span>
      <span class="itemlist__cell itemlist__cell--thumb" v-if="item.hasOwnProperty('thumbnail')"><img :src="item.thumbnail" /></span>
      <span class="itemlist__cell itemlist__cell--name">{{ item.name }}</span>
      <span v-if="item.hasOwnProperty('size')">{{ item.size }}</span>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'

  export default {
    name: 'A17Itemlist',
    props: {
      items: {
        type: Array,
        default: function () {
          return []
        }
      },
      selectedItems: {
        type: Array,
        default: function () {
          return []
        }
      }
    },
    data: function () {
      return {
        listItems: this.items
      }
    },
    computed: {
      checkedItems: function () {
        let checkItemsIds = []

        if (this.selectedItems.length) {
          this.selectedItems.forEach(function (item) {
            checkItemsIds.push(item.id)
          })
        }

        return checkItemsIds
      },
      ...mapState({
        listItemsLoading: state => state.mediaLibrary.loading
      })
    },
    methods: {
      loadingProgress: function (index) {
        return {
          'width': this.listItemsLoading[index].progress ? this.listItemsLoading[index].progress + '%' : '0%'
        }
      },
      isSelected: function (id) {
        const result = this.selectedItems.filter(function (item) {
          return item.id === id
        })

        return result.length > 0
      },
      toggleSelection: function (id) {
        this.$emit('change', id)
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .itemlist {
    display:block;
    padding: 10px;
  }

  .itemlist__row {
    display:flex;
    overflow: hidden;
    background:white;
    position:relative;
    // color:$color__link;
    border:1px solid $color__border--light;
    margin-bottom: -1px;
    cursor:pointer;
    padding:20px 15px 20px 0;

    &:hover {
      background-color: $color__f--bg;
    }
  }

  .itemlist__row:first-child {
    border-top-left-radius: 2px;
    border-top-right-radius: 2px;
  }

  .itemlist__cell {
    margin-left:15px;
    white-space: nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
  }

  .itemlist__cell > *:first-child {
    display: inline-block;
    vertical-align: top;
  }

  .itemlist__cell--btn {
    width:15px;
  }

  .itemlist__cell--name {
    flex-grow: 1;
  }

  .itemlist__cell--thumb {
    display:inline-block;
    max-width:50px;

    img {
      display:block;
      max-width:50px;
      height:auto;
    }
  }

  .itemlist__cell--loading {
    flex-grow: 1;
    height: 4px;
  }

  .itemlist__progress {
    height: 4px;
    width: 100%;
    background: $color__border--focus;
    border-radius: 2px;
    position: relative;
  }

  .itemlist__progressBar {
    position: absolute;
    top:0;
    left:0;
    width: 100%;
    border-radius: 2px;
    height:4px;
    background: $color__action;
  }

  .itemlist__progressError {
    color:$color__error;
  }
</style>
