<template>
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
            @click.prevent="$refs.userDropdown.toggle()"
            class="dam-header__dropdown"
          >
            <!-- {{
              userData.role === 'SUPERADMIN'
                ? $trans('nav.admin')
                : userData.name
            }} -->
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
</template>

<script>
  import { mapGetters } from 'vuex'

  import A17Avatar from '@/components/Avatar.vue'
  import a17Filter from '@/components/Filter.vue'

  export default {
    name: 'A17Medialibrary',
    components: {
      'a17-avatar': A17Avatar,
      'a17-filter': a17Filter
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
      submitSearch: function(formData) {}
    },
    mounted() {}
  }
</script>

<style lang="scss" scoped>
  .dam-header {
    display: flex;
    flex-flow: row;
    width: 100%;
    background: $color__border--light;
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
