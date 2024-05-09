<template>
  <div>
    <div class="dam-header">
      <div class="dam-header__title">
        <h1>{{ customTitle ? customTitle : title }}</h1>
      </div>
      <div class="dam-header__action">
        <div ref="form">
          <a17-filter @submit="submitSearch"> </a17-filter>
        </div>
        <a17-button variant="validate" size="small">{{
          $trans('dam.add-new', 'Add new')
        }}</a17-button>
        <div v-if="userData && usersManagement">
          <a17-dropdown ref="userDropdown" position="bottom-right" :offset="8">
            <button
              :aria-label="$trans('dam.user-nav')"
              @click.prevent="$refs.userDropdown.toggle()"
              class="dam-header__dropdown"
            >
              <a17-avatar
                v-if="userData.name || userData.thumbnail"
                :name="userData.name ?? null"
                :thumbnail="userData.thumbnail ?? null"
              />
              <span
                symbol="dropdown_module"
                class="icon icon--dropdown_module"
                aria-hidden="true"
              >
                <svg>
                  <title>dropdown_module</title>
                  <use
                    xmlns:xlink="http://www.w3.org/1999/xlink"
                    xlink:href="#icon--dropdown_module"
                  ></use>
                </svg>
              </span>
            </button>
            <div slot="dropdown__content">
              <a
                v-if="userData.can_access_user_management"
                :href="userData.user_management_route"
                >{{ $trans('nav.cms-users') }}</a
              >
              <a :href="userData.edit_profile_route">{{
                $trans('nav.profile')
              }}</a>
              <a href="#" data-logout-btn>{{ $trans('nav.logout') }}</a>
            </div>
          </a17-dropdown>
        </div>
      </div>
    </div>
    <div v-if="filters" class="dam-filters">
      <a17-button
        variant="ghost"
        class="dam-filters__mobileToggle"
        :aria-expanded="filtersModalOpen ? 'true' : 'false'"
        @click="openFiltersModal"
        ref="openFiltersBtn"
        >{{ $trans('dam.filter-by', 'Filter by')
        }}<span v-svg symbol="filter"></span
      ></a17-button>

      <div
        :class="[
          'dam-filters__modal',
          { 'dam-filters__modal--open': filtersModalOpen }
        ]"
        :role="isMobile ? 'dialog' : null"
        :aria-modal="isMobile ? 'true' : null"
        :aria-labelledby="isMobile ? 'filtersModalHeading' : null"
      >
        <div class="dam-filters__modal-header">
          <h3 class="f--regular" id="filtersModalHeading">
            {{ $trans('dam.filter-by', 'Filter by') }}
          </h3>
          <a17-button
            :aria-label="$trans('dam.close', 'Close')"
            variant="ghost"
            class="dam-filters__close"
            @click="closeFiltersModal"
            ref="closeFiltersBtn"
            ><span v-svg symbol="close"></span
          ></a17-button>
        </div>
        <div class="dam-filters__modal-content">
          <a17-dam-filter-dropdown
            v-for="(item, i) in filtersToDisplay"
            :key="i"
            :label="item.label"
            :items="item.items"
            :hasSearch="item.searchable"
            :advanced="item.advanced"
            ref="filterDropdown"
          >
          </a17-dam-filter-dropdown>

          <a17-button variant="ghost" @click="toggleAdvanced">
            <template v-if="!showAdvanced">{{
              $trans('dam.show-advanced', 'Show advanced')
            }}</template>
            <template v-else>{{
              $trans('dam.hide-advanced', 'hide advanced')
            }}</template>
          </a17-button>
        </div>
        <div class="dam-filters__modal-footer">
          <a17-button variant="ghost" @click="clearFilters">{{
            $trans('dam.clear', 'Clear')
          }}</a17-button>
          <a17-button variant="ghost" @click="applyFilters">{{
            $trans('dam.apply', 'Apply')
          }}</a17-button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'

  import A17Avatar from '@/components/Avatar.vue'
  import a17Filter from '@/components/Filter.vue'
  import A17DamFilterDropdown from '@/components/dam/DamFilterDropdown.vue'

  export default {
    name: 'A17Medialibrary',
    components: {
      'a17-avatar': A17Avatar,
      'a17-filter': a17Filter,
      'a17-dam-filter-dropdown': A17DamFilterDropdown
    },
    props: {
      customTitle: {
        type: String,
        default: null
      },
      currentUser: {
        type: String,
        default: null
      },
      filters: {
        type: Array,
        default: null
      },
      usersManagement: {
        type: Boolean,
        default: true
      }
    },
    data: function() {
      return {
        filtersModalOpen: false,
        isMobile: false,
        showAdvanced: false
      }
    },
    computed: {
      ...mapGetters(['fieldValueByName']),
      title() {
        // Get the title from the store
        const title = this.fieldValueByName(this.name)
          ? this.fieldValueByName(this.name)
          : ''
        const titleValue =
          typeof title === 'string' ? title : title[this.currentLocale.value]
        return titleValue || this.warningMessage
      },
      userData() {
        return JSON.parse(this.currentUser)
      },
      filtersToDisplay() {
        if (!this.isMobile && !this.showAdvanced) {
          return this.filters.filter(item => item.advanced !== true)
        }

        return this.filters
      }
    },
    watch: {
      filtersModalOpen(newValue) {
        if (newValue) {
          document.documentElement.classList.add('s--modal')
        } else {
          document.documentElement.classList.remove('s--modal')
        }
      }
    },
    methods: {
      openFiltersModal() {
        this.filtersModalOpen = true

        setTimeout(() => {
          this.$refs.closeFiltersBtn.$el.focus()
        }, 10)
      },
      closeFiltersModal() {
        this.filtersModalOpen = false

        setTimeout(() => {
          this.$refs.openFiltersBtn.$el.focus()
        }, 10)
      },
      applyFilters() {
        this.$refs.filterDropdown.forEach(el => {
          el.applyFilters()
        })
      },
      clearFilters() {
        this.$refs.filterDropdown.forEach(el => {
          el.clearFilters()
        })
      },
      submitSearch(formData) {},
      getMediaQuery() {
        if (typeof window !== 'undefined') {
          const mq = getComputedStyle(document.documentElement)
            .getPropertyValue('--breakpoint')
            .trim()
            .replace(/"/g, '')
          this.isMobile = mq === 'xsmall' || mq === 'small'
        }
      },
      toggleAdvanced() {
        this.showAdvanced = !this.showAdvanced
      }
    },
    mounted() {
      this.getMediaQuery()

      window.addEventListener('resize', this.getMediaQuery)
    },
    beforeDestroy() {
      window.removeEventListener('resize', this.getMediaQuery)
    }
  }
</script>

<style lang="scss" scoped>
  .dam-header {
    display: flex;
    flex-flow: row;
    width: 100%;
    background: $color__border;
    border-bottom: 1px solid $color__border;
    padding: rem-calc(20);
    justify-content: space-between;
    align-items: center;

    @include breakpoint('medium+') {
      padding: 0 rem-calc(20);
    }
  }

  .dam-header__title {
    font-weight: 600;

    h1 {
      font-weight: 600;
    }
  }

  .dam-header__action {
    display: none;
    flex-direction: row;
    gap: rem-calc(20);
    align-items: center;

    @include breakpoint('medium+') {
      display: flex;
    }
  }

  .dam-header__dropdown {
    display: flex;
    background: none;
    border: none;
    padding: 0;
    align-items: center;
    gap: rem-calc(8);
    cursor: pointer;
    transition: filter 300ms ease;

    &:hover {
      filter: brightness(0.9);
    }
  }

  .dam-filters {
    padding: rem-calc(16);
    display: flex;
    flex-flow: row wrap;
    gap: rem-calc(20);
    background: $color__black--5;
    border-bottom: 1px solid $color__black--10;

    @include breakpoint('medium+') {
      padding: rem-calc(20);
    }
  }

  .dam-filters__mobileToggle {
    display: flex;
    flex-flow: row;
    align-items: center;
    border: none;
    background: $color__border;

    .icon {
      margin-left: rem-calc(10);
    }

    @include breakpoint('medium+') {
      display: none;
    }
  }

  .dam-filters__modal {
    position: fixed;
    inset: 0;
    background: $color__light;
    z-index: 100;
    height: 100svh;
    overflow-y: auto;
    display: flex;
    flex-flow: column;
    opacity: 0;
    visibility: hidden;
    transition: all 300ms ease;

    &--open {
      opacity: 1;
      visibility: visible;
    }

    @include breakpoint('medium+') {
      position: relative;
      inset: unset;
      background: none;
      z-index: unset;
      height: auto;
      overflow: visible;
      display: block;
      opacity: 1;
      visibility: visible;
      transition: none;
    }
  }

  .dam-filters__modal-header {
    display: flex;
    flex-flow: row;
    justify-content: space-between;
    height: rem-calc(68);
    padding: 0 rem-calc(16);
    align-items: center;
    background: $color__border;
    border-bottom: 1px solid $color__modal--header;
    flex-shrink: 0;

    .f--regular {
      font-weight: 600;
    }

    .button {
      padding: 0;
      border: none;
    }

    @include breakpoint('medium+') {
      display: none;
    }
  }

  .dam-filters__modal-content {
    background: $color__light;
    overflow-y: auto;
    height: 100%;

    > .button {
      @include breakpoint('small-') {
        display: none;
      }
    }

    @include breakpoint('medium+') {
      background: none;
      display: flex;
      flex-flow: row wrap;
      gap: rem-calc(20);
      overflow-y: visible;
      height: auto;
    }
  }

  .dam-filters__modal-footer {
    display: flex;
    flex-flow: row;
    justify-content: flex-end;
    height: rem-calc(68);
    padding: 0 rem-calc(16);
    align-items: center;
    background: $color__border;
    border-top: 1px solid $color__modal--header;
    gap: rem-calc(16);
    margin-top: auto;
    flex-shrink: 0;

    @include breakpoint('medium+') {
      display: none;
    }
  }
</style>
