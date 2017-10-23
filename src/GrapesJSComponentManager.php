<?php

namespace Drupal\dragon;

use Drupal\Component\Plugin\Factory\DefaultFactory;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Extension\ThemeHandlerInterface;

/**
 * Provides an GrapesJS Block plugin manager.
 *
 * @see \Drupal\dragon\Annotation\GrapeJSComponent
 * @see \Drupal\dragon\GrapeJSComponentInterface
 * @see plugin_api
 */
class GrapesJSComponentManager extends DefaultPluginManager {

  /**
   * Constructs a GrapesJSComponentManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct(
      'Plugin/GrapesJS/Component',
      $namespaces,
      $module_handler,
      'Drupal\dragon\Plugin\GrapesJS\Component\GrapesJSComponentInterface',
      'Drupal\dragon\Annotation\GrapesJSComponent'
    );
    $this->alterInfo('grapesjs_component');
    $this->setCacheBackend($cache_backend, 'grapesjs_component_plugins');
    $this->factory = new DefaultFactory($this->getDiscovery());
  }

}
