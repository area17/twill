import TableCellActions_ from './TableCellActions'
import TableCellBulk_ from './TableCellBulk'
import TableCellDates_ from './TableCellDates'
import TableCellFeatured_ from './TableCellFeatured'
import TableCellDraggable_ from './TableCellDraggable'
import TableCellLanguages_ from './TableCellLanguages'
import TableCellPublished_ from './TableCellPublished'
import TableCellGeneric_ from './TableCellGeneric'
import TableCellNested_ from './TableCellNested'
import TableCellThumbnail_ from './TableCellThumbnail'

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
