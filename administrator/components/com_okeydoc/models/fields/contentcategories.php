<?php
/**
 * @package Okey DOC 2
 * @copyright Copyright (c)2017 - 2018 Lucas Sanner
 * @license GNU General Public License version 3, or later
 * @contact lucas.sanner@gmail.com
 */

defined('_JEXEC') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');


//Field which allow the users to link a document with one or several content categories.
//The categories previously selected (if any) are displayed within the input field whereas
//the drop down list displays the rest of the selectable categories.


class JFormFieldContentcategories extends JFormFieldList
{
  protected $type = 'contentcategories';


  protected function getInput()
  {
    //Get the item id directly from the form loaded with data.
    $itemId = $this->form->getValue('id');

    if($itemId) {
      // Get the current user object.
      $user = JFactory::getUser();
      $groups = implode(',', $user->getAuthorisedViewLevels());

      //Get the content category ids previously selected.
      $db = JFactory::getDbo();
      $query = $db->getQuery(true);
      $query->select('id');
      $query->from('#__categories');
      $query->join('LEFT', '#__okeydoc_document_map ON id=item_id');
      $query->where('extension="com_content" AND item_type="category" AND document_id='.$itemId.' AND access IN ('.$groups.')');
      $db->setQuery($query);
      $selected = $db->loadColumn();

      //Assign the id array to the value attribute to get the selected categories
      //displayed in the input field.
      $this->value = $selected;
    }

    $input = parent::getInput();

    return $input;
  }


  protected function getOptions()
  {
    $options = array();
      
    // Get the current user object.
    $user = JFactory::getUser();
    $groups = implode(',', $user->getAuthorisedViewLevels());
    $userId = $user->get('id');

    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $query->select('id AS value, title AS text, created_user_id');
    $query->from('#__categories');
    $query->where('access IN ('.$groups.') AND extension = "com_content"');
    $db->setQuery($query);
    $categories = $db->loadObjectList();

    //Check for edit permissions.
    foreach($categories as $i => $category)
    {
      $accessEdit = false;
      $asset = 'com_content.category.'.$category->value; //Note: value = id.

      // Check general edit permission first.
      if($user->authorise('core.edit', $asset)) {
	$accessEdit = true;
      }
      // Now check if edit.own is available.
      elseif(!empty($userId) && $user->authorise('core.edit.own', $asset)) {
	// Check for a valid user and that they are the owner.
	if($userId == $category->created_user_id) {
	  $accessEdit = true;
	}
      }

      //Unauthorised categories are removed from the array.
      if(!$accessEdit) {
	unset($categories[$i]);
      }
    }

    // Merge any additional options in the XML definition.
    $options = array_merge(parent::getOptions(), $categories);

    return $options;
  }
}


