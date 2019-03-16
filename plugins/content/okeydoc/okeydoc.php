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
      file_put_contents('debog_file.txt', print_r($context, true));
    if($context == 'com_okeydoc.document' || $context == 'com_okeydoc.form') {

      // Set the content categories and/or articles linked to the document.
      
      $db = JFactory::getDbo();
      $query = $db->getQuery(true);
      // First deletes the possible previous categories and articles linked to this document.
      $query->delete('#__okeydoc_document_linking')
	    ->where('doc_id='.(int)$data->id.' AND linking_type="internal" AND item_type IN ("category", "article")');
      $db->setQuery($query);
      $db->execute();

      // Initialize the SQL clause variables.
      $columns = array('doc_id', 'item_id', 'item_type', 'linking_type');
      $values = array();

      if(count($this->jform['contcatids'])) {
	// Builds the VALUE SQL clause.
	foreach($this->jform['contcatids'] as $contCatId) {
	  $values[] = (int)$data->id.','.(int)$contCatId.',"category", "internal"';
	}

	// Inserts the linked categories.
	$query->clear();
	$query->insert('#__okeydoc_document_linking')
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
	  $values[] = (int)$data->id.','.(int)$articleId.',"article", "internal"';
	}

	// Inserts the linked articles.
	$query->clear();
	$query->insert('#__okeydoc_document_linking')
	      ->columns($columns)
	      ->values($values);
	$db->setQuery($query);
	$db->execute();
      }
    }
    elseif($context == 'com_content.article' || $context == 'com_content.form') {
      $text = $data->introtext.$data->fulltext;
      $docIds = $this->searchDocumentLinks($text);

      if(!empty($docIds)) {
      }
    }
    elseif($context == 'com_categories.category') {
      $docIds = $this->searchDocumentLinks($data->description);

      if(!empty($docIds)) {
      }
    }
  }


  public function searchDocumentLinks($text)
  {
    $docIds = array();
    // Searches document links into the given text then extracts the document ids.
    preg_match_all('#<a href="index\.php\?option=com_okeydoc&amp;tmpl=component&amp;view=download&amp;id=([0-9]*)#i', $text, $matches);
    // Stores the document ids if any.
    foreach($matches[1] as $docId) {
      if(!in_array($docId, $docIds)) {
	$docIds[] = $docId;
      }
    }

    return $docIds;
  }


  public function onContentBeforeDelete($context, $data)
  {
    return true;
  }


  public function onContentAfterDelete($context, $data)
  {
    if($context == 'com_okeydoc.document') {
      $db = JFactory::getDbo();
      $query = $db->getQuery(true);
      // 
      $query->delete('#__okeydoc_document_linking')
	    ->where('doc_id='.(int)$data->id);
      $db->setQuery($query);
      $db->execute();
      //
      $query->clear();
      $query->delete('#__okeydoc_archive')
	    ->where('doc_id='.(int)$data->id);
      $db->setQuery($query);
      $db->execute();
    }
    elseif($context == 'com_content.article') {
      $text = $data->introtext.$data->fulltext;
      $docIds = $this->searchDocumentLinks($text);

      if(!empty($docIds)) {
	$db = JFactory::getDbo();
	$query = $db->getQuery(true);
	// 
	$query->delete('#__okeydoc_document_linking')
	      ->where('item_id='.(int)$data->id.' AND item_type="article" AND linking_type="external"')
	      ->where('doc_id IN('.implode(',', $docIds).')');
	$db->setQuery($query);
	$db->execute();
      }
    }
    elseif($context == 'com_categories.category') {
    }
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

