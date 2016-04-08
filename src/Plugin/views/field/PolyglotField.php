<?php

/**
 * @file
 * Contains \Drupal\views_polyglot\Plugin\views\field\PolyglotField.
 */

namespace Drupal\views_polyglot\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;


/**
 * A handler to provide a field that is constructed by the administrator using PHP.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("polyglot_field")
 */
class PolyglotField extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    return $options;
  }
  public function adminLabel($short = FALSE) {
    return t('Polyglot Field');
  }
  /**
   * {@inheritdoc}
   */
/* public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    $form += views_php_form_element($this,
      array('use_php_setup', t('Use setup code'), t('If checked, you can provide PHP code to be run once right before execution of the view. This may be useful to define functions to be re-used in the value and/or output code.')),
      array('php_setup', t('Setup code'), t('Code to run right before execution of the view.'), FALSE),
      array('$view', '$handler', '$static')
    );
    $form += views_php_form_element($this,
      FALSE,
      array('php_value', t('Value code'), t('Code to construct the value of this field.'), FALSE),
      array('$view', '$handler', '$static', '$row')
    );
    $form += views_php_form_element($this,
      array('use_php_click_sortable', t('Enable click sort'), t('If checked, you can use PHP code to enable click sort on the field.')),
      array('php_click_sortable', t('Click sort code'), t('The comparison code must return an integer less than, equal to, or greater than zero if the first row should respectively appear before, stay where it was compared to, or appear after the second row.'), FALSE),
      array(
        '$view', '$handler', '$static',
        '$row1' => t('Data of row.'),
        '$row2' => t('Data of row to compare against.'),
      )
    );
    $form['use_php_click_sortable']['#type'] = 'select';
    $form['use_php_click_sortable']['#options'] = array(
      self::CLICK_SORT_DISABLED => t('No'),
      self::CLICK_SORT_NUMERIC => t('Sort numerically'),
      self::CLICK_SORT_ALPHA => t('Sort alphabetically'),
      self::CLICK_SORT_ALPHA_CASE => t('Sort alphabetically (case insensitive)'),
      self::CLICK_SORT_NAT => t('Sort using a "natural order" algorithm'),
      self::CLICK_SORT_NAT_CASE => t('Sort using a "natural order" algorithm (case insensitive)'),
      self::CLICK_SORT_PHP => t('Sort using custom PHP code'),
    );
    $form['use_php_click_sortable']['#default_value'] = $this->options['use_php_click_sortable'];
    $form['php_click_sortable']['#states'] = array(
      'visible' => array(
        ':input[name="options[use_php_click_sortable]"]' => array('value' => (string)self::CLICK_SORT_PHP),
      ),
    );
    $form += views_php_form_element($this,
      FALSE,
      array('php_output', t('Output code'), t('Code to construct the output of this field.'), TRUE),
      array('$view', '$handler', '$static', '$row', '$data', '$value' => t('Value of this field.'))
    );

    $form['#attached']['library'][] = 'views_php/drupal.views_php';
  }*/

  /**
   * {@inheritdoc}
   */
  public function clickSortable() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function clickSort($order) {
    //DO NOTHING
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    $translation_langs = $values->_entity->getTranslationLanguages(); //Available translation languages of this node
    $nid = $values -> _entity->id(); //node id
    $output = array();  // a list of links
    foreach ($translation_langs as $id => $obj) {
      $lang_code = $obj -> getId();
      $lang_name = $obj -> getName();
      $options = array(
                   'language' => $obj,
                 );

      $url_to_lang = \Drupal\Core\Url::fromRoute('entity.node.canonical', ['node' => $nid], $options);

      $link = Link::fromTextAndUrl($lang_name, $url_to_lang);
      $output[] = $link->toRenderable();  //attach to the render array
    }
    return $output;
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Provide an field alias but don't actually alter the query.
    $this->field_alias = 'views_polyglot_' . $this->position;  //??what is position???
    // Inform views_polyglot_views_pre_execute() to seize control over the query.
    $this->view->polyglot = TRUE;
  }


}


