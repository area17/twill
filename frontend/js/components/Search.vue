<template>
  <div class="container search__container">
    <div class="search__overlay" v-show="searchValue"></div>
    <div class="search__input">
      <input type="search" class="form__input" ref="search" name="search" autocomplete="off" :placeholder="placeholder" @input="onSearchInput" />
      <span v-svg symbol="search"></span>
    </div>
    <div class="search__results" v-show="searchValue">
      <ul>
        <li v-for="(item, index) in searchResults" :key="item.id">
          <a :href="item.href" class="search__result">
            <div class="search__cell search__cell--thumb">
              <figure class="search__result__thumb">
                <img :src="item.thumbnail" />
              </figure>
            </div>
            <div class="search__cell search__cell--pubstate">
              <span class="search__result__pubstate" :class="{'search__result__pubstate--live': item.published }"></span>
            </div>
            <div class="search__cell">
              <span class="search__result__title">{{ item.title }}</span>
              <p class="f--note">
                {{ item.activity }} <timeago :auto-update="1" :since="new Date(item.date)"></timeago> by {{ item.author }}
                <span class="search__result__type">{{ item.type }}</span>
              </p>
            </div>
          </a>
        </li>
        <li class="search__results__no-result" v-show="loading">
          Loading…
        </li>
        <li class="search__results__no-result" v-show="searchValue && !searchResults.length && !loading">
          No results found.
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
  import debounce from 'lodash/debounce'
  import axios from 'axios'
  let CancelToken = axios.CancelToken
  let source = CancelToken.source()
  let firstFocusableEl = document.querySelector('.header .header__title > a')
  let lastFocusableEl

  export default {
    name: 'A17Search',
    props: {
      open: {
        type: Boolean,
        default: false
      },
      opened: {
        type: Boolean,
        default: false
      },
      placeholder: {
        type: String,
        default: 'Search everything…'
      },
      endpoint: {
        type: String,
        default: null
      }
    },
    data: function () {
      return {
        searchValue: null,
        loading: false,
        searchResults: []
      }
    },
    watch: {
      open: function () {
        this.toggleSearch()
      },
      opened: function () {
        if (this.opened) {
          lastFocusableEl = this.$refs.search
          lastFocusableEl.focus()
        }
      }
    },
    computed: {
    },
    methods: {
      toggleSearch: function () {
        if (this.open) {
          document.addEventListener('keydown', this.handleKeyDown, false)
        } else {
          this.$refs.search.blur()
          this.searchResults = []
          this.searchValue = null
          document.removeEventListener('keydown', this.handleKeyDown, false)
        }
      },
      handleKeyDown: function (event) {
        if (event.keyCode && event.keyCode === 9) {
          if (event.shiftKey) {
            // backwards
            if (document.activeElement.isEqualNode(firstFocusableEl)) {
              lastFocusableEl.focus()
              event.preventDefault()
            }
          } else {
            // onwards
            if (document.activeElement.isEqualNode(lastFocusableEl)) {
              firstFocusableEl.focus()
              event.preventDefault()
            }
          }
        }
      },
      setLastFocusElement: function () {
        let resultsLength = this.searchResults.length
        if (resultsLength) {
          setTimeout(function () {
            lastFocusableEl = document.querySelectorAll('.search__result')[resultsLength - 1]
          }, 1)
        } else {
          lastFocusableEl = this.$refs.search
        }
      },
      fetchSearchResults: function () {
        let self = this
        let data = {
          'search': this.searchValue
        }

        if (this.loading) {
          source.cancel()
          source = CancelToken.source()
        } else {
          this.loading = true
        }

        this.$http.get(this.endpoint, {
          params: data,
          cancelToken: source.token
        }).then(function (resp) {
          self.searchResults = resp.data
          self.loading = false
          self.setLastFocusElement()
        }, function (resp) {
          // handle error
          if (!axios.isCancel(resp)) {
            self.loading = false
          }
        })
      },
      onSearchInput: debounce(function (event) {
        this.searchValue = event.target.value
        if (this.searchValue && this.searchValue !== '') {
          this.fetchSearchResults()
        } else {
          this.searchResults = []
          this.setLastFocusElement()
        }
      }, 300)
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .header__search .search__overlay {
    display: none !important;
  }

  .header__search .search__container {
    display: block;
    position: relative;
    padding-top: 40px;
  }

  .dashboard__search .search__container {
    position: relative;
    padding-bottom: 25px;
    background: $color__overlay--header;
  }

  .search__input {
    position: relative;
    z-index: $zindex__search;

    .form__input {
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
  }

  .search__container .icon--search {
    position: absolute;
    top: 13px;
    left: 15px;
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
    box-shadow: 0 0 2px rgba($color__overlay--header, 0.3);
    overflow: auto;
  }

  .dashboard__search .search__results {
    position: absolute;
    z-index: $zindex__search;
    @each $name, $point in $breakpoints {
      @include breakpoint('#{$name}') {
        width: calc(100% - #{map-get($outer-gutters, $name) * 2});
      }
    }
  }

  .search__results__no-result {
    padding: 0 30px;
    height: 70px;
    background: $color__border;
    border-radius: 2px;
    line-height: 70px;
  }

  .search__result {
    display: flex;
    min-height: $itemHeight;
    padding: 20px;
    border-bottom: 1px solid $color__border--light;
    cursor: pointer;
    flex-direction: row;
    justify-content: flex-start;
    outline: none;
    text-decoration: none;

    li:last-child & {
      border-bottom: 0;
    }

    &:hover,
    &:focus {
      background: $color__verylight;
    }
  }

  .search__cell {
    vertical-align: top;
    padding-top: 4px;
  }

  .search__cell--thumb {
    width: 50px;
    padding-top: 0;
  }

  .search__cell--pubstate {
    width: 38px;
    padding: 10px 15px;
  }

  .search__result__title {
    display: inline-block;
    margin-bottom: 5px;
    color: $color__link;
  }

  .search__result__type {
    &::before {
      content: "•";
      display: inline;
      padding: 0 8px 0 5px;
      font-size: 11px;
      position: relative;
      top: -2px;
    }
  }

  .search__result__thumb {
    img {
      display: block;
      width: 50px;
      min-height: 50px;
      background: $color__border--light;
    }
  }

  .search__result__pubstate {
    border-radius: 50%;
    height: 9px;
    width: 9px;
    display: block;
    background: $color__fborder;
  }

  .search__result__pubstate--live {
    background: $color__publish;
  }
</style>
