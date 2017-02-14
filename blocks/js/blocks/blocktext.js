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
