SirTrevor.Blocks.Blockquoterich = (function() {

  var text_fields = a17cms.Helpers.sirTrevorBaseTextFields([
    {
      name: "text",
      label: "Quote",
      type: "medium_textarea",
    }
  ]);

  return SirTrevor.Blocks.Masterpreview.extend({
    type: "blockquoterich",
    title: "Quote",
    icon_name: 'quote',

    editorHTML: a17cms.Helpers.sirTrevorBaseEditor("", text_fields),
  });
})();
