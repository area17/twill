a17cms.Helpers.callbackUpdateResourceId = function(datas) {
  console.log(datas);
  var $editor = $("#" + datas["role"]);
  var $hidden_resource = $("[data-resource-id]", $editor);
  var resource_datas = datas.data[0];
  var new_resource = resource_datas.id;
  if(typeof(resource_datas.resourceName) != "undefined" && resource_datas.resourceName !== null) {
    new_resource = resource_datas.resourceName;
  }

  if(new_resource) {
    $hidden_resource.val(new_resource);
    $hidden_resource.trigger('refresh:resource_id');
  }
};
