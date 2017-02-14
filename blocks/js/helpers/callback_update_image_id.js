a17cms.Helpers.callbackUpdateImageId = function(datas) {
  var $editor = $("#" + datas["role"]);
  var $hidden_image_id = $("[data-image-id]", $editor);
  var image_datas = datas.data[0];
  var new_id = image_datas.id;

  if(new_id) {
    $hidden_image_id.val(new_id);
    $hidden_image_id.trigger('refresh:image_thumbnails');
    $hidden_image_id.trigger('show:crop_button');
    $hidden_image_id.trigger('show:remove_button');
    $('[data-crop]', $editor).val("");
  }
};
