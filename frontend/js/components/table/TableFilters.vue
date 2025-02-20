<template>
  <div>
    <ul class="secondarynav secondarynav--desktop" slot="navigation">
      <li v-for="(navItem, index) in navFilters"
          :key="index"
          class="secondarynav__item"
          :class="{ 's--on' : navActive === navItem.slug }">
        <a href="#" v-on:click.prevent="filterStatus(navItem.slug)">
          <span class="secondarynav__link">{{ navItem.name }}</span>
          <span class="secondarynav__number" v-if="navItem.number !== null">({{ navItem.number }})</span>
        </a>
      </li>
    </ul>

    <div class="secondarynav secondarynav--mobile secondarynav--dropdown" slot="navigation"
         v-if="navFilters.length && selectedNav">
      <a17-dropdown ref="secondaryNavDropdown" position="bottom-left" width="full" :offset="0">
        <a17-button class="secondarynav__button" variant="dropdown-transparent" size="small"
                    @click="$refs.secondaryNavDropdown.toggle()">
          <span class="secondarynav__link">{{ selectedNav.name }}</span><span
            class="secondarynav__number">({{ selectedNav.number }})</span>
        </a17-button>
        <div slot="dropdown__content">
          <ul>
            <li v-for="(navItem, index) in navFilters" class="secondarynav__item"
                :key="index">
              <a href="#" v-on:click.prevent="filterStatus(navItem.slug)">
                <span class="secondarynav__link">{{ navItem.name }}</span>
                <span class="secondarynav__number" v-if="navItem.number !== null">({{ navItem.number }})</span>
              </a>
            </li>
          </ul>
        </div>
      </a17-dropdown>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'

  import ACTIONS from '@/store/actions'
  import { DATATABLE } from '@/store/mutations'

  export default {
    name: 'A17TableFilters',
    data: function () {
      return {
        navFilters: this.$store.state.datatable.filtersNav
      }
    },
    computed: {
      selectedNav: function () {
        const navItem = this.navFilters.filter((n) => {
          return n.slug === this.navActive
        })

        return navItem.length > 0 ? navItem[0] : null
      },
      ...mapState({
        navActive: state => state.datatable.filter.status
      })
    },
    methods: {
      filterStatus: function (slug) {
        if (this.navActive === slug) return
        this.$store.commit(DATATABLE.UPDATE_DATATABLE_PAGE, 1)
        this.$store.commit(DATATABLE.UPDATE_DATATABLE_FILTER_STATUS, slug)
        this.$store.dispatch(ACTIONS.GET_DATATABLE)
      }
    }
  }
</script>
