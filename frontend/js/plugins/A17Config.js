// mutations
import axios from 'axios'
import get from 'lodash/get'
import mapValues from 'lodash/mapValues'
// Plugins
import VueTimeago from 'vue-timeago'

import a17Avatar from '@/components/Avatar.vue'
import a17BrowserField from '@/components/BrowserField.vue'
// Generic Components
import a17Button from '@/components/Button.vue'
import a17Buttonbar from '@/components/ButtonBar.vue'
import a17Checkbox from '@/components/Checkbox.vue'
import a17CheckboxGroup from '@/components/CheckboxGroup.vue'
import a17ColorField from '@/components/ColorField.vue'
import a17DatePicker from '@/components/DatePicker.vue'
import a17Dialog from '@/components/Dialog.vue'
import a17Dropdown from '@/components/Dropdown.vue'
import a17FileField from '@/components/files/FileField.vue'
import a17HiddenField from '@/components/HiddenField.vue'
import a17Infotip from '@/components/Infotip.vue'
import a17Inputframe from '@/components/InputFrame.vue'
import a17Locale from '@/components/LocaleField.vue'
// Media Library
import a17MediaLibrary from '@/components/media-library/MediaLibrary.vue'
import a17MediaField from '@/components/MediaField.vue'
import a17MediaFieldTranslated from '@/components/MediaFieldTranslated.vue'
import a17Modal from '@/components/Modal.vue'
import a17Multiselect from '@/components/MultiSelect.vue'
import a17Radio from '@/components/Radio.vue'
import a17RadioGroup from '@/components/RadioGroup.vue'
import a17Select from '@/components/Select.vue'
import a17SingleCheckbox from '@/components/SingleCheckbox.vue'
import a17Singleselect from '@/components/SingleSelect.vue'
import A17SingleSelectPermissions from '@/components/SingleSelectPermissions.vue'
import a17Slideshow from '@/components/Slideshow.vue'
import a17Textfield from '@/components/Textfield.vue'
import a17VSelect from '@/components/VSelect.vue'
import a17Wysiwyg from '@/components/Wysiwyg.vue'
import a17WysiwygTipTap from '@/components/WysiwygTiptap.vue'
import Sticky from '@/directives/sticky'
// Directives
import SvgSprite from '@/directives/svg'
import Tooltip from '@/directives/tooltip'
import { MEDIA_LIBRARY } from '@/store/mutations'
// Error handler
import { globalError } from '@/utils/errors'
import { locales } from '@/utils/locale'

const isProd = process.env.NODE_ENV === 'production'

const A17Config = {
  install (Vue, opts) {
    // Globals components
    Vue.component('a17-button', a17Button)
    Vue.component('a17-infotip', a17Infotip)
    Vue.component('a17-slideshow', a17Slideshow)
    Vue.component('a17-browserfield', a17BrowserField)
    Vue.component('a17-textfield', a17Textfield)
    Vue.component('a17-hiddenfield', a17HiddenField)
    Vue.component('a17-wysiwyg', a17Wysiwyg)
    Vue.component('a17-wysiwyg-tiptap', a17WysiwygTipTap)
    Vue.component('a17-inputframe', a17Inputframe)
    Vue.component('a17-mediafield', a17MediaField)
    Vue.component('a17-mediafield-translated', a17MediaFieldTranslated)
    Vue.component('a17-radio', a17Radio)
    Vue.component('a17-radiogroup', a17RadioGroup)
    Vue.component('a17-checkbox', a17Checkbox)
    Vue.component('a17-singlecheckbox', a17SingleCheckbox)
    Vue.component('a17-checkboxgroup', a17CheckboxGroup)
    Vue.component('a17-singleselect-permissions', A17SingleSelectPermissions)
    Vue.component('a17-multiselect', a17Multiselect)
    Vue.component('a17-singleselect', a17Singleselect)
    Vue.component('a17-select', a17Select)
    Vue.component('a17-vselect', a17VSelect)
    Vue.component('a17-locale', a17Locale)
    Vue.component('a17-dropdown', a17Dropdown)
    Vue.component('a17-buttonbar', a17Buttonbar)
    Vue.component('a17-modal', a17Modal)
    Vue.component('a17-dialog', a17Dialog)
    Vue.component('a17-datepicker', a17DatePicker)
    Vue.component('a17-filefield', a17FileField)
    Vue.component('a17-colorfield', a17ColorField)
    Vue.component('a17-avatar', a17Avatar)

    // Media Library
    Vue.component('a17-medialibrary', a17MediaLibrary)

    // Globale Vue mixin : Use global mixins sparsely and carefully!
    Vue.mixin({
      methods: {
        openFreeMediaLibrary: function () {
          this.$store.commit(MEDIA_LIBRARY.UPDATE_MEDIA_CONNECTOR, null) // reset connector
          this.$store.commit(MEDIA_LIBRARY.RESET_MEDIA_TYPE) // reset to first available type
          this.$store.commit(MEDIA_LIBRARY.UPDATE_REPLACE_INDEX, -1) // we are not replacing an image here
          this.$store.commit(MEDIA_LIBRARY.UPDATE_MEDIA_MAX, 0) // set max to 0
          this.$store.commit(MEDIA_LIBRARY.UPDATE_MEDIA_FILESIZE_MAX, 0) // set filesize max to 0
          this.$store.commit(MEDIA_LIBRARY.UPDATE_MEDIA_WIDTH_MIN, 0) // set width min to 0
          this.$store.commit(MEDIA_LIBRARY.UPDATE_MEDIA_HEIGHT_MIN, 0) // set height min to 0
          this.$store.commit(MEDIA_LIBRARY.UPDATE_MEDIA_MODE, false) // set the strict to false (you can change the active type)

          if (this.$root.$refs.mediaLibrary) this.$root.$refs.mediaLibrary.open()
        }
      }
    })

    // Configurations
    Vue.config.productionTip = isProd
    Vue.config.devtools = !isProd
    Vue.prototype.$http = axios

    window.$trans = Vue.prototype.$trans = function (key, defaultValue) {
      return get(window[process.env.VUE_APP_NAME].twillLocalization.lang, key, defaultValue)
    }

    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

    axios.interceptors.response.use((response) => response, (error) => {
      globalError('CONTENT', error)

      return Promise.reject(error)
    })

    // Plugins
    Vue.use(VueTimeago, {
      name: 'timeago', // component name
      locale: window[process.env.VUE_APP_NAME].twillLocalization.locale,
      locales: mapValues(locales, 'date-fns')
    })

    // Directives
    Vue.use(SvgSprite)
    Vue.use(Tooltip)
    Vue.use(Sticky)
  }
}

export default A17Config
