a17cms.Behaviors.preview = function(element){

  var $mask = null;
  var loading_klass = "js-loading";

  var $form = $("#" + element.data("submit-form"));
  var revision = element.data("preview-revision");
  var compare = element.data("compare");
  var errorsContainer = $('[data-ajax-errors-container]');

  element.on("click", _handlePreview);

  function _loadingForm() {
    a17cms.Helpers.navigate_away.deactivate($form);
    errorsContainer.empty();
    $mask = $('<div class="a17modal_mask"></div>').appendTo('body')
    $form.addClass(loading_klass);
  }

  function _loadedForm() {
    $form.removeClass(loading_klass);
    $mask.remove();
  }

  function _isLoading() {
    return $form.hasClass(loading_klass);
  }

  function _openPreviewModal() {
    $.event.trigger({
      "type" : "modal_open",
      "modal_config": window[element.data("options")]
    });
  }

  function _handleErrors(jqXHR, errorThrown) {
    if (jqXHR.status == 404) {
      text_message = "Ajax submit url not found";
    } else if (jqXHR.status == 422) {
      var html_error = '<div class="message message-error"><p>A validation error occured</p><ul>{{error}}</ul><a href="#" class="close" data-behavior="close_message">Close</a></div>';
      var errors_markup = "";

      $.each(jQuery.parseJSON(jqXHR.responseText), function(key, value) {
        $form.find("div.input." + key).addClass('field_with_errors');
        errors_markup += "<li>" + value + "</li>";
      });

      errorsContainer.html(html_error.replace("{{error}}", errors_markup));
      a17cms.LoadBehavior(errorsContainer.get(0))
      return;
    } else {
      text_message = "Something went wrong, please retry";
      console.log(errorThrown);
    }

    $.event.trigger({
      type: "notification_open",
      message: text_message,
      style: "error",
      is_centered: true
    });
  }

  function _submit_form() {
    if(_isLoading()) return false;

    _loadingForm();

    var method = $form.attr('method') || "POST";
    var url = $form.attr('data-preview-url');

    if (typeof SirTrevor !== "undefined") {
        var st = SirTrevor.getInstance();
        st.onFormSubmit(true);
        $('.a17cms-editor', $form).find('input, select, textarea').prop("disabled", true);
    }

    var params = $form.serializeArray();

    $('.a17cms-editor', $form).find('input, select, textarea').prop("disabled", false);

    params.push({name: "_preview", value: 1});
    if (revision !== undefined) {
      params.push({name: "_revision", value: revision});
    }
    if (compare !== undefined) {
      params.push({name: "_compare", value: 1});
    }

    $.ajax({
      type: method,
      url: url,
      data: $.param(params)
    }).done(function(response) {
      _openPreviewModal();
    }).fail(function(jqXHR,textStatus,errorThrown) {
      _handleErrors(jqXHR, errorThrown)
    }).always(function() {
      _loadedForm();
    });
  }

  function _handlePreview(event) {
    event.preventDefault();

    if ($form.length > 0) {
      _submit_form();
    } else {
      console.log("No form with ID: " + element.data("submit-form"));
    }
  }

};

