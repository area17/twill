<template>
  <div>
    <ul class="secondarynav secondarynav--desktop" slot="navigation">
        <li v-for="navItem in navFilters" class="secondarynav__item" :class="{ 's--on' : navActive === navItem.slug }">
            <a href="#" v-on:click.prevent="filterStatus(navItem.slug)">
                <span class="secondarynav__link">{{ navItem.name }}</span><span class="secondarynav__number">({{ navItem.number }})</span>
            </a>
        </li>
    </ul>

    <div class="secondarynav secondarynav--mobile secondarynav--dropdown" slot="navigation" v-if="navFilters.length">
        <a17-dropdown ref="secondaryNavDropdown" position="bottom-left" width="full" :offset="0">
            <a17-button class="secondarynav__button" variant="dropdown-transparent" size="small" @click="$refs.secondaryNavDropdown.toggle()">
                <span class="secondarynav__link">{{ selectedNav.name }}</span><span class="secondarynav__number">({{ selectedNav.number }})</span>
            </a17-button>
            <div slot="dropdown__content">
                <ul>
                    <li v-for="navItem in navFilters" class="secondarynav__item">
                        <a href="#" v-on:click.prevent="filterStatus(navItem.slug)">
                            <span class="secondarynav__link">{{ navItem.name }}</span><span class="secondarynav__number">({{ navItem.number }})</span>
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
  import { DATATABLE } from '@/store/mutations'
  import ACTIONS from '@/store/actions'

  export default {
    name: 'A17TableFilters',
    data: function () {
      return {
        navFilters: this.$store.state.datatable.filtersNav
      }
    },
    computed: {
      selectedNav: function () {
        let self = this
        const navItem = self.navFilters.filter(function (n) {
          return n.slug === self.navActive
        })
        return navItem[0]
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
