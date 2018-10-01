<?php
/**
 * @package Okey DOC 2
 * @copyright Copyright (c) 2017 - 2018 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */


defined( '_JEXEC' ) or die; // No direct access
 

jimport( 'joomla.application.component.view');
require_once JPATH_COMPONENT.'/helpers/okeydoc.php';
 

class OkeydocViewDocument extends JViewLegacy
{
  protected $item;
  protected $form;
  protected $state;

  //Display the view.
  public function display($tpl = null)
  {
    $this->item = $this->get('Item');
    $this->form = $this->get('Form');
    $this->state = $this->get('State');

    //Check for errors.
    if(count($errors = $this->get('Errors'))) {
      JFactory::getApplication()->enqueueMessage($errors, 'error');
      return false;
    }

    //Display the toolbar.
    $this->addToolBar();

    $this->setDocument();

    //Display the template.
    parent::display($tpl);
  }


  protected function addToolBar() 
  {
    //Make main menu inactive.
    JFactory::getApplication()->input->set('hidemainmenu', true);

    $user = JFactory::getUser();
    $userId = $user->get('id');

    //Get the allowed actions list
    $canDo = OkeydocHelper::getActions($this->state->get('filter.category_id'));
    $isNew = $this->item->id == 0;
    $checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $userId);

    //Display the view title (according to the user action) and the icon.
    JToolBarHelper::title($isNew ? JText::_('COM_OKEYDOC_NEW_DOCUMENT') : JText::_('COM_OKEYDOC_EDIT_DOCUMENT'), 'pencil-2');

    if($isNew) {
      //Check the "create" permission for the new records.
      if($canDo->get('core.create')) {
	JToolBarHelper::apply('document.apply', 'JTOOLBAR_APPLY');
	JToolBarHelper::save('document.save', 'JTOOLBAR_SAVE');
	JToolBarHelper::custom('document.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
      }
    }
    else {
      // Can't save the record if it's checked out.
      if(!$checkedOut) {
	// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
	if($canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $userId)) {
	  // We can save the new record
	  JToolBarHelper::apply('document.apply', 'JTOOLBAR_APPLY');
	  JToolBarHelper::save('document.save', 'JTOOLBAR_SAVE');

	  // We can save this record, but check the create permission to see if we can return to make a new one.
	  if($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_okeydoc', 'core.create'))) > 0) {
	    JToolBarHelper::custom('document.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
	  }
	}
      }

      // If checked out, we can still save
      if($canDo->get('core.create')) {
	//Note: Not yet possible as the linked file has to be downloaded again. 
	//JToolBarHelper::save2copy('document.save2copy');
      }
    }

    JToolBarHelper::cancel('document.cancel', 'JTOOLBAR_CANCEL');
  }


  protected function setDocument() 
  {
    //Include a css file (if needed).
    //$doc = JFactory::getDocument();
    //$doc->addStyleSheet(JURI::base().'components/com_okeydoc/okeydoc.css');
  }
}



