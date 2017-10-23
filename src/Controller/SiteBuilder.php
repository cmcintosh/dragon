<?php

namespace Drupal\dragon\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\dragon\Entity\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class SiteBuilder extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Symfony\Component\HttpKernel\HttpKernelInterface definition.
   *
   * @var Symfony\Component\HttpKernel\HttpKernelInterface
   */
  protected $http_kernel;

  public function __construct(HttpKernelInterface $http_kernel) {
    $this->httpKernel = $http_kernel;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('http_kernel')
    );
  }

  /**
  * Returns the node title.
  */
  public function layoutNodeTitle($entity_type = "node", $node) {
    $entity = entity_load($entity_type, $node);
    return $entity->label;
  }

  /**
  * Returns the site builder page.
  */
  public function layoutNodeBuilder ($view_mode = 'default', $entity_type = "node", $node) {
    $entity = node_load($node);
    $view_builder = \Drupal::entityTypeManager()->getViewBuilder($entity_type);
    $storage = \Drupal::entityTypeManager()->getStorage($entity_type);
    $build = $view_builder->view($entity, $view_mode);
    $pluginData = $this->getGrapesJSPlugins($entity_type, $entity->bundle(), $entity);
    $libraries = [
      'dragon/siteBuilder'
    ];
    $libraries = array_merge($libraries, $pluginData['library']);
    $current_theme = \Drupal::config('system.theme')->get('default');
    $settings = [
      'entity_type'      => $entity_type,
      'bundle'           => $entity->bundle(),
      'blocks'           => $this->getGrapesJSBlocks(),
      'components'       => $this->getGrapesJSComponents(),
      'plugins'          => $pluginData['plugins'],
      'pluginsOpts'      => $pluginData['pluginsOpts'],
      'assets'           => $this->getGrapesJSAssets(),
      'devices'          => $this->getDevices(),
      'fonts'            => $this->getFonts(),
      'colors'           => $this->getColors(),
      'current_template' => 'page.html.twig',
      'current_theme'    => $current_theme,
      'base_theme'       => $this->getBaseTheme($current_theme),
      'built_by_dragon'  => $this->getBuiltByDragon($current_theme),
      'preLoad'          => [],
      'preStore'         => [],
    ];

    $settings += $pluginData['drupalSettings'];

    return [
      '#attached' => [
        'drupalSettings' => [
          'dragon' => [
            'siteBuilder' => $settings
          ]
        ],
        'library' => $libraries,
      ],
      'builder' => [
        '#theme' => 'dragon_builder',
      ],
      'content' => $build
    ];
  }


  /**
  * Returns the mini-builder that will be used for entities.
  */
  public function entityBuilder (Request $request) {
    $entity_type = $request->request->get('entity_type');
    $bundle = $request->request->get('bundle');
    $entity_id = $request->request->get('entity');

  }

  /**
  * Internal function to get the defined Components.
  */
  private function getGrapesJSComponents() {
    // @TODO: This needs a bit more thought....
    return [];
  }

  /**
  * Internal function to get the defined Blocks and add them to drupalSettings
  */
  private function getGrapesJSBlocks() {
    $type = \Drupal::service('plugin.manager.grapejs_block');
    $plugin_definitions = $type->getDefinitions();

    $blockData = [];
    foreach($plugin_definitions as $definition) {
      $plugin = $type->createInstance($definition->id, []);
      $blockData[$definition->id] = [
        'label' => $plugin->getLabel(),
        'attributes' => $plugin->getAttributes(),
        'content' => $plugin->getTemplate(),
      ];
    }
    return $blockData;
  }

  /**
  * Internal function to get the plugins defined and add them to drupalSettings.
  */
  private function getGrapesJSPlugins($entity_type, $bundle, $entity) {
    // @TODO: Create this plugin type....
    $type = \Drupal::service('plugin.manager.grapejs_plugin');
    $plugin_definitions = $type->getDefinitions();

    $pluginData = [
      'plugins' => [],
      'pluginsOpts' => [],
      'library' => [],
      'drupalSettings' => [],
    ];
    foreach($plugin_definitions as $definition) {
      $plugin = $type->createInstance($definition['id'], []);
      $pluginData['plugins'][] = $definition['id'];
      $pluginData['pluginsOpts'][] = $plugin->getOptions();
      $pluginData['library'][] = $plugin->getLibrary();
      $settings = $plugin->drupalSettings($entity_type, $bundle, $entity);
      foreach($settings as $id => $setting) {
        $pluginData['drupalSettings'][$id] = $setting;
      }

    }
    return $pluginData;
  }

  /**
  * Internal function to get Assets for grapejs.  Atm this is just images.
  */
  private function getGrapesJSAssets() {
    // @TODO create this logic....
    return [];
  }

  /**
  * Internal function to get the defined Devices or Breakpoints.
  */
  private function getDevices() {
    $entities = entity_load_multiple('breakpoint');

    $devices = [];
    foreach($entities as $entity) {
      $devices[] = [
        'name' => $entity->get('id'),
        'width' => $entity->get('width')
      ];
    }
    return $devices;
  }

  /**
  * Internal function for getting the defined fonts.
  */
  private function getFonts() {
    $entities = entity_load_multiple('font');

    $fonts = [];
    foreach($entities as $entity) {
      $fonts[] = [
        'value' => $entity->get('name'),
        'name' => $entity->get('name'),
        'class' => 'wem-font-' . $entity->get('id')
      ];
    }
    return $fonts;
  }

  /**
  * Internal function for getting the defined colors.
  */
  private function getColors() {
    $entities = entity_load_multiple('color');

    $colors = [];
    foreach($entities as $entity) {
      $colors[] = [
        'value' => $entity->get('name'),
        'name' => $entity->get('name'),
        'class' => 'wem-color-' . $entity->get('id')
      ];
    }
    return $colors;
  }

  /**
  * End point to get data from the builder.
  */
  public function saveGrapesJS(Request $request) {

    $template_id = $request->request->get('theme') . "-" . $request->request->get('template');
    $data = $request->request->get('data');

    if ($entity = entity_load('template', $template_id)){
      $entity->set('layout', $request->request->get('data'));
    }
    else {
      $entity = Template::create([
        'id' => $template_id,
        'layout' => $request->request->get('data'),
        'template' => $request->request->get('template'),
        'theme' => $request->request->get('theme')
      ]);
    }

    try {
      if ($entity->save()) {
        return new JsonResponse([
          'success' => 1
        ]);
      }
      else {
        return new JsonResponse([
          'success' => 0
        ]);
      }
    }
    catch (\Exception $e) {
      watchdog_exception('dragon', $e);
      return new JsonResponse([
        'success' => 0
      ]);
    }
  }

  /**
  * End point to load data from drupal for the builder.
  */
  public function loadGrapesJS(Request $request) {
    $template_id = $request->request->get('theme') . "-" .$request->request->get('template');
    $entity = entity_load('template', $template_id);
    if ($entity) {
      return new JsonResponse([
        'template' => $entity->get('template'),
        'theme' => $entity->get('theme'),
        'data' => $entity->get('layout'),
      ]);
    }
    else {
      return new JsonResponse([
        'empty' => t('no template found')
      ]);
    }
  }

  /**
  * Handles deleting a template from the builder.
  */
  public function deleteGrapesJS(Request $request) {
    $template_id = $request->request->get('theme') . "-" .$request->request->get('template');
    $entity = entity_load('template', $template_id);
    if ($entity) {
      $entity->delete();
    }
    return new JsonResponse([
      'success' => 1
    ]);
  }

  /**
  * End point to return html for a block.
  */
  public function returnBlock(Request $request) {
    $entity = entity_load('block', $request->request->get('block_id'));
    $display = \Drupal::entityTypeManager()
      ->getViewBuilder('block')
      ->view($entity);
    return new JsonResponse([
      'content' => render($display)
    ]);
  }

  /**
  * Endpoint used to save the template to a twig file.
  */
  public function createTwig(Request $request) {
    return new JsonResponse([
      'success' => 0
    ]);
  }

  /**
  * Endpoint used to rebuild the theme.
  */
  public function buildTheme(Request $request) {
    // 1. Get the target theme's info.yml file.
    $files = []; // Files that will be created for this theme.

    $original_theme = $request->request->get('theme');
    $new_theme = $request->request->get('new_theme');

    $uri = drupal_get_path('theme', $original_theme) .  "/{$original_theme}.info.yml";

    $theme_info = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($uri));
    $libraries_info = \Symfony\Component\Yaml\Yaml::parse(file_get_contents(drupal_get_path('theme', $original_theme) .  "/{$original_theme}.libraries.yml"));

    $theme_info['name'] = $new_theme;
    $theme_info['base theme'] = $original_theme;
    $theme_info['description'] = "A {$original_theme} based theme built by Dragon Drupal.";
    $theme_info['dragon_built'] = 'Yes';

    $theme_info['libraries']= [
      "{$new_theme}/global"
    ];

    $theme_uri = drupal_get_path('theme', $original_theme);
    $theme_parts = explode('/', $theme_uri);
    $theme_parts[count($theme_parts)-1] = $new_theme;

    if ($key = array_search('contrib', $theme_parts)) {
      $theme_parts[$key] = 'custom';
    }

    $theme_uri = implode('/', $theme_parts);
    if (!file_exists($theme_uri)) {
      mkdir($theme_uri, 0777, true);
      mkdir($theme_uri . '/dragon/css', 0777, true);
      mkdir($theme_uri . '/templates/system', 0777, true);
    }

    // Store the theme info.
    $info_yaml['libraries'] = [
      $new_theme . '/global'
    ];
    $info_yaml = \Symfony\Component\Yaml\Yaml::dump($theme_info);
    $files[] = [
      'filename' => "{$new_theme}/{$new_theme}.info.yml",
      'contents' => $info_yaml
    ];

    $files[] = [
      'filename' => "{$new_theme}/{$new_theme}.libraries.yml",
      'contents' =>
      "global:
          css:
            theme:
              dragon/css/style.css: {}"
    ];

    // 2. Load all templates that are associated for the target theme from the database.
    $query = \Drupal::entityQuery('template');
    $entity_ids = $query->execute();
    $templates = \Drupal::entityTypeManager()->getStorage('template')->loadMultiple($entity_ids);

    // 3. Update the current physical templates.
    $libraries= [];
    $style_css ='';
    foreach($templates as $id => $template) {
      if ($template->get('id') == $original_theme. '-' . $template->get('template')) {
        $id = $template->get('id');
        $data = $template->get('layout');

        // @TODO add some handling for block / region / view / field templates...

        $template_path = "{$new_theme}/templates/system/" . $template->get('template');
        $css_content .= "\n\r" . $data['gjs-css'];
        $files[] = [
          'filename' => $template_path,
          'contents' => $data['gjs-html']
        ];
        $style_css .= $css_content . "\n\r";
      }
    }

    $files[] = [
      'filename' => $theme_uri . '/dragon/css/style.css',
      'contents' => $style_css,
    ];

    // 8. Create the zip file for the theme.
    $zip = new \ZipArchive();
    $filepath = drupal_realpath('public://') . "/{$new_theme}.zip";
    if (file_exists($filepath)) {
      unlink($filepath);
    }
    if ($zip->open($filepath, \ZipArchive::CREATE)!==TRUE) {
      \Drupal::logger('dragon')->notice("Could not open:" . $filepath);
      return new JsonResponse([
        'success' => 0
      ]);
    }

    // Add all of our required files to the theme.
    foreach($files as $file) {
      $zip->addFromString($file['filename'], $file['contents']);
    }
    $zip->close();

    return new JsonResponse([
      'success' => 1,
      'uri' => file_create_url("public://{$new_theme}.zip"),
    ]);
  }

  /**
  * returns the current theme's base theme,
  */
  private function getBaseTheme($theme) {
    $uri = drupal_get_path('theme', $theme) .  "/{$theme}.info.yml";
    $theme_info = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($uri));
    if (isset($theme_info['base theme'])) {
      return $theme_info['base theme'];
    }
    return '';
  }

  /**
  * returns if this theme was built by dragon.
  */
  private function getBuiltByDragon($theme) {
    $uri = drupal_get_path('theme', $theme) .  "/{$theme}.info.yml";
    $theme_info = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($uri));
    return (isset($theme_info['dragon_built']));
  }


}
