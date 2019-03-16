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


//Field which allow the users to link a document with one or several articles.
//The articles previously selected (if any) are displayed within the input field whereas
//the drop down list displays the rest of the selectable articles.

class JFormFieldArticles extends JFormFieldList
{

  protected $type = 'articles';

  protected function getInput()
  {
    //Get the item id directly from the form loaded with data.
    $itemId = $this->form->getValue('id');

    if($itemId) {
      // Get the current user object.
      $user = JFactory::getUser();
      $groups = implode(',', $user->getAuthorisedViewLevels());

      //Get the article ids previously selected.
      $db = JFactory::getDbo();
      $query = $db->getQuery(true);
      $query->select('id')
	    ->from('#__content')
	    ->join('LEFT', '#__okeydoc_document_linking ON id=item_id')
	    ->where('item_type="article" AND doc_id='.$itemId.' AND linking_type="internal"')
	    ->where('access IN ('.$groups.')');
      $db->setQuery($query);
      $selected = $db->loadColumn();

      //Assign the id array to the value attribute to get the selected articles
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
    $query->select('id AS value, title AS text, created_by');
    $query->from('#__content');
    $query->where('access IN ('.$groups.')');
    $db->setQuery($query);
    $articles = $db->loadObjectList();

    //Check for edit permissions.
    foreach($articles as $i => $article)
    {
      $accessEdit = false;
      $asset = 'com_content.article.'.$article->value; //Note: value = id.

      // Check general edit permission first.
      if($user->authorise('core.edit', $asset)) {
	$accessEdit = true;
      }
      // Now check if edit.own is available.
      elseif(!empty($userId) && $user->authorise('core.edit.own', $asset)) {
	// Check for a valid user and that they are the owner.
	if($userId == $article->created_by) {
	  $accessEdit = true;
	}
      }

      //Unauthorised articles are removed from the array.
      if(!$accessEdit) {
	unset($articles[$i]);
      }
    }

    // Merge any additional options in the XML definition.
    $options = array_merge(parent::getOptions(), $articles);

    return $options;
  }
}



