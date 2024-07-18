<template>
  <div>
    <div class="dam-header">
      <div class="dam-header__title">
        <h1>
          <a v-if="editUrl" @click.prevent="openEditModal" href="#">
            <span class="f--underlined--o">{{ customTitle ? customTitle : title }}</span> <span v-svg symbol="edit"></span>
          </a>
          <span v-else>{{ customTitle ? customTitle : title }}</span>
        </h1>
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
  import {mapGetters, mapState} from 'vuex'
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
      }
    },
    data: function() {
      return {}
    },
    computed: {
      ...mapGetters(['fieldValueByName']),
      ...mapState({
        filters: state => state.mediaLibrary.filters
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
      }
    },
    watch: {},
    methods: {
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
        this.$root.$refs.editionModal.open()
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
    mounted() {}
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
