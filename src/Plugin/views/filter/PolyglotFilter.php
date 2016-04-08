<?php

/**
 * @file
 * Contains \Drupal\views_polyglot\Plugin\views\filter\PolyglotFilter.
 */

namespace Drupal\views_polyglot\Plugin\views\filter;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\views\Plugin\views\filter\FilterPluginBase;


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
    return $options;
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
