CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Initial Goals
 * Requirements
 * Recommended modules
 * Installation
 * Configuration
 * Maintainers

 INTRODUCTION
------------

The Drag-on module aims to provide Drag and Drop layout functionality for Drupal utilizing the GrapesJS library, found: https://github.com/artf/grapesjs.  

INITIAL GOALS:
------------
 - [ ] A field widget that allows creating layouts for nodes / pages / entities using a Long Text field api field.

 - [ ] A full-theme generation tool that allows site builders to create a whole theme from the Drag-on interface.

 - [ ] Full integration with Drupal 8's Javascript Behavior system that will allow attaching of behaviors to page elements easier for site builders.

 - [ ] Extendable system that utilizes Drupal 8's Plugin API

 - [ ] Remove need for generated themes to require non-core modules.

 - [ ] Generate themes that ship with configuration files that build elements that are needed.

 REQUIREMENTS
 ------------

 Currently this module generates themes in a way that requires a few things to be added:

  * Twig Teak (https://www.drupal.org/project/twig_tweak)
  * GrapesJS library, found https://github.com/artf/grapesjs

INSTALLATION
------------

 * Install as you would normally install a contributed Drupal module. Visit:
   https://drupal.org/documentation/install/modules-themes/modules-8
   for further information.

CONFIGURATION
-------------
To start using the module just click the Dragon icon in the Admin toolbar, this will initialize the editor for the current page.

MAINTAINERS
-----------
Current maintainers:
* Christopher McIntosh (cmcintosh) https://www.drupal.org/u/cmcintosh

This project has been sponsored by:
* Wembassy.com - Developed initially for a Hackathon competition where it placed 2nd, https://drupalpilipinas.org.ph/events/2017/apphack

SUPPORT GOALS
-------------
We are currently raising funds for the completion of this project. Our current supporter goals are below:

- [ ] $250 _Production Ready_
      - Full Page Theme builder for Drupal
      - Exportable themes
      - Documentation for extending Dragon with custom Elements
- [ ] $275  _WYSIWYG_
      - Editor plugin for Long Text fields
      - Additional widgets
         - Slideshow widget
         - Google Maps Widget ('Location markers')
         - Youtube/Vimeo Widget
- [ ] $325 _Deep Integration_
      - Integration with Drupal 8 Javascript API for Behavior creation
      - Web-based editor for creating new behaviors.
      - Additional widgets:
        - Media Selector widget
        - Countdown Timer widget
        - Image Gallery widget
        - Portfolio Widget
        - CTA Widget
        - Social Icon(s) Widget
- [ ] $375 _Going Commercial_
     - Support for Drupal Commerce
        - Cart Widgets
        - Product Suggestion Widget
        - Product Preview Widget
        - Product Zoom Effect Widget
        - Order History Widget
      - Shortcode Support
        - Ability to create and export shortcodes
        - Ability to reuse shortcodes across site.
- [ ] $400 _Forms and Things_
      - Form Builder support
        - Customize the layout and look of any Drupal form
        - Create custom form elements
        - Create behaviors for form elements
      - Mautic Integration
        - Support for customizing embedded Mautic generated forms
- [ ] $500 _A/B Testing_
      - Built in support for running an A/B test inside the theme
      - Integration with third party services for tracking A/B testing
        - Google Analytics
        - Mautic
      - Extensive A/B logic creation:
        - rotation %
        - ability to determine factors for scoring success
        - integration with Hotjar
        - integration with Open Web Analytics
        - integration with ClickHeatMap
