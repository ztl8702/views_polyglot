<?php

/**
 * @file
 * Contains advance_language_filter\advance_language_filter.views.inc..
 * Provide a custom views field data that isn't tied to any other module. */

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Render\Markup;
use Drupal\field\FieldConfigInterface;
use Drupal\field\FieldStorageConfigInterface;
use Drupal\system\ActionConfigEntityInterface;

/**
* Implements hook_views_data().
*/
function views_polyglot_views_data() {

    $data['views']['table']['group'] = t('Custom Global');
    $data['views']['table']['join'] = array(
      // #global is a special flag which allows a table to appear all the time.
      '#global' => array(),
    );


    $data['views']['polyglot_filter'] = array(
        'title' => t('Polyglot'),
        'help' => t('Advance language filter'),
        'filter' => array(
            'id' => 'polyglot_filter',
        ),
    );

    return $data;
}
