(function($, Drupal) {

    Drupal.behaviors.dragonStorage = {
      attach: function(context, settings) {

        grapesjs.plugins.add('drupal-storage', function(editor, opts){
          var modal = editor.Modal;                   // Modal Object.
          var cmdm = editor.Commands;                 // Command Object.
          var pnm = editor.Panels;                    // Panels Object
          var storageManager = editor.StorageManager; // StorageManager Object.

          /**
          * Command for Saving to database.
          */
          cmdm.add('save-twig', {
            run: function(editor, sender) {
              console.log("Called Save Twig");
              editor.store();
            }
          });

          /**
          * Command used for exporting a theme.
          */
          cmdm.add('export-theme', {
            run: function(editor, sender) {
              settings.dragon.editor.store();
              $('#dragon-loader').show();
              params = {
                'theme' : settings.dragon.page.current_theme,
                'new_theme' : settings.dragon.page.new_theme,
              };
              $.ajax({
                type: "POST",
                url: "/js/grapesjs/generate",
                data: params,
                async: false,
                success: function(e) {
                  if (e.status !== 0) {
                    window.open(e.uri,'_blank');
                  }
                  else {
                    alert("There was an issue creating the theme, please check your error logs.");
                  }
                  $('#dragon-loader').hide();
                },
                always: function(e) {
                  $('#dragon-loader').hide();
                },
                dataType: "json"
              });

            }
          })

          /**
          * Command for Displaying the modal for exporting a theme.
          */
          cmdm.add('export-theme-modal', {
            run: function(editor, sender) {
              modal.setTitle("Export Theme");
              modal.setContent(`
                <div class="form-inline">
                  <div class="form-group col-md-12">
                    <label class="col-md-4">Current Theme</label> <div id="current-theme" class="form-control"></div>
                  </div>
                  <div class="form-group col-md-12" id="base-theme-wrapper">
                    <label class="col-md-4">Base Theme</label> <div id="base-theme" class="form-control"> </div>
                  </div>
                  <div class="form-group col-md-12" id="rebuild-theme-wrapper">
                    <label class="col-md-4">Rebuild Theme</label> <input class="form-control" id="rebuild-theme" type="checkbox">
                  </div>
                  <div class="form-group col-md-12" id="new-theme-wrapper">
                    <label class="col-md-4">New Theme</label> <input class="form-control" id="new-theme" type="textfield">
                  </div>

                  <button id="export-theme" class="btn btn-success">Export Theme</button>
                </div>
              `);

              // Update with variables.
              $('#current-theme').html(settings.dragon.page.current_theme);
              $('#base-theme').html(settings.dragon.page.base_theme);



              $('#export-theme').on('click', function(){
                alert('Exporting theme now');
                settings.dragon.page.rebuild = $('#rebuild-theme').val();
                settings.dragon.page.new_theme = $('#new-theme').val();
                var exportCmd = cmdm.get('export-theme');
                exportCmd.run();
              });

              modal.open();
              if (settings.dragon.page.base_theme == false || settings.dragon.page.base_theme == undefined || settings.dragon.page.base_theme == '' || settings.dragon.page.dragon_built == false) {
                $('#rebuild-theme-wrapper').hide();
              }

              if (settings.dragon.page.base_theme == false || settings.dragon.page.base_theme == undefined || settings.dragon.page.base_theme == '' || settings.dragon.page.dragon_built == false) {
                $('#base-theme-wrapper').hide();
              }
            }
          });

          /*
          * Create the Storage Controls.
          */
          var panelTemplates = pnm.addPanel({
            id: 'templates-a',
            visible: true,
            buttons: [
              {
                id: 'export-html-template',
                className: 'fa fa-download btn btn-small btn-success',
                command: 'export-template',
                attributes: { title: 'View code' },
              },
              {
                id: 'save-twig',
                className: 'btn btn-warning btn-small fa fa-floppy-o',
                command: 'save-twig',
                html: 'Save Template',
                attributes: { title: 'Save Template' },
              },
              {
                id: 'export-theme',
                className: 'btn btn-danger btn-small fa fa-cubes',
                command: 'export-theme-modal',
                attributes: { title: 'Rebuild Theme'}
              },
            ]
          });

          /**
          * On editor initialization.
          */
          editor.on('load', function(e){

            $('.gjs-pn-btn.fa.fa-trash.icon-blank').on('click', function(){
              drupalDelete();
            });

            if ($('#gjs-pn-templates-a select').length > 0) {
              return;
            }

            var wrapper = $('<div></div>').append($('<label></label>').html('Template:'));
            var select = $('<select></select>').css({'width' : '200px'});
            select.addClass('chosen');

            // Add available suggestions to the list.
            for (i in settings.dragon.page.suggestions) {
              var template_name = settings.dragon.page.suggestions[i] + ".html.twig";
              var option = $('<option></option>')
                .attr('value', template_name)
                .html(template_name);
              if ( settings.dragon.page.existing_templates.indexOf(template_name) !== -1 ) {
                option.addClass('existing_template');
                option.css({'background' : 'rgb(51, 122, 183)', 'color' : '#fff', 'font-weight' : 'bold'});
              }
                select.append(option);
            }

            wrapper.append(select);
            $('#gjs-pn-templates-a').append(wrapper);
            $('#gjs-pn-templates-a select').val(settings.dragon.page.current_template);
            $('.chosen').chosen();

            if (settings.dragon.page.existing_templates.indexOf(settings.dragon.page.current_template) !== -1 ) {
              $('#gjs-pn-templates-a .chosen-single').css({ 'background' : 'rgb(51, 122, 183)', 'color' : '#fff', 'font-weight' : 'bold' });
            }

            // Set background color on existing templates.
            $('#gjs-pn-templates-a .chosen-results').each(function(e){
              if( settings.dragon.page.existing_templates.indexOf($(this).html()) > -1) {
                $(this).css({ 'background' : 'rgb(51, 122, 183)', 'color' : '#fff', 'font-weight' : 'bold' });
              }
            });

            // Handle when we change the template.  Ask if the user wants to update the display
            // if loading an existing template.
            $('#gjs-pn-templates-a select').on('change', function(evt, params){
              settings.dragon.page.current_template = $(this).val();
              if ( settings.dragon.page.existing_templates.indexOf( $(this).val() ) > -1) {
                $('#gjs-pn-templates-a .chosen-single').css({ 'background' : 'rgb(51, 122, 183)', 'color' : '#fff', 'font-weight' : 'bold' });
                if (confirm('Refresh editor contents from template?')) {
                    editor.load();
                }
              }
              else {
                $('#gjs-pn-templates-a .chosen-single').css({ 'background' : 'rgb(51, 122, 183)', 'color' : '#ccc', 'font-weight' : 'normal' });
              }
            });
            drupalSettings.dragon.builder.storage_init = true;
          });


          /**
          * Drupal storage engine.
          *
          * - template is the id,
          * - theme
          * - variant - original by default
          */

          // Save the Template information to drupal.
          var saving = false;
          var drupalStore = function(data) {
            if (saving) { return; }
            saving = true;
            for (var i in settings.dragon.builder.preStore) {
              data = settings.dragon.builder.preStore[i](data);
            }
            $.ajax({
              type: "POST",
              url: "/js/grapesjs/save",
              data: {
                template: settings.dragon.page.current_template,
                theme: settings.dragon.page.current_theme,
                data: data,
              },
              async: false,
              success: function(e) {
                $('#dragon-loader').hide();
                setTimeout(function(){ saving = false; }, 500);
              },
              dataType: "json"
            });

          }

          // Load the information from drupal
          var drupalLoad = function(keys) {
            $.ajax({
              type: "POST",
              url: "/js/grapesjs/load",
              data: {
                template: settings.dragon.page.current_template,
                theme: settings.dragon.page.current_theme
              },
              async: false,
              success: function(e) {
                console.log("Load Success");
                if (e.data !== undefined) {
                  for (var i in settings.dragon.builder.preLoad) {
                    console.log("Calling preLoad", i);
                    e.data = settings.dragon.builder.preLoad[i](e.data);
                  }

                  editor.setComponents(e.data['gjs-html']);
                  editor.setStyle(e.data['gjs-css']);
                }
              },
              dataType: "json"
            });
          }

          // Delete the information from drupal
          var drupalDelete = function() {
            $.ajax({
              type: "POST",
              url: "/js/grapesjs/delete",
              data: {
                template: settings.dragon.page.current_template,
                theme: settings.dragon.page.current_theme
              },
              async: false,
              success: function(e) {
                // we want to reload from the page's content....
                var content = $(settings.dragon.page.current_page_html);
                content.find('#toolbar-administration').remove();
                editor.setComponents(content.html());
              },
              dataType: "json"
            })
          }

        storageManager.add('drupal', {
          load: drupalLoad,
          store: drupalStore
        });
        storageManager.setCurrent('drupal');

        });// end of plugin
      } // end of behavior
    };

})(jQuery, Drupal);
