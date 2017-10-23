<?php

namespace Drupal\dragon\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\dragon\Entity\Font;
use Drupal\dragon\Entity\FontPack;
use Drupal\Core\File\FileSystem;
use Drupal\file\Entity\File;

class FontAdminManageForm extends FormBase {

  /**
  * {@inheritdoc}
  */
  public function getFormId() {
    return 'font_admin_manage';
  }

  /**
  * {@inheritdoc}
  */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['#attached']['library'][] = 'dragon/dragon_admin';

    $form['detail'] = [
      '#type' => 'fieldset',
      '#title' => t('Fonts')
    ];

    $form['detail']['uploads'] = [
      '#type' => 'details',
      '#title' => t('Upload Fonts'),
      '#prefix' => '<div id="font-upload-wrapper">',
      '#suffix' => '</div>',
      '#tree' => TRUE,
      '#open' => TRUE,
      '#description' => t('Upload Zipped Font Families to be used for the site.'),
    ];

    $fonts = $this->getFonts();
    $this->uploadsForm($form, $form_state, $fonts);

    $form['detail']['webfonts'] = [
      '#type' => 'details',
      '#title' => t('Web fonts'),
      '#tree' => TRUE,
    ];

    $this->webfontsForm($form, $form_state, $fonts);

    $form['detail']['packs'] = [
      '#type' =>  'details',
      '#title' => t('Font Packs'),
      '#tree' => TRUE,
    ];

    $packs = $this->getFontPacks();
    $this->fontpackForm($form, $form_state, $packs);

    $form['detail']['submit'] = [
      '#type' => 'submit',
      '#value' => t('Save')
    ];

