<template>
  <a17-inputframe :error="error" :label="label" :locale="locale" @localize="updateLocale" :size="size" :name="name">
    <a17-slideshow v-if="max > 1 || max === 0" :name="name" :cropContext="cropContext" :max="max" :required="required" :withAddInfo="withAddInfo" :withVideoUrl="withVideoUrl" :withCaption="withCaption" :extraMetadatas="extraMetadatas">
      <slot />
    </a17-slideshow>
    <a17-mediafield v-else :name="name" :cropContext="cropContext" :required="required" :withAddInfo="withAddInfo" :withVideoUrl="withVideoUrl" :withCaption="withCaption" :extraMetadatas="extraMetadatas">
      <slot />
    </a17-mediafield>
  </a17-inputframe>
</template>

<script>
  import localeMixin from '@/mixins/locale'
  import inputframeMixin from '@/mixins/inputFrame'

  export default {
    name: 'A17MediafieldTranslated',
    mixins: [localeMixin, inputframeMixin],
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
      cropContext: {
        type: String,
        default: ''
      },
      withAddInfo: {
        type: Boolean,
        default: true
      },
      withVideoUrl: {
        type: Boolean,
        default: true
      },
      withCaption: {
        type: Boolean,
        default: true
      },
      max: {
        type: Number,
        default: 1
      },
      extraMetadatas: {
        type: Array,
        default () {
          return []
        }
      }
    }
  }
</script>
