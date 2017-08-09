a17cms.Helpers.sirTrevorBaseTextFields = function(fields) {

  var languages = (typeof BLOCK_LANGUAGES !== 'undefined') ? BLOCK_LANGUAGES : ((typeof DEFAULT_BLOCK_LANGUAGE !== 'undefined') ? [DEFAULT_BLOCK_LANGUAGE] : ['en']);

  var form_editor = "";

  for (i = 0; i < fields.length; i++) {

    var field = fields[i];
    if (!field.html) {
      field.html = "<div class='input field_with_lang' data-lang='{{lang}}'>"

      field.html += "<label>" + field.label + "<span class='lang_tag' data-behavior='lang_toggle'>{{lang}}</span>";

      if (field.hint !== undefined) {
        field.html += "<span class='hint'>"+ field.hint +"</span>";
      }

      field.html += "</label>";

      field.label = (field.label === undefined) ? 'text' : field.label;
      field.placeholder = (field.placeholder === undefined) ? '' : field.placeholder;
      field.maxlength = (field.maxlength === undefined) ? '500' : field.maxlength;
      field.type = (field.type === undefined) ? 'input' : field.type;
      field.attr = (field.attr === undefined) ? '' : field.attr;
      field.class = (field.class === undefined) ? "a17-input-block" : field.class;
      switch (field.type) {
        case 'input':
          field.html += "<input type='text' class='" + field.class + "' name='" + field.name + "_{{lang}}' maxlength='" + field.maxlength + "' placeholder='" + field.placeholder + "' autocomplete='false' " + field.attr + " />";
          break;
        case 'textarea':
          field.html += "<textarea class='" + field.class + "' name='" + field.name + "_{{lang}}' maxlength='" + field.maxlength + "' placeholder='" + field.placeholder + "' " + field.attr + " />";
          break;
        case 'medium_textarea':
          field.html += "<textarea class='" + field.class + " a17-input-medium-editor textarea-medium-editor' name='" + field.name + "_{{lang}}' rows='20' data-medium-editor-show-button='Display source code' data-medium-editor-hide-button='Hide source code' " + field.attr + " />";
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