    return $form;
  }

  /**
  * Returns the sub form elements for uploading fonts.
  */
  private function uploadsForm(array &$form, FormStateInterface $form_state, $fonts) {

    $form['detail']['uploads']['fonts'] = [
      '#type' => 'table',
      '#rows' => [],
    ];

    foreach($fonts as $font) {
      if ($font->get('type') !== 'upload') {
        continue;
      }

      $fid = $font->get('file');
      $features = $this->getFontFeatures($fid);
      $form['detail']['uploads']['fonts'][] = [
        'original_id' => [
          '#type' => 'value',
          '#value' => $font->get('id')
        ],
        'id' => [
          '#type' => 'textfield',
          '#value' => $font->get('id'),
          '#size' => 10,
          '#placeholder' => t('Label')
        ],
        'file' => [
          '#type' => 'managed_file',
          '#upload_validators'    => [
            'file_validate_extensions'    => array('zip'),
          ],
          '#default_value' => $font->get('file')
        ],
        'features' => [
          [
            '#type' => 'checkbox',
            '#title' => t('EDT'),
            '#disabled' => TRUE,
            '#default_value' => $features['edt']
          ],
          [
            '#type' => 'checkbox',
            '#title' => t('SVG'),
            '#disabled' => TRUE,
            '#default_value' => $features['svg']
          ],
          [
            '#type' => 'checkbox',
            '#title' => t('TTF'),
            '#disabled' => TRUE,
            '#default_value' => $features['ttf']
          ],
          [
            '#type' => 'checkbox',
            '#title' => t('WOFF'),
            '#disabled' => TRUE,
            '#default_value' => $features['woff']
          ],
          [
            '#type' => 'checkbox',
            '#title' => t('WOFF2'),
            '#disabled' => TRUE,
            '#default_value' => $features['woff2']
          ]
        ],
        'actions' => [
          'delete' => [
            '#type' => 'submit',
            '#value' => t('Delete')
          ]
        ]
      ];

    }

    $form['detail']['uploads']['fonts'][] = [
      'original_id' => [
        '#type' => 'value',
        '#value' => ''
      ],
      'id' => [
        '#type' => 'textfield',
        '#size' => 10,
        '#placeholder' => t('Label')
      ],
      'file' => [
        '#type' => 'managed_file',
        '#upload_validators'    => [
          'file_validate_extensions'    => array('zip'),
        ]
      ],
      'features' => [
        [
          '#type' => 'checkbox',
          '#title' => t('EDT'),
          '#disabled' => TRUE,
        ],
        [
          '#type' => 'checkbox',
          '#title' => t('SVG'),
          '#disabled' => TRUE,
        ],
        [
          '#type' => 'checkbox',
          '#title' => t('TTF'),
          '#disabled' => TRUE,
        ],
        [
          '#type' => 'checkbox',
          '#title' => t('WOFF'),
          '#disabled' => TRUE,
        ],
        [
          '#type' => 'checkbox',
          '#title' => t('WOFF2'),
          '#disabled' => TRUE,
        ]
      ],
      'actions' => [
        'delete' => [
          '#type' => 'submit',
          '#value' => t('Delete')
        ]
      ]
    ];

  }

  /**
  * Returns the sub form elements for web fonts.
  */
  private function webfontsForm(array &$form, FormStateInterface $form_state, $fonts) {

    $form['detail']['webfonts']['fonts'] = [
      '#type' => 'table',
      '#rows' => [],
    ];

    foreach($fonts as $font) {
      if ($font->get('type') !== 'webfont') {
        continue;
      }
      $form['detail']['webfonts']['fonts'][] = [
        'original_id' => [
          '#type' => 'value',
          '#value' => $font->get('id')
        ],
        'id' => [
          '#type' => 'textfield',
          '#default_value' => $font->get('id'),
          '#size' => 10,
          '#placeholder' => t('Label')
        ],
        'url' => [
          '#type' => 'textfield',
          '#default_value' => $font->get('url'),
          '#title' => '@import',
          '#size' => 20,
          '#placeholder' => t('Url')
        ],
        'actions' => [
          'delete' => [
            '#type' => 'button',
            '#value' => t('Delete')
          ]
        ]
      ];

    }

    $form['detail']['webfonts']['fonts'][] = [
      'original_id' => [
        '#type' => 'value',
        '#value' => ''
      ],
      'id' => [
        '#type' => 'textfield',
        '#size' => 10,
        '#placeholder' => t('Label')
      ],
      'url' => [
        '#type' => 'textfield',
        '#title' => '@import',
        '#size' => 20,
        '#placeholder' => t('Url')
      ],
      'actions' => [
        'delete' => [
          '#type' => 'button',
          '#value' => t('Delete')
        ]
      ]
    ];

  }


  /**
  * Returns the sub form elements for font packs.
  */
  private function fontpackForm(array &$form, FormStateInterface $form_state, $packs) {

    $form['detail']['packs']['fonts'] = [
      '#type' => 'table',
      '#rows' => []
    ];

    foreach($packs as $pack) {
      $form['detail']['packs']['fonts'][] = [
        'label' => [
          '#type' => 'textfield',
          '#size' => 10,
          '#placeholder' => t('Label'),
          '#default_value' => $pack->get('name')
        ],
        'id' => [
          '#type' => 'textfield',
          '#size' => 15,
          '#title' => '$wem-pack-',
          '#placeholder' => t('Variable'),
          '#default_value' => $pack->get('id')
        ],
        'families' => [
          '#type' => 'textfield',
          '#size' => 25,
          '#placeholder' => t('Font Pack'),
          '#default_value' => $pack->get('families')
        ],
        'ff_smoothing' => [
          '#type' => 'checkbox',
          '#title' => t('Firefox'),
          '#prefix' => '<label>Smoothing</label>',
          '#default_value' => $pack->get('ff_smoothing'),
        ],
        'webkit_smoothing' => [
          '#type' => 'checkbox',
          '#title' => t('Webkit'),
          '#default_value' => $pack->get('webkit_smoothing')
        ]
      ];
    }

    $form['detail']['packs']['fonts'][] = [
      'label' => [
        '#type' => 'textfield',
        '#size' => 10,
        '#placeholder' => t('Label')
      ],
      'id' => [
        '#type' => 'textfield',
        '#size' => 15,
        '#title' => '$wem-font-',
        '#placeholder' => t('Variable')
      ],
      'families' => [
        '#type' => 'textfield',
        '#size' => 25,
        '#placeholder' => t('Font Pack')
      ],
      'ff_smoothing' => [
        '#type' => 'checkbox',
        '#title' => t('Firefox'),
        '#prefix' => '<label>Smoothing</label>'
      ],
      'webkit_smoothing' => [
        '#type' => 'checkbox',
        '#title' => t('Webkit')
      ]
    ];

  }

  /**
  * {@inheritdoc}
  */
  public function submitForm (array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    // Process uploaded fonts.
    foreach($values['detail']['uploads']['fonts'] as $delta => $font) {
      // Check if this font id exists.
      if ($entity = Font::load($font['original_id'])) {
          $entity->delete();
          $entity = Font::create([
            'id'    => $font['id'],
            'name'  => $font['id'],
            'type'  => 'upload',
            'file'  => $font['file'],
            'url'   => '',
            'eot'   => '',
            'svg'   => '',
            'ttf'   => '',
            'woff'  => '',
            'woff2' => '',
            'json'  => '',
          ]);
          $entity->save();


      }
      else if (!empty($font['id'])){

        // new font
        $entity = Font::create([
          'id'    => $font['id'],
          'name'  => $font['id'],
          'type'  => 'upload',
          'file'  => $font['file'],
          'url'   => '',
          'eot'   => '',
          'svg'   => '',
          'ttf'   => '',
          'woff'  => '',
          'woff2' => '',
          'json'  => '',
        ]);
       $entity->save();


      }

    }

    // Process web fonts.
    foreach($values['detail']['webfonts']['fonts'] as $delta => $font) {

      // Check if this font id exists.
      if ($entity = Font::load($font['original_id'])) {
          $entity->delete();
          $entity = Font::create([
            'id'    => $font['id'],
            'name'  => $font['id'],
            'type'  => 'webfont',
            'file'  => '',
            'url'   => $font['url'],
            'eot'   => '',
            'svg'   => '',
            'ttf'   => '',
            'woff'  => '',
            'woff2' => '',
            'json'  => '',
          ]);
          $entity->save();
      }
      else if (!empty($font['id'])){

        // new font
        $entity = Font::create([
          'id'    => $font['id'],
          'name'  => $font['id'],
          'type'  => 'webfont',
          'file'  => '',
          'url'   => $font['url'],
          'eot'   => '',
          'svg'   => '',
          'ttf'   => '',
          'woff'  => '',
          'woff2' => '',
          'json'  => '',
        ]);
       $entity->save();
      }
    }

    // Process font packs.
    foreach($values['detail']['packs']['fonts'] as $delta => $pack) {
      if ($entity = FontPack::load($font['original_id'])) {
          $entity->delete();
          $entity = FontPack::create([
            'id'    => $pack['id'],
            'name'  => $pack['id'],
            'families' => $pack['families'],
            'ff_smoothing' => $pack['ff_smoothing'],
            'webkit_smoothing' => $pack['webkit_smoothing']
          ]);
          $entity->save();
      }
      else if (!empty($pack['id'])){

        // new font
        $entity = FontPack::create([
          'id'    => $pack['id'],
          'name'  => $pack['id'],
          'families' => $pack['families'],
          'ff_smoothing' => $pack['ff_smoothing'],
          'webkit_smoothing' => $pack['webkit_smoothing']
        ]);
        $entity->save();
      }
    }

  }

  private function getFontFeatures($fid) {
    $file = File::load(array_shift($fid));
    $features = [
      'edt' => 0,
      'svg' => 0,
      'ttf' => 0,
      'woff' => 0,
      'woff2' => 0,
    ];

    if ($file == null) { return $features; }
    $realpath = drupal_realpath($file->getFileUri());
    $zip = new \ZipArchive;
    $res = $zip->open($realpath);



    for($i = 0; $i < $zip->numFiles; $i++) {
      $entry = $zip->getNameIndex($i);
      foreach($features as $id => $dat) {
        if(preg_match('#\.(' . $id . ')$#i', $entry)){
          $features[$id] = 1;
        }
      }
    }

    return $features;
  }

  /**
  * Helper function for returning currently available fonts.
  */
  private function getFonts() {
    $entities = entity_load_multiple('font');
    return $entities;
  }

  /**
  * Helper function for returning currently available font packs.
  */
  private function getFontPacks() {
    $entities = entity_load_multiple('font_pack');
    return $entities;
  }

}
