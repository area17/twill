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
