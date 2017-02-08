a17cms.Helpers.sirTrevorBaseDiaporamaEditor = function(opts) {
  var opts = opts || {};
  var options = {
    bt_label: "Add Images",
    bt_label_crop: "Crop first image",
    note: "",
    name: "image_id",
    crop_field_name: "image_id_crop"
  };

  var HTMLnote = (options.note != "") ? "<small class='a17-small-note hint' style='display:inline-block; margin-left:15px;'>" + options.note + "</small>" : "";

  var image_editor   = "<div class='input' data-images-containers>";
    image_editor  +=     "<div class='a17cms-image-list'></div>";
    image_editor  +=     "<input type='hidden' class='a17-input-block' name='" + options.name + "' autocomplete='false' data-image-id />";
    image_editor  +=     "<input type='hidden' class='a17-input-block' name='" + options.crop_field_name + "' autocomplete='false' data-crop />";
    image_editor  +=     "<button type='button' class='btn btn-small' data-bt-images>" + options.bt_label + "</button> ";
    image_editor  +=     "<button type='button' class='btn btn-small btn-border disabled' data-crop-image>" + options.bt_label_crop + "</button> ";
    image_editor  +=     HTMLnote;
    image_editor  += "</div>";

  return image_editor;
}
