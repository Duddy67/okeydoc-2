<?php
/**
 * @package Okey DOC 2
 * @copyright Copyright (c) 2015 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

// No direct access
defined( '_JEXEC' ) or die; 
 

class OkeydocViewDocuments extends JViewLegacy
{
  protected $items;
  protected $state;
  protected $pagination;


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
    $this->items = $this->get('Items');
    $this->state = $this->get('State');
    $this->pagination = $this->get('Pagination');
    $this->filterForm = $this->get('FilterForm');
    $this->activeFilters = $this->get('ActiveFilters');

    // Checks for errors.
    if(count($errors = $this->get('Errors'))) {
      JFactory::getApplication()->enqueueMessage($errors, 'error');
      return false;
    }

    $this->addToolBar();
    $this->setDocument();
    $this->sidebar = JHtmlSidebar::render();

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
    // Displays the view title and the icon.
    JToolBarHelper::title(JText::_('COM_OKEYDOC_DOCUMENTS_TITLE'), 'stack');

    // Gets the allowed actions list
    $canDo = OkeydocHelper::getActions();
    $user = JFactory::getUser();

    // The user is allowed to create or is able to create in one of the component categories.
    if($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_okeydoc', 'core.create'))) > 0) {
      JToolBarHelper::addNew('document.add', 'JTOOLBAR_NEW');
    }

    if($canDo->get('core.edit') || $canDo->get('core.edit.own') || 
       (count($user->getAuthorisedCategories('com_okeydoc', 'core.edit'))) > 0 || 
       (count($user->getAuthorisedCategories('com_okeydoc', 'core.edit.own'))) > 0) {
      JToolBarHelper::editList('document.edit', 'JTOOLBAR_EDIT');
    }

    if($canDo->get('core.edit.state')) {
      JToolBarHelper::divider();
      JToolBarHelper::custom('documents.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
      JToolBarHelper::custom('documents.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
      JToolBarHelper::divider();
      JToolBarHelper::archiveList('documents.archive','JTOOLBAR_ARCHIVE');
      JToolBarHelper::custom('documents.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
      JToolBarHelper::trash('documents.trash','JTOOLBAR_TRASH');
    }

    // Checks for delete permission.
    if($canDo->get('core.delete') || count($user->getAuthorisedCategories('com_okeydoc', 'core.delete'))) {
      JToolBarHelper::divider();
      JToolBarHelper::deleteList('', 'documents.delete', 'JTOOLBAR_DELETE');
    }

    if($canDo->get('core.admin')) {
      JToolBarHelper::divider();
      JToolBarHelper::preferences('com_okeydoc', 550);
    }
  }


  /**
   * Includes possible css and Javascript files.
   *
   * @return  void
   */
  protected function setDocument() 
  {
    $doc = JFactory::getDocument();
    $doc->addStyleSheet(JURI::base().'components/com_okeydoc/okeydoc.css');
  }
}


