import { PUBLICATION } from '../mutations'

const state = {
  withPublicationToggle: window[process.env.VUE_APP_NAME].STORE.publication.withPublicationToggle || false,
  published: window[process.env.VUE_APP_NAME].STORE.publication.published || false,
  publishSubmit: (window[process.env.VUE_APP_NAME].STORE.publication.published || !window[process.env.VUE_APP_NAME].STORE.publication.withPublicationToggle) ? 'update' : 'live',
  publishedLabel: window[process.env.VUE_APP_NAME].STORE.publication.publishedLabel || 'Live',
  draftLabel: window[process.env.VUE_APP_NAME].STORE.publication.draftLabel || 'Draft',
  expiredLabel: window[process.env.VUE_APP_NAME].STORE.publication.expiredLabel || 'Expired',
  scheduledLabel: window[process.env.VUE_APP_NAME].STORE.publication.scheduledLabel || 'Scheduled',
  withPublicationTimeframe: window[process.env.VUE_APP_NAME].STORE.publication.withPublicationTimeframe || false,
  startDate: window[process.env.VUE_APP_NAME].STORE.publication.startDate || null,
  endDate: window[process.env.VUE_APP_NAME].STORE.publication.endDate || null,
  visibility: window[process.env.VUE_APP_NAME].STORE.publication.visibility || false,
  reviewProcess: window[process.env.VUE_APP_NAME].STORE.publication.reviewProcess || [],
  userInfo: window[process.env.VUE_APP_NAME].STORE.publication.userInfo || null,
  createWithoutModal: window[process.env.VUE_APP_NAME].STORE.publication.createWithoutModal || false,
  hasUnsavedChanges: false,
  saveType: undefined,
  visibilityOptions: [
    {
      value: 'public',
      label: 'Public'
    },
    {
      value: 'private',
      label: 'Private'
    }
  ],
  submitDisableMessage: window[process.env.VUE_APP_NAME].STORE.publication.submitDisableMessage || '',
  // @todo(3.x): Remove 'submitOptions' default values as they are now defined in ModuleController.php
  submitOptions: window[process.env.VUE_APP_NAME].STORE.publication.submitOptions || {
    draft: [
      {
        name: 'save',
        text: window.$trans('publisher.save', 'Save as draft'),
        disabled: false
      },
      {
        name: 'save-close',
        text: window.$trans('publisher.save-close', 'Save as draft and close'),
        disabled: false
      },
      {
        name: 'save-new',
        text: window.$trans('publisher.save-new', 'Save as draft and create new'),
        disabled: false
      },
      {
        name: 'cancel',
        text: window.$trans('publisher.cancel', 'Cancel'),
        disabled: false
      }
    ],
    live: [
      {
        name: 'publish',
        text: window.$trans('publisher.publish', 'Publish'),
        disabled: false
      },
      {
        name: 'publish-close',
        text: window.$trans('publisher.publish-close', 'Publish and close'),
        disabled: false
      },
      {
        name: 'publish-new',
        text: window.$trans('publisher.publish-new', 'Publish and create new'),
        disabled: false
      },
      {
        name: 'cancel',
        text: window.$trans('publisher.cancel', 'Cancel'),
        disabled: false
      }
    ],
    update: [
      {
        name: 'update',
        text: window.$trans('publisher.update', 'Update'),
        disabled: false
      },
      {
        name: 'update-close',
        text: window.$trans('publisher.update-close', 'Update and close'),
        disabled: false
      },
      {
        name: 'update-new',
        text: window.$trans('publisher.update-new', 'Update and create new'),
        disabled: false
      },
      {
        name: 'cancel',
        text: window.$trans('publisher.cancel', 'Cancel'),
        disabled: false
      }
    ]
  }
}

// getters
const getters = {
  reviewProcessComplete: state => {
    return state.reviewProcess.filter(reviewProcess => reviewProcess.checked)
  },
  getSubmitOptions: state => {
    return (state.published || !state.withPublicationToggle) ? state.submitOptions[state.publishSubmit] : state.submitOptions.draft
  },
  isEnabledSubmitOption: (state, getters) => name => {
    // default is true (for example on custom form or if we dont have submitOptions setup)
    let enabled = true
    let activeOption = {}

    const matches = getters.getSubmitOptions.filter((el) => el.name === name)
    if (matches.length) activeOption = matches[0]

    if (activeOption.hasOwnProperty('disabled')) {
      enabled = !activeOption.disabled
    }

    return enabled
  },
  getSaveType: (state, getters) => {
    return state.saveType || getters.getSubmitOptions[0].name
  }
}

const mutations = {
  [PUBLICATION.UPDATE_PUBLISH_START_DATE] (state, newValue) {
    state.startDate = newValue
  },
  [PUBLICATION.UPDATE_PUBLISH_END_DATE] (state, newValue) {
    state.endDate = newValue
  },
  [PUBLICATION.UPDATE_PUBLISH_STATE] (state, newValue) {
    state.published = newValue
  },
  [PUBLICATION.UPDATE_PUBLISH_SUBMIT] (state) {
    state.publishSubmit = (state.published || !state.withPublicationToggle) ? 'update' : 'live'
  },
  [PUBLICATION.UPDATE_PUBLISH_VISIBILITY] (state, newValue) {
    state.visibility = newValue
  },
  [PUBLICATION.UPDATE_REVIEW_PROCESS] (state, newValue) {
    let currentStep = ''
    let currentIndex = -1

    if (newValue.length) {
      currentStep = newValue[newValue.length - 1]

      state.reviewProcess.forEach(function (option, index) {
        if (option.value === currentStep) currentIndex = index
      })
    }

    // update disabled states & checked state
    state.reviewProcess.forEach(function (option, index) {
      option.checked = index <= currentIndex
      option.disabled = !(index === currentIndex || index === (currentIndex + 1))
    })
  },
  [PUBLICATION.UPDATE_SAVE_TYPE] (state, newValue) {
    state.saveType = newValue
  },
  [PUBLICATION.UPDATE_HAS_UNSAVED_CHANGES] (state, newValue) {
    state.hasUnsavedChanges = newValue
  }
}

export default {
  state,
  getters,
  mutations
}
