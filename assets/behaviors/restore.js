a17cms.Behaviors.restore = function(element){
  
  var $form = $("#" + element.data("submit-form"));
  var action = $form.attr('data-restore-url');
  var revision = element.data("revision");

  element.on("click", _handleRestore);

  function _handleRestore(event) {
    event.preventDefault();    
    var input = $("<input>").
      attr("type", "hidden").
      attr("name", "revision").
      val(revision);
    
    $form.attr("action", action);  
    $form.append($(input));
    $form.submit();
  }

};
    