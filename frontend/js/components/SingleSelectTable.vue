<template>
  <div class="multiselectorTable">
    <div class="multiselectorTable__filter" v-if="searchable">
      <a17-filter @submit="submitFilter"/>
    </div>
    <div class="multiselectorTable__items">
      <slot />

      <div class="multiselectorTable__empty" v-if="empty"  :style="emptyStyle">
        <h4>{{ emptyMessage }}</h4>
      </div>
    </div>
  </div>
</template>

<script>
  import a17Filter from './Filter.vue'

  export default {
    name: 'A17SingleselectTable',
    components: {
      'a17-filter': a17Filter
    },
    props: {
      searchable: {
        type: Boolean,
        default: true
      },
      emptyMessage: {
        type: String,
        default: 'No results found. Please try another search'
      }
    },
    data: function () {
      return {
        empty: false,
        emptyHeight: 120
      }
    },
    computed: {
      emptyStyle: function () {
        return { 'height': this.emptyHeight + 'px' }
      }
    },
    methods: {
      submitFilter (formData) {
        // filter the list with an ol'good querySelectorAll
        const allItems = this.$el.querySelectorAll('[data-singleselect-table-filterable]')
        this.emptyHeight = Math.max(120, allItems[0].parentElement.offsetHeight)

        if (allItems) {
          allItems.forEach((itemEl) => {
            const filterClass = 'multiselectorTable__item--hidden'
            const filterValue = itemEl.getAttribute('data-singleselect-table-filterable')

            if (formData.search) {
              this.empty = true
              const query = formData.search
              if (filterValue.toUpperCase().includes(query.toUpperCase())) {
                itemEl.classList.remove(filterClass)
                this.empty = false
              } else {
                itemEl.classList.add(filterClass)
              }
            } else {
              itemEl.classList.remove(filterClass)
              this.empty = false
            }
          })
        }
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .multiselectorTable__items {
    border:1px solid $color__border;
    margin-top: 20px;
  }

  .multiselectorTable__filter {
    background:$color__border--light;
    margin-left:-20px;
    margin-right:-20px;
    padding-left: 20px;
    padding-right: 20px;
  }

  .multiselectorTable__item {
    padding: 13.5px 20px;
    border-bottom: 1px solid $color__border--light;

    &:last-child {
      border-bottom: 0 none;
    }

    &.multiselectorTable__item--hidden {
      display : none;
    }
  }

  .multiselectorTable__empty {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 120px;
    padding: 15px 20px;

    h4 {
      @include font-medium();
      font-weight: 400;
      color: $color__f--text;
    }
  }
</style>
