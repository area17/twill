SirTrevor.Blocks.Base = (function(){

  return SirTrevor.Block.extend({

  // -------------- Block Options -------------- //
  optionLibrary: function(){
    return this.option_library;
  },

  optionCrop: function(){
    return this.option_crop;
  },

  optionCropRatio: function(){
    return this.option_crop_ratio;
  },

  optionCallbackCrop: function(){
    return "callbackUpdateCropData";
  },

  isLoaded: false,

  // -------------- Medias Helpers -------------- //

  /* getMediaContainerClosest
    return the HTML element that is closest to a children
  */
  getMediaContainerClosest: function($el) {
    return $el.closest('[data-images-containers]');
  },

  /* getAllMediaContainer
    return the HTML elements that are containing images related inputs
  */
  getAllMediaContainer: function() {
    var $containers = this.$editor.find('[data-images-containers]');

    return (!$containers.length) ? false : $containers;
  },

  /* getImageIdField
    return the input hidden containing the id of the image
  */
  getImageIdField: function($container) {
    return $container.find('[data-image-id]');
  },

  getMediaLibraryOptions: function(url, uniq_id, callback, title) {
    return {
      "type" : "iframe",
      "title" : title,
      "url" : url + "?role=" + uniq_id,
      "meta" : {
        "mode" : "inserting",
        "role" : uniq_id,
        "callback" : callback,
        "single-selection" : "true"
      }
    };
  },

  getCropOptions: function(url, uniq_id, callback, title, media_id, media_crop_params, ratio) {
    return {
      "type" : "iframe",
      "title" : title,
      "url" : url + "?role=" + uniq_id + "&id=" + media_id + "&crop=" + encodeURIComponent(media_crop_params) + "&ratio=" + ratio,
      "meta" : {
        "role" : uniq_id,
        "callback" : callback,
      }
    };
  },

  // -------------- Custom method for preview -------------- //

  getPreviewMode: function() {
    var self = this;
    var $editor = self.$editor;
    var $preview_mode = $editor.find(".a17cms-preview-mode");

    if(!$preview_mode.length) {
      $editor.append("<div class='a17cms-preview-mode'></div>");
      $preview_mode = $editor.find(".a17cms-preview-mode");
    }

    return $preview_mode;
  },

  updatePreview: function() {
    var self = this;
    var $editor = self.$editor;
    var $texts = self.getInputBlock();
    var data = $texts.serializeJSON();
    var complete_data = { "type": self.type, "data": data };
    var selected_locale = $editor.find('.field_with_lang.selected').first().data('lang');
    var url = self.option_template;

    var edit_button = "<button type='button' class='btn btn-primary a17-edit-button'>Edit</button>";
    var edit_button_fail = "<button type='button' class='btn btn-primary a17-edit-button' style='display:block; background-color: #e61414;'>Block error</button>";

    console.log("updatePreview");
    console.log(complete_data);

    // display loader
    updatePreviewHTML("<div class='a17cms-preview-mode-loader'>Loading " + self.type + "...</div>");

    if (selected_locale) {
      url += '?locale=' + selected_locale;
    }

    $.ajax({
      url: url,
      data: complete_data,
      type: "POST"
    }).done(function(response) {
      updatePreviewHTML(response + edit_button);
    }).fail(function(jqXHR,textStatus,errorThrown) {
      self.consoleMessage("Can't find the preview of the block (updatePreview)");
      console.log(data);
      updatePreviewHTML(edit_button_fail);
    }).always(function() {
      self.bindPreviewButton();
      self.performValidations();
    });

    function updatePreviewHTML(html) {
      var $preview_mode = $editor.find(".a17cms-preview-mode");

      // build the template only if necessary
      if ($preview_mode.length > 0) $preview_mode.empty().append(html);
      else $editor.append("<div class='a17cms-preview-mode'>" + html + "</div>");
    }
  },

  setEditor: function() {
    var self = this;

    //update uniq ids, callback and misc options
    this.$editor.attr('id', self.uniqId);
  },

  // -------------- Data Helpers -------------- //

  /* Fix bug : https://github.com/madebymany/sir-trevor-js/issues/357 */
  validationFailMsg: function(){
    return i18n.t("errors:validation_fail",{ type:this.title })
  },

  /* Generic _serializeData implementation to serialize the block into a plain object.
   * Overwrited here to retrieve radio button values
   * If you want to get the data of your block use block.getBlockData()
   */
  _serializeData: function() {
    var data = {};

    /* Simple to start. Add conditions later */
    if (this.hasTextBlock()) {
    data.text = this.getTextBlockHTML();
    data.isHtml = true;
    }

    // Add any inputs to the data attr
    if (this.$(':input').not('.st-paste-block').length > 0) {
    data = this.$(':input').not('.st-paste-block').serializeJSON();
    }

    return data;
  },

  /* setDataToField
   * Set value of form fields based on JSON values :
   * This is used when the block are loaded to fill the form
   */
  setDataToField: function($field, new_value) {

    if($field.length) {

    switch($field.prop("type")) {
      case "text" :
      case "hidden":
      $field.val(new_value);
      break;

      case "radio" :
      case "checkbox" :
      $field.prop("checked", false);
      $field.filter('*[value="' + new_value + '"]').prop("checked", true);
      break;

      case "select" :
      $field.find("option").prop("selected", false);
      $field.find('option[value="' + new_value + '"]').prop("selected", true);
      break;

      default:
      $field.val(new_value);
    }

    }

  },

  buildOnLoad: function() {
    this.buildTemplate();
  },

  buildTemplate: function() {
    // used in block to add functionalitites
  },

  /* beforeBlockRender
   * Set uniq ID on the editor before the block render :
   * The uniqId is used to parse the DOM when appending new content from the modal, for example
   */
  beforeBlockRender: function() {
    var self = this;

    // generate uniq id for the editor
    if (_.isUndefined(this.uniqId)) {
    this.uniqId = self.getUniqId("a17cms-editor");
    }
  },

  /* backToEdit
   * Click action : Empty preview and show the form
   */
  backToEdit: function() {
    this.$editor.find('.a17cms-editor-mode').show();
    this.$editor.find('.a17cms-preview-mode').hide();
  },

  /* showPreviewMode
   * Append the data into the preview template
   * Template is using underscore templating : http://underscorejs.org/#template
   */
  showPreviewMode: function(template_html, data) {
    var self = this;
    var $editor = this.$editor;

    var $editor_mode = $editor.find('.a17cms-editor-mode');
    var $preview_mode = $editor.find('.a17cms-preview-mode');

    var output = _.template(template_html);
    $editor_mode.hide();
    $preview_mode.html(output(data));
    $preview_mode.append("<button type='button' class='btn btn-primary a17-edit-button'>Edit</button>");
    self.bindPreviewButton();
  },

  /* showPreviewContent
   * Append the html into the preview template
   */
  bindPreviewButton: function() {
    var self = this;
    var $editor = this.$editor;

    var $preview_mode = $editor.find('.a17cms-preview-mode');

    $preview_mode.find(".btn-primary").on("click", function(e) {
    e.preventDefault;
    self.backToEdit();
    });

  },

  /* submitData
   * Get fields and save the data as a JSON
   */
  submitData: function() {
    var self = this;

    var $targets = self.getInputBlock();

    data = $targets.serializeJSON();

    if(self.valid()) self.setAndLoadData(data);
  },

  /* getInputClass
   * Class for text field to be considered as field to retrieve and save
   */
  getInputClass: function() {
    return 'a17-input-block';
  },

  /* getInputBlock
   * Get all the fields to save/edit
   */
  getInputBlock: function() {
    if (_.isUndefined(this.input_block)) {
    this.input_block = this.$('.' + this.getInputClass());
    }

    return this.input_block;
  },

  /* getInputBlock
   * Get all the fields used to submit the editor
   */
  getSubmitBlock: function() {
    if (_.isUndefined(this.submit_block)) {
    this.submit_block = this.$('.a17-submit-block');
    }

    return this.submit_block;
  },

  /* getUniqId
   * Calculate uniq ID
   * beware : this wont work in a loop
   */
  getUniqId: function(namespace) {
    var id = new Date().getTime().toString(16);

    return namespace + "-" + id;
  },


  /* bindAutoResize
   * Auto resize textarea : bind event on keyup and on editor load
   */
  bindAutoResize: function($textareas) {
    var self = this;

    // Auto resize textarea
    $textareas.filter('textarea').on('focus, blur', function (e) { self.autoResizeInputBlock(this); });
    $textareas.filter('textarea').on('keyup.key_autoresize', function (e) { self.autoResizeInputBlock(this); });
    setTimeout(function() {
    $textareas.filter('textarea').each(function() { self.autoResizeInputBlock(this); });
    }, 250)
  },

  /* autoResizeInputBlock
   * Calculate new height on a textarea, based on a hidden clone
   */
  autoResizeInputBlock: function(input) {
    var $input = $(input);
    var $hiddenDiv = this.$editor.find("*[data-name='" + $input.attr("name") +"']");

    content = $input.val();
    content = content.replace(/\n/g, '<br>');
    $hiddenDiv.html(content + '<br>');

    var h = Math.max(60, $hiddenDiv.height());
    $hiddenDiv.hide();

    $input.css('height', h);
  },

  afterBlockRender: function() {
    var self = this;

    var $textareas = this.getInputBlock();
    var $textarea = $textareas.filter('textarea');

    if(this.option_class) {
      var custom_class = this.option_class;
      $textarea.addClass(custom_class);
    }

    if($textarea.length) {
      $textarea.each(function() {
        if ($(this).hasClass('textarea-medium-editor')) {
          self.setMediumEditor($(this), self.option_settings);
        }
      });
    }
  },

  /* setMediumEditor
   * Create new medium based on settings
   */
  setMediumEditor: function($field, settings) {
    var self = this;

    // options : settings and custom class
    var settings = settings ? settings : {};

    var name = "medium_editor_" + $field.attr("name") + "_" + self.uniqId;
    window[name] = new MediumEditor($field.get(0), settings);
    //workaround to remove inline styles (contenteditable issue)
    var medium_editor = window[name].elements[0];
    $(medium_editor).on("input", function() {
    $(medium_editor).find("[style]").contents().unwrap();
    });

    if($field.is('[data-medium-editor-show-button]')) a17cms.Helpers.show_medium_editor_source($field);
  }

  });

})();

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

    //after block render
    self.afterBlockRender();
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
      var $resource_title_input = $resources_container.find("[data-resource-title-input]");

      if($resources_id.val() == "") {
        $bt_add_resource.show();
        $bt_remove_resource.hide();

        $resource_title.text("").hide();
        $resource_title.next('small').show();

      } else {
        $bt_add_resource.hide();
        $bt_remove_resource.show();

        $resource_title.text($resource_title_input.val()).show();
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

a17cms.Helpers.sirTrevorBaseDiaporamaEditor = function(opts) {
  var opts = opts || {};
  var options = {
    bt_label: "Add Images",
    bt_label_crop: "Crop first image",
    note: "",
    name: "image_id",
    crop_field_name: "image_id_crop"
  };

  var HTMLnote = (options.note != "") ? "<small class='a17-small-note hint' style='display:inline-block; margin-left:15px;'>" + options.note + "</small>" : "";

  var image_editor   = "<div class='input' data-images-containers>";
    image_editor  +=     "<div class='a17cms-image-list'></div>";
    image_editor  +=     "<input type='hidden' class='a17-input-block' name='" + options.name + "' autocomplete='false' data-image-id />";
    image_editor  +=     "<input type='hidden' class='a17-input-block' name='" + options.crop_field_name + "' autocomplete='false' data-crop />";
    image_editor  +=     "<button type='button' class='btn btn-small' data-bt-images>" + options.bt_label + "</button> ";
    image_editor  +=     "<button type='button' class='btn btn-small btn-border disabled' data-crop-image>" + options.bt_label_crop + "</button> ";
    image_editor  +=     HTMLnote;
    image_editor  += "</div>";

  return image_editor;
}

a17cms.Helpers.sirTrevorBaseEditor = function(title, custom) {
  var title = (title != "") ? "<div class='input'><h3>" + title + "</h3></div>" : "";
  var form_editor   = "<div class='a17cms-editor'>";
    form_editor  +=     "<div class='a17cms-editor-mode'>";
    form_editor  +=         title;
    form_editor  +=         custom;
    form_editor  +=         "<div class='input input-centered'><button type='button' class='btn btn-primary a17-submit-block'>Preview</button></div>";
    form_editor  +=     "</div>";
    form_editor  += "</div>";

  return form_editor;
}

a17cms.Helpers.sirTrevorMetadata = function(option) {
  return '<input type="hidden" name="' + option.name + '[' + option.index +'][' + option.key + ']" data-name="' + option.name + '[<% item_index %>][' + option.key + ']" data-item-id="' + option.id  + '" value="' + option.value + '" >';
};

a17cms.Helpers.sirTrevorBaseImageEditor = function(opts) {
  var opts = opts || {};
  var options = {
    bt_label: "Add Image",
    bt_label_remove: "Remove Image",
    bt_label_crop: "Crop Image",
    note: "",
    name: "image_id",
    crop_field_name: "image_id_crop"
  };

  // extend default options
  $.extend( true, options, opts );

  var HTMLnote = (options.note != "") ? "<small class='a17-small-note hint' style='display:block; margin-top:15px;'>" + options.note + "</small>" : "";

  var image_editor   = "<div class='input a17cms-image-list--single' data-images-containers>";
    image_editor  +=     "<div class='a17cms-image-list'></div>";
    image_editor  +=     "<input type='hidden' class='a17-input-block' name='" + options.name + "' autocomplete='false' data-image-id />";
    image_editor  +=     "<input type='hidden' class='a17-input-block' name='" + options.crop_field_name +"' autocomplete='false' data-crop />";
    image_editor  +=     "<button type='button' class='btn btn-small' data-bt-image>" + options.bt_label + "</button> ";
    image_editor  +=     "<button type='button' class='btn btn-small btn-border disabled' data-crop-image>" + options.bt_label_crop + "</button> ";
    image_editor  +=     "<button type='button' class='btn btn-small btn-border disabled' data-remove-image>" + options.bt_label_remove + "</button>";
    image_editor  +=     HTMLnote;
    image_editor  += "</div>";

  return image_editor;
}

a17cms.Helpers.sirTrevorBaseLanguageFields = function(fields, languages) {

  var form_editor = "";

  var languages = (typeof BLOCK_LANGUAGES !== 'undefined') ? BLOCK_LANGUAGES : ((typeof DEFAULT_BLOCK_LANGUAGE !== 'undefined') ? [DEFAULT_BLOCK_LANGUAGE] : ['en']);

  for (i = 0; i < fields.length; i++) {
    var field = fields[i];

    for (j = 0; j < languages.length; j++) {
      var language = languages[j];

      if(field.html) {
        var html = field.html;
        html = html.replace(/{{lang}}/g, language);

        form_editor += html;
      }
    }
  }

  return form_editor;
}

a17cms.Helpers.sirTrevorBaseResourceEditor = function(opts) {
  var opts = opts || {};
  var options = {
    bt_label: "Add",
    bt_label_remove: "Remove",
    note: "",
    name: "resource_id",
    display_name: "resource",
  };

  var languages = (typeof BLOCK_LANGUAGES !== 'undefined') ? BLOCK_LANGUAGES : ((typeof DEFAULT_BLOCK_LANGUAGE !== 'undefined') ? [DEFAULT_BLOCK_LANGUAGE] : ['en']);

  // extend default options
  $.extend( true, options, opts );

  var HTMLnote = (options.note != "") ? "<small class='a17-small-note hint' style='display:inline-block; margin-left:15px;'>" + options.note + "</small>" : "";

  var resource_settings_options = "";

  var resource_editor  = "<div class='columns'>";
    resource_editor +=     "<div class='col'>";

  for (j = 0; j < languages.length; j++) {
    var language = languages[j];

    var resource_editor_lang  = "<div class='input field_with_lang' data-lang='" + language + "'>";
      resource_editor_lang +=     "<label>Attached " + options.display_name + "<span class='lang_tag' data-behavior='lang_toggle'>" + language + "</span></label>";
      resource_editor_lang +=     "<div class='input' data-resources-containers>";
      resource_editor_lang +=         "<input type='hidden' class='a17-input-block' name='" + options.name + "_" + language + "' autocomplete='false' data-resource-id />";
      resource_editor_lang +=         "<input type='hidden' class='a17-input-block' name='" + options.name + "_title_" + language + "' autocomplete='false' data-resource-title-input />";
      resource_editor_lang +=         "<button type='button' class='btn btn-small' data-bt-resource>" + options.bt_label + "</button> ";
      resource_editor_lang +=         "<button type='button' class='btn btn-small btn-light' data-remove-resource>" + options.bt_label_remove + "</button>";
      resource_editor_lang +=         "<small class='a17-small-note' style='display:inline-block; margin-left:15px;' data-resource-title></small>" + HTMLnote;
      resource_editor_lang +=     "</div>";
      resource_editor_lang += "</div>";

    resource_editor += resource_editor_lang;

    resource_settings_options += "<option value='" + language + "'>Always use the " + options.display_name + " attached to " + language + "</option>";
  }

  resource_editor +=     "</div>";
  if (languages.length > 1) {
    resource_editor +=     "<div class='col'>";
    resource_editor +=         "<div class='input'>";
    resource_editor +=             "<label>Attached " + options.display_name + " settings</label>";
    resource_editor +=             "<select name='resource_locale' class='a17-input-block' data-behavior='selector' data-minimum-results-for-search='10'>";
    resource_editor +=                 "<option value='1'>Unique " + options.display_name + " per language</option>";
    resource_editor +=                 resource_settings_options;
    resource_editor +=             "</select>";
    resource_editor +=         "</div>";
    resource_editor +=     "</div>";
  }
  resource_editor += "</div>";

  return resource_editor;
}

a17cms.Helpers.sirTrevorBaseTextFields = function(fields) {

  var languages = (typeof BLOCK_LANGUAGES !== 'undefined') ? BLOCK_LANGUAGES : ((typeof DEFAULT_BLOCK_LANGUAGE !== 'undefined') ? [DEFAULT_BLOCK_LANGUAGE] : ['en']);

  var form_editor = "";

  for (i = 0; i < fields.length; i++) {

    var field = fields[i];
    if (!field.html) {
      field.html = "<div class='input field_with_lang' data-lang='{{lang}}'>"

      field.html += "<label>" + field.label + "<span class='lang_tag' data-behavior='lang_toggle'>{{lang}}</span></label>";

      field.label = (field.label === undefined) ? 'text' : field.label;
      field.placeholder = (field.placeholder === undefined) ? '' : field.placeholder;
      field.maxlength = (field.maxlength === undefined) ? '500' : field.maxlength;
      field.type = (field.type === undefined) ? 'input' : field.type;

      switch (field.type) {
        case 'input':
          field.html += "<input type='text' class='a17-input-block' name='" + field.name + "_{{lang}}' maxlength='" + field.maxlength + "' placeholder='" + field.placeholder + "' autocomplete='false' />";
          break;
        case 'textarea':
          field.html += "<textarea class='a17-input-block' name='" + field.name + "_{{lang}}' maxlength='" + field.maxlength + "' placeholder='" + field.placeholder + "' />";
          break;
        case 'medium_textarea':
          field.html += "<textarea class='a17-input-block a17-input-medium-editor textarea-medium-editor' name='" + field.name + "_{{lang}}' rows='20' data-medium-editor-show-button='Display source code' data-medium-editor-hide-button='Hide source code' />";
          break;
      }

      field.html += "</div>";
    }

    for (j = 0; j < languages.length; j++) {
      var language = languages[j];

      if (field.html) {
        var html = field.html;
        html = html.replace(/{{lang}}/g, language);
        form_editor += html;
      }
    }
  }

  return form_editor;
}

a17cms.Helpers.callbackUpdateCropData = function(datas) {
  var $editor = $("#" + datas["role"]);
  var $hidden_crop_data = $("[data-crop]", $editor);

  if(datas) {
    $hidden_crop_data.val(JSON.stringify(datas['data']));
  }
};

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

a17cms.Helpers.callbackUpdateImageIds = function(datas) {
  var max_images = 10;
  var $editor = $("#" + datas["role"]);
  var $hidden_image_id = $("[data-image-id]", $editor);
  var current_val = $hidden_image_id.val();

  var image_datas = datas.data[0];
  var new_id = image_datas.id;

  if(new_id) {
    var imgs_ids = current_val.split(',').map(Number);
    var length = current_val != "" ? imgs_ids.length : 0;

    // Maximum images : 10
    if(imgs_ids.length > max_images) return false;

    // if image dont exist already
    var index = imgs_ids.indexOf(Number(new_id));

    if(index == -1) {
      var new_val = current_val != "" ? current_val + "," : current_val;
      $hidden_image_id.val(new_val + new_id);
      $hidden_image_id.trigger('refresh:image_thumbnails');
      $hidden_image_id.trigger('show:crop_button');
      $('[data-crop]', $editor).val("");
    }
  }
};

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

SirTrevor.Blocks.Blockquote = (function() {

  var text_fields = a17cms.Helpers.sirTrevorBaseTextFields([
    {
      name: "text",
      label: "Quote",
      type: "textarea",
      maxlength: "500",
      placeholder: "Enter Quote here",
    }
  ]);

  return SirTrevor.Blocks.Masterpreview.extend({
    type: "blockquote",
    title: "Quote",
    icon_name: 'quote',

    editorHTML: a17cms.Helpers.sirTrevorBaseEditor("", text_fields),

    validations: ['requireQuote'],
    requireQuote: function() {
      var self = this;
      var $quote = self.$editor.find('[name="text_en"]');
      if ($quote.val() === "") {
          self.setError($quote, "Quote can't be empty.");
      }
    }
  });
})();

SirTrevor.Blocks.Blockseparator = (function(){

  var editorMarkup = "<hr class='ThinLine' /><input type='hidden' class='a17-input-block' name='active' value='true' />";

  return SirTrevor.Blocks.Masterpreview.extend({

    type: "blockseparator",
    title: "Separator",

    icon_name: 'separator',

    editorHTML: a17cms.Helpers.sirTrevorBaseEditor("Separator", editorMarkup),
  });

})();

SirTrevor.Blocks.Blocktext = (function(){

  var text_fields = a17cms.Helpers.sirTrevorBaseTextFields([
    {
      name: "html",
      label: "Text",
      type: "medium_textarea",
    }
  ]);

  return SirTrevor.Blocks.Masterpreview.extend({

    type: "blocktext",
    title: "Text",

    icon_name: 'text',

    editorHTML: a17cms.Helpers.sirTrevorBaseEditor("", text_fields)
  });
})();

SirTrevor.Blocks.Blocktitle = (function(){

  var text_fields = a17cms.Helpers.sirTrevorBaseTextFields([
    {
      name: "title",
      label: "Title",
      type: "input",
      maxlength: "100",
    }
  ]);

  return SirTrevor.Blocks.Masterpreview.extend({

    type: "blocktitle",
    title: "Title",

    icon_name: 'text',

    editorHTML: a17cms.Helpers.sirTrevorBaseEditor("", text_fields),

    validations: ['requireTitle'],
    requireTitle: function() {
      var self = this;
      var $title = self.$editor.find('[name="title_en"]');

      if ($title.val() === "") {
        self.setError($title, "Title can't be empty.");
      }
    }
  });
})();

SirTrevor.Blocks.Button = (function(){

  var text_fields = a17cms.Helpers.sirTrevorBaseTextFields([
    {
      name: "label",
      label: "Button Label",
      type: "input",
      maxlength: "100",
    },
    {
      name: "url",
      label: "Button link URL (if no file attached)",
      type: "input",
      maxlength: "500",
    }
  ]);

  var html_editor_resource_field = a17cms.Helpers.sirTrevorBaseResourceEditor({
    note: "Pease link the button to an existing file",
    display_name: "file"
  });


  return SirTrevor.Blocks.Masterpreview.extend({
    type: "button",
    title: "Button",

    icon_name: 'image',

    editorHTML: a17cms.Helpers.sirTrevorBaseEditor("Button",  text_fields + html_editor_resource_field),
  });
})();

SirTrevor.Blocks.Image = (function() {

  var image_editor = a17cms.Helpers.sirTrevorBaseImageEditor({
    note: "Required sizes : min-width: 2100px – min-height: 1410px"
  });

  return SirTrevor.Blocks.Masterpreview.extend({
    type: "image",
    title: "Image",
    icon_name: 'image',

    editorHTML: a17cms.Helpers.sirTrevorBaseEditor("Image", image_editor),

    validations: ['requireImage'],
    requireImage: function() {
      var self = this;
      var $image = self.$editor.find('[name="image_id"]');
      if ($image.val() === "") {
        self.setError($image, "You need to have one image.");
      }
    }
  });

})();

SirTrevor.Blocks.Diaporama = (function(){

  var image_editor = a17cms.Helpers.sirTrevorBaseDiaporamaEditor({
    note: "Required sizes : min-width: 2100px – min-height: 1410px"
  });

  var text_fields = a17cms.Helpers.sirTrevorBaseTextFields([
    {
      name: "title",
      label: "Title",
      type: "input",
      maxlength: "150",
    }
  ]);

  return SirTrevor.Blocks.Masterdiaporama.extend({

    type: "diaporama",
    title: "Diaporama",

    icon_name: 'image',

    editorHTML: a17cms.Helpers.sirTrevorBaseEditor("Diaporama", text_fields + image_editor),

  });

})();

SirTrevor.Blocks.Imagegrid = (function(){

  var image_editor_left = a17cms.Helpers.sirTrevorBaseImageEditor({
    note : "Required sizes : min-width: 800px – min-height: 800px",
    bt_label: "Add Left Image",
    bt_label_remove: "Remove Left Image",
    name: "image_left_id",
    crop_field_name: "image_left_id_crop"
  });
  var image_editor_right = a17cms.Helpers.sirTrevorBaseImageEditor({
    note : "Required sizes : min-width: 800px – min-height: 800px",
    bt_label: "Add Right Image",
    bt_label_remove: "Remove Right Image",
    name: "image_right_id",
    crop_field_name: "image_right_id_crop"
  });

  return SirTrevor.Blocks.Masterpreview.extend({

    type: "imagegrid",
    title: "Two images",

    icon_name: 'image',

    editorHTML: a17cms.Helpers.sirTrevorBaseEditor("Two images", image_editor_left + image_editor_right),


    validations: ['requireImageLeft', 'requireImageRight'],

    requireImageLeft: function() {
      var self = this;
      var $imagesLeft = self.$editor.find('[name="image_left_id"]');

      if ($imagesLeft.val() === "") {
      self.setError($imagesLeft, "You need to have one image in the left column.");
      }
    },

    requireImageRight: function() {
      var self = this;
      var $imagesRight = self.$editor.find('[name="image_right_id"]');

      if ($imagesRight.val() === "") {
      self.setError($images, "You need to have one image in the right column.");
      }
    }
  });
})();

SirTrevor.Blocks.Imagetext = (function(){

  var html_editor_image = a17cms.Helpers.sirTrevorBaseImageEditor({
    note: "Required sizes : min-width: 800px – min-height: 800px"
  });

  var html_editor_fields = a17cms.Helpers.sirTrevorBaseTextFields([
    {
      name: "title",
      label: "Title",
      type: "input",
      maxlength: "100",
      placeholder: "",
    },
    {
      name: "text",
      label: "Text",
      type: "textarea",
    }
  ]
  );

  html_editor_fields  += "<div class='input'>";
  html_editor_fields  +=     "<label>Image position</label>";
  html_editor_fields  +=     "<select name='image_position' class='a17-input-block' data-behavior='selector' data-minimum-results-for-search='3' data-selector-width='25%'>";
  html_editor_fields  +=         "<option value='1'>Left</option>";
  html_editor_fields  +=         "<option value='0'>Right</option>";
  html_editor_fields  +=     "</select>";
  html_editor_fields  += "</div>";

  return SirTrevor.Blocks.Masterpreview.extend({

    type: "imagetext",
    title: "Image + text",

    icon_name: 'image',

    editorHTML: a17cms.Helpers.sirTrevorBaseEditor("Image + text", html_editor_image + html_editor_fields),

    // Custom Validation

    validations: ['requireImage'],

    requireImage: function() {
      var self = this;
      var $image = self.$editor.find('[name="image_id"]');

      if ($image.val() === "") {
        self.setError($image, "You need to have one image.");
      }
    },
  });

})();
