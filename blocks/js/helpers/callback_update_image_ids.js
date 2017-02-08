a17cms.Helpers.callbackUpdateImageIds = function(datas) {
  var max_images = 10;
  var $editor = $("#" + datas["role"]);
  var $hidden_image_id = $("[data-image-id]", $editor);
  var current_val = $hidden_image_id.val();

  var image_datas = datas.data[0];
  var new_id = image_datas.id;

  if(new_id) {
    var imgs_ids = current_val.split(',').map(Number);
    var length = current_val != "" ? imgs_ids.length : 0;

    // Maximum images : 10
    if(imgs_ids.length > max_images) return false;

    // if image dont exist already
    var index = imgs_ids.indexOf(Number(new_id));

    if(index == -1) {
      var new_val = current_val != "" ? current_val + "," : current_val;
      $hidden_image_id.val(new_val + new_id);
      $hidden_image_id.trigger('refresh:image_thumbnails');
      $hidden_image_id.trigger('show:crop_button');
      $('[data-crop]', $editor).val("");
    }
  }
};
