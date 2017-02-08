a17cms.Helpers.callbackUpdateCropData = function(datas) {
  var $editor = $("#" + datas["role"]);
  var $hidden_crop_data = $("[data-crop]", $editor);

  if(datas) {
    $hidden_crop_data.val(JSON.stringify(datas['data']));
  }
};
