<?php
/**
 * @package Okey DOC 2
 * @copyright Copyright (c)2015 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;


/**
 *Field which allow the users to link a document with one or several content categories.
 *The categories previously selected (if any) are displayed within the input field whereas
 *the drop down list displays the rest of the selectable categories.
 */
class JFormFieldContentcategories extends JFormFieldList
{
  protected $type = 'contentcategories';


  /**
   * Method to get the field input for a category field.
   *
   * @return  string  The field input.
   */
  protected function getInput()
  {
    // Gets the item id directly from the form loaded with data.
    $itemId = $this->form->getValue('id');

    if($itemId) {
      // Gets the current user object.
      $user = JFactory::getUser();
      $groups = implode(',', $user->getAuthorisedViewLevels());

      // Gets the content category ids previously selected.
      $db = JFactory::getDbo();
      $query = $db->getQuery(true);
      $query->select('id')
	    ->from('#__categories')
	    ->join('LEFT', '#__okeydoc_document_linking ON id=item_id')
	    ->where('extension="com_content" AND item_type="category" AND linking_type="internal" AND doc_id='.$itemId)
	    ->where('access IN ('.$groups.')');
      $db->setQuery($query);
      $selected = $db->loadColumn();

      // Assign the id array to the value attribute to get the selected categories
      // displayed in the input field.
      $this->value = $selected;
    }

    $input = parent::getInput();

    return $input;
  }


  /**
   * Method to get a list of categories.
   *
   * @return  array  The field option objects.
   */
  protected function getOptions()
  {
    $options = array();
      
    // Gets the current user object.
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

    // Checks for edit permissions.
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

      // Unauthorised categories are removed from the array.
      if(!$accessEdit) {
	unset($categories[$i]);
      }
    }

    // Merge any additional options in the XML definition.
    $options = array_merge(parent::getOptions(), $categories);

    return $options;
  }
}

