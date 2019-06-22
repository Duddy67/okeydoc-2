<?php
/**
 * @package Okey DOC 2
 * @copyright Copyright (c) 2015 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

// No direct access to this file.
defined('_JEXEC') or die; 


class OkeydocModelDocument extends JModelItem
{

  protected $_context = 'com_okeydoc.document';

  /**
   * Method to auto-populate the model state.
   *
   * Document. Calling getState in this method will result in recursion.
   *
   * @since   1.6
   *
   * @return void
   */
  protected function populateState()
  {
    $app = JFactory::getApplication('site');

    // Load state from the request.
    $pk = $app->input->getInt('id');
    $this->setState('document.id', $pk);

    // Load the global parameters of the component.
    $params = $app->getParams();
    $this->setState('params', $params);

    $this->setState('filter.language', JLanguageMultilang::isEnabled());
  }


  // Returns a Table object, always creating it.
  public function getTable($type = 'Document', $prefix = 'OkeydocTable', $config = array()) 
  {
    return JTable::getInstance($type, $prefix, $config);
  }


  /**
   * Method to get a single record.
   *
   * @param   integer  $pk  The id of the primary key.
   *
   * @return  mixed    Object on success, false on failure.
   *
   * @since   12.2
   */
  public function getItem($pk = null)
  {
    $pk = (!empty($pk)) ? $pk : (int)$this->getState('document.id');
    $user = JFactory::getUser();

    if($this->_item === null) {
      $this->_item = array();
    }

    if(!isset($this->_item[$pk])) {
      $db = JFactory::getDbo();
      $query = $db->getQuery(true);
      $query->select($this->getState('list.select', 'd.id,d.title,d.alias,d.intro_text,d.full_text,d.catid,d.published,d.file_location,'.
				     'd.file_name,d.file_icon,d.file_type,d.file_size,d.file_icon,d.downloads,d.author AS file_author,'.
				     'd.email_required,d.checked_out,d.checked_out_time,d.created,d.created_by,d.access,d.params,'.
				     'd.metadata,d.metakey,d.metadesc,d.hits,d.publish_up,d.publish_down,d.language,'.
				     'd.modified,d.modified_by'))
	    ->from($db->quoteName('#__okeydoc_document').' AS d')
	    ->where('d.id='.$pk);

      // Join on category table.
      $query->select('ca.title AS category_title, ca.alias AS category_alias, ca.access AS category_access')
	    ->join('LEFT', '#__categories AS ca on ca.id = d.catid');

      // Join on user table.
      $query->select('us.name AS author')
	    ->join('LEFT', '#__users AS us on us.id = d.created_by');

      // Join over the categories to get parent category titles
      $query->select('parent.title as parent_title, parent.id as parent_id, parent.path as parent_route, parent.alias as parent_alias')
	    ->join('LEFT', '#__categories as parent ON parent.id = ca.parent_id');

      // Filter by language
      if($this->getState('filter.language')) {
	$query->where('d.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')');
      }

      if((!$user->authorise('core.edit.state', 'com_okeydoc')) && (!$user->authorise('core.edit', 'com_okeydoc'))) {
	// Filter by start and end dates.
	$nullDate = $db->quote($db->getNullDate());
	$nowDate = $db->quote(JFactory::getDate()->toSql());
	$query->where('(d.publish_up = '.$nullDate.' OR d.publish_up <= '.$nowDate.')')
	      ->where('(d.publish_down = '.$nullDate.' OR d.publish_down >= '.$nowDate.')');
      }

      $db->setQuery($query);
      $data = $db->loadObject();

      if(is_null($data)) {
	JFactory::getApplication()->enqueueMessage(JText::_('COM_OKEYDOC_ERROR_DOCUMENT_NOT_FOUND'), 'error');
	return false;
      }

      // Convert parameter fields to objects.
      $registry = new JRegistry;
      $registry->loadString($data->params);

      $data->params = clone $this->getState('params');
      $data->params->merge($registry);

      $user = JFactory::getUser();
      // Technically guest could edit an article, but lets not check that to improve performance a little.
      if(!$user->get('guest')) {
	$userId = $user->get('id');
	$asset = 'com_okeydoc.document.'.$data->id;

	// Check general edit permission first.
	if($user->authorise('core.edit', $asset)) {
	  $data->params->set('access-edit', true);
	}

	// Now check if edit.own is available.
	elseif(!empty($userId) && $user->authorise('core.edit.own', $asset)) {
	  // Check for a valid user and that they are the owner.
	  if($userId == $data->created_by) {
	    $data->params->set('access-edit', true);
	  }
	}
      }

      // Get the tags
      $data->tags = new JHelperTags;
      $data->tags->getItemTags('com_okeydoc.document', $data->id);

      $this->_item[$pk] = $data;
    }

    return $this->_item[$pk];
  }


  /**
   * Increment the hit counter for the document.
   *
   * @param   integer  $pk  Optional primary key of the document to increment.
   *
   * @return  boolean  True if successful; false otherwise and internal error set.
   */
  public function hit($pk = 0)
  {
    $input = JFactory::getApplication()->input;
    $hitcount = $input->getInt('hitcount', 1);

    if($hitcount) {
      $pk = (!empty($pk)) ? $pk : (int) $this->getState('document.id');

      $table = JTable::getInstance('Document', 'OkeydocTable');
      $table->load($pk);
      $table->hit($pk);
    }

    return true;
  }
}

