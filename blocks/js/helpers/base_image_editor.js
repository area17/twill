a17cms.Helpers.sirTrevorBaseImageEditor = function(opts) {
  var opts = opts || {};
  var options = {
    bt_label: "Add Image",
    bt_label_remove: "Remove Image",
    bt_label_crop: "Crop Image",
    note: "",
    name: "image_id",
    crop_field_name: "image_id_crop"
  };

  // extend default options
  $.extend( true, options, opts );

  var HTMLnote = (options.note != "") ? "<small class='a17-small-note hint' style='display:block; margin-top:15px;'>" + options.note + "</small>" : "";

  var image_editor   = "<div class='input a17cms-image-list--single' data-images-containers>";
    image_editor  +=     "<div class='a17cms-image-list'></div>";
    image_editor  +=     "<input type='hidden' class='a17-input-block' name='" + options.name + "' autocomplete='false' data-image-id />";
    image_editor  +=     "<input type='hidden' class='a17-input-block' name='" + options.crop_field_name +"' autocomplete='false' data-crop />";
    image_editor  +=     "<button type='button' class='btn btn-small' data-bt-image>" + options.bt_label + "</button> ";
    image_editor  +=     "<button type='button' class='btn btn-small btn-border disabled' data-crop-image>" + options.bt_label_crop + "</button> ";
    image_editor  +=     "<button type='button' class='btn btn-small btn-border disabled' data-remove-image>" + options.bt_label_remove + "</button>";
    image_editor  +=     HTMLnote;
    image_editor  += "</div>";

  return image_editor;
}
