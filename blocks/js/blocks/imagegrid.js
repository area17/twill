SirTrevor.Blocks.Imagegrid = (function(){

  var image_editor_left = a17cms.Helpers.sirTrevorBaseImageEditor({
    note : "Required sizes : min-width: 800px – min-height: 800px",
    bt_label: "Add Left Image",
    bt_label_remove: "Remove Left Image",
    name: "image_left_id",
    crop_field_name: "image_left_id_crop"
  });
  var image_editor_right = a17cms.Helpers.sirTrevorBaseImageEditor({
    note : "Required sizes : min-width: 800px – min-height: 800px",
    bt_label: "Add Right Image",
    bt_label_remove: "Remove Right Image",
    name: "image_right_id",
    crop_field_name: "image_right_id_crop"
  });

  return SirTrevor.Blocks.Masterpreview.extend({

    type: "imagegrid",
    title: "Two images",

    icon_name: 'image',

    editorHTML: a17cms.Helpers.sirTrevorBaseEditor("Two images", image_editor_left + image_editor_right),


    validations: ['requireImageLeft', 'requireImageRight'],

    requireImageLeft: function() {
      var self = this;
      var $imagesLeft = self.$editor.find('[name="image_left_id"]');

      if ($imagesLeft.val() === "") {
      self.setError($imagesLeft, "You need to have one image in the left column.");
      }
    },

    requireImageRight: function() {
      var self = this;
      var $imagesRight = self.$editor.find('[name="image_right_id"]');

      if ($imagesRight.val() === "") {
      self.setError($images, "You need to have one image in the right column.");
      }
    }
  });
})();
