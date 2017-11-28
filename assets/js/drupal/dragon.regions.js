(function($, Drupal) {
        Drupal.behaviors.dragonRegions = {
            attach: function(context, settings) {
                grapesjs.plugins.add('drupal-regions', function(editor, opts) {
                        var domComponents = editor.DomComponents;
                        var blockManager = editor.BlockManager;
                        var pnm = editor.Panels;
                        var defaultType = domComponents.getType('default');
                        var defaultModel = defaultType.model;
                        var defaultView = defaultType.view;

                        // Create the model for the region element.
                        var regionModel = defaultModel.extend({
                              draggable: 'div',
                              droppable: true,
                            },
                            // Static functions.
                            {
                              isComponent: function(el) {
                                var attr = $(el).attr('data-region');
                                if (typeof attr !== typeof undefined && attr !== false){
                                   return {
                                     'type' : 'region'
                                   }
                                }
                              },
                            }
                        );

                        // Create the view for the region element.
                        var regionView = defaultView.extend({
                            events: {
                                drop: function(event, ui) {
                                    console.log('Dropped', event);
                                    console.log('Dropped', ui);
                                }
                            }
                        });

                        // Create the actual component.
                        var regionComponent = domComponents.addType('region', {
                            removable: true,
                            content: '',
                            attributes: {
                                'data-region': ''
                            },
                            model: regionModel,
                            view: regionView,
                        });

                        // Create GrapeJS Blocks
                        for (var i in settings.dragon.regions) {
                            var region = settings.dragon.regions[i];
                            blockManager.add(i, {
                                label: region,
                                content: '<div data-region="' +
                                    i + '"><span>' +
                                    region + '</span></div>',
                                category: 'Regions'
                            });
                        }
                    });
                  }
                };

            })(jQuery, Drupal);
