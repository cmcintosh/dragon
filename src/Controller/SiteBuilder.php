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
  protected $service;

  public function __construct(HttpKernelInterface $http_kernel) {
    $this->httpKernel = $http_kernel;
     $this->service = \Drupal::service('plugin.manager.grapejs_plugin');
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('http_kernel')
    );
  }

  /**
  * Internal function to get the plugins defined and add them to drupalSettings.
  */
  private function getGrapesJSPlugins($entity_type, $bundle, $entity) {
    // @TODO: Create this plugin type....
    $plugin_definitions = $this->type->getDefinitions();
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
  * End point to get data from the builder.
  */
  public function save(Request $request) {

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
  public function load(Request $request) {
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
  * Endpoint used to rebuild the theme.
  */
  public function generate(Request $request) {
    // 1. Setup elements needed.
    $files = [];     // Files that will be created for this theme.
    $functions = []; // Theme functions to be included.
    $plugin_definitions = $this->type->getDefinitions();
    $original_theme = $request->request->get('theme');
    $new_theme = $request->request->get('new_theme');
    $uri = drupal_get_path('theme', $original_theme) .  "/{$original_theme}.info.yml";

    // 2. Load our templates
    $query = \Drupal::entityQuery('template');
    $entity_ids = $query->execute();
    $templates = \Drupal::entityTypeManager()->getStorage('template')->loadMultiple($entity_ids);

    // Next sort by weight.
    usort($plugin_definitions, function($a, $b) {
      return $a['weight'] < $b['weight'];
    });

    $data = [];

    foreach($plugin_definitions as $id => $definition) {
      $plugin = $this->manager->createInstance($id, [ ]);
      $data[] = $plugin->generate($templates);
    }

    // 3. Generate the theme, for now just download - zip.
    foreach($data as $row) {
      // 3. a. create the files.


    }


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

    // @todo find out how do do this via Yaml dump..
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
      'filename' => "{$new_theme}/dragon/css/style.css",
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
