SirTrevor.Blocks.Blockseparator = (function(){

  var editorMarkup = "<hr class='ThinLine' /><input type='hidden' class='a17-input-block' name='active' value='true' />";

  return SirTrevor.Blocks.Masterpreview.extend({

    type: "blockseparator",
    title: "Separator",

    icon_name: 'separator',

    editorHTML: a17cms.Helpers.sirTrevorBaseEditor("Separator", editorMarkup),
  });

})();
