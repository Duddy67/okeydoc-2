<?php
/**
 * @package Okey DOC 2 
 * @copyright Copyright (c) 2017 - 2018 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */


defined('_JEXEC') or die; // No direct access.

jimport('joomla.application.component.controller');


class OkeydocController extends JControllerLegacy
{
  public function display($cachable = false, $urlparams = false) 
  {
    require_once JPATH_COMPONENT.'/helpers/okeydoc.php';

    //Display the submenu.
    OkeydocHelper::addSubmenu($this->input->get('view', 'documents'));

    //Set the default view.
    $this->input->set('view', $this->input->get('view', 'documents'));

    //Display the view.
    parent::display();
  }
}


