import { PUBLICATION } from '../mutations'

const state = {
  withPublicationToggle: window[process.env.VUE_APP_NAME].STORE.publication.withPublicationToggle || false,
  published: window[process.env.VUE_APP_NAME].STORE.publication.published || false,
  publishSubmit: (window[process.env.VUE_APP_NAME].STORE.publication.published || !window[process.env.VUE_APP_NAME].STORE.publication.withPublicationToggle) ? 'update' : 'live',
  publishedLabel: window[process.env.VUE_APP_NAME].STORE.publication.publishedLabel || 'Live',
  draftLabel: window[process.env.VUE_APP_NAME].STORE.publication.draftLabel || 'Draft',
  withPublicationTimeframe: window[process.env.VUE_APP_NAME].STORE.publication.withPublicationTimeframe || false,
  startDate: window[process.env.VUE_APP_NAME].STORE.publication.startDate || null,
  endDate: window[process.env.VUE_APP_NAME].STORE.publication.endDate || null,
  visibility: window[process.env.VUE_APP_NAME].STORE.publication.visibility || false,
  reviewProcess: window[process.env.VUE_APP_NAME].STORE.publication.reviewProcess || [],
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
  submitOptions: window[process.env.VUE_APP_NAME].STORE.publication.submitOptions || {
    draft: [
      {
        name: 'save',
        text: 'Save as draft'
      },
      {
        name: 'save-close',
        text: 'Save as draft and close'
      },
      {
        name: 'save-new',
        text: 'Save as draft and create new'
      },
      {
        name: 'cancel',
        text: 'Cancel'
      }
    ],
    live: [
      {
        name: 'publish',
        text: 'Publish'
      },
      {
        name: 'publish-close',
        text: 'Publish and close'
      },
      {
        name: 'publish-new',
        text: 'Publish and create new'
      },
      {
        name: 'cancel',
        text: 'Cancel'
      }
    ],
    update: [
      {
        name: 'update',
        text: 'Update'
      },
      {
        name: 'update-close',
        text: 'Update and close'
      },
      {
        name: 'update-new',
        text: 'Update and create new'
      },
      {
        name: 'cancel',
        text: 'Cancel'
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
