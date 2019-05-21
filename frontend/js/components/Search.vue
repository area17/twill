<template>
  <div class="container search" :class="{ 'search--dashboard' : type === 'dashboard' }">
    <transition name="fade_search-overlay" v-if="type === 'dashboard'">
      <div class="search__overlay" v-show="readyToShowResult" @click="toggleSearch"></div>
    </transition>
    <div class="search__input">
      <input type="search" class="form__input" ref="search" name="search" autocomplete="off" :placeholder="placeholder" @input="onSearchInput" />
      <span v-svg symbol="search"></span>
    </div>
    <transition name="fade_search-overlay">
      <div class="search__results" v-show="readyToShowResult">
        <ul>
          <li v-for="item in searchResults" :key="item.id">
            <a :href="item.href" class="search__result">
              <div class="search__cell search__cell--thumb hide--xsmall">
                <figure class="search__thumb">
                  <img :src="item.thumbnail" />
                </figure>
              </div>
              <div class="search__cell search__cell--pubstate hide--xsmall">
                <span class="search__pubstate" :class="{'search__pubstate--live': item.published }"></span>
              </div>
              <div class="search__cell">
                <span class="search__title">{{ item.title }}</span>
                <p class="f--note">
                  {{ item.activity }} <timeago :auto-update="1" :since="new Date(item.date)"></timeago> by {{ item.author }}
                  <span class="search__type">{{ item.type }}</span>
                </p>
              </div>
            </a>
          </li>
          <li class="search__no-result" v-show="loading">
            Loading…
          </li>
          <li class="search__no-result" v-show="readyToShowResult && !searchResults.length && !loading">
            No results found.
          </li>
        </ul>
      </div>
    </transition>
  </div>
</template>

<script>
  import debounce from 'lodash/debounce'
  import axios from 'axios'
  import htmlClasses from '@/utils/htmlClasses'
  const html = document.documentElement
  const htmlSearchClasses = [ htmlClasses.search, htmlClasses.overlay ]
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
      },
      type: {
        type: String,
        default: 'header' // Enum : [ 'header', 'dashboard' ]
      }
    },
    data: function () {
      return {
        searchValue: '',
        loading: false,
        readyToShowResult: false,
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
    methods: {
      toggleSearch: function () {
        htmlSearchClasses.forEach((klass) => {
          html.classList.toggle(klass)
        })
        if (this.open) {
          document.addEventListener('keydown', this.handleKeyDown, false)
        } else {
          this.$refs.search.blur()
          this.searchResults = []
          this.searchValue = ''
          this.readyToShowResult = false
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

        this.readyToShowResult = true

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
        if (this.searchValue && this.searchValue.length > 2) {
          if (this.type === 'dashboard') {
            htmlSearchClasses.forEach((klass) => {
              html.classList.add(klass)
            })
          }
          this.fetchSearchResults()
        } else {
          if (this.type === 'dashboard') {
            htmlSearchClasses.forEach((klass) => {
              html.classList.remove(klass)
            })
          }
          this.readyToShowResult = false
          this.searchResults = []
          this.setLastFocusElement()
        }
      }, 300)
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .search {
    display: block;
    position: relative;
    padding-top: 40px;
  }

  .search--dashboard {
    padding-top: 0;
    padding-bottom: 25px;
    background: $color__overlay--header;
  }

  .search__overlay {
    position: fixed;
    top: 60px;
    left: 0;
    right: 0;
    width: 100%;
    bottom:0;
    background: rgba($color__overlay--header, 0.9);
    z-index: $zindex__search - 1;
  }

  .search__input {
    position: relative;
    z-index: $zindex__search;

    .form__input {
      display: block;
      padding-left: 45px;
      border: 0;
      box-shadow: none;
      font-size: 17px;
      line-height: 46px;
    }
  }

  /* Dashboard input */
  .search--dashboard {
    .icon--search {
      color: $color__header--light;
    }

    .search__input .form__input {
      background-color: $color__header--sep;
      color:$color__black--40;

      @include placeholder() {
        color:$color__black--40;
      }
    }

    .search__input .form__input:focus {
      background-color: $color__f--bg;
      color:$color__text--forms;
    }

    .search__input .form__input:focus + .icon--search {
      color: $color__icons;
    }
  }

  .icon--search {
    position: absolute;
    top: 13px;
    left: 15px;
    width: 24px;
    height: 24px;
    color: $color__icons;
    pointer-events: none;
    transition: color 0.12s ease-in-out;

    svg {
      width: 24px;
      height: 24px;
    }
  }

  .form__input:focus + .icon--search {
    color: $color__text;
  }

  $itemHeight: 91px;

  .search__results {
    position: relative;
    margin-top: 10px;
    max-height: ($itemHeight * 3);
    background: $color__background;
    border-radius: 2px;
    box-shadow: 0 0 2px rgba($color__overlay--header, 0.3);
    overflow: auto;
    z-index: $zindex__search;
  }

  .search--dashboard .search__results {
    position: absolute;
    @each $name, $point in $breakpoints {
      @include breakpoint('#{$name}') {
        width: calc(100% - #{map-get($outer-gutters, $name) * 2});
      }
    }
  }

  .search__no-result {
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

  .search__title {
    display: inline-block;
    margin-bottom: 5px;
    color: $color__link;
  }

  .search__type {
    &::before {
      content: "•";
      display: inline;
      padding: 0 8px 0 5px;
      font-size: 11px;
      position: relative;
      top: -2px;
    }
  }

  .search__thumb {
    img {
      display: block;
      width: 50px;
      min-height: 50px;
      background: $color__border--light;
    }
  }

  .search__pubstate {
    border-radius: 50%;
    height: 9px;
    width: 9px;
    display: block;
    background: $color__fborder;
  }

  .search__pubstate--live {
    background: $color__publish;
  }
</style>
