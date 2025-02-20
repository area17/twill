<template>
  <a17-inputframe :error="error"
                  :label="label"
                  :locale="locale"
                  @localize="updateLocale"
                  :size="size"
                  :note="note"
                  :name="name">
    <a17-slideshow v-if="max > 1 || max === 0"
                   :name="name"
                   :cropContext="cropContext"
                   :max="max"
                   :required="required"
                   :buttonOnTop="buttonOnTop"
                   :withAddInfo="withAddInfo"
                   :withVideoUrl="withVideoUrl"
                   :withCaption="withCaption"
                   :altTextMaxLength="altTextMaxLength"
                   :captionMaxLength="captionMaxLength"
                   :extraMetadatas="extraMetadatas"
                   :disabled="disabled">
      <slot />
    </a17-slideshow>
    <a17-mediafield v-else
                    :name="name"
                    :cropContext="cropContext"
                    :required="required"
                    :withAddInfo="withAddInfo"
                    :withVideoUrl="withVideoUrl"
                    :withCaption="withCaption"
                    :altTextMaxLength="altTextMaxLength"
                    :captionMaxLength="captionMaxLength"
                    :extraMetadatas="extraMetadatas"
                    :disabled="disabled">
      <slot />
    </a17-mediafield>
  </a17-inputframe>
</template>

<script>
  import inputframeMixin from '@/mixins/inputFrame'
  import localeMixin from '@/mixins/locale'
  import mediaFieldMixin from '@/mixins/mediaField.js'

  export default {
    name: 'A17MediafieldTranslated',
    mixins: [localeMixin, inputframeMixin, mediaFieldMixin],
    props: {
      name: {
        type: String,
        required: true
      },
      disabled: {
        type: Boolean,
        default: false
      },
      required: {
        type: Boolean,
        default: false
      },
      max: {
        type: Number,
        default: 1
      },
      buttonOnTop: {
        type: Boolean,
        default: false
      }
    }
  }
</script>
