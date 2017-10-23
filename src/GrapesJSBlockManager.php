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
 * @see \Drupal\dragon\Annotation\GrapeJSBlock
 * @see \Drupal\dragon\GrapeJSBlockInterface
 * @see plugin_api
 */
class GrapesJSBlockManager extends DefaultPluginManager {

  /**
   * Constructs a GrapeJSBlockManager object.
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
      'Plugin/GrapesJS/Block',
      $namespaces,
      $module_handler,
      'Drupal\dragon\Plugin\GrapesJS\Block\GrapesJSBlockInterface',
      'Drupal\dragon\Annotation\GrapesJSBlock'
    );
    $this->alterInfo('grapesjs_block');
    $this->setCacheBackend($cache_backend, 'grapesjs_block_plugins');
    $this->factory = new DefaultFactory($this->getDiscovery());
  }

}
