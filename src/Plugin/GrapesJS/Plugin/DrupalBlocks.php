<?php

namespace Drupal\dragon\Plugin\GrapesJS\Plugin;

use Drupal\dragon\Plugin\GrapesJS\Plugin\GrapesJSPluginBase;
use Drupal\dragon\Plugin\GrapesJS\Plugin\GrapesJSPluginInterface;

/**
* @GrapesJSPlugin(
*   id = "drupal-blocks",
*   library = "dragon/drupalBlocks",
*   weight = 98
* )
*/
class DrupalBlocks extends GrapesJSPluginBase implements GrapesJSPluginInterface {

    /**
    * {@inheritdoc}
    */
    public function getLibrary() {
      return $this->pluginDefinition['library'];
    }

    /**
    * {@inheritdoc}
    */
    public function getOptions() {
      return [ ];
    }

    // @TODO: find a way to return rendered blocks via an ajax call.

    /**
    * {@inheritdoc}
    */
    public function drupalSettings() {
      $blocks = [];

      $type = \Drupal::service('plugin.manager.block');
      $plugin_definitions = $type->getDefinitions();

      foreach($plugin_definitions as $id => $definition) {
        $plugin = $type->createInstance($id, []);
        $build = $plugin->build();
        $suggestions = $this->getSuggestions($id);
        $current_template = $this->findMostReleventTemplate($suggestions);

        $blocks[$id] = [
          'id' => $id,
          'label' => $definition['admin_label'],
          'content' => isset($build['#markup']) ? $build['#markup'] : render($build),
          'suggestions' => $suggestions,
          'current_temlpate' => $current_template
        ];

        if ($blocks[$id]['content'] == '' || $blocks[$id]['content'] == null) {
          // Some blocks are not compatible with rendering out this way,
          // For now hide them until we can sort out how to do this.
          unset($blocks[$id]);
        }
      }
      return [
        'drupalBlockInfo' => [],
        'drupalBlocks' => $blocks
      ];
    }

    /**
    * {@inheritdoc}
    */
    public function generate(&$templates) {}

    /**
    * Builds the template suggestions for any given block.
    */
    private function getSuggestions($id) {
      $id = str_replace(':', '--', $id);
      $path = \Drupal::service('path.current')->getPath();
      $path_args = explode('/', $path);
      $a = array_shift($path_args);
      $suggestions = ['block'] + theme_get_suggestions($path_args, 'block', '--');
      $suggestion_alters = [];
      foreach($suggestions as $d) {
        $suggestion_alters[] = $d;
        $suggestion_alters[] = $d . "--{$id}";
      }

      return $suggestion_alters;
    }


    /*
    * Returns the most relevent template in the config settings, or the physical
    * template that is in the theme.
    */
    private function findMostReleventTemplate( $suggestions) {
      $config = \Drupal::config('system.theme');
      $current_theme = $config->get('default');

      $template = 'block.html.twig';
      foreach($suggestions as $suggestion) {
        if ($entity = entity_load('template', "{$current_theme}-{$suggestion}")) {
          $template = $suggestion;
        }
      }

      return $template;
    }

}
