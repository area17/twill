import { PUBLICATION } from '../mutations'

const state = {
  withPublicationToggle: window.STORE.publication.withPublicationToggle || false,
  published: window.STORE.publication.published || false,
  publishSubmit: (window.STORE.publication.published || !window.STORE.publication.withPublicationToggle) ? 'update' : 'live',
  publishedLabel: window.STORE.publication.publishedLabel || 'Live',
  draftLabel: window.STORE.publication.draftLabel || 'Draft',
  withPublicationTimeframe: window.STORE.publication.withPublicationTimeframe || false,
  startDate: window.STORE.publication.startDate || null,
  endDate: window.STORE.publication.endDate || null,
  visibility: window.STORE.publication.visibility || false,
  reviewProcess: window.STORE.publication.reviewProcess || [],
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
  submitDisableMessage: window.STORE.publication.submitDisableMessage || '',
  submitOptions: window.STORE.publication.submitOptions || {
    draft: [
      {
        name: 'save',
        text: 'Save as draft',
        disabled: false
      },
      {
        name: 'save-close',
        text: 'Save as draft and close',
        disabled: false
      },
      {
        name: 'save-new',
        text: 'Save as draft and create new',
        disabled: false
      },
      {
        name: 'cancel',
        text: 'Cancel',
        disabled: false
      }
    ],
    live: [
      {
        name: 'publish',
        text: 'Publish',
        disabled: false
      },
      {
        name: 'publish-close',
        text: 'Publish and close',
        disabled: false
      },
      {
        name: 'publish-new',
        text: 'Publish and create new',
        disabled: false
      },
      {
        name: 'cancel',
        text: 'Cancel',
        disabled: false
      }
    ],
    update: [
      {
        name: 'update',
        text: 'Update',
        disabled: false
      },
      {
        name: 'update-close',
        text: 'Update and close',
        disabled: false
      },
      {
        name: 'update-new',
        text: 'Update and create new',
        disabled: false
      },
      {
        name: 'cancel',
        text: 'Cancel',
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
    return (state.published || !state.withPublicationToggle) ? state.submitOptions[state.publishSubmit] : state.submitOptions['draft']
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
  }
}

export default {
  state,
  getters,
  mutations
}
