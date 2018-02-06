<template>
  <div class="search">
    <a17-button type="button" class="search__toggle" @click="toggleSearch">
      <span v-svg symbol="search" v-show="!open"></span>
      <span v-svg symbol="close_modal" v-show="open"></span>
    </a17-button>
    <transition name="search-fade">
      <div class="search__overlay" v-show="open">
        <div class="container search__container">
          <input type="search" class="form__input search__input" name="search" :value="searchValue" :placeholder="placeholder" @input="onSearchInput" />
          <span v-svg symbol="search"></span>
          <div class="search__results" v-show="searchValue">
            <ul>
              <li class="search__results__item" v-for="(result, index) in searchResults" :key="result">
                {{result}}
              </li>
              <li class="search__results__no-result" v-show="searchValue && !searchResults.length">
                No results found.
              </li>
            </ul>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
  const html = document.documentElement
  let htmlClasses = ['s--search', 's--overlay']

  export default {
    name: 'A17Search',
    props: {
      placeholder: {
        type: String,
        default: 'Search everything…'
      },
      initialSearchValue: {
        type: String,
        default: ''
      }
    },
    data: function () {
      return {
        open: false,
        searchValue: this.initialSearchValue,
        searchResults: []
      }
    },
    watch: {
      initialSearchValue: function () {
        this.searchValue = this.initialSearchValue
      }
    },
    computed: {
    },
    methods: {
      toggleSearch: function () {
        this.open = !this.open
        htmlClasses.forEach((klass) => {
          html.classList.toggle(klass)
        })
      },
      onSearchInput: function (event) {
        this.searchValue = event.target.value
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .button.search__toggle {
    padding-left: 26px;
    padding-right: 0;

    .icon {
      position: relative;
      top: 3px;
      width: 20px;
      height: 20px;
      color: $color__text--light;

      &.icon--search {
        top: 5px;
      }
    }

    &:hover .icon {
      color: $color__background;
    }
  }

  .search__overlay {
    position: fixed;
    top: 60px;
    left: 0;
    width: 100vw;
    height: calc(100vh - 60px);
    background: rgba($color__overlay--header, 0.5);
    z-index: $zindex__search;
    opacity: 1;
  }

  .search-fade-enter-active,
  .search-fade-leave-active {
    transition: opacity 0.14s $bezier__bounce;
  }

  .search-fade-enter,
  .search-fade-leave-to {
    opacity: 0;
  }

  .search__container {
    display: block;
    position: relative;
    padding-top: 40px;
  }

  .form__input.search__input {
    display: block;
    padding-left: 45px;
    color: $color__text;
    border: 0;
    box-shadow: none;
    font-size: 17px;
    line-height: 46px;
    @include placeholder() {
      color: $color__text--light;
    }
  }

  .search__container .icon--search {
    position: absolute;
    top: 53px;
    left: 65px;
    width: 24px;
    height: 24px;
    color: $color__overlay--header;

    svg {
      width: 24px;
      height: 24px;
    }
  }

  $itemHeight: 91px;

  .search__results {
    margin-top: 10px;
    max-height: ($itemHeight * 3);
    background: $color__background;
    border-radius: 2px;
    overflow: auto;
  }

  .search__results__item {
    height: $itemHeight;
    border-bottom: 1px solid $color__border--light;
    cursor: pointer;

    &:last-child {
      border-bottom: 0;
    }

    &:hover {
      background: $color__ultralight;
    }
  }

  .search__results__no-result {
    padding: 0 30px;
    height: 70px;
    background: $color__border;
    border-radius: 2px;
    line-height: 70px;
  }
</style>
