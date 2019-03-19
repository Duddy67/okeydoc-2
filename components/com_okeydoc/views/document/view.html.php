<?php
/**
 * @package Okey DOC 2
 * @copyright Copyright (c) 2015 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

JLoader::register('FilemanagerTrait', JPATH_ADMINISTRATOR.'/components/com_okeydoc/traits/filemanager.php');


/**
 * HTML View class for the Okey DOC 2 component.
 */
class OkeydocViewDocument extends JViewLegacy
{
  use FilemanagerTrait;

  protected $state;
  protected $item;
  protected $nowDate;
  protected $user;
  protected $uri;


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
    // Initialise variables
    $this->state = $this->get('State');
    $this->item = $this->get('Item');
    $user = JFactory::getUser();

    // Check for errors.
    if(count($errors = $this->get('Errors'))) {
      JFactory::getApplication()->enqueueMessage($errors, 'error');
      return false;
    }

    // Compute the category slug.
    $this->item->catslug = $this->item->category_alias ? ($this->item->catid.':'.$this->item->category_alias) : $this->item->catid;
    //Get the possible extra class name.
    $this->pageclass_sfx = htmlspecialchars($this->item->params->get('pageclass_sfx'));

    // Gets the user object and the current url, (needed in the document edit layout).
    $this->user = JFactory::getUser();
    $this->uri = JUri::getInstance();

    // Increments the hits for this document.
    $model = $this->getModel();
    $model->hit();

    $this->nowDate = JFactory::getDate()->toSql();

    if($this->item->file_size != 'unknown') {
      $this->item->conversion = $this->byteConverter($this->item->file_size);
    }

    $this->setDocument();

    parent::display($tpl);
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

