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
      
      // First deletes the possible previous categories and articles linked to this document.
      $this->deleteDocumentLinkings($data->id, null, null, 'internal');

      // Initialize the SQL clause variables.
      $values = array();

      if(count($this->jform['contcatids'])) {
	// Builds the VALUE SQL clause.
	foreach($this->jform['contcatids'] as $contCatId) {
	  $values[] = (int)$data->id.','.(int)$contCatId.',"category", "internal"';
	}

	// Inserts the linked categories.
	$this->insertDocumentLinkings($values);
      }

      if(count($this->jform['articleids'])) {
	// Resets the value array.
	$values = array();

	// Builds the VALUE SQL clause.
	foreach($this->jform['articleids'] as $articleId) {
	  $values[] = (int)$data->id.','.(int)$articleId.',"article", "internal"';
	}

	// Inserts the linked articles.
	$this->insertDocumentLinkings($values);
      }
    }
    elseif($context == 'com_content.article' || $context == 'com_content.form') {
      $text = $data->introtext.$data->fulltext;
      $docIds = $this->searchDocumentLinks($text);
      $this->deleteDocumentLinkings(null, $data->id, 'article', 'external');

      if(!empty($docIds)) {
	$values = array();

	// Builds the VALUE SQL clause.
	foreach($docIds as $docId) {
	  $values[] = (int)$docId.','.(int)$data->id.',"article", "external"';
	}

        $this->insertDocumentLinkings($values);
      }
    }
    elseif($context == 'com_categories.category' && $data->extension == 'com_content') {
      $docIds = $this->searchDocumentLinks($data->description);
      $this->deleteDocumentLinkings(null, $data->id, 'category', 'external');

      if(!empty($docIds)) {
	$values = array();

	// Builds the VALUE SQL clause.
	foreach($docIds as $docId) {
	  $values[] = (int)$docId.','.(int)$data->id.',"category", "external"';
	}

        $this->insertDocumentLinkings($values);
      }
    }
  }


  public function onContentBeforeDelete($context, $data)
  {
    return true;
  }


  public function onContentAfterDelete($context, $data)
  {
    if($context == 'com_okeydoc.document') {
      // 
      $this->deleteDocumentLinkings($data->id);

      //
      $db = JFactory::getDbo();
      $query = $db->getQuery(true);

      $query->delete('#__okeydoc_archive')
	    ->where('doc_id='.(int)$data->id);
      $db->setQuery($query);
      $db->execute();
    }
    elseif($context == 'com_content.article') {
      $text = $data->introtext.$data->fulltext;
      $docIds = $this->searchDocumentLinks($text);
      $this->deleteDocumentLinkings($docIds, $data->id, 'article', 'external');
    }
    elseif($context == 'com_categories.category' && $data->extension == 'com_content') {
      $docIds = $this->searchDocumentLinks($data->description);
      $this->deleteDocumentLinkings($docIds, $data->id, 'category', 'external');
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


  private function deleteDocumentLinkings($docIds = null, $itemId = null, $itemType = null, $linkingType = null)
  {
    // Prevents the complete deletion of the rows as well as the request to fail. 
    if(($docIds === null && $itemId === null && $itemType === null && $linkingType === null) || (is_array($docIds) && empty($docIds))) {
      return;
    }

    if($docIds !== null && !is_array($docIds)) {
      $docId = (int)$docIds;
      $docIds = array($docId);
    }

    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    // 
    $query->delete('#__okeydoc_document_linking');

    if($docIds !== null) {
      $query->where('doc_id IN('.implode(',', $docIds).')');
    }

    if($itemId !== null) {
      $query->where('item_id='.(int)$itemId);
    }

    if($itemType !== null) {
      $query->where('item_type='.$db->Quote($itemType));
    }

    if($linkingType !== null) {
      $query->where('linking_type='.$db->Quote($linkingType));
    }

    $db->setQuery($query);
    $db->execute();
  }


  private function insertDocumentLinkings($values)
  {
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $columns = array('doc_id', 'item_id', 'item_type', 'linking_type');

    $query->insert('#__okeydoc_document_linking')
	  ->columns($columns)
	  ->values($values);
    $db->setQuery($query);
    $db->execute();
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
}

