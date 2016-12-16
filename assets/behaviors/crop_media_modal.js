a17cms.Behaviors.crop_media_modal = function($element) {

  var $modal = parent.a17cms.Helpers.modal.get_active();

  // Parent window vars
  if($modal.length == 0) {
    alert('The library need to be loaded into an iframe, via a modal window.');
    return false;
  }

  var modal_datas = $modal.data();

  // Important : Role is mandatory
  // Identifies the role of the media in the entity ('featured', 'body', etc.)
  var role = modal_datas.role ? modal_datas.role : "";

  if(role === "") {
    alert('Role is undefined !');
    return false;
  }

  $('[data-crop-insert]').on('click',function(event){
    event.preventDefault();
    var data_of_crop = $("[data-jcrop-role]").serializeJSON();
    a17cms.Helpers.call_function_in_parent_window(modal_datas.callback, { "data": data_of_crop, "role": role });
    data_of_crop = {};
    parent.$.event.trigger({ type: "modal_close"});
  });
}
