SirTrevor.Blocks.Imagetext = (function(){

  var html_editor_image = a17cms.Helpers.sirTrevorBaseImageEditor({
    note: "Required sizes : min-width: 800px – min-height: 800px"
  });

  var html_editor_fields = a17cms.Helpers.sirTrevorBaseTextFields([
    {
      name: "title",
      label: "Title",
      type: "input",
      maxlength: "100",
      placeholder: "",
    },
    {
      name: "text",
      label: "Text",
      type: "textarea",
    }
  ]
  );

  html_editor_fields  += "<div class='input'>";
  html_editor_fields  +=     "<label>Image position</label>";
  html_editor_fields  +=     "<select name='image_position' class='a17-input-block' data-behavior='selector' data-minimum-results-for-search='3' data-selector-width='25%'>";
  html_editor_fields  +=         "<option value='1'>Left</option>";
  html_editor_fields  +=         "<option value='0'>Right</option>";
  html_editor_fields  +=     "</select>";
  html_editor_fields  += "</div>";

  return SirTrevor.Blocks.Masterpreview.extend({

    type: "imagetext",
    title: "Image + text",

    icon_name: 'image',

    editorHTML: a17cms.Helpers.sirTrevorBaseEditor("Image + text", html_editor_image + html_editor_fields),

    // Custom Validation

    validations: ['requireImage'],

    requireImage: function() {
      var self = this;
      var $image = self.$editor.find('[name="image_id"]');

      if ($image.val() === "") {
        self.setError($image, "You need to have one image.");
      }
    },
  });

})();
