(function($, Drupal) {


  // We want to add the Drupal Blocks as drag-n-drop components via grapesjs
  Drupal.behaviors.dragonBlocks = {
    attach: function (context, settings) {

      grapesjs.plugins.add('drupal-blocks', function(editor, opts) {
        var domComponents = editor.DomComponents;
        var blockManager = editor.BlockManager;
        var pnm = editor.Panels;
        var cmdm = editor.Commands;                 // Command Object.
        var defaultType = domComponents.getType('default');
        var defaultModel = defaultType.model;
        var defaultView = defaultType.view;
        var modal = editor.Modal;

        var blockEditModalContent = `
          <div class="container form">
            <div class="form-group">
              <label>Template Name</label> <select class="chosen" id="block-template-select"> </select>
            </div>

            <div id="block-editor"> </div>

            <div class="row">
              <button class="btn btn-info" id="block-layout-save">Save & Close</button>
              <button class="btn btn-danger" id="block-layout-cancel">Cancel</button>
            </div>
          </div>
        `;

        // This allows us to display a modal and allows users to set the template used for a given block.
        // When generating a theme this will create a template for this customized view.
        cmdm.add('drupal-block-edit', {
          run: function(editor, sender) {
            var target = editor.getSelected().attributes.attributes['data-block'];
            console.log(target);
            // Redirect to this block's editing page.
            modal.setTitle("Select template for " + target);

            var modalContent = $(blockEditModalContent).clone();
            modal.setContent(modalContent);

            //Get template suggestions for the block.
            var suggestions = settings.dragon.drupalBlocks[target].suggestions;
            for (var i in suggestions) {
              var option =$('<option></option>').attr('value', suggestions[i]).html(suggestions[i]);
              if ( settings.dragon.drupalBlocks[target].current_template == suggestions[i] ) {
                option.addClass('existing_template');
                option.css({'background' : 'rgb(51, 122, 183)', 'color' : '#fff', 'font-weight' : 'bold'});
              }
              $('#block-template-select').append(
                option
              );
            }
            modal.open();
            $('#block-template-select').val(settings.dragon.drupalBlocks[target].current_template);
            $('#block-template-select').chosen();
            $('#block-template-select').val(settings.dragon.drupalBlocks[target].current_template);
            $('#block-template-select').trigger("chosen:updated");

            // Setup handlers for saving / closing
            $('#block-layout-cancel').on('click', function(){
              modal.close();
            });

            $('#block-layout-save').on('click', function() {
              var target = editor.getSelected().attributes.attributes['data-block'];
              settings.dragon.drupalBlocks[target].current_template = $('#block-template-select').val();
              modal.close();
            });
          }
        });


        // Create the model for the block
        var blockModel = defaultModel.extend({
              defaults: Object.assign({}, defaultModel.prototype.defaults, {
                draggable: 'div, nav, region',
                droppable: false,
                traits: [
                  {
                      'label' : 'data-template',
                      'placeolder': 'E.g. block.html.twig',
                  }
                ],
                toolbar: [
                  {
                    attributes: {class: 'fa fa-arrows'},
                    command: 'tlb-move',
                  },{
                    attributes: {class: 'fa fa-clone'},
                    command: 'tlb-clone',
                  },{
                    attributes: {class: 'fa fa-trash'},
                    command: 'tlb-delete',
                  },
                  {
                    attributes: {class: 'fa fa-pencil'},
                    command: 'drupal-block-edit'
                  }
                ],
              })
            },
            // Static functions.
            {
              isComponent: function(el) {
                var attr = $(el).attr('data-block');
                if (typeof attr !== typeof undefined && attr !== false && ($(el).is('div') || $(el).is('nav')) ){
                  return {
                     'type' : 'block'
                   }
                }
              },
            }
        );

        // Create the view for the block element.
        var blockView = defaultView.extend({
            attributes: {
              'data-gjs-editable' : false,
              'data-gjs-removable': false,
            },
            render: function($p) {
              this.renderAttributes();
              var model = this.model;
              this.updateContent();
              this.renderChildren();
              this.updateScript();
              return this;
            },
        });

        // Create the actual component.
        var blockComponent = domComponents.addType('block', {
            removable: true,
            content: '',
            attributes: {
                'data-block': '',
                'data-template': 'block.html.twig',
                'data-gjs-editable' : false
            },
            model: blockModel,
            view: blockView,
        });

        // Create GrapeJS Blocks
        for (var i in settings.dragon.drupalBlocks) {
            if (i !== 'broken') {
              var block = settings.dragon.drupalBlocks[i];
              blockManager.add(i, {
                  label: block.label,
                  attributes: { 'class' : 'fa fa-cubes' },
                  content: '<div data-block="' +
                      i + '"><span data-block-content="1">' +
                      block.content + '</span></div>',
                  category: 'Blocks'
              });
            }
        }

        // This function runs when loading the data from Drupal so that we replace the twig code with content for the block
        settings.dragon.builder.preLoad.drupalBlocks = function(data) {
          var htmlData = $(data['gjs-html']);
          htmlData.find("[data-block]").each(function(){
            var id = $(this).attr('data-block');
            $(this).html(settings.dragon.drupalBlocks[id].content);
          });
          var container = $('<div></div>').append(htmlData);
          data['gjs-html'] = container.html();
          return data;
        }

        // end of plugin
      });

      //  Lets us loop through and generate template data to be stored in the block templates.
      settings.dragon.builder.preStore.drupalBlocks = function(data) {
        data.drupalBlocks = {};

        // We need to find our blocks, and store the template information for them....
        var htmlData = $(data['gjs-html']);
        htmlData.find("[data-block]").each(function(){
          var id = $(this).attr('data-block');
          var template = (settings.dragon.drupalBlocks[id] == undefined) ? 'block.html.twig' : settings.dragon.drupalBlocks[id].current_template;

          data.drupalBlocks[id] = {
            id : id,
            content  : $(this).html(),
            template : template
          };
          $(this).html("{{ drupal_block('" + id + "') }}");
        });

        var container = $('<div></div>').append(htmlData);
        data['gjs-html'] = $(container).html();
        return data;
      }

      // end of behavior
    }
  }

})(jQuery, Drupal);
