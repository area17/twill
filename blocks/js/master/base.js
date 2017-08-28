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

  getImageMetasContainer: function($container) {
    return $container.find('[data-image-metas-container]');
  },

  getImageMetasFields: function($container) {
    return $container.find('[data-image-metas]');
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
    updatePreviewHTML("<div class='a17cms-preview-mode-loader'>Loading " + self.title + "...</div>");

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
      if (!self.valid()) {
        self.backToEdit();
      }
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
    if (this.$(':input').not('.st-paste-block, .' + this.getExcludedClass()).length > 0) {
    data = this.$(':input').not('.st-paste-block, .' + this.getExcludedClass()).serializeJSON();
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

  /* getExcludedClass
   * Class for text field NOT to be considered as field to retrieve and save
   */
  getExcludedClass: function() {
    return 'a17-input-disabled';
  },

  /* getInputBlock
   * Get all the fields to save/edit
   */
  getInputBlock: function() {
    var self = this;
    var $editor = self.$editor;

    this.input_block = self.$editor.find('.' + this.getInputClass());

    //if (_.isUndefined(this.input_block)) {
    //this.input_block = this.$('.' + this.getInputClass());
    //}

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
          if ($(this).hasClass('textarea-medium-editor--link-only')) {
            self.setMediumEditor($(this), self.option_settings_link_only);
          } else {
            self.setMediumEditor($(this), self.option_settings);
          }
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
