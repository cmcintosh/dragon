(function($, Drupal) {
  grapesjs.plugins.add('drupal-fields', function(editor, opts) {
    let config = opts;

    var domComponents = editor.DomComponents;
    var defaultType = domComponents.getType('default');
    var defaultTypeModel = defaultType.model;
    var defaultTypeView = defaultType.view;

    // Extend the default Type Model.
    var fieldTypeModel = defaultTypeModel.extend({
      defaults: Object.assign({}, defaultTypeModel.prototype.defaults, {
        draggable: 'div, span, li',
        droppable: false,
      })
    });

    // Extend the default Type View.
    var fieldTypeView = defaultTypeView.extend({
      defaults: Object.assign({}, defaultTypeView.prototype.defaults, {

      })
    });

    var field = domComponents.addComponent({
      model: fieldTypeModel,
      view: fieldTypeView,
    });

  });
})(jQuery, Drupal);
