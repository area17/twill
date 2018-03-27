/* All the store actions are listed here */

/* Buckets */
export const GET_BUCKETS = 'getBucketsData'
export const SAVE_BUCKETS = 'saveBuckets'

/* Datatable */
export const GET_DATATABLE = 'getDatatableDatas'
export const SET_DATATABLE_NESTED = 'setDatatableNestedDatas'
export const SET_DATATABLE = 'setDatatableDatas'
export const TOGGLE_PUBLISH = 'togglePublishedData'
export const DELETE_ROW = 'deleteData'
export const RESTORE_ROW = 'restoreData'
export const TOGGLE_FEATURE = 'toggleFeaturedData'
export const BULK_PUBLISH = 'bulkPublishData'
export const BULK_FEATURE = 'bulkFeatureData'
export const BULK_EXPORT = 'bulkExportData'
export const BULK_DELETE = 'bulkDeleteData'
export const BULK_RESTORE = 'bulkRestoreData'

/* Form */
export const REPLACE_FORM = 'replaceFormData'
export const SAVE_FORM = 'saveFormData'
export const UPDATE_FORM_IN_LISTING = 'updateFormInListing'
export const CREATE_FORM_IN_MODAL = 'createFormInModal'

/* Previews */
export const GET_ALL_PREVIEWS = 'getAllPreviews'
export const GET_PREVIEW = 'getPreview'

/* Revisions */
export const GET_REVISION = 'getRevisionContent'
export const GET_CURRENT = 'getCurrentContent'

export default {
  GET_BUCKETS,
  SAVE_BUCKETS,
  GET_DATATABLE,
  SET_DATATABLE_NESTED,
  SET_DATATABLE,
  TOGGLE_PUBLISH,
  DELETE_ROW,
  RESTORE_ROW,
  TOGGLE_FEATURE,
  BULK_PUBLISH,
  BULK_FEATURE,
  BULK_EXPORT,
  BULK_DELETE,
  BULK_RESTORE,
  REPLACE_FORM,
  SAVE_FORM,
  UPDATE_FORM_IN_LISTING,
  CREATE_FORM_IN_MODAL,
  GET_ALL_PREVIEWS,
  GET_PREVIEW,
  GET_REVISION,
  GET_CURRENT
}
