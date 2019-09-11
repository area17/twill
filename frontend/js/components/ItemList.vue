<template>
  <div class="itemlist">
    <table class="itemlist__table">
      <tbody>
        <tr class="itemlist__row" v-for="(item, index) in itemsLoading" :key="item.id" >
          <td class="itemlist__cell itemlist__cell--loading" :class="{ 'itemlist__cell--error' : item.error }" :colspan="columnsNumber">
            <span class="itemlist__progress" v-if="!item.error" ><span class="itemlist__progressBar" :style="loadingProgress(index)"></span></span>
            <span class="itemlist__progressError" v-else>Upload Error</span>
          </td>
        </tr>
        <tr class="itemlist__row"
            v-for="item in items"
            :key="`${item.endpointType}_${item.id}`"
            :class="{ 's--picked': isSelected(item, keysToCheck)}"
            @click.exact.prevent="toggleSelection(item)"
            @click.shift.exact.prevent="shiftToggleSelection(item)">
          <td class="itemlist__cell itemlist__cell--btn" v-if="item.hasOwnProperty('id')">
            <a17-checkbox name="item_list" :value="item.endpointType + '_' + item.id" :initialValue="checkedItems" theme="bold"/>
          </td>
          <td class="itemlist__cell itemlist__cell--thumb" v-if="item.hasOwnProperty('thumbnail')">
            <img :src="item.thumbnail" />
          </td>
          <td class="itemlist__cell itemlist__cell--name" v-if="item.hasOwnProperty('name')">
            <div v-if="item.hasOwnProperty('renderHtml')" v-html="item.name"></div>
            <div v-else>{{ item.name }}</div>
          </td>
          <td class="itemlist__cell" v-for="extraColumn in extraColumns" :class="rowClass(extraColumn)">
            <template v-if="extraColumn === 'size'">{{ item[extraColumn] | uppercase}}</template>
            <template v-else>{{ item[extraColumn] }}</template>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
  import a17VueFilters from '@/utils/filters.js'
  import mediaItemsMixin from '@/mixins/mediaLibrary/mediaItems'

  export default {
    name: 'A17Itemlist',
    props: {
      keysToCheck: {
        type: Array,
        default: () => ['id']
      }
    },
    mixins: [mediaItemsMixin],
    filters: a17VueFilters,
    computed: {
      columnsNumber: function () {
        if (!this.items.length) return 0

        let numb = this.extraColumns.length

        const firstItem = this.items[0]

        if (firstItem.hasOwnProperty('id')) numb++
        if (firstItem.hasOwnProperty('name')) numb++
        if (firstItem.hasOwnProperty('thumbnail')) numb++

        return numb
      },
      extraColumns: function () {
        if (!this.items.length) return []

        const firstItem = this.items[0]

        return Object.keys(firstItem).filter(key => { // exclude columns here
          return ![
            'id', 'name', 'thumbnail', 'src', 'original', 'edit',
            'crop', 'deleteUrl', 'updateUrl', 'updateBulkUrl', 'deleteBulkUrl', 'endpointType'
          ].includes(key) && typeof firstItem[key] === 'string' // only strings
        })
      },
      checkedItems: function () {
        let checkItemsIds = []

        if (this.selectedItems.length) {
          this.selectedItems.forEach(function (item) {
            checkItemsIds.push(item.endpointType + '_' + item.id)
          })
        }

        return checkItemsIds
      }
    },
    methods: {
      rowClass: function (item) {
        return 'itemlist__cell--' + item
      },
      loadingProgress: function (index) {
        return {
          'width': this.itemsLoading[index].progress ? this.itemsLoading[index].progress + '%' : '0%'
        }
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .itemlist {
    padding: 10px;
    overflow:hidden;
  }

  .itemlist__table {
    width: 100%;
    border-collapse: collapse;
    border-spacing: 0;
    // table-layout: fixed;
    white-space: nowrap;
  }

  .itemlist__table {
    th, td {
      border-top:1px solid $color__border--light;
      border-bottom:1px solid $color__border--light;
      vertical-align: top;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    td:first-child {
      border-left:1px solid $color__border--light;
    }

    td:last-child {
      border-right:1px solid $color__border--light;
    }
  }

  .itemlist__row {
    overflow: hidden;
    background:white;
    position:relative;

    cursor:pointer;

    &:hover {
      background-color: $color__f--bg;
    }
  }

  .itemlist__row:first-child {
    border-top-left-radius: 2px;
    border-top-right-radius: 2px;
  }

  .itemlist__cell {
    padding:20px 10px;
    white-space: nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
    vertical-align:middle;

    &:first-child {
      padding-left:20px;
    }

    &:last-child {
      padding-left:20px;
    }
  }

  .itemlist__cell > *:first-child {
    display: block;
  }

  .itemlist__cell--btn {
    width:1px;
    // width:15px + 20px + 10px;
  }

  .itemlist__cell--type {
    width: 150px;
  }

  // .itemlist__cell--name {
  //   width:40%;
  // }

  .itemlist__cell--thumb {
    width:50px;

    img {
      display:block;
      width:50px;
      height:auto;
      background: $color__border--light;
    }
  }

  .itemlist__cell--loading {
    height: 4px;
  }

  .itemlist__cell--error {
    height:auto;
  }

  .itemlist__progress {
    height: 4px;
    width: 15%;
    min-width:120px;
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
