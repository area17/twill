<template>
  <div class="dam__wrapper">
    <a17-dam-navigation
      :app-name="appName"
      :env-label="envLabel"
      :sections="navSections"
    ></a17-dam-navigation>
    <div class="dam__content">
      <a17-dam-header
        :custom-title="customTitle"
        :current-user="currentUser"
        :users-management="usersManagement"
        :filters="filters"
      ></a17-dam-header>

      <div class="dam__page">
        <a17-dam-listing
          :authorized="authorized"
          :extra-metadatas="extraMetadatas"
        />
      </div>
    </div>
  </div>
</template>

<script>
  import a17DamNavigation from '@/components/dam/DamNavigation.vue'
  import a17DamHeader from '@/components/dam/DamHeader.vue'
  import a17DamListing from '@/components/dam/DamListing.vue'

  export default {
    name: 'A17Dam',
    components: {
      'a17-dam-navigation': a17DamNavigation,
      'a17-dam-header': a17DamHeader,
      'a17-dam-listing': a17DamListing
    },
    props: {
      authorized: {
        type: Boolean,
        default: false
      },
      navSections: {
        type: String,
        default: null
      },
      appLink: {
        type: String,
        default: null
      },
      appName: {
        type: String,
        default: null
      },
      envLabel: {
        type: String,
        default: null
      },
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
      extraMetadatas: {
        type: Array,
        default() {
          return []
        }
      },
      translatableMetadatas: {
        type: Array,
        default() {
          return []
        }
      }
    },
    data: function() {
      return {}
    },
    computed: {},
    watch: {},
    methods: {},
    mounted() {}
  }
</script>

<style lang="scss">
  .dam__content {
    flex: 0 1 100%;
    display: flex;
    flex-flow: column;
    height: 100vh;
  }

  .dam__page {
    display: flex;
    flex-flow: row;
    flex: 0 1 100%;
    overflow-y: auto;
  }
</style>

<style lang="scss" scoped>
  .dam__wrapper {
    display: flex;
    flex-flow: row;
    width: 100%;
  }
</style>
