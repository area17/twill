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
