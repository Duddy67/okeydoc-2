<?php
/**
 * @package Okey DOC 2
 * @copyright Copyright (c) 2015 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

// No direct access
defined('_JEXEC') or die('Restricted access');


/**
 * The Okey DOC 2 content plugin.
 *
 */
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


  /**
   * The display event.
   *
   * @param   string    $context     The context
   * @param   stdClass  $item        The item
   * @param   Registry  $params      The params
   * @param   integer   $limitstart  The start
   *
   * @return  string
   *
   * @since   3.7.0
   */
  public function onContentAfterDisplay($context, &$item, &$params, $limitstart)
  {
  }


  /**
   * Updates internal and external document linkings.
   *
   * @param   string   $context  The context of the content passed to the plugin (added in 1.6)
   * @param   object   $data     A JTableContent object
   * @param   boolean  $isNew    If the content is just about to be created
   *
   * @return  void     Result will be omitted.
   *
   * @since   1.6
   */
  public function onContentAfterSave($context, $data, $isNew)
  {
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


  /**
   * Don't allow a document to be deleted if articles or article categories contain links to
   * this document.
   *
   * @param   string  $context  The context for the content passed to the plugin.
   * @param   object  $data     The data relating to the content that was deleted.
   *
   * @return  boolean
   *
   * @since   1.6
   */
  public function onContentBeforeDelete($context, $data)
  {
    if($context == 'com_okeydoc.document') {
      // Checks that the document is not linked from an article or an article category.
      $model = JModelLegacy::getInstance('Document', 'OkeydocModel');
      $linkinks = $model->getExternalDocumentLinkings($data->id);

      if(!empty($linkinks['article']) || !empty($linkinks['category'])) {
	JFactory::getApplication()->enqueueMessage(JText::_('COM_OKEYDOC_WARNING_DOCUMENT_STILL_LINKED'), 'Warning');
	return false;
      }
    }

    return true;
  }


  /**
   * Removes all the rows relating to the deleted document in the mapping tables.
   * Checks for deleted articles or article categories and removes the document linkings
   * accordingly..
   *
   * @param   string  $context  The context for the content passed to the plugin.
   * @param   object  $data     The data relating to the content that was deleted.
   *
   * @return  void     Result will be omitted.
   *
   * @since   1.6
   */
  public function onContentAfterDelete($context, $data)
  {
    if($context == 'com_okeydoc.document') {
      // Removes the document linkings relating to this document.
      $this->deleteDocumentLinkings($data->id);

      // Deletes the archived file data binds to this document.
      $db = JFactory::getDbo();
      $query = $db->getQuery(true);
      $query->delete('#__okeydoc_archive')
	    ->where('doc_id='.(int)$data->id);
      $db->setQuery($query);
      $db->execute();
    }
    elseif($context == 'com_content.article') {
      // Searches links to any document in the article text.
      $text = $data->introtext.$data->fulltext;
      $docIds = $this->searchDocumentLinks($text);
      // Removes all linkings relating to this article from the linking table.
      $this->deleteDocumentLinkings($docIds, $data->id, 'article', 'external');
    }
    elseif($context == 'com_categories.category' && $data->extension == 'com_content') {
      // Same for the article category text.
      $docIds = $this->searchDocumentLinks($data->description);
      $this->deleteDocumentLinkings($docIds, $data->id, 'category', 'external');
    }
  }


  /**
   * Utility function which deletes rows in the #__okeydoc_document_linking table according to the given parameters.
   *
   * @param   array    $docIds        The ids of the concerned documents.
   * @param   integer  $itemId        The id of the linked item.
   * @param   string   $itemType      The type of the linked item (article or category).
   * @param   string   $linkingType   The type of linkink to remove (internal or external).
   *
   * @return  void     
   */
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
    // Builds the query according to the given parameters. 
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


  /**
   * Utility function which inserts rows in the #__okeydoc_document_linking table according to the given values.
   *
   * @param   array    $values   The values to insert into the table.
   *
   * @return  void     
   */
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


  /**
   * Parses the given string and searches for links leading to documents.
   *
   * @param   string   $text     The string to search into.
   *
   * @return  array              The document ids found into the given string.     
   */
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

