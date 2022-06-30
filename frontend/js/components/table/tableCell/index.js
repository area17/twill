import TableCellActions_ from './TableCellActions.vue'
import TableCellBulk_ from './TableCellBulk.vue'
import TableCellDates_ from './TableCellDates.vue'
import TableCellFeatured_ from './TableCellFeatured.vue'
import TableCellDraggable_ from './TableCellDraggable.vue'
import TableCellLanguages_ from './TableCellLanguages.vue'
import TableCellPublished_ from './TableCellPublished.vue'
import TableCellGeneric_ from './TableCellGeneric.vue'
import TableCellNested_ from './TableCellNested.vue'
import TableCellThumbnail_ from './TableCellThumbnail.vue'

export const TableCellSpecificColumns = [
  'draggable',
  'bulk',
  'languages',
  'featured',
  'published',
  'thumbnail',
  'publish_start_date',
  'nested'
]

export const TableCellPrefix = 'a17-table-cell-'
/* Components */
export const TableCellActions = TableCellActions_
export const TableCellBulk = TableCellBulk_
export const TableCellDates = TableCellDates_
export const TableCellFeatured = TableCellFeatured_
export const TableCellDraggable = TableCellDraggable_
export const TableCellLanguages = TableCellLanguages_
export const TableCellPublished = TableCellPublished_
export const TableCellGeneric = TableCellGeneric_
export const TableCellNested = TableCellNested_
export const TableCellThumbnail = TableCellThumbnail_

export default {
  [TableCellPrefix + 'actions']: TableCellActions,
  [TableCellPrefix + 'bulk']: TableCellBulk,
  [TableCellPrefix + 'publish_start_date']: TableCellDates,
  [TableCellPrefix + 'featured']: TableCellFeatured,
  [TableCellPrefix + 'draggable']: TableCellDraggable,
  [TableCellPrefix + 'generic']: TableCellGeneric,
  [TableCellPrefix + 'languages']: TableCellLanguages,
  [TableCellPrefix + 'published']: TableCellPublished,
  [TableCellPrefix + 'nested']: TableCellNested,
  [TableCellPrefix + 'thumbnail']: TableCellThumbnail
}
