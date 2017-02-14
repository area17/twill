a17cms.Helpers.callbackUpdateResourceId = function(datas) {
  var $editor = $("#" + datas["role"]);
  var $hidden_resource = $("[data-resource-id]", $editor);
  var $hidden_resource_title = $("[data-resource-title-input]", $editor);
  var resource_datas = datas.data[0];
  var new_resource = resource_datas.id;

  if(new_resource) {
    $hidden_resource.val(new_resource);
    if(typeof(resource_datas.resourceName) != "undefined" && resource_datas.resourceName !== null) {
        $hidden_resource_title.val(resource_datas.resourceName);
    }
    $hidden_resource.trigger('refresh:resource_id');
  }
};
