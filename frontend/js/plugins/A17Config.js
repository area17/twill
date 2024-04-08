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


const A17Config = {
  install (app, opts) {
    // Globals components
    app.component('a17-button', a17Button)
    app.component('a17-infotip', a17Infotip)
    app.component('a17-slideshow', a17Slideshow)
    app.component('a17-browserfield', a17BrowserField)
    app.component('a17-textfield', a17Textfield)
    app.component('a17-hiddenfield', a17HiddenField)
    app.component('a17-wysiwyg', a17Wysiwyg)
    app.component('a17-wysiwyg-tiptap', a17WysiwygTipTap)
    app.component('a17-inputframe', a17Inputframe)
    app.component('a17-mediafield', a17MediaField)
    app.component('a17-mediafield-translated', a17MediaFieldTranslated)
    app.component('a17-radio', a17Radio)
    app.component('a17-radiogroup', a17RadioGroup)
    app.component('a17-checkbox', a17Checkbox)
    app.component('a17-singlecheckbox', a17SingleCheckbox)
    app.component('a17-checkboxgroup', a17CheckboxGroup)
    app.component('a17-singleselect-permissions', A17SingleSelectPermissions)
    app.component('a17-multiselect', a17Multiselect)
    app.component('a17-singleselect', a17Singleselect)
    app.component('a17-select', a17Select)
    app.component('a17-vselect', a17VSelect)
    app.component('a17-locale', a17Locale)
    app.component('a17-dropdown', a17Dropdown)
    app.component('a17-buttonbar', a17Buttonbar)
    app.component('a17-modal', a17Modal)
    app.component('a17-dialog', a17Dialog)
    app.component('a17-datepicker', a17DatePicker)
    app.component('a17-filefield', a17FileField)
    app.component('a17-colorfield', a17ColorField)
    app.component('a17-avatar', a17Avatar)

    // Media Library
    app.component('a17-medialibrary', a17MediaLibrary)

    // Globale app mixin : Use global mixins sparsely and carefully!
    app.mixin({
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
    app.config.globalProperties.$http = axios
    app.config.compilerOptions.whitespace = 'condense'

    window.$trans = app.config.globalProperties.$trans = function (key, defaultValue) {
      return get(window[process.env.VUE_APP_NAME].twillLocalization.lang, key, defaultValue)
    }

    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

    axios.interceptors.response.use((response) => response, (error) => {
      globalError('CONTENT', error)

      return Promise.reject(error)
    })

    // Plugins
    app.use(VueTimeago, {
      name: 'timeago', // component name
      locale: window[process.env.VUE_APP_NAME].twillLocalization.locale,
      locales: mapValues(locales, 'date-fns')
    })

    // Directives
    app.use(SvgSprite)
    app.use(Tooltip)
    app.use(Sticky)
  }
}

export default A17Config
