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
