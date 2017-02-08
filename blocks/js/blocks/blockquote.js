SirTrevor.Blocks.Blockquote = (function() {

  var text_fields = a17cms.Helpers.sirTrevorBaseTextFields([
    {
      name: "text",
      label: "Quote",
      type: "textarea",
      maxlength: "500",
      placeholder: "Enter Quote here",
    }
  ]);

  return SirTrevor.Blocks.Masterpreview.extend({
    type: "blockquote",
    title: "Quote",
    icon_name: 'quote',

    editorHTML: a17cms.Helpers.sirTrevorBaseEditor("", text_fields),

    validations: ['requireQuote'],
    requireQuote: function() {
      var self = this;
      var $quote = self.$editor.find('[name="text_en"]');
      if ($quote.val() === "") {
          self.setError($quote, "Quote can't be empty.");
      }
    }
  });
})();
