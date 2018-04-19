<?php

namespace Drupal\dragon\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Entity\ContentEntityType;
use Symfony\Component\HttpFoundation\Request;

class DragonInfoController extends ControllerBase {

  public function overview () {
    return [
      '#markup' => 'Coming soon'
    ];
  }

  /**
  * Return the images in the system.
  */
  public function assets() {
    $ids = \Drupal::entityQuery('file')
      ->condition('filemime', "%image%", 'LIKE')
      ->execute();

    $entities = entity_load_multiple('file', $ids);
    $resource = [];
    foreach($entities as $entity) {
      $resource[] = [
        'type' => 'image',
        'src' => file_create_url($entity->getFileUri()),
      ];
    }

    return new JsonResponse($resource);
  }

  /**
  * Handles Saving image uploads to drupal.
  *
  * @TODO: provide more controls for where to store
  * uploads as well as how to handle setting metadata.
  */
  public function image_upload(Request $request) {
    $destination = "public://";
    $field_validators = [
      'file_validate_extensions' => [ 'jpg gif png jpeg' ]
    ];
    if (isset($destination) && !file_prepare_directory($destination, FILE_CREATE_DIRECTORY)) {
      return new JsonResponse([
        'main_error' => $this
          ->t('The destination directory could not be created.'),
        'errors' => '',
      ]);
    }

    try {
      $result = file_save_upload(0, [], $destination);
    }
    catch (\Exception $e) {
      watchdog_exception('dragon_image_upload', $e);
    }

    if (is_array($result) && $result[0]) {
      $file = $result[0];
      return new JsonResponse([
        'data' => [
          file_create_url($file->getFileUri())
        ]
      ]);
    }
    else {
      return new JsonResponse([
        'main_error' => $this
          ->t('There was an issue saving the image.'),
        'errors' => '',
      ], 400);
    }

  }

  /**
  * Handles downloading aviary files from amazon to local.
  */
  public function aviary_save(Request $request) {
    $file = system_retrieve_file($request->query->get('url'), 'public://', TRUE);
    if ($file) {
      return new JsonResponse([
        'url' => [
          file_create_url($file->getFileUri())
        ]
      ]);
    }
    else {
      return new JsonResponse([
        'main_error' => $this
          ->t('There was an issue saving the image.'),
        'errors' => '',
        ], 400);
    }
  }

  
  /**
  * Return the available blocks in the system.
  */
  public function blocks() {
    $ids = \Drupal::entityQuery('block')
      ->execute();

    $resource = [];

    $block_manager = \Drupal::service('plugin.manager.block');
    $plugins = $block_manager->getDefinitions();
    $renderer = \Drupal::service('renderer');

    foreach ($plugins as $id => $plugin) {
      $plugin_block = $block_manager->createInstance($id, []);
      $render = $plugin_block->build();
      $resource[] = [
        'id' => $id,
        'label' => $plugin_block->label(),
        'content' => $renderer->render($render)
      ];
    }

    return new JsonResponse($resource);
  }

  /**
  * Return the available entities and display modes in the system.
  */
  public function entities() {

    $resource = [];
    $entity_type_definitions = \Drupal::entityTypeManager()->getDefinitions();
    $renderer = \Drupal::service('renderer');

    /* @var $definition EntityTypeInterface */
    foreach ($entity_type_definitions as $entity_type => $definition) {

      if ($definition instanceof ContentEntityType) {
        $resource[$entity_type] = [
          'id' => $entity_type,
          'label' => $definition->getLabel()->render(),
          'bundles' => [],
        ];

        $display_modes = \Drupal::service('entity_display.repository')->getViewModes($definition->id());
        $bundles = entity_get_bundles($definition->id());


        foreach($bundles as $bundle_id => $bundle) {
          // Generate a prview for this entity, bundle, display mode.
          $resource[$definition->id()]['bundles'][$bundle_id] = [
            'id' => $definition->id() . "." . $bundle_id,
            'label' => $bundle['label'],
            'display_modes' => [],
          ];
          $keys = $definition->getKeys();
          // Get an example entity to use.
          $query = \Drupal::entityQuery($entity_type);
          if ( !empty($keys['bundle']) ) {
            $query->condition($keys['bundle'], $bundle_id)  ;
          }
         $ids = $query ->execute();
         $entity = entity_load($entity_type, array_shift($ids));

         $view_builder = \Drupal::entityTypeManager()->getViewBuilder($entity_type);
         foreach($display_modes as $display_id => $display_info) {
           if ($entity && $display_info['status']) {
             $build = $view_builder->view($entity, $display_id);
             $resource[$definition->id()]['bundles'][$bundle_id]['display_modes'][$display_id] = [
               'id' => $display_id,
               'label' => $display_info['label'],
               'content' => $renderer->render($build)
             ];
           }
         }
        }
      }
    }

    return new JsonResponse($resource);
  }

}
