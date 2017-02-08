/**
 * Master block :
 * Block with [Edit] and [Preview] button
 *
 */

SirTrevor.Blocks.Masterpreview = (function(){

  return SirTrevor.Blocks.Base.extend({

  // -------------- Little helper -------------- //

  consoleMessage: function(message) {
    console.warn("Blocks Editor – Error : " + message);
  },

  // -------------- Block Options -------------- //

  optionCallback: function(){
    return "callbackUpdateImageId";
  },

  optionCallbackResource: function(){
    return "callbackUpdateResourceId";
  },

  // -------------- Resources Helpers -------------- //

  /* getResourceContainerClosest
    return the HTML element that is closest to a children
  */
  getResourceContainerClosest: function($el) {
    return $el.closest('[data-resources-containers]');
  },

  /* getAllResourceContainer
    return the HTML elements that are containing resources related inputs
  */
  getAllResourceContainer: function() {
    var $containers = this.$editor.find('[data-resources-containers]');

    return (!$containers.length) ? false : $containers;
  },

  /* getResourceIdField
    return the input hidden containing the id of the resource
  */
  getResourceIdField: function($container) {
    return $container.find('[data-resource-id]');
  },

  // -------------- Medias Helpers -------------- //

  /* getThumbImageHTML
    return the HTML of the image list in the Editor view
  */
  getThumbImageHTML: function($field) {
    var self = this;
    var output = "";
    var val = $field.val();

    if(val !== "") {
      $.ajax({
        url: self.option_library_thumbnail,
        data: { id: val }
      }).done(function(response) {
        output += "<div class='a17cms-image-thumbnail'>";
        output +=     "<div class='a17cms-image-thumbnail-inner'>";
        output +=         "<img src='" + response + "' />";
        output +=     "</div>";
        output += "</div>";
      }).fail(function(jqXHR,textStatus,errorThrown) {
        self.consoleMessage("Can't find the image URL in Items with ID : " + val + " (getListImagesHTML)");
        console.log(val);
      }).always(function() {
        $field.prev().html(output);
      });
    } else {
      $field.prev().html(output);
    }
  },

  // -------------- Block Events -------------- //

  loadData: function(data){
    console.warn("loadData");

    var self = this;
    var $texts = self.getInputBlock();

    self.isLoaded = true;

    $.each(data, function(key, new_value) {
      var $field = $texts.filter('*[name="' + key + '"]');
      if($field.length) self.setDataToField($field, new_value);
    });

    //after block render
    self.afterLoadData(data);

    //build the output
    self.buildTemplate();
  },

  afterLoadData: function(data) {
  },

  onBlockRender: function(){
    console.warn("onBlockRender");

    var self = this;
    var $editor = self.$editor;
    var $texts = self.getInputBlock();
    var $submits = self.getSubmitBlock();

    self.setEditor();

    //build the output
    if(self.isLoaded === false) self.buildNewBlock();

    // Click handler : custom submit
    $submits.on('click', function(e) {
      e.preventDefault();

      data = $texts.serializeJSON();
      self.$editor.find('.a17cms-form-editor').hide();
      self.$editor.find('.a17-preview-editor').show();
      self.setAndLoadData(data);
    });

    // build the image lists in the edit mode
    var $items_containers = self.getAllMediaContainer();

    if($items_containers.length) {
      $items_containers.each(function(i) {
        var uniq_id = self.uniqId + i;
        self.bindMediaContainer($(this), uniq_id);
      });
    }

    // build the resource in the edit mode
    var $items_containers = self.getAllResourceContainer();

    if($items_containers.length) {
      $items_containers.each(function(i) {
        var uniq_id = self.uniqId + i + i;
        self.bindResourceContainer($(this), uniq_id);
      });
    }

    a17cms.LoadBehavior($editor.get(0));
    // Send message to the lang_switcher behavior, so it can
    // refresh the language tabs possibly contained in the form
    // repeater that we just added.
    $(window).trigger('a17cms.lang_switcher.refresh');


    //after block render
    self.afterBlockRender();
  },

  afterBlockRender: function() {
  },

  bindMediaContainer: function($items_container, uniq_id) {
    var self = this;
    var url = self.optionLibrary();
    var url_crop = self.optionCrop();
    var callback = self.optionCallback();
    var callback_crop = self.optionCallbackCrop();
    var crop_ratio = self.optionCropRatio();
    var $bt_add_image = $items_container.find('[data-bt-image]');
    var $bt_remove_image = $items_container.find('[data-remove-image]');
    var $bt_crop_image = $items_container.find('[data-crop-image]');
    var $images_id = self.getImageIdField($items_container);

    $items_container.attr("id", uniq_id);

    // Click handler : Remove image
    $bt_remove_image.on("click", function(event) {
      event.preventDefault();

      var $bt = $(this);
      var $items_container = self.getMediaContainerClosest($bt);
      var $images_id = self.getImageIdField($items_container);

      $images_id.val("");
      $items_container.find('[data-crop]').val("");
      $images_id.trigger('refresh:image_thumbnails');
      $bt_crop_image.addClass('disabled');
      $bt.addClass('disabled');
    });

    // Click handler : Add image
    $bt_add_image.on("click",function(event) {
      event.preventDefault();

      var title = $(this).text();
      var options = self.getMediaLibraryOptions(url, uniq_id, callback, title);

      $.event.trigger({
        "type" : "modal_open",
        "modal_config" : options
      });
    });

    // Click handler : Crop image
    $bt_crop_image.on("click",function(event) {
      event.preventDefault();

      var $bt = $(this);
      var title = $bt.text();
      var $items_container = self.getMediaContainerClosest($bt);
      var $images_id = self.getImageIdField($items_container).val();
      var $crop_data = $items_container.find('[data-crop]').val();
      var options = self.getCropOptions(url_crop, uniq_id, callback_crop, title, $images_id, $crop_data, crop_ratio);

      $.event.trigger({
        "type" : "modal_open",
        "modal_config" : options
      });

    });

    /* Refresh id */
    $images_id.on('refresh:image_thumbnails', function(event) {
      var $field = $(this);

      // update preview of the image thumbnail
      self.getThumbImageHTML($field);

      // Update the preview output too
      self.updatePreview();
    });

    $images_id.on('show:crop_button', function (event) {
      $bt_crop_image.removeClass('disabled');
    });

    $images_id.on('show:remove_button', function (event) {
      $bt_remove_image.removeClass('disabled');
    });

    // update preview of the image thmbnail when init
    self.getThumbImageHTML($images_id);

    // init buttons
    if ($images_id.val()) {
      $images_id.trigger('show:crop_button');
      $images_id.trigger('show:remove_button');
    }

  },

  bindResourceContainer: function($resources_container, uniq_id) {
    var self = this;
    var url = self.option_browser;
    var callback = self.optionCallbackResource();
    var $bt_add_resource = $resources_container.find('[data-bt-resource]');
    var $bt_remove_resource = $resources_container.find('[data-remove-resource]');
    var $resources_id = self.getResourceIdField($resources_container);

    $resources_container.attr("id", uniq_id);

    // Click handler : Remove resource
    $bt_remove_resource.on("click", function(event) {
      event.preventDefault();

      var $bt = $(this);
      var $resources_container = self.getResourceContainerClosest($bt);
      var $resources_id = self.getResourceIdField($resources_container);

      $resources_id.val("");
      $resources_id.trigger('refresh:resource_id');
      $bt_remove_resource.hide();

      displayResourceButton();
    });

    // Click handler : Add resource
    $bt_add_resource.on("click",function(event) {
      event.preventDefault();

      var title = $(this).text();
      var options = self.getMediaLibraryOptions(url, uniq_id, callback, title);

      $.event.trigger({
        "type" : "modal_open",
        "modal_config" : options
      });
    });

    /* Refresh preview */
    $resources_id.on('refresh:resource_id', function(event) {
      self.updatePreview();
      displayResourceButton();
    });

    // Init display of the Remove Resource Button
    displayResourceButton();

    // Init display of the Remove Resource Button
    function displayResourceButton() {
      var $resource_title = $resources_container.find("[data-resource-title]");

      if($resources_id.val() == "") {
        $bt_add_resource.show();
        $bt_remove_resource.hide();

        $resource_title.text("").hide();
        $resource_title.next('small').show();

      } else {
        $bt_add_resource.hide();
        $bt_remove_resource.show();

        $resource_title.text($resources_id.val()).show();
        $resource_title.next('small').hide();
      }
    }
  },

  // -------------- Custom methods to build HTML template, Preview -------------- //

  buildTemplate: function() {
    var self = this;
    var $editor = self.$editor;
    var $edit_mode = $editor.find(".a17cms-editor-mode");
    var $preview_mode = self.getPreviewMode();

    self.updatePreview();

    $preview_mode.show();
    $edit_mode.hide();
  },

  buildNewBlock: function() {
    var self = this;
    var $editor = self.$editor;
    var $edit_mode = $editor.find(".a17cms-editor-mode");
    var $preview_mode = self.getPreviewMode();

    $preview_mode.hide();
    $edit_mode.show();
  }
  })

})();
