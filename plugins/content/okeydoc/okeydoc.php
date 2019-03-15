<?php
/**
 * @package Okey DOC 2
 * @copyright Copyright (c) 2018 - 2018 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
// Import the JPlugin class
jimport('joomla.plugin.plugin');


class plgContentOkeydoc extends JPlugin
{
  protected $post;
  protected $jform;

  /**
   * Constructor.
   *
   * @param   object  &$subject  The object to observe
   * @param   array   $config    An optional associative array of configuration settings.
   *
   * @since   3.7.0
   */
  public function __construct(&$subject, $config)
  {
    // Loads the component language file.
    $lang = JFactory::getLanguage();
    $langTag = $lang->getTag();
    $lang->load('com_okeydoc', JPATH_ROOT.'/administrator/components/com_okeydoc', $langTag);

    // Gets the POST and jform data.
    $this->post = JFactory::getApplication()->input->post->getArray();
    $this->jform = JFactory::getApplication()->input->post->get('jform', array(), 'array');

    parent::__construct($subject, $config);
  }


  public function onContentPrepare($context, &$data, &$params, $page)
  {
  }


  public function onContentAfterTitle($context, &$data, &$params, $limitstart)
  {
  }


  public function onContentBeforeDisplay($context, &$data, &$params, $limitstart)
  {
  }


  public function onContentAfterDisplay($context, &$data, &$params, $limitstart)
  {
  }


  public function onContentBeforeSave($context, $data, $isNew)
  {
    return true;
  }


  public function onContentAfterSave($context, $data, $isNew)
  {
    if($context == 'com_okeydoc.document' || $context == 'com_okeydoc.form') {

      // Set the content categories and/or articles linked to the document.
      
      $db = JFactory::getDbo();
      $query = $db->getQuery(true);
      // First deletes the possible previous categories and articles linked to this document.
      $query->delete('#__okeydoc_document_map')
	    ->where('doc_id='.(int)$data->id.' AND item_type IN ("category", "article")');
      $db->setQuery($query);
      $db->execute();
    }

    // Initialize the SQL clause variables.
    $columns = array('doc_id', 'item_id', 'item_type');
    $values = array();

    if(count($this->jform['contcatids'])) {
      // Builds the VALUE SQL clause.
      foreach($this->jform['contcatids'] as $contCatId) {
	$values[] = (int)$data->id.','.(int)$contCatId.',"category"';
      }

      // Inserts the linked categories.
      $query->clear();
      $query->insert('#__okeydoc_document_map')
	    ->columns($columns)
	    ->values($values);
      $db->setQuery($query);
      $db->execute();
    }

    if(count($this->jform['articleids'])) {
      // Resets the value array.
      $values = array();

      // Builds the VALUE SQL clause.
      foreach($this->jform['articleids'] as $articleId) {
	$values[] = (int)$data->id.','.(int)$articleId.',"article"';
      }

      // Inserts the linked articles.
      $query->clear();
      $query->insert('#__okeydoc_document_map')
	    ->columns($columns)
	    ->values($values);
      $db->setQuery($query);
      $db->execute();
    }
  }


  public function onContentBeforeDelete($context, $data)
  {
    return true;
  }


  public function onContentAfterDelete($context, $data)
  {
  }


  public function onContentPrepareForm($form, $data)
  {
    return true;
  }


  public function onContentPrepareData($context, $data)
  {
    return true;
  }


  public function onContentChangeState($context, $pks, $value)
  {
    return true;
  }
}

