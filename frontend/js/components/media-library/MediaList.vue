<template>
  <div class="itemlist">
    <div class="itemlist__item" v-for="(item, index) in listItems" :key="item.id" :class="{ 's--picked': isSelected(item.id) }" @click.prevent="toggleSelection(item.id)">
      <span class="itemlist__button">
        <a17-checkbox name="item_list" :value="item.id" :initialValue="checkedItems" theme="bold"></a17-checkbox>
        {{ item.name }}
      </span>
      <!-- <span>{{ item.size }}</span> -->
    </div>
  </div>
</template>

<script>
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
      }
    },
    methods: {
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
  @import "../../../scss/setup/variables.scss";
  @import "../../../scss/setup/colors.scss";
  @import "../../../scss/setup/mixins.scss";

  .itemlist {
    display:block;
    padding: 10px;
  }

  .itemlist__item {
    display:flex;
    overflow: hidden;
    background:white;
    position:relative;
    // color:$color__link;
    border:1px solid $color__border--light;
    margin-bottom: -1px;
    cursor:pointer;
    padding:20px;

    &:hover {
      background-color: $color__f--bg;
    }
  }

  .itemlist__item:first-child {
    border-top-left-radius: 2px;
    border-top-right-radius: 2px;
  }

  .itemlist__button > *:first-child {
    display: inline-block;
    vertical-align: top;
  }

  .itemlist__button {
    flex-grow: 1;
  }
</style>
