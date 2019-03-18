<?php
/**
 * @package Okey DOC 2 
 * @copyright Copyright (c) 2015 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die; // No direct access.


class OkeydocController extends JControllerLegacy
{
  public function display($cachable = false, $urlparams = false) 
  {
    // Displays the submenu.
    OkeydocHelper::addSubmenu($this->input->get('view', 'documents'));

    // Sets the default view.
    $this->input->set('view', $this->input->get('view', 'documents'));

    // Displays the view.
    parent::display();
  }
}


