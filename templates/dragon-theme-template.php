<?php

/**
* @file
* - contains the template used for generating the theme's
* .theme file for preprocess functions. ** DO NOT ALTER **
*/

/**
* The user may have requested some entities to be available
* on a page, block, or region so we track them here to make it so.
*
* Example:
* $entity_dependency = [
*   'block' => [
*     'block-$id' => [
*       'node'             => $entity,
*       'commerce_product' => $entity,
*     ]
*   ]
* ]
*/
$entity_dependency = [
  'block'  => {$block_dependency},
  'region' => {$region_dependency},
  'page'   => {$page_dependency},
];

/**
* Additionally, we may have overridden regions or blocks for
* specific pages or variants.
*
* Example:
* $overrides = [
*    'page' => [
*      {template_suggestion} => [
*         {block/region} => [
*           {variant_id/original}
*         ]
*      ]
*   ]
* ];
*/
$overrides = [
  'page' => {$overriden_page}
];

/**
* Track A/B Variants here.
*
* Example:
* $variants = [
*   {path} => [ {variant_name} ]
* ]
*/
$variants = {$variant_list};

/**
* Implements hook_preprocess_page
*/
function {$theme}_preprocess_page(&$vars) {
  $current_path = \Drupal::service('path.current')->getPath()
  $active_variant = {$theme}_get_active_variant($current_path);

}



/**
* Implements hook_theme_suggestions_hook_alter
*/
function {$theme}_theme_suggestions_block_alter(array &$suggestions, array $variables) {

    $current_path = \Drupal::service('path.current')->getPath()

    //  check if the
    if (isset($overrides[$hook][$active_variant])) {
      $suggestions[] = 'page__' . $overrides[$hook][$active_variant];
    }

}

/**
* Local function to return variants for the current path.
*/
function {$theme}_get_active_variant($path) {
  // @TODO: some sort of math to determine which variant to use here.
  return $variants[$path];
}

//pong - driver
