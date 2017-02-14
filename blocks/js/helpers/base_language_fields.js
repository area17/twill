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
