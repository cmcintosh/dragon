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
 * A field widget that allows creating layouts for nodes / pages / entities using a Long Text field api field.

 * A full-theme generation tool that allows site builders to create a whole theme from the Drag-on interface.

 * Full integration with Drupal 8's Javascript Behavior system that will allow attaching of behaviors to page elements easier for site builders.

 * Extendable system that utilizes Drupal 8's Plugin API

 * Remove need for generated themes to require non-core modules.

 * Generate themes that ship with configuration files that build elements that are needed.

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
