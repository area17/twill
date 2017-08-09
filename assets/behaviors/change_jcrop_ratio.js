a17cms.Behaviors.change_jcrop_ratio = function($element) {
  $element.on('change', function(event) {
    var $option = $(this).find('option:selected');
    $ratio = $option.data('ratio');
    $imgId = $option.data('jcrop-img-id');
    $("[data-jcrop-id='" + $imgId + "']").data('Jcrop').setOptions({ aspectRatio: $ratio });
  });
}
