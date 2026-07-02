<template>
  <div class="dam-results-table datatable" data-sticky-id="thead" data-sticky-offset="0">
    <div class="datatable__sticky" data-sticky-top="thead">
      <div class="datatable__stickyHead" data-sticky-target="thead">
        <div class="container">
          <div class="datatable__stickyInner">
            <div class="datatable__setup">
              <a17-dropdown
                v-if="hideableColumns.length"
                ref="setupDropdown"
                class="datatable__setupDropdown"
                position="bottom-right"
                :title="$trans('listing.columns.show')"
                :clickable="true"
                :offset="-10"
              >
                <button
                  type="button"
                  class="datatable__setupButton"
                  @click="$refs.setupDropdown.toggle()"
                >
                  <span v-svg symbol="preferences"></span>
                </button>
                <div slot="dropdown__content">
                  <a17-checkboxgroup
                    name="visibleColumns"
                    :options="checkboxesColumns"
                    :selected="visibleColumnsNames"
                    @change="updateActiveColumns"
                    :min="minimumVisibleColumns"
                  />
                </div>
              </a17-dropdown>
            </div>
            <div class="datatable__stickyTable">
              <a17-table :columnsWidth="columnsWidth" :xScroll="xScroll" @scroll="updateScroll">
                <thead>
                  <tr ref="stickyHeadRow" class="tablehead">
                    <td
                      v-for="col in visibleColumns"
                      :key="`sticky-${col.name}`"
                      class="tablehead__cell f--small"
                      :class="headerCellClasses(col)"
                      @click="handleSort(col)"
                    >
                      <span v-if="shouldShowHeaderLabel(col)">
                        {{ col.label }}
                        <span class="tablehead__arrow">↓</span>
                      </span>
                      <button
                        v-else-if="col.name === 'bulk'"
                        type="button"
                        class="tablehead__bulkToggle"
                        @click.stop="toggleBulkSelect"
                      >
                        <span class="tablehead__bulkCheckbox" :class="{ 'tablehead__bulkCheckbox--active': bulkValue }"></span>
                      </button>
                    </td>
                    <td class="tablehead__spacer">&nbsp;</td>
                  </tr>
                </thead>
              </a17-table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="container">
      <div class="datatable__table" :class="{ 'datatable__table--empty': isEmpty }">
        <a17-table :xScroll="xScroll" :columnsWidth="columnsWidth" @scroll="updateScroll">
          <thead>
            <tr ref="headRow" class="tablehead">
              <td
                v-for="col in visibleColumns"
                :key="col.name"
                class="tablehead__cell f--small"
                :class="headerCellClasses(col)"
                @click="handleSort(col)"
              >
                <span v-if="shouldShowHeaderLabel(col)">
                  {{ col.label }}
                  <span class="tablehead__arrow">↓</span>
                </span>
                <button
                  v-else-if="col.name === 'bulk'"
                  type="button"
                  class="tablehead__bulkToggle"
                  @click.stop="toggleBulkSelect"
                >
                  <span class="tablehead__bulkCheckbox" :class="{ 'tablehead__bulkCheckbox--active': bulkValue }"></span>
                </button>
              </td>
              <td class="tablehead__spacer">&nbsp;</td>
            </tr>
          </thead>
          <tbody>
            <tr v-for="row in rows" :key="row.id" class="tablerow">
              <td
                v-for="col in visibleColumns"
                :key="`${row.id}-${col.name}`"
                class="tablecell"
                :class="rowCellClasses(col)"
              >
                <template v-if="col.name === 'bulk'">
                  <button
                    type="button"
                    class="tablecell__bulkToggle"
                    :aria-pressed="isRowSelected(row.id) ? 'true' : 'false'"
                    @click="toggleRowSelection(row.id)"
                  >
                    <span class="tablecell__bulkCheckbox" :class="{ 'tablecell__bulkCheckbox--active': isRowSelected(row.id) }"></span>
                  </button>
                </template>

                <template v-else-if="col.name === 'thumbnail' && row[col.name]">
                  <a
                    class="tablecell__thumb"
                    :class="{ 'tablecell__thumb--rounded': col.variation === 'rounded' }"
                    :href="row.edit || '#'"
                  >
                    <img :src="row[col.name]" :alt="plainText(row.name || row.title || '')">
                  </a>
                </template>

                <template v-else-if="col.name === 'published'">
                  <button
                    type="button"
                    class="tablecell__pubbutton"
                    @click="togglePublished(row)"
                  >
                    <span
                      class="tablecell__pubstate"
                      :class="{ 'tablecell__pubstate--live': Boolean(row.published) }"
                    ></span>
                  </button>
                </template>

                <template v-else-if="col.name === 'featured'">
                  <button
                    type="button"
                    class="tablecell__flag"
                    :class="{ 'tablecell__flag--active': Boolean(row.featured) }"
                    @click="toggleBooleanState('featured', row)"
                  >
                    F
                  </button>
                </template>

                <template v-else-if="col.name === 'starred'">
                  <button
                    v-if="row.hasOwnProperty('starred')"
                    type="button"
                    class="tablecell__starbutton"
                    v-tooltip
                    :data-tooltip-title="row.starred ? 'Unstar' : 'Star'"
                    @click.prevent="toggleStarred(row)"
                  >
                    <span
                      v-svg
                      :symbol="[row.starred ? 'star-feature_active' : 'star-feature']"
                      class="tablecell__starstate"
                      :class="{ 'tablecell__starstate--live': row.starred }"
                    ></span>
                  </button>
                </template>

                <template v-else-if="col.name === 'languages'">
                  <span>{{ formatLanguages(row[col.name]) }}</span>
                </template>

                <template v-else-if="shouldRenderHtmlCell(col, row)">
                  <span
                    class="tablecell__raw"
                    :class="{ truncate: Boolean(col.truncate) && !containsAnchor(row[col.name]) }"
                    v-html="row[col.name]"
                    @click="handleHtmlCellClick($event, row)"
                  ></span>
                </template>

                <template v-else>
                  <a
                    v-if="isPrimaryLinkColumn(col, row)"
                    class="tablecell__link"
                    :href="row.edit || '#'"
                    @click.prevent="openRowEdit(row)"
                  >
                    {{ formatCellValue(row[col.name]) }}
                  </a>
                  <span
                    v-else
                    :class="{ truncate: Boolean(col.truncate) }"
                  >
                    {{ formatCellValue(row[col.name]) }}
                  </span>
                </template>
              </td>
              <td class="tablecell tablecell--spacer">&nbsp;</td>
              <td class="tablecell tablecell--sticky">
                <a17-dropdown
                  v-if="hasRowActions(row)"
                  :ref="rowActionsRef(row.id)"
                  position="bottom-right"
                  :fixed="true"
                >
                  <a17-button variant="icon" type="button" @click="toggleRowActions(row.id)">
                    <span v-svg symbol="more-dots"></span>
                  </a17-button>
                  <div slot="dropdown__content">
                    <a
                      v-if="row.permalink"
                      :href="row.permalink"
                      target="_blank"
                      rel="noopener noreferrer"
                    >
                      {{ $trans('listing.dropdown.view-permalink', 'View permalink') }}
                    </a>
                    <a
                      v-if="canEditRow(row)"
                      :href="row.edit || '#'"
                      @click.prevent="openRowEdit(row)"
                    >
                      {{ $trans('listing.dropdown.edit', 'Edit') }}
                    </a>
                    <a
                      v-if="row.duplicate && !row.deleted"
                      href="#"
                      @click.prevent="duplicateRow(row)"
                    >
                      {{ $trans('listing.dropdown.duplicate', 'Duplicate') }}
                    </a>
                    <a
                      v-if="row.deleted"
                      href="#"
                      @click.prevent="restoreRow(row)"
                    >
                      {{ $trans('listing.dropdown.restore', 'Restore') }}
                    </a>
                    <a
                      v-if="row.deleted && row.destroyable"
                      href="#"
                      @click.prevent="destroyRow(row)"
                    >
                      {{ $trans('listing.dropdown.destroy', 'Destroy') }}
                    </a>
                    <a
                      v-else-if="row.delete && !row.deleted"
                      href="#"
                      @click.prevent="deleteRow(row)"
                    >
                      {{ $trans('listing.dropdown.delete', 'Delete') }}
                    </a>
                  </div>
                </a17-dropdown>
              </td>
            </tr>
          </tbody>
        </a17-table>

        <template v-if="isEmpty">
          <div class="datatable__empty">
            <h4>{{ emptyMessage }}</h4>
          </div>
        </template>

        <a17-paginate
          v-if="maxPage > 1 || (defaultMaxPage > maxPage && !isEmpty)"
          :max="maxPage"
          :value="page"
          :offset="offset"
          :availableOffsets="availableOffsets"
          @changePage="updatePage"
          @changeOffset="updateOffset"
        />
      </div>
    </div>

    <a17-spinner v-if="loading">Loading&hellip;</a17-spinner>
  </div>
