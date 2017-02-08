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
