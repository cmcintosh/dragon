(function($, Drupal) {

  Drupal.behaviors.dragonFields = {
    attach: function(context, settings) {
      grapesjs.plugins.add('drupal-fields', function(editor, opts) {
        let config = opts;
        var domComponents = editor.DomComponents;
        var defaultType = domComponents.getType('default');
        var defaultTypeModel = defaultType.model;
        var defaultTypeView = defaultType.view;
        var blockManager = editor.BlockManager;

            // Extend the default Type Model.
            var fieldTypeModel = defaultTypeModel.extend({
              defaults: Object.assign({},
                defaultTypeModel.prototype.defaults, {
                  draggable: 'div, span, li',
                  droppable: false,
                })
              },
              {
                isComponent: function(el) {
                  var attr = $(el).attr('data-field');
                  if (typeof attr !== typeof undefined && attr !== false && ($(el).is('div') || $(el).is('nav')) ){
                    return {
                       'type' : 'field'
                     }
                  }
                }
              }
            );


            // Extend the default Type View.
            var fieldTypeView = defaultTypeView.extend({
              defaults: Object.assign({}, defaultTypeView.prototype.defaults, {

              })
            });

            // Creates the Field Type element for the editor
            var fieldType = domComponents.addType('field', {
              model: fieldTypeModel,
              view: fieldTypeView,
            });

            for(var i in settings.dragon.drupalFields) {

              for (var k in settings.dragon.drupalFields[i]) {
                for (var j in settings.dragon.drupalFields[i][k]) {
                  var field = settings.dragon.drupalFields[i][k][j];
                  if (field.label !== '' && field.label !== undefined && field.label !== 'password') {
                    blockManager.add(i, {
                        label: field.label,
                        attributes: { 'class' : 'fa fa-cubes' },
                        content: '<div data-field="' +
                            i + '.' + j + '"><span data-field-content="1">' +
                            field.content + '</span></div>',
                        category: settings.dragon.drupalFields[i]['label']
                    });
                  }
                }
              }
            }
        });
    }
  }

})(jQuery, Drupal);