</template>

<script>
  import axios from 'axios'
  import debounce from 'lodash/debounce'
  import { mapState } from 'vuex'

  import a17Paginate from '@/components/table/Paginate.vue'
  import a17Spinner from '@/components/Spinner.vue'
  import a17Table from '@/components/table/Table.vue'
  import ACTIONS from '@/store/actions'
  import { DATATABLE, FORM, MODALEDITION } from '@/store/mutations'
  import { globalError } from '@/utils/errors'
  import { getStorage, setStorage } from '@/utils/localeStorage.js'
  import { replaceState } from '@/utils/pushState.js'
  import NOTIFICATION from '@/store/mutations/notification'

  const bootstrapDatatable = () => window[process.env.VUE_APP_NAME].STORE.datatable || {}

  const cloneColumns = (columns = []) => columns.map(column => ({ ...column }))

  export default {
    name: 'A17DamResultsTable',
    components: {
      'a17-paginate': a17Paginate,
      'a17-spinner': a17Spinner,
      'a17-table': a17Table
    },
    props: {
      emptyMessage: {
        type: String,
        default: ''
      }
    },
    data () {
      const datatable = bootstrapDatatable()

      return {
        bulkIds: [],
        columns: cloneColumns(datatable.columns || []),
        columnsWidth: [],
        defaultMaxPage: Number(datatable.defaultMaxPage || datatable.maxPage || 1),
        defaultOffset: Number(datatable.defaultOffset || datatable.offset || 60),
        loading: false,
        localStorageKey: datatable.localStorageKey || window.location.pathname,
        maxPage: Number(datatable.maxPage || 1),
        page: Number(datatable.page || 1),
        rows: Array.isArray(datatable.data) ? datatable.data : [],
        sortDir: datatable.sortDir || 'asc',
        sortKey: datatable.sortKey || '',
        offset: Number(datatable.offset || 60),
        xScroll: 0
      }
    },
    computed: {
      ...mapState({
        currentStatus: state => state.mediaLibrary.currentStatus,
        filterData: state => state.mediaLibrary.filterData,
        initialFilterData: state => state.mediaLibrary.initialFilterData,
        searchData: state => state.mediaLibrary.searchData
      }),
      availableOffsets () {
        const values = [this.defaultOffset, this.defaultOffset * 3, this.defaultOffset * 6]

        return values.filter((value, index) => value > 0 && values.indexOf(value) === index)
      },
      bulkValue () {
        return this.bulkIds.length > 0 && this.bulkIds.length === this.rows.length
      },
      checkboxesColumns () {
        return this.hideableColumns.map(column => ({
          value: column.name,
          label: column.label
        }))
      },
      hideableColumns () {
        return this.columns.filter(column => column.optional)
      },
      isEmpty () {
        return this.rows.length === 0
      },
      minimumVisibleColumns () {
        return this.hideableColumns.length > 1 ? 2 : 1
      },
      primaryLinkColumnName () {
        const preferredColumns = ['title', 'name']
        const fallbackColumn = this.columns.find(column => (
          !['bulk', 'thumbnail', 'published', 'featured', 'starred', 'languages'].includes(column.name)
        ))

        return preferredColumns.find(name => this.columns.some(column => column.name === name)) ||
          (fallbackColumn ? fallbackColumn.name : null)
      },
      visibleColumns () {
        return this.columns.filter(column => column.visible)
      },
      visibleColumnsNames () {
        return this.visibleColumns.map(column => column.name)
      }
    },
    watch: {
      currentStatus () {
        this.reloadFromFilters()
      },
      filterData: {
        handler () {
          this.reloadFromFilters()
        },
        deep: true
      },
      loading () {
        this.$nextTick(() => {
          this.getColumnWidth()
        })
      },
      searchData: {
        handler () {
          this.reloadFromFilters()
        },
        deep: true
      }
    },
    methods: {
      buildFilterPayload () {
        const payload = {
          ...this.initialFilterData,
          ...this.filterData
        }

        if (this.searchData.search) {
          payload.search = this.searchData.search
        } else {
          delete payload.search
        }

        if (this.currentStatus && this.currentStatus !== 'all') {
          payload.status = this.currentStatus
        } else {
          delete payload.status
        }

        return payload
      },
      buildRequestParams () {
        const params = {
          filter: this.buildFilterPayload(),
          offset: this.offset,
          page: this.page
        }

        if (this.sortKey) {
          params.sortDir = this.sortDir
          params.sortKey = this.sortKey
        }

        return params
      },
      canEditRow (row) {
        return Boolean((row.edit || row.editInModal) && !row.deleted)
      },
      commitNotification (message, variant = 'error') {
        if (!message) {
          return
        }

        this.$store.commit(NOTIFICATION.SET_NOTIF, { message, variant })
      },
      containsAnchor (value) {
        return typeof value === 'string' && value.includes('<a')
      },
      formatCellValue (value) {
        if (Array.isArray(value)) {
          return value.join(', ')
        }

        if (value && typeof value === 'object') {
          if (Object.prototype.hasOwnProperty.call(value, 'label')) {
            return value.label
          }

          return ''
        }

        return value ?? ''
      },
      formatLanguages (languages) {
        if (!Array.isArray(languages)) {
          return ''
        }

        return languages.map(language => language.label || language.value || language).join(', ')
      },
      getColumnWidth () {
        if (!this.$refs.headRow) {
          return
        }

        const tds = this.$refs.headRow.children
        const measuredWidths = []

        for (let index = 0; index < tds.length; index++) {
          measuredWidths.push(tds[index].offsetWidth)
        }

        const totalWidth = measuredWidths.reduce((sum, width) => sum + width, 0)

        if (totalWidth <= 0) {
          return
        }

        this.columnsWidth = measuredWidths.map((width, index) => {
          if (index === measuredWidths.length - 1) {
            const usedWidth = measuredWidths
              .slice(0, -1)
              .reduce((sum, currentWidth) => sum + (currentWidth / totalWidth) * 100, 0)

            return `${Math.max(0, 100 - usedWidth).toFixed(4)}%`
          }

          return `${((width / totalWidth) * 100).toFixed(4)}%`
        })
      },
      handleSort (column) {
        if (!column.sortable) {
          return
        }

        if (this.sortKey === column.name) {
          this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc'
        } else {
          this.sortDir = 'asc'
        }

        this.sortKey = column.name
        this.page = 1
        this.reloadTable()
      },
      hasRowActions (row) {
        return Boolean(row.permalink || row.edit || row.editInModal || row.duplicate || row.delete || row.deleted)
      },
      handleHtmlCellClick (event, row) {
        const link = event.target.closest('a[data-edit="true"]')

        if (!link) {
          return
        }

        event.preventDefault()
        this.openRowEdit(row)
      },
      isPrimaryLinkColumn (column, row) {
        if (!this.canEditRow(row)) {
          return false
        }

        if (['title', 'name'].includes(column.name)) {
          return true
        }

        return column.name === this.primaryLinkColumnName
      },
      headerCellClasses (column) {
        return [
          column.name === 'bulk' ? 'tablehead__cell--bulk' : '',
          column.name === 'featured' || column.name === 'published' || column.name === 'starred' ? 'tablehead__cell--icon' : '',
          column.name === 'thumbnail' ? 'tablehead__cell--thumb' : '',
          column.sortable ? 'tablehead__cell--sortable' : '',
          this.sortKey === column.name ? 'tablehead__cell--sorted' : '',
          this.sortKey === column.name && this.sortDir ? `tablehead__cell--sorted${this.sortDir}` : '',
          column.shrink === true ? 'tablehead__cell--shrink' : ''
        ]
      },
      initEvents () {
        window.addEventListener('resize', this.resize)
        this.resize()
      },
      isRowSelected (rowId) {
        return this.bulkIds.includes(rowId)
      },
      openRowEdit (row) {
        if (row.editInModal) {
          this.$store.commit(MODALEDITION.UPDATE_MODAL_MODE, 'update')
          this.$store.commit(MODALEDITION.UPDATE_MODAL_ACTION, row.updateUrl || row.edit || '')
          this.$store.commit(FORM.UPDATE_FORM_LOADING, true)

          this.$store.dispatch(ACTIONS.REPLACE_FORM, row.editInModal).then(() => {
            this.$nextTick(() => {
              if (this.$root.$refs.editionModal) {
                this.$root.$refs.editionModal.open()
              }
            })
          }).catch(() => {
            this.commitNotification('Your content can not be edited, please retry')
          })

          return
        }

        if (row.edit) {
          window.location.href = row.edit
        }
      },
      plainText (value) {
        return String(value).replace(/<[^>]*>?/gm, '')
      },
      reloadFromFilters () {
        this.page = 1
        this.reloadTable()
      },
      reloadTable () {
        if (this.loading) {
          return
        }

        this.loading = true

        axios.get(window[process.env.VUE_APP_NAME].CMS_URLS.index, {
          params: this.buildRequestParams()
        }).then(resp => {
          this.rows = resp.data.tableData ? resp.data.tableData : []
          this.maxPage = resp.data.maxPage ? resp.data.maxPage : 1
          if (Array.isArray(resp.data.tableMainFilters)) {
            this.$store.commit(DATATABLE.UPDATE_DATATABLE_NAV, resp.data.tableMainFilters)
          }
          if (this.page > this.maxPage) {
            this.page = this.maxPage
          }
          this.bulkIds = []
          this.loading = false
          this.syncUrl()
        }).catch(error => {
          this.loading = false
          globalError('DAM-RESULTS-TABLE', {
            message: 'Get request error.',
            value: error
          })
        })
      },
      resize: debounce(function () {
        this.getColumnWidth()
      }, 100),
      restorePreferences () {
        let shouldReload = false

        const storedOffset = getStorage(this.localStorageKey + '_page-offset')
        if (storedOffset) {
          const parsedOffset = parseInt(storedOffset, 10)

          if (!Number.isNaN(parsedOffset) && parsedOffset !== this.offset) {
            this.offset = parsedOffset
            shouldReload = true
          }
        }

        const storedColumns = getStorage(this.localStorageKey + '_columns-visible')
        if (storedColumns) {
          try {
            const visibleColumns = JSON.parse(storedColumns)
            this.columns = this.columns.map(column => ({
              ...column,
              visible: column.optional ? visibleColumns.includes(column.name) : true
            }))
          } catch (error) {}
        }

        return shouldReload
      },
      rowCellClasses (column) {
        return {
          'tablecell--bulk': column.name === 'bulk',
          'tablecell--icon': column.name === 'featured' || column.name === 'published' || column.name === 'starred',
          'tablecell--thumb': column.name === 'thumbnail'
        }
      },
      runRowRequest (request, successMessage) {
        request.then(resp => {
          this.commitNotification(successMessage || resp.data.message, resp.data.variant || 'success')
          this.reloadTable()
        }).catch(error => {
          this.commitNotification(
            error.response && error.response.data && error.response.data.message
              ? error.response.data.message
              : 'The request could not be completed.'
          )
        })
      },
      shouldShowHeaderLabel (column) {
        return !['bulk', 'published', 'featured', 'starred'].includes(column.name)
      },
      shouldRenderHtmlCell (column, row) {
        return Boolean(column.html || this.looksLikeHtml(row[column.name]))
      },
      looksLikeHtml (value) {
        return typeof value === 'string' && /<[^>]+>/.test(value)
      },
      rowActionsRef (rowId) {
        return `rowActions-${rowId}`
      },
      syncUrl () {
        const url = new URL(window.location.href)
        const filterPayload = this.buildFilterPayload()

        url.search = ''

        Object.keys(filterPayload).forEach(key => {
          const value = filterPayload[key]

          if (
            value === null ||
            value === undefined ||
            value === '' ||
            (Array.isArray(value) && value.length === 0) ||
            (typeof value === 'object' && !Array.isArray(value) && Object.keys(value).length === 0)
          ) {
            return
          }

          url.searchParams.set(
            key,
            typeof value === 'string' ? value : JSON.stringify(value)
          )
        })

        if (this.page > 1) {
          url.searchParams.set('page', this.page)
        }

        if (this.offset !== this.defaultOffset) {
          url.searchParams.set('offset', this.offset)
        }

        replaceState(url)
      },
      toggleBooleanState (type, row) {
        if (row.deleted) {
          return
        }

        const endpointMap = {
          featured: window[process.env.VUE_APP_NAME].CMS_URLS.feature,
          published: window[process.env.VUE_APP_NAME].CMS_URLS.publish,
          starred: window[process.env.VUE_APP_NAME].CMS_URLS.starred
        }

        const endpoint = endpointMap[type]
        if (!endpoint) {
          this.commitNotification(`The ${type} action is not available on this listing.`)
          return
        }

        const payload = {
          active: row[type],
          id: row.id
        }

        this.runRowRequest(axios.put(endpoint, payload))
      },
      togglePublished (row) {
        this.toggleBooleanState('published', row)
      },
      toggleStarred (row) {
        this.toggleBooleanState('starred', row)
      },
      toggleBulkSelect () {
        this.bulkIds = this.bulkValue ? [] : this.rows.map(row => row.id)
      },
      toggleRowActions (rowId) {
        const dropdownRef = this.$refs[this.rowActionsRef(rowId)]
        const dropdown = Array.isArray(dropdownRef) ? dropdownRef[0] : dropdownRef

        if (dropdown && dropdown.toggle) {
          dropdown.toggle()
        }
      },
      toggleRowSelection (rowId) {
        if (this.isRowSelected(rowId)) {
          this.bulkIds = this.bulkIds.filter(id => id !== rowId)
        } else {
          this.bulkIds = [...this.bulkIds, rowId]
        }
      },
      updateActiveColumns (visibleColumns) {
        this.columns = this.columns.map(column => ({
          ...column,
          visible: column.optional ? visibleColumns.includes(column.name) : true
        }))
        setStorage(this.localStorageKey + '_columns-visible', JSON.stringify(visibleColumns))
        this.$nextTick(() => {
          this.getColumnWidth()
        })
      },
      updateOffset (value) {
        this.offset = value
        this.page = 1
        setStorage(this.localStorageKey + '_page-offset', String(value))
        this.reloadTable()
      },
      updatePage (value) {
        if (value === this.page) {
          return
        }

        this.page = value
        this.reloadTable()
      },
      duplicateRow (row) {
        this.runRowRequest(axios.put(row.duplicate))
      },
      restoreRow (row) {
        this.runRowRequest(
          axios.put(window[process.env.VUE_APP_NAME].CMS_URLS.restore, { id: row.id })
        )
      },
      destroyRow (row) {
        const request = () => this.runRowRequest(
          axios.put(window[process.env.VUE_APP_NAME].CMS_URLS.forceDelete, { id: row.id })
        )

        if (this.$root.$refs.warningDestroyRow) {
          this.$root.$refs.warningDestroyRow.open(request)
          return
        }

        request()
      },
      deleteRow (row) {
        const request = () => this.runRowRequest(axios.delete(row.delete))

        if (this.$root.$refs.warningDeleteRow) {
          this.$root.$refs.warningDeleteRow.open(request)
          return
        }

        request()
      },
      updateScroll (newValue) {
        this.xScroll = newValue
      }
    },
    mounted () {
      const shouldReload = this.restorePreferences()
      this.initEvents()

      if (shouldReload) {
        this.reloadTable()
      } else {
        this.$nextTick(() => {
          this.getColumnWidth()
        })
      }
    },
    beforeDestroy () {
      window.removeEventListener('resize', this.resize)
    }
  }
