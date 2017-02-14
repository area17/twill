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
