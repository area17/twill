SirTrevor.Blocks.Image = (function() {

  var image_editor = a17cms.Helpers.sirTrevorBaseImageEditor({
    note: "Required sizes : min-width: 2100px – min-height: 1410px"
  });

  return SirTrevor.Blocks.Masterpreview.extend({
    type: "image",
    title: "Image",
    icon_name: 'image',

    editorHTML: a17cms.Helpers.sirTrevorBaseEditor("Image", image_editor),

    validations: ['requireImage'],
    requireImage: function() {
      var self = this;
      var $image = self.$editor.find('[name="image_id"]');
      if ($image.val() === "") {
        self.setError($image, "You need to have one image.");
      }
    }
  });

})();
