<template>
  <div>
    <div class="dam-header">
      <div class="dam-header__title">
        <h1>{{ customTitle ? customTitle : title }}</h1>
      </div>
      <div class="dam-header__action">
        <div ref="form">
          <a17-filter @submit="submitSearch" :initial-search-value="initialSearchValue" > </a17-filter>
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
    <a17-dam-filters v-if="filters" :filters="filters"></a17-dam-filters>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'

  import A17Avatar from '@/components/Avatar.vue'
  import a17Filter from '@/components/Filter.vue'
  import A17DamFilters from '@/components/dam/DamFilters.vue'

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
      filters: {
        type: Array,
        default: null
      },
      usersManagement: {
        type: Boolean,
        default: true
      },
      initialSearchValue: {
        type: String,
        default: ''
      }
    },
    data: function() {
      return {}
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
      }
    },
    watch: {},
    methods: {
      submitSearch(formData) {
        // TODO: refactor this, this is a prototype for demo purposes
        if (formData.search) {
          window.location.href = '/admin/dam?search=' + formData.search
        } else {
          window.location.href = '/admin/dam'
        }

      },
      openModal() {
        this.$root.$refs.editionModal.open()
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
