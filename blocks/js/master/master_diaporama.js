/**
 * Master block :
 * Multiple images blocks
 *
 * For each image list, UI must be as follow :
 *
 * |--------------------------|
 * | data-images-containers   |
 * |                          |
 * | |---------------------|  |
 * | | .a17cms-image-list  |  |
 * | |---------------------|  |
 * | | data-image-id       |  |
 * | |---------------------|  |
 * |                          |
 * |--------------------------|
 *
 */

SirTrevor.Blocks.Masterdiaporama = (function(){



  return SirTrevor.Blocks.Base.extend({
  // -------------- Little helper -------------- //

  consoleMessage: function(message) {
    console.warn("Blocks Editor – Error : " + message);
  },

  // -------------- Block Options -------------- //

  optionCallback: function(){
    return "callbackUpdateImageIds";
  },

  /* getListImagesHTML
    return the HTML of the image list in the Editor view
  */
  getListImagesHTML: function($field) {
    var self = this;
    var val = $field.val();
    var $list = $field.prev();
    var output = "";

    if(val !== "") {
      var imgs_ids = val.split(',');

      if(imgs_ids.length) {
        $.each(imgs_ids, function(i) {
           var id_image = imgs_ids[i];

           output += "<div class='a17cms-image-thumbnail' data-id='" + id_image + "'>";
           output +=     "<div class='a17cms-image-thumbnail-inner'>";
           output +=         "<img src='data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7' data-id='" + id_image + "' />";
           output +=         "<a href='#' data-remove-images='" + id_image + "' class='a17cms-image-trash'></a>";
           output +=         "<a href='#' class='a17cms-image-sortable-handle'></a>";
           output +=     "</div>";
           output += "</div>";
        });
      }
    }

    $list.html(output);

    // reload thumbnails
    $imagesToLoad = $list.find("img[data-id]");

    if($imagesToLoad.length) {
      $.each($imagesToLoad, function() {
        self.getImageSRC($(this));
      });
    }
  },

  getImageSRC: function($image) {
    var self = this;
    var id_image = $image.data("id");

    $.ajax({
      url: self.option_library_thumbnail,
      data: { id: id_image }
    }).done(function(response) {
       $image.attr("src", response).removeAttr("data-id");
    }).fail(function(jqXHR,textStatus,errorThrown) {
      self.consoleMessage("Can't find the image URL in Items with ID : " + id_image + " (getListImagesHTML)");
    });
  },

  loadData: function(data){
    var self = this;
    var $texts = self.getInputBlock();

    self.isLoaded = true;

    $.each(data, function(key, new_value) {
      var $field = $texts.filter('*[name="' + key + '"]');
      if($field.length) self.setDataToField($field, new_value);
    });

    //build the output
    self.buildTemplate();

  },

  onBlockRender: function(){
    var self = this;
    var $editor = self.$editor;
    var $texts = self.getInputBlock();
    var $submits = self.getSubmitBlock();

    self.setEditor();

    //build the output
    if(self.isLoaded === false) self.buildNewBlock();

    // build the image lists in the edit mode
    var $items_containers = self.getAllMediaContainer();

    // Click handler : custom submit
    $submits.on('click', function(e) {
      e.preventDefault();

      data = $texts.serializeJSON();
      self.$editor.find('.a17cms-form-editor').hide();
      self.$editor.find('.a17-preview-editor').show();
      self.setAndLoadData(data);
    });

    if($items_containers.length == 0) return false;

    $items_containers.each(function(i) {
      var uniq_id = self.uniqId + i;

      self.bindItemsContainer($(this), uniq_id);
    });

    a17cms.LoadBehavior($editor.get(0));
    // Send message to the lang_switcher behavior, so it can
    // refresh the language tabs possibly contained in the form
    // repeater that we just added.
    $(window).trigger('a17cms.lang_switcher.refresh');

  },

  bindItemsContainer: function($items_container, uniq_id) {
    var self = this;
    var url = self.optionLibrary();
    var url_crop = self.optionCrop();
    var callback = self.optionCallback();
    var callback_crop = self.optionCallbackCrop();
    var crop_ratio = self.optionCropRatio();
    var $bt_images = $items_container.find('[data-bt-images]');
    var $bt_crop_image = $items_container.find('[data-crop-image]');
    var $images_ids = self.getImageIdField($items_container);
    var $images_list = $items_container.find(".a17cms-image-list");

    $items_container.attr("id", uniq_id);

    $images_list.sortable({
      handle : '.a17cms-image-sortable-handle',
      forcePlaceholderSize: true,
      placeholder : 'a17cms-image-thumbnail a17cms-image-thumbnail-placeholder',
      update : function( event, ui ) {
        var imgs_ids = [];
        $images_list.children().each(function() {
          imgs_ids.push($(this).data('id'));
        });

        $images_ids.val(imgs_ids.join(","));

        // update Preview
        self.updatePreview();
      }
    });
    $images_list.disableSelection();

    /* Remove image on click */
    $items_container.on("click", "[data-remove-images]", function(event) {
      event.preventDefault();

      var $bt = $(this);
      var id = parseInt($bt.data("remove-images"));
      var $items_container = self.getMediaContainerClosest($bt);
      var $images_ids = self.getImageIdField($items_container);

      if(id > 0) {
        var imgs_ids = $images_ids.val().split(",").map(Number);
        var index = imgs_ids.indexOf(id);

        // remove id
        if(index != -1) imgs_ids.splice(index, 1);

        $images_ids.val(imgs_ids.join(","));
        $images_ids.trigger('refresh:image_thumbnails');

        if (!imgs_ids.length) {
          $bt_crop_image.addClass('disabled');
          $items_container.find('[data-crop]').val("");
        }
      }

    });

    // Click handler : Add images
    $bt_images.on("click",function(event) {
      event.preventDefault();

      var options = self.getMediaLibraryOptions(url, uniq_id, callback, "Add Image");

      $.event.trigger({
        "type" : "modal_open",
        "modal_config" : options
      });
    });


    $bt_crop_image.on("click",function(event) {
      event.preventDefault();

      var $bt = $(this);
      var title = $bt.text();
      var $items_container = self.getMediaContainerClosest($bt);
      var $images_ids = self.getImageIdField($items_container).val().split(",");
      var $crop_data = $items_container.find('[data-crop]').val();
      var options = self.getCropOptions(url_crop, uniq_id, callback_crop, title, $images_ids[0], $crop_data, crop_ratio);

      $.event.trigger({
        "type" : "modal_open",
        "modal_config" : options
      });

    });


    /* Refresh ids */
    $images_ids.on('refresh:image_thumbnails', function(event) {
      var $field = $(this);
      self.getListImagesHTML($field);

      // Update the preview output too
      self.updatePreview();
    });

     $images_ids.on('show:crop_button', function (event) {
      $bt_crop_image.removeClass('disabled');
    });

    $images_ids.trigger('refresh:image_thumbnails');

    if ($images_ids.val()) {
      $bt_crop_image.removeClass('disabled');
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
