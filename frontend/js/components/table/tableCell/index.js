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
export const TableCellActions = require('./TableCellActions')
export const TableCellBulk = require('./TableCellBulk')
export const TableCellDates = require('./TableCellDates')
export const TableCellFeatured = require('./TableCellFeatured')
export const TableCellDraggable = require('./TableCellDraggable')
export const TableCellLanguages = require('./TableCellLanguages')
export const TableCellPublished = require('./TableCellPublished')
export const TableCellGeneric = require('./TableCellGeneric')
export const TableCellNested = require('./TableCellNested')
export const TableCellThumbnail = require('./TableCellThumbnail')

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