</script>

<style lang="scss" scoped>
  table {
    width: 100%;
  }

  .datatable__table {
    border: 1px solid $color__border--light;
    border-radius: 2px;
    position: relative;
  }

  .datatable__table--empty {
    border: none;
    border-top: 1px solid $color__border--light;
  }

  .datatable__empty {
    align-items: center;
    display: flex;
    height: 300px;
    justify-content: center;
    min-height: calc(100vh - #{170px + 60px + 100px + 100px + 100px});
    padding: 15px 20px;

    h4 {
      @include font-medium();
      color: $color__f--text;
      font-weight: 400;
    }
  }

  .datatable__setup {
    position: absolute;
    right: 0;
    top: 0;
    width: 50px;
    z-index: 1;
  }

  .datatable__setupDropdown {
    background: linear-gradient(to right, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 1) 25%);
    float: right;
    padding: 18px 20px 16px 15px;
  }

  .datatable__setupButton,
  .tablehead__bulkToggle,
  .tablecell__bulkToggle,
  .tablecell__pubbutton,
  .tablecell__flag,
  .tablecell__starbutton {
    @include btn-reset;
  }

  .datatable__setupButton {
    color: $color--icons;
    padding: 0;

    &:focus,
    &:hover {
      color: $color--text;
    }
  }

  .datatable__sticky {
    height: 60px;
  }

  .datatable__stickyInner {
    position: relative;
  }

  .tablehead__cell {
    color: $color__text--light;
    padding: 20px 10px;
    vertical-align: top;
    white-space: nowrap;

    &:hover {
      color: $color__text;
    }
  }

  .tablehead__cell--bulk,
  .tablehead__cell--thumb,
  .tablehead__cell--icon {
    width: 1px;

    .tablehead__arrow {
      display: none;
    }
  }

  .tablehead__cell--bulk {
    padding-left: 10px;
    padding-right: 10px;
  }

  .tablehead__cell--thumb {
    width: 100px;
  }

  .tablehead__cell--icon {
    width: 30px;
  }

  .tablehead__cell--sortable {
    cursor: pointer;

    &:hover .tablehead__arrow {
      opacity: 1;
    }
  }

  .tablehead__cell--sorted {
    color: $color__text;

    .tablehead__arrow {
      opacity: 1;
    }
  }

  .tablehead__cell--sorteddesc .tablehead__arrow {
    transform: rotate(180deg);
  }

  .tablehead__arrow {
    display: inline-block;
    margin-left: 10px;
    opacity: 0;
    position: relative;
    top: -1px;
    transition: all .2s linear;
  }

  .tablehead__spacer {
    padding-left: 25px;
    padding-right: 25px;
    width: 1px;
  }

  .tablehead__bulkCheckbox,
  .tablecell__bulkCheckbox {
    background: $color__background;
    border: 1px solid $color__border;
    border-radius: 2px;
    display: inline-block;
    height: 15px;
    width: 15px;
  }

  .tablehead__bulkCheckbox--active,
  .tablecell__bulkCheckbox--active {
    background: $color__publish;
    border-color: $color__publish;
  }

  .tablerow {
    border-bottom: 1px solid $color__border--light;
    position: relative;

    &:hover td {
      background-color: $color__f--bg;
    }
  }

  .tablecell {
    background-color: $color__background;
    overflow: hidden;
    padding: 20px 10px;
    vertical-align: top;
  }

  .tablecell--bulk,
  .tablecell--icon,
  .tablecell--thumb {
    width: 1px;
  }

  .tablecell--spacer {
    padding-left: 25px;
    padding-right: 25px;
    width: 1px;
  }

  .tablecell--sticky {
    background: linear-gradient(to right, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 1) 25%);
    overflow: visible;
    padding: 15px 20px;
    position: absolute;
    right: 0;
    top: auto;
  }

  .tablerow:hover > .tablecell--sticky {
    background: linear-gradient(to right, #{rgba($color__f--bg, 0)} 0%, #{rgba($color__f--bg, 1)} 25%);
  }

  .tablecell__thumb {
    background: $color__border--light;
    display: block;
    float: left;

    img {
      display: block;
      height: auto;
      min-height: 80px;
      width: 80px;
    }
  }

  .tablecell__thumb--rounded {
    border-radius: 50%;
    height: 36px;
    margin: -8px 0;
    overflow: hidden;
    width: 36px;

    img {
      min-height: 36px;
      width: 36px;
    }
  }

  .tablecell__link {
    color: $color__link;
    text-decoration: none;
  }

  .tablecell__pubbutton {
    align-items: center;
    display: inline-flex;
    justify-content: center;
    min-height: 16px;
    min-width: 16px;
  }

  .tablecell__pubstate {
    background: $color__grey--54;
    border-radius: 999px;
    display: block;
    height: 10px;
    width: 10px;
  }

  .tablecell__pubstate--live {
    background: $color__publish;
  }

  .tablecell__flag {
    color: $color__grey--54;
    font-size: 12px;
    line-height: 1;
  }

  .tablecell__flag--active {
    color: $color__publish;
  }

  .tablecell__starbutton {
    align-items: center;
    display: inline-flex;
    justify-content: center;
    min-height: 16px;
    min-width: 16px;
  }

  .tablecell__starstate {
    cursor: pointer;
    transition: background-color 0.3s ease, border-color 0.3s ease;
  }

  .tablecell__starstate--live {
    color: $color__red;
  }

  .truncate {
    display: inline-block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    width: 85%;
  }

  @include breakpoint('medium+') {
    .datatable__stickyHead.sticky__fixedTop {
      background-color: rgba($color__border--light, 0.97);
      border-bottom: 1px solid rgba($color__black, 0.05);
      display: block;
      top: 0;

      .datatable__setupDropdown {
        background: linear-gradient(to right, rgba($color__border--light, 0) 0%, $color__border--light 25%);
      }
    }
  }

  :deep(colgroup col:nth-child(2)) {
    min-width: 100px;
  }
</style>
