(function ($, Drupal) {

  Drupal.behaviors.dragon_breakpoint_ruler = {
    attach: function(context, settings) {
      // Initialize the breakpoint tool + preview

      $('.preview').ruler({
        showCrosshair : false
      });

      $('.settings .form-item').on('change', function(){
        var previewWrapper = $(this).closest('.breakpoint').find('.preview-wrapper');
        renderPreview(previewWrapper);
      });

      $('.preview-wrapper').each(function(){
        renderPreview($(this));
      });

      function renderPreview(el) {
        var parent = el.parent();
        var container_width = $(parent).find('.container-width').val();
        var columns = $(parent).find('.breakpoint-columns').val();
        var inner_gutters = $(parent).find('.breakpoint-inner_gutters').val();
        var outer_gutters = $(parent).find('.breakpoint-outer_gutters').val();
        var column_width = ((container_width - outer_gutters) / columns) - (inner_gutters * 2);

        $(parent).find('.preview').css({ width: container_width + "px" });
        $(parent).find('.content .col').remove();
        $(parent).find('.outer_gutter').css({ width: outer_gutters + "px"});

        for(var i =0; i < columns; i++) {
          var col = $('<div/>')
            .addClass('col')
            .append(
              $('<div/>').addClass('inner_gutter')
            ).append(
              $('<div/>').addClass('inner_gutter')
            );
          $(parent).find('.preview .content').append(col);
        }
        $(parent).find('.content .col').css({ width: column_width + "px" });
        $(parent).find('.content .inner_gutter').css({ width: inner_gutters + "px" });

        $(parent).find('.preview').ruler({
          showCrosshair : false
        });
      }
    }
  };

})(jQuery, Drupal);
