<template>
  <div class="multiselectorPermissions">
    <div class="multiselectorPermissions__filter" v-if="searchable">
      <a17-filter @submit="submitFilter" :full-width="true">
        <div slot="additional-actions" v-if="groups.length && listUser" class="multiselectorPermissions__groups">
          <a17-dropdown class="multiselectorPermissions__dd" ref="groupDropdown"
                        position="bottom-right" :clickable="true">
            <button class="multiselectorPermissions__button" @click="$refs.groupDropdown.toggle()" type="button">Groups</button>
            <div slot="dropdown__content">
              <a17-checkboxgroup name="permissionsGroups" :selected="activeGroups" :options="groups" @change="updateUserPermission" />
            </div>
          </a17-dropdown>
        </div>
      </a17-filter>
    </div>
    <div class="multiselectorPermissions__items">
      <slot />
      <div class="multiselectorPermissions__empty" v-if="empty" :style="emptyStyle">
        <h4>{{ emptyMessage }}</h4>
      </div>
      <div class="multiselectorPermissions__empty" v-if="allHidden" :style="emptyStyle">
        <h4>{{ allHiddenMessage }}</h4>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapGetters,mapState } from 'vuex'

  import { FORM } from '@/store/mutations'

  import a17Filter from './Filter.vue'

  export default {
    name: 'A17SingleSelectPermissions',
    components: {
      'a17-filter': a17Filter
    },
    props: {
      searchable: {
        type: Boolean,
        default: true
      },
      listUser: {
        type: Boolean,
        default: false
      },
      emptyMessage: {
        type: String,
        default: 'No results found. Please try another search'
      },
      allHiddenMessage: {
        type: String,
        default: 'Use the search box to find items'
      }
    },
    data: function () {
      return {
        empty: false,
        allHidden: false,
        activeGroups: [],
        emptyHeight: 120
      }
    },
    computed: {
      emptyStyle: function () {
        return { height: this.emptyHeight + 'px' }
      },
      ...mapGetters([
        'fieldsByName'
      ]),
      ...mapState({
        groups: state => state.permissions.groups,
        groupUserMapping: state => state.permissions.groupUserMapping
      })
    },
    mounted () {
      if (!this.listUser) {
        const allItems = this.$el.querySelectorAll('[data-singleselect-permissions-field]')
        const filterClass = 'multiselectorPermissions__item--hidden'

        if (allItems.length) {
          let hiddenItemsCount = 0

          allItems.forEach((itemEl) => {
            const fieldName = itemEl.getAttribute('data-singleselect-permissions-field')
            const fieldValue = this.fieldsByName(fieldName)
            const permission = fieldValue.length ? fieldValue[0].value : ''

            if (!permission) {
              itemEl.classList.add(filterClass)
              hiddenItemsCount++
            }
          })

          if (hiddenItemsCount === allItems.length) {
            this.allHidden = true
          }
        }
      }
    },
    methods: {
      submitFilter (formData) {
        const allItems = this.$el.querySelectorAll('[data-singleselect-permissions-filterable]')
        const filterClass = 'multiselectorPermissions__item--hidden'

        if (allItems.length) {
          this.emptyHeight = Math.max(120, allItems[0].parentElement.offsetHeight)
          this.empty = true
          this.allHidden = false

          allItems.forEach((itemEl) => {
            const filterValue = itemEl.getAttribute('data-singleselect-permissions-filterable')

            if (formData.search) {
              const query = formData.search
              if (filterValue.toUpperCase().includes(query.toUpperCase())) {
                itemEl.classList.remove(filterClass)
                this.empty = false
              } else {
                itemEl.classList.add(filterClass)
              }
            } else {
              itemEl.classList.remove(filterClass)
              this.empty = false
            }
          })
        }
      },
      setUserPermission (fieldName) {
        const field = {}
        field.name = fieldName
        field.value = 'view-item'
        this.$store.commit(FORM.UPDATE_FORM_FIELD, field)
      },
      updateUserPermission (selectedGroups) {
        this.activeGroups = selectedGroups

        selectedGroups.forEach((selectedGroup) => {
          if (this.groupUserMapping[selectedGroup]) {
            this.groupUserMapping[selectedGroup].forEach((userId) => {
              // If the user's permission is <= view, it will be updated
              const fieldName = `user_${userId}_permission`
              const currentPermission = this.fieldsByName(fieldName)

              if (currentPermission.length) {
                if (currentPermission[0].value === '' || currentPermission[0].value === 'view-item') {
                  this.setUserPermission(fieldName)
                }
              } else {
                this.setUserPermission(fieldName)
              }
            })
          }
        })
      }
    }
  }
</script>

<style lang="scss" scoped>
  @import '~styles/setup/_mixins-colors-vars.scss';

  .multiselectorPermissions__items {
    border: 1px solid $color__border;
    margin-top: 20px;
  }

  .multiselectorPermissions__filter {
    background:$color__border--light;
    margin-left:-20px;
    margin-right:-20px;
    padding-left: 20px;
    padding-right: 20px;

    .filter__search {
      display: flex;
      width: 100%;
    }
  }

  .multiselectorPermissions__item {
    padding-left: 20px;
    border-bottom: 1px solid $color__border--light;
    display: flex;
    align-items: center;
    flex-wrap: wrap;

    label {
      padding: 20px 0;
      flex-grow: 1;
    }

    .multiselectorOuter {
      padding: 13.5px 0;
    }

    .avatar {
      margin-left: -8px;
    }

    .avatar + label {
      margin-left: 10px;
    }

    &:last-child {
      border-bottom: 0 none;
    }

    &.multiselectorPermissions__item--hidden {
      display : none;
    }
  }

  .multiselectorPermissions__empty {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 120px;
    padding: 15px 20px;

    h4 {
      @include font-medium();
      font-weight: 400;
      color: $color__f--text;
    }
  }

  $nativeSelectHeight: 35px;

  .multiselectorPermissions__groups {
    flex-grow: 1;
    display: flex;
    align-items: flex-end;
  }

  .multiselectorPermissions__button {
    @include btn-reset;
    border: 1px solid $color__fborder;
    background-color: $color__background;
    border-radius: 2px;
    color:$color__text--light;
    height: $nativeSelectHeight;
    min-width: 120px;
    text-align: left;
    position: relative;
    margin-left: auto;

    @include breakpoint('large+') {
      min-width: 200px;
    }

    &::after {
      content:'';
      display:inline-block;
      width: 0;
      height: 0;
      margin-top: -1px;
      border-width: 4px 4px 0;
      border-style: solid;
      border-color: $color__icons transparent transparent;
      position: absolute;
      right: 10px;
      top: 50%;
      margin-left: 5px;
    }

    &:focus,
    &:hover {
      color:$color__text;

      &::after {
        border-color: $color__text transparent transparent;
      }
    }
  }
</style>
