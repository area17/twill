<template>
  <div>
    <div class="dam-header">
      <div class="dam-header__title">
        <h1 v-if="!showBreadcrumb">
          <button v-if="editUrl" @click.prevent="openEditModal">
            <span class="f--underlined--o">{{ customTitle ? customTitle : title }}</span> <span v-svg symbol="edit"></span>
          </button>
          <span v-else>{{ customTitle ? customTitle : title }}</span>
        </h1>
        <nav v-else class="breadcrumb">
          <ul class="breadcrumb__items">
            <!-- TODO: Pass in url instead of hard coding -->
            <li class="breadcrumb__item"><a href="/admin/dam/collections">Collections</a></li>
            <li class="breadcrumb__item">
              <a17-dropdown
                ref="infoDropdown"
                position="bottom-left"
                :offset="8"
                :clickable="true"
                :min-width="396"
              >
                <button
                  @click.prevent="$refs.infoDropdown.toggle()"
                  class="dropdown__button"
                >
                {{ customTitle ? customTitle : title }}
                  <span v-svg symbol="dropdown_module"></span>
                </button>
                <div slot="dropdown__content">
                  <div v-if="partner || description" class="dropdown__top">
                    <template v-if="partner">
                      <span class="dropdown__label">Created by</span>
                      <span>{{ partner }}</span>
                    </template>
                    <p v-if="description">{{ description }}</p>
                  </div>
                  <div class="dropdown__bottom">
                    <button aria-label="Copy link" @click.prevent="copyLink" data-copy="test">
                      <span v-svg symbol="clone"></span><span>Copy link</span>
                    </button>
                    <button aria-label="Edit" v-if="editUrl" @click.prevent="openEditModal">
                      <span v-svg symbol="edit"></span><span>Edit</span>
                    </button>
                  </div>
                </div>
              </a17-dropdown>
            </li>
          </ul>
      </nav>
      </div>
      <div class="dam-header__action">
        <div ref="form">
          <a17-filter @submit="submitSearch" ref="filters" @searchInput="setSearch" :initial-search-value="initialSearchValue" > </a17-filter>
        </div>
        <a17-button variant="validate" size="small" @click="openModal">{{
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
    <a17-dam-filters v-if="filters.length > 0" @resetFilters="clearSearch" ref="damFilters"></a17-dam-filters>
  </div>
</template>

<script>
  import { mapGetters, mapState } from 'vuex'

  import A17Avatar from '@/components/Avatar.vue'
  import a17Filter from '@/components/Filter.vue'
  import A17DamFilters from '@/components/dam/DamFilters.vue'
  import {FORM, MEDIA_LIBRARY, MODALEDITION} from "@/store/mutations";
  import ACTIONS from "@/store/actions";
  import NOTIFICATION from "@/store/mutations/notification";

  export default {
    name: 'A17DamHeader',
    components: {
      'a17-avatar': A17Avatar,
      'a17-filter': a17Filter,
      'a17-dam-filters': A17DamFilters
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
      usersManagement: {
        type: Boolean,
        default: true
      },
      initialSearchValue: {
        type: String,
        default: ''
      },
      editUrl: {
        type: String,
        default: null
      },
      updateUrl: {
        type: String,
        default: null
      },
    },
    data: function() {
      return {}
    },
    computed: {
      ...mapGetters(['fieldValueByName']),
      ...mapState({
        damView : state => state.mediaLibrary.damView,
        filters: state => state.mediaLibrary.filters,
        userInfo: state => state.publication.userInfo
      }),
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
      showBreadcrumb() {
        return this.updateUrl.includes('collections')
      },
      partner() {
        // TODO: Get creator of current item
        if (this.userInfo) {
          return this.userInfo.user_name
        }

        return null
      },
      description() {
        return this.fieldValueByName('description')
      }
    },
    watch: {},
    methods: {
      copyLink(e) {
        const textToCopy = e.target.getAttribute('data-copy')

        navigator.clipboard.writeText(textToCopy)
          .then(() => {
            this.$store.commit(NOTIFICATION.SET_NOTIF, {
              message: 'Link copied to clipboard',
              variant: 'success'
            })
          })
          .catch(err => {
            this.$store.commit(NOTIFICATION.SET_NOTIF, {
              message: 'Failed to copy text: ' + err,
              variant: 'error'
            })
          })
      },
      submitSearch() {
        this.$refs.damFilters.applyFilters()
      },
      setSearch(searchValue){
        this.$store.commit(MEDIA_LIBRARY.SET_DAM_SEARCH, { search: searchValue})
      },
      clearSearch(){
        this.$store.commit(MEDIA_LIBRARY.SET_DAM_SEARCH, {})
        this.$refs.filters.searchValue = ''
      },
      openModal() {
        this.damView === 'landing' ? this.$root.$refs.damMediaLibrary.open() : this.$root.$refs.editionModal.open()
      },
      openEditModal() {
        const endpoint = this.editUrl
        this.$store.commit(MODALEDITION.UPDATE_MODAL_MODE, 'update')
        this.$store.commit(MODALEDITION.UPDATE_MODAL_ACTION, this.updateUrl)
        this.$store.commit(FORM.UPDATE_FORM_LOADING, true)

        this.$store.dispatch(ACTIONS.REPLACE_FORM, endpoint).then(() => {
          this.$nextTick(function () {
            if (this.$root.$refs.editionModal) this.$root.$refs.editionModal.open()
          })
        }, (errorResponse) => {
          this.$store.commit(NOTIFICATION.SET_NOTIF, {
            message: 'Your content can not be edited, please retry',
            variant: 'error'
          })
        })
      }
    },
    mounted() {
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

      a {
        text-decoration: none;
      }
    }
  }

  .breadcrumb {
    overflow: visible;

    a,
    button {
      color: $color__black--90;
    }
  }

  .breadcrumb__item,
  .breadcrumb__item span:not(.breadcrumb__link) {
    height: auto;
  }

  .dropdown__button {
    @include btn-reset;
    padding: 0 0 0 rem-calc(10);
    display: flex;
    flex-flow: row;
    align-items: center;

    .icon {
      display: block;
      padding: 0;
      margin-left: 12px;
    }
  }

  .dropdown--active .dropdown__button .icon {
    transform: rotate(180deg);
  }

  .dropdown__content span {
    padding: 0;
    height: auto;
    line-height: 20px;


    &.dropdown__label {
      color: $color__black--90;
    }
  }

  .dropdown__content p {
    margin-top: 8px;
  }

  .dropdown__content button {
    display: flex;
    align-items: center;
    min-height: rem-calc(44);
    line-height: normal;
    color: $color__grey--54;
    font-size: rem-calc(15);

    span,
    svg {
      pointer-events: none;
    }
  }

  .dropdown__scroller {
    padding: 0;
  }

  .dropdown__top {
    background: $color__background--light;
    border-bottom: 1px solid $color__light;
    padding: rem-calc(16);
    margin-top: -10px;
    color: $color__grey--54;
  }

  .dropdown__bottom {
    margin-bottom: -6px;
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
</style>
