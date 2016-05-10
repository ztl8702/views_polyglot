<?php

/**
 * @file
 * Contains \Drupal\views_polyglot\Plugin\views\filter\PolyglotFilter.
 */

namespace Drupal\views_polyglot\Plugin\views\filter;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\views\Plugin\views\filter\FilterPluginBase;
use Drupal\Core\Form\FormStateInterface;


/**
 * Provides filtering by a sequence of language priority. 
 * Ensures that only one language version is shown.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("polyglot_filter")
 */
class PolyglotFilter extends FilterPluginBase {

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  protected $languagePriority = ['en', 'zh-hans','cdo']; //TODO: Change this to an option
  protected $langcodeAlias;
  

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $available_langs = \Drupal::languageManager()->getLanguages();
    
    $available_langcodes = [];
    $count = 0;
    foreach ($available_langs as $id => $obj) {
        $available_langcode[] = array (
            'langcode' => $id,
            'weight' => $count++
        );
    }
    $options['language_priority'] = array(
        'default' => $available_langcode
    );
    
  //  error_log(print_r($options, true));
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    $form['language_priority'] = $this->buildDraggableTable($this->options);
  
  }
  function my_sort($a,$b)
  {
    if ($a['weight']==$b['weight']) return 0;
    return ($a['weight']<$b['weight'])?-1:1;
  }

  private function buildDraggableTable($options) {
    $table = array(
      '#type' => 'table',
      '#title' => t('Language Priority'),
      '#description' => t('Des'),
      '#header' => array('label' => t('Language'), 'weight' => t('Weight')),
    
       // TableDrag: Each array value is a list of callback arguments for
       // drupal_add_tabledrag(). The #id of the table is automatically prepended;
       // if there is none, an HTML ID is auto-generated.
       '#tabledrag' => array(
         array(
           'action' => 'order',
           'relationship' => 'sibling',
           'group' => 'mytable-order-weight',
         ),
       ),
    );
  // Build the table rows and columns.
  // The first nested level in the render array forms the table row, on which you
  // likely want to set #attributes and #weight.
  // Each child element on the second level represents a table column cell in the
  // respective table row, which are render elements on their own. For single
  // output elements, use the table cell itself for the render element. If a cell
  // should contain multiple elements, simply use nested sub-keys to build the
  // render element structure for drupal_render() as you would everywhere else.
   error_log('build');
   error_log(print_r( $options['language_priority'], true));
   $p=$options['language_priority'];
   uasort($p,function ($a,$b){if ($a['weight']==$b['weight']) return 0;  return ($a['weight']<$b['weight'])?-1:1;});
   error_log(print_r( $p, true));
  
   //usort($options['language_priority'], "my_sort");

    $entities = $p;//$options['language_priority']; //[0=>'one', 1 => 'two', 2 => 'three'];
    
    foreach ($entities as $id => $lang) {
      // TableDrag: Mark the table row as draggable.
      $item=[];
      $item['#attributes']['class'][] = 'draggable';
      // TableDrag: Sort the table row according to its existing/configured weight;
      $item['#weight'] = intval($lang['weight']);

      
      $item['label'] = array(
        '#plain_text' => $lang['langcode'],
      );
      

      // TableDrag: Weight column element.
      $item['weight'] = array(
        '#type' => 'weight',
        '#title' => t('Weight for @title', array('@title' => $lang['langcode'])),
        '#title_display' => 'invisible',
        '#default_value' => $lang['weight'],
        // Classify the weight element for #tabledrag.
        '#attributes' => array('class' => array('mytable-order-weight')),
      );
      
      $table[$id]=$item; 
    }
    
    return $table;
  }

public function submitOptionsForm(&$form, FormStateInterface $form_state) {
  //error_log(print_r( $this->options['language_priority'], true));
  
  error_log(print_r( $form_state->getValue(['options','language_priority']), true));
  $p = $form_state->getValue(['options','language_priority']);
  usort($p,"my_sort");
  $form_state->setValue(['options','language_priority'], $p);
  // Do not store these values.
  $form_state->unsetValue('expose_button');
  $form_state->unsetValue('group_button');

  if (!$this->isAGroup()) {
    $this->operatorSubmit($form, $form_state);
    $this->valueSubmit($form, $form_state);
  }
  if (!empty($this->options['exposed'])) {
    $this->submitExposeForm($form, $form_state);
  }
  if ($this->isAGroup()) {
    $this->buildGroupSubmit($form, $form_state);
  }
 $this->valueSubmit($form, $form_state);
 error_log('submitt');
 
}

  function canExpose() {
    return FALSE;
  }
  
  function adminSummary() {
    return t("Polyglot");
  }
  /**
   * {@inheritdoc}
   */
  /*public function getValueOptions() {
    if (!isset($this->valueOptions)) {
      $this->valueTitle = $this->t('Language');
      // Pass the current values so options that are already selected do not get
      // lost when there are changes in the language configuration.
      $this->valueOptions = $this->listLanguages(LanguageInterface::STATE_ALL | LanguageInterface::STATE_SITE_DEFAULT | PluginBase::INCLUDE_NEGOTIATED, array_keys($this->value));
    }
    return $this->valueOptions;
  }
*/
  /**
   * {@inheritdoc}
   */
  public function query() {
    //inform views_polyglot_views_post_execute to seize control
    $this->view->polyglot = TRUE; 


    // In order to get the translation language of each row, we need
    // to add the language code of the entity to the query. Skip if the site
    // is not multilingual or the entity is not translatable. 
    // This part is adapted from the query() function in TranslatationRenderPlugin
    if (!\Drupal::languageManager()->isMultilingual() || !\Drupal::entityTypeManager()->getDefinition('node')->hasKey('langcode')) {
      return;
    }
    $langcode_key = \Drupal::entityTypeManager()->getDefinition('node')->getKey('langcode');
    $storage = \Drupal::entityManager()->getStorage('node');

    if ($table = $storage->getTableMapping()->getFieldTableName($langcode_key)) {
      $table_alias = $this->ensureMyTable();
      $this->langcodeAlias = $this->query->addField($table_alias, $langcode_key);
    }
    
  }



  function getPriorityLangcode($avail_langs) {
	foreach ($this->languagePriority as $i => $code) {
    	if (isset($avail_langs[$code])) 
		return $code;
    }
    return NULL;
  }
  
   /**
   *
   * @see views_polyglot_views_post_execute()
   */  
  function polyglotPostExecute() {
    foreach ($this->view->result as $i => $result) {
      //dpm($i);
      $translation_langs = $result->_entity->getTranslationLanguages();
      $row_lang = $result->langcode;
      $lang_to_display=$this->getPriorityLangcode($translation_langs);
//      dpm($lang_to_display);

      if ($row_lang != 'und' && $row_lang != $lang_to_display) {
        unset($this->view->result[$i]); 
//        dpm('unset'.$i);
      }
    }

  } 
}
