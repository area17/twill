<template>
  <div class="video">
    <a17-locale type="a17-textfield" :attributes="{ label: 'Title', name: fieldName('title'), type: 'text', maxlength: 250, inStore: 'value' }"></a17-locale>
    <a17-locale type="a17-textfield" :attributes="{ label: 'Video URL', name: fieldName('url'), type: 'text', maxlength: 250, inStore: 'value' }"></a17-locale>
    <a17-datepicker :name="fieldName('date')" label="Video Date" place-holder="Video Date" :enableTime="false" :allowInput="true" :clear="true" inStore="date" ></a17-datepicker>
    <a17-inputframe label="Thumbnail"><a17-mediafield :name="fieldName('thumbnail')" cropContext="cover" ref="thumbnail">Minimum image width 1300px</a17-mediafield></a17-inputframe>
    <a17-inputframe label="Slideshow"><a17-slideshow :name="fieldName('slideshow')" :max="4" cropContext="slideshow" ref="slideshow">Minimum image width / height: 1500px</a17-slideshow></a17-inputframe>
    <a17-inputframe label="News"><a17-browserfield :name="fieldName('news')" :max="6" itemLabel="News" endpoint="http://www.mocky.io/v2/59d77e61120000ce04cb1c5b" modalTitle="Attach news" ref="news">Add up to 6 news</a17-browserfield></a17-inputframe>
  </div>
</template>

<script>
  // Demo Video Block

  // This block can used to create other blocks in your Application
  // This show how you can easily create complex blocks that are connected with the media library or any related content

  export default {
    name: 'A17Video',
    props: {
      name: {
        type: String,
        required: true
      }
    },
    methods: {
      fieldName: function (id) {
        return this.name + '[' + id + ']'
      }
    },
    beforeDestroy: function () {
      // Demo :
      // Delete a video block : we need to remove the media / slideshow / news contents from the global store
      // Other form fields (like text, datepicker will take care of this by themselves)

      this.$refs.thumbnail.deleteMedia()
      this.$refs.slideshow.deleteSlideshow()
      this.$refs.news.deleteAll()
    }
  }
</script>

<style lang="scss" scoped>
  @import "../../../scss/setup/variables.scss";
  @import "../../../scss/setup/colors.scss";
  @import "../../../scss/setup/mixins.scss";

  .video {
    margin-top:-15px;
  }
</style>
