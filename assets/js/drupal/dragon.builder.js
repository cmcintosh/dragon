(function($, Drupal) {
    Drupal.behaviors.dragon = {
        attach: function(context, settings) {
            var originalPage = $('body').html();
            var styles = $('link');
            var scripts = $('script');

            /**
             * Displays or hides the loader animation.
             */
            var toggleLoader = function() { }

            var getGoogleFonts = function() {
              var fonts = [];
              var fontLoader =[];
              $.ajax({
                url: "https://www.googleapis.com/webfonts/v1/webfonts?sort=popularity&key=AIzaSyBOghVvvBIvGVnjVQMBnkqwUaJzMKEcyEA",
                context: document.body
              }).done(function(resp) {
                // Loop through and return available fonts.
                for (var i in resp.items) {
                  fontLoader.push(resp.items[i].family);

                  fonts.push({
                    value: resp.items[i].family,
                    name: resp.items[i].family
                  });

                  if (fonts.length > 10) {
                    break;
                  }
                }

                WebFont.load({
                  google: {
                    families: fontLoader
                  }
                });

                $( this ).addClass( "done" );
              });



              return fonts;
            }

            /**
             * Style Manager Sectors, this is what defines what we can change.
             */
            var styleManagerSectors = [{
                    name: 'Positioning',
                    buildProps: ['float', 'display', 'position', 'top', 'right', 'left', 'bottom'],
                    properties: [{
                            name: 'Alignment',
                            property: 'float',
                            type: 'radio',
                            defaults: 'none',
                            list: [{
                                    value: 'none',
                                    className: 'fa fa-times'
                                },
                                {
                                    value: 'left',
                                    className: 'fa fa-align-left'
                                },
                                {
                                    value: 'right',
                                    className: 'fa fa-align-right'
                                }
                            ],
                        },
                        {
                            property: 'position',
                            type: 'select'
                        }
                    ],
                },
                {
                    name: 'Dimension',
                    open: false,
                    buildProps: ['width', 'height', 'max-width', 'min-height', 'margin', 'padding', 'overflow'],
                    properties: [{
                            property: 'margin',
                            properties: [{
                                    name: 'Top',
                                    property: 'margin-top'
                                },
                                {
                                    name: 'Right',
                                    property: 'margin-right'
                                },
                                {
                                    name: 'Bottom',
                                    property: 'margin-bottom'
                                },
                                {
                                    name: 'Left',
                                    property: 'margin-left'
                                }
                            ],
                        },
                        {
                            property: 'padding',
                            properties: [{
                                    name: 'Top',
                                    property: 'padding-top'
                                },
                                {
                                    name: 'Right',
                                    property: 'padding-right'
                                },
                                {
                                    name: 'Bottom',
                                    property: 'padding-bottom'
                                },
                                {
                                    name: 'Left',
                                    property: 'padding-left'
                                }
                            ],
                        }
                    ],
                },
                {
                    name: 'Typography',
                    open: false,
                    buildProps: ['font-family', 'font-size', 'font-weight', 'letter-spacing', 'color', 'line-height', 'text-align', 'text-decoration', 'text-shadow'],
                    properties: [{
                            name: 'Font',
                            property: 'font-family',
                            list: getGoogleFonts(),
                        },
                        {
                            name: 'Weight',
                            property: 'font-weight'
                        },
                        {
                            name: 'Font color',
                            property: 'color',
                        },
                        {
                            property: 'text-align',
                            type: 'radio',
                            defaults: 'left',
                            list: [{
                                    value: 'left',
                                    name: 'Left',
                                    className: 'fa fa-align-left'
                                },
                                {
                                    value: 'center',
                                    name: 'Center',
                                    className: 'fa fa-align-center'
                                },
                                {
                                    value: 'right',
                                    name: 'Right',
                                    className: 'fa fa-align-right'
                                },
                                {
                                    value: 'justify',
                                    name: 'Justify',
                                    className: 'fa fa-align-justify'
                                }
                            ],
                        }, {
                            property: 'text-decoration',
                            type: 'radio',
                            defaults: 'none',
                            list: [{
                                    value: 'none',
                                    name: 'None',
                                    className: 'fa fa-times'
                                },
                                {
                                    value: 'underline',
                                    name: 'underline',
                                    className: 'fa fa-underline'
                                },
                                {
                                    value: 'line-through',
                                    name: 'Line-through',
                                    className: 'fa fa-strikethrough'
                                }
                            ],
                        }, {
                            property: 'text-shadow',
                            properties: [{
                                    name: 'X position',
                                    property: 'text-shadow-h'
                                },
                                {
                                    name: 'Y position',
                                    property: 'text-shadow-v'
                                },
                                {
                                    name: 'Blur',
                                    property: 'text-shadow-blur'
                                },
                                {
                                    name: 'Color',
                                    property: 'text-shadow-color'
                                }
                            ],
                        }
                    ],
                }, {
                    name: 'Decorations',
                    open: false,
                    buildProps: ['opacity', 'background-color', 'border-radius', 'border', 'box-shadow', 'background'],
                    properties: [{
                        type: 'slider',
                        property: 'opacity',
                        defaults: 1,
                        step: 0.01,
                        max: 1,
                        min: 0,
                    }, {
                        property: 'border-radius',
                        properties: [{
                                name: 'Top',
                                property: 'border-top-left-radius'
                            },
                            {
                                name: 'Right',
                                property: 'border-top-right-radius'
                            },
                            {
                                name: 'Bottom',
                                property: 'border-bottom-left-radius'
                            },
                            {
                                name: 'Left',
                                property: 'border-bottom-right-radius'
                            }
                        ],
                    }, {
                        property: 'box-shadow',
                        properties: [{
                                name: 'X position',
                                property: 'box-shadow-h'
                            },
                            {
                                name: 'Y position',
                                property: 'box-shadow-v'
                            },
                            {
                                name: 'Blur',
                                property: 'box-shadow-blur'
                            },
                            {
                                name: 'Spread',
                                property: 'box-shadow-spread'
                            },
                            {
                                name: 'Color',
                                property: 'box-shadow-color'
                            },
                            {
                                name: 'Shadow type',
                                property: 'box-shadow-type'
                            }
                        ],
                    }, {
                        property: 'background',
                        properties: [{
                                name: 'Image',
                                property: 'background-image'
                            },
                            {
                                name: 'Repeat',
                                property: 'background-repeat'
                            },
                            {
                                name: 'Position',
                                property: 'background-position'
                            },
                            {
                                name: 'Attachment',
                                property: 'background-attachment'
                            },
                            {
                                name: 'Size',
                                property: 'background-size'
                            }
                        ],
                    }, ],
                }, {
                    name: 'Animation',
                    open: false,
                    buildProps: ['transition', 'perspective', 'transform'],
                    properties: [{
                        property: 'transition',
                        properties: [{
                                name: 'Property',
                                property: 'transition-property'
                            },
                            {
                                name: 'Duration',
                                property: 'transition-duration'
                            },
                            {
                                name: 'Easing',
                                property: 'transition-timing-function'
                            }
                        ],
                    }, {
                        property: 'transform',
                        properties: [{
                                name: 'Rotate X',
                                property: 'transform-rotate-x'
                            },
                            {
                                name: 'Rotate Y',
                                property: 'transform-rotate-y'
                            },
                            {
                                name: 'Rotate Z',
                                property: 'transform-rotate-z'
                            },
                            {
                                name: 'Scale X',
                                property: 'transform-scale-x'
                            },
                            {
                                name: 'Scale Y',
                                property: 'transform-scale-y'
                            },
                            {
                                name: 'Scale Z',
                                property: 'transform-scale-z'
                            }
                        ],
                    }]
                },
                {
                    name: 'Flex',
                    open: false,
                    properties: [{
                        name: 'Flex Container',
                        property: 'display',
                        type: 'select',
                        defaults: 'block',
                        list: [{
                                value: 'block',
                                name: 'Disable'
                            },
                            {
                                value: 'flex',
                                name: 'Enable'
                            }
                        ],
                    }, {
                        name: 'Flex Parent',
                        property: 'label-parent-flex',
                        type: 'integer',
                    }, {
                        name: 'Direction',
                        property: 'flex-direction',
                        type: 'radio',
                        defaults: 'row',
                        list: [{
                            value: 'row',
                            name: 'Row',
                            className: 'icons-flex icon-dir-row',
                            title: 'Row',
                        }, {
                            value: 'row-reverse',
                            name: 'Row reverse',
                            className: 'icons-flex icon-dir-row-rev',
                            title: 'Row reverse',
                        }, {
                            value: 'column',
                            name: 'Column',
                            title: 'Column',
                            className: 'icons-flex icon-dir-col',
                        }, {
                            value: 'column-reverse',
                            name: 'Column reverse',
                            title: 'Column reverse',
                            className: 'icons-flex icon-dir-col-rev',
                        }],
                    }, {
                        name: 'Justify',
                        property: 'justify-content',
                        type: 'radio',
                        defaults: 'flex-start',
                        list: [{
                            value: 'flex-start',
                            className: 'icons-flex icon-just-start',
                            title: 'Start',
                        }, {
                            value: 'flex-end',
                            title: 'End',
                            className: 'icons-flex icon-just-end',
                        }, {
                            value: 'space-between',
                            title: 'Space between',
                            className: 'icons-flex icon-just-sp-bet',
                        }, {
                            value: 'space-around',
                            title: 'Space around',
                            className: 'icons-flex icon-just-sp-ar',
                        }, {
                            value: 'center',
                            title: 'Center',
                            className: 'icons-flex icon-just-sp-cent',
                        }],
                    }, {
                        name: 'Align',
                        property: 'align-items',
                        type: 'radio',
                        defaults: 'center',
                        list: [{
                            value: 'flex-start',
                            title: 'Start',
                            className: 'icons-flex icon-al-start',
                        }, {
                            value: 'flex-end',
                            title: 'End',
                            className: 'icons-flex icon-al-end',
                        }, {
                            value: 'stretch',
                            title: 'Stretch',
                            className: 'icons-flex icon-al-str',
                        }, {
                            value: 'center',
                            title: 'Center',
                            className: 'icons-flex icon-al-center',
                        }],
                    }, {
                        name: 'Flex Children',
                        property: 'label-parent-flex',
                        type: 'integer',
                    }, {
                        name: 'Order',
                        property: 'order',
                        type: 'integer',
                        defaults: 0,
                        min: 0
                    }, {
                        name: 'Flex',
                        property: 'flex',
                        type: 'composite',
                        properties: [{
                            name: 'Grow',
                            property: 'flex-grow',
                            type: 'integer',
                            defaults: 0,
                            min: 0
                        }, {
                            name: 'Shrink',
                            property: 'flex-shrink',
                            type: 'integer',
                            defaults: 0,
                            min: 0
                        }, {
                            name: 'Basis',
                            property: 'flex-basis',
                            type: 'integer',
                            units: ['px', '%', ''],
                            unit: '',
                            defaults: 'auto',
                        }],
                    }, {
                        name: 'Align',
                        property: 'align-self',
                        type: 'radio',
                        defaults: 'auto',
                        list: [{
                            value: 'auto',
                            name: 'Auto',
                        }, {
                            value: 'flex-start',
                            title: 'Start',
                            className: 'icons-flex icon-al-start',
                        }, {
                            value: 'flex-end',
                            title: 'End',
                            className: 'icons-flex icon-al-end',
                        }, {
                            value: 'stretch',
                            title: 'Stretch',
                            className: 'icons-flex icon-al-str',
                        }, {
                            value: 'center',
                            title: 'Center',
                            className: 'icons-flex icon-al-center',
                        }],
                    }]
                }
            ];

            /**
             * Editor default settings that will be used to initialize the editor.
             */
            var editorSettings = {
                container: 'body',
                components: originalPage,
                fromElement: 1,
                showOffsets: 0,
                plugins: settings.dragon.builder.plugins,
                pluginsOpts: settings.dragon.builder.pluginOpts,
                assetManager: {
                    assets: settings.dragon.builder.assets
                },
                storageManager: {
                    storeComponents: 1,
                    storeStyles: 1,
                    autoSave: 0,
                    type: 'local'
                },
                styleManager: {
                    sectors: styleManagerSectors,
                }
            };
            /**
             * Initializes the editor.
             */
            var initializeSiteBuilder = function() {

              settings.dragon.page.current_page_html = $('html').html();
                var toolbar = $('#toolbar-administration');
                $('#toolbar-administration').remove();
                settings.dragon.editor = grapesjs.init(editorSettings);

                var cmdm = settings.dragon.editor.Commands;
                cmdm.add('open-github', {
                    run: function(em, sender) {
                        sender.set('active', false);
                        window.open('https://github.com/cmcintosh/drops-8', '_blank');
                    },
                });

                // Attach CSS and JS from Drupal.
                var styles = $('head style');
                styles.each(function(){
                  var tag = $(this).prop('outerHTML');
                  settings.dragon.editor.getComponents().add('<style>' + $(this).html() + '</style>');
                });

                var iframe = settings.dragon.editor.Canvas.getFrameEl();
                $('head link').each(function(){
                  iframe.contentDocument.head.appendChild(this);
                });

                $('body').prepend(toolbar);

                // Also add the search box for the components.
                var pnm = settings.dragon.editor.Panels;
                var panelSearch = pnm.addPanel({
                  id: 'search-a',
                  visible: true,
                  buttons: [
                    {
                      class: 'search-dragon-blocks-wrapper',
                    },
                  ]
                });

                var searchBox = $('#gjs-pn-search-a')
                  .append(
                    $('<input></input>')
                      .attr('type', 'textfield')
                      .attr('id', 'search-dragon-blocks')
                      .attr('placeholder', 'Search')
                  );

                function isBlocksVisible() {
                  if ($('.gjs-pn-btn.fa.fa-th-large').hasClass('gjs-pn-active')) {
                    $('div#gjs-pn-search-a').css({
                      width: $('.gjs-block-category').width()
                    });
                    $('div#gjs-pn-search-a').show();
                  }
                  else {
                    $('div#gjs-pn-search-a').hide();
                  }

                }

                // Show or hide the block search box.
                $('#gjs-pn-views .gjs-pn-btn').on('click', function(){
                  isBlocksVisible();
                });

                $('div#gjs-pn-search-a').show();

                // Text search
                $('#search-dragon-blocks').on('keyup', function(){
                  var text = $(this).val().toLowerCase();
                  $('.gjs-block-label').each(function(){
                    var label = $(this).html().toLowerCase();
                    if (label.indexOf(text) < 0) {
                      $(this).parent().hide();
                    }
                    else {
                      $(this).parent().show();
                    }
                  });
                });

                //  a bit of css to help with the width / layout of the search box
                $('#gjs-pn-search-a').css({'width' : $('#gjs-pn-views-container').width() + 'px'});

                // Font Family dynamic loading.
                $('#gjs-sm-font-family .gjs-field.gjs-select select').on('change', function(){
                    if (settings.dragon.page.fonts == undefined) {
                      settings.dragon.page.fonts = [];
                    }
                    settings.dragon.page.fonts.push($(this).val());
                    WebFont.load({
                      google: {
                        families: settings.dragon.page.fonts
                      }
                    });
                });
            }

            /**
             * When a user clicks the Dragon Admin button.
             */
            $('#toolbar-link-dragon-admin, .toolbar-icon.toolbar-icon-dragon-admin').on('click', function(e) {
                toggleLoader();
                if (settings.dragon.builder.state) {
                    // If the builder is active we want to hide it, then reinitialize the page.
                    if (confirm("Are you sure you wish to leave?")) {
                        reload();
                    }
                } else {
                    initializeSiteBuilder();
                    Drupal.attachBehaviors();
                    $(this).css({'background-color' : '#337ab7', 'color' : '#fff'});
                    settings.dragon.builder.state = true;
                }
                e.preventDefault();
                return false;
            });
        }
    };

})(jQuery, Drupal);
