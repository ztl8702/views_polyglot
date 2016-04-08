<?php

/**
 * @file
 * Contains \Drupal\views_polyglot\Plugin\views\field\PolyglotField.
 */

namespace Drupal\views_polyglot\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
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
/*
    $options['use_php_setup'] = array('default' => FALSE);
    $options['php_setup'] = array('default' => '');
    $options['php_value'] = array('default' => '');
    $options['php_output'] = array('default' => '');
    $options['use_php_click_sortable'] = array('default' => self::CLICK_SORT_DISABLED);
    $options['php_click_sortable'] = array('default' => FALSE);
*/
    return $options;
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
    dpm($values);
    return NULL;
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

  /**
   *
   * @see views_polyglot_views_pre_execute()
   * @see self::php_post_execute()
   */
  public function polyglotPreExecute() {
    // Execute static PHP code.
    
  }

  /**
   *
   * @see views_polyglot_views_post_execute()
   */
  public function polyglotPostExecute(&$values) {
    // Execute value PHP code.
    /*if (!empty($this->options['php_value'])) {
      $function = create_function('$view, $handler, &$static, $row', $this->options['php_value'] . ';');
      ob_start();
      foreach ($this->view->result as $i => &$row) {
        $normalized_row = new ViewsPhpNormalizedRow();
        foreach ($this->view->display_handler->getHandlers('field') as $field => $handler) {
          // Do not add our own field. Also, do not add other fields that have no data yet. This occurs because
          // the value code is evaluated in hook_views_post_execute(), but field data is made available in hook_views_pre_render(),
          // which is called after hook_views_post_execute().
          if ((empty($handler->aliases) || empty($handler->aliases['entity_type'])) && $handler->field_alias != $this->field_alias) {
            $normalized_row->$field = isset($row->{$handler->field_alias}) ? $row->{$handler->field_alias} : NULL;
          }
        }
        $row->{$this->field_alias} = $function($this->view, $this, $this->php_static_variable, $normalized_row);
      }
      ob_end_clean();
    }
*/

  }

}


