<?php
/**
 * @package Okey DOC 2
 * @copyright Copyright (c) 2015 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

// No direct access to this file.
defined('_JEXEC') or die; 


class OkeydocHelper
{
  /**
   * Creates the tabs bar ($viewName = name of the active view).
   *
   * @param   string  $viewName  The name of the view to display.
   *
   * @return  void
   *
   */
  public static function addSubmenu($viewName)
  {
    JHtmlSidebar::addEntry(JText::_('COM_OKEYDOC_SUBMENU_DOCUMENTS'),
				      'index.php?option=com_okeydoc&view=documents', $viewName == 'documents');

    JHtmlSidebar::addEntry(JText::_('COM_OKEYDOC_SUBMENU_CATEGORIES'),
				      'index.php?option=com_categories&extension=com_okeydoc', $viewName == 'categories');

    if($viewName == 'categories') {
      $document = JFactory::getDocument();
      $document->setTitle(JText::_('COM_OKEYDOC_ADMINISTRATION_CATEGORIES'));
    }
  }


  /**
   * Gets the list of the allowed actions for the user.
   *
   * @param   array    $catIds    The category ids to check against.
   *
   * @return  JObject             The allowed actions for the current user.
   *
   */
  public static function getActions($catIds = array())
  {
    $user = JFactory::getUser();
    $result = new JObject;

    $actions = array('core.admin', 'core.manage', 'core.create', 'core.edit',
		     'core.edit.own', 'core.edit.state', 'core.delete');

    // Gets from the core the user's permission for each action.
    foreach($actions as $action) {
      // Checks permissions against the component. 
      if(empty($catIds)) { 
	$result->set($action, $user->authorise($action, 'com_okeydoc'));
      }
      else {
	// Checks permissions against the component categories.
	foreach($catIds as $catId) {
	  if($user->authorise($action, 'com_okeydoc.category.'.$catId)) {
	    $result->set($action, $user->authorise($action, 'com_okeydoc.category.'.$catId));
	    break;
	  }

	  $result->set($action, $user->authorise($action, 'com_okeydoc.category.'.$catId));
	}
      }
    }

    return $result;
  }


  /**
   * Builds the user list for the filter.
   *
   * @param   string   $itemName    The name of the item to check the users against.
   *
   * @return  object                The list of the users.
   *
   */
  public static function getUsers($itemName)
  {
    // Create a new query object.
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $query->select('u.id AS value, u.name AS text');

    $query->from('#__users AS u');
    // Gets only the names of users who have created items, this avoids to
    // display all of the users in the drop down list.
    $query->join('INNER', '#__okeydoc_'.$itemName.' AS i ON i.created_by = u.id');
    $query->group('u.id');
    $query->order('u.name');
    $db->setQuery($query);

    return $db->loadObjectList();
  }
}

