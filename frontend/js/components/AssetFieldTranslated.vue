<template>
  <a17-inputframe :error="error" :label="label" :locale="locale" @localize="updateLocale" :size="size" :name="name" :note="fieldNote">
    <a17-assetfield
      :name="name"
      :cropContext="cropContext"
      :max="max"
      :required="required"
      :button-on-top="buttonOnTop"
      :with-add-info="withAddInfo"
      :with-video-url="withVideoUrl"
      :with-caption="withCaption"
      :altTextMaxLength="altTextMaxLength"
      :captionMaxLength="captionMaxLength"
      :extraMetadatas="extraMetadatas"
      :disabled="disabled"
    ></a17-assetfield>
  </a17-inputframe>
</template>

<script>
  import inputframeMixin from '@/mixins/inputFrame'
  import mediaFieldMixin from '@/mixins/mediaField.js'
  import localeMixin from "@/mixins/locale";


  export default {
    name: "A17AssetFieldTranslated",
    mixins: [localeMixin, inputframeMixin, mediaFieldMixin],
    props: {
      fieldNote: {
        type: String,
        default: ''
      },
      name: {
        type: String,
        required: true
      },
      itemLabel: {
        type: String,
        default: 'asset'
      },
      max: {
        type: Number,
        default: 10
      },
      allowFile: {
        type: Boolean,
        default: true
      },
      disabled: {
        type: Boolean,
        default: false
      },
      buttonOnTop: {
        type: Boolean,
        default: false
      },
      required: {
        type: Boolean,
        default: false
      },
    },
  }
</script>


