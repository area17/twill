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

    editorHTML: a17cms.Helpers.sirTrevorBaseEditor("", text_fields),

    afterBlockRender: function(){
      var self = this;

      var $textareas = this.getInputBlock();
      var $textarea = $textareas.filter('textarea');

      if(this.option_class) {
        var custom_class = this.option_class;
        $textarea.addClass(custom_class);
      }

      if($textarea.length) {
        $textarea.each(function() {
          self.setMediumEditor($(this), self.option_settings);
        });
      }
    },
  });
})();
