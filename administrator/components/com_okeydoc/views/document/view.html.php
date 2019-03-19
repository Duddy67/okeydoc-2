<?php
/**
 * @package Okey DOC 2
 * @copyright Copyright (c) 2015 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

// No direct access
defined( '_JEXEC' ) or die; 
 

class OkeydocViewDocument extends JViewLegacy
{
  protected $item;
  protected $form;
  protected $state;
  protected $archives;
  protected $extDocLinkings;


  /**
   * Execute and display a template script.
   *
   * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
   *
   * @return  mixed  A string if successful, otherwise an Error object.
   *
   * @see     \JViewLegacy::loadTemplate()
   * @since   3.0
   */
  public function display($tpl = null)
  {
    $this->item = $this->get('Item');
    $this->form = $this->get('Form');
    $this->state = $this->get('State');
    $this->archives = $this->getModel()->getArchives();
    $this->extDocLinkings = $this->getModel()->getExternalDocumentLinkings();

    // Checks for errors.
    if(count($errors = $this->get('Errors'))) {
      JFactory::getApplication()->enqueueMessage($errors, 'error');
      return false;
    }

    $this->addToolBar();
    $this->setDocument();

    // Displays the template.
    parent::display($tpl);
  }


  /**
   * Add the page title and toolbar.
   *
   * @return  void
   *
   * @since   1.6
   */
  protected function addToolBar() 
  {
    // Makes main menu inactive.
    JFactory::getApplication()->input->set('hidemainmenu', true);

    $user = JFactory::getUser();
    $userId = $user->get('id');

    // Gets the allowed actions list
    $canDo = OkeydocHelper::getActions($this->state->get('filter.category_id'));
    $isNew = $this->item->id == 0;
    $checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $userId);

    // Displays the view title (according to the user action) and the icon.
    JToolBarHelper::title($isNew ? JText::_('COM_OKEYDOC_NEW_DOCUMENT') : JText::_('COM_OKEYDOC_EDIT_DOCUMENT'), 'pencil-2');

    if($isNew) {
      // Checks the "create" permission for the new records.
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
	// Note: Not yet possible as the linked file has to be downloaded again. 
	//JToolBarHelper::save2copy('document.save2copy');
      }
    }

    JToolBarHelper::cancel('document.cancel', 'JTOOLBAR_CANCEL');
  }


  /**
   * Includes possible css and Javascript files.
   *
   * @return  void
   */
  protected function setDocument() 
  {
    $doc = JFactory::getDocument();
    $doc->addStyleSheet(JURI::base().'components/com_okeydoc/css/okeydoc.css');
  }
}

