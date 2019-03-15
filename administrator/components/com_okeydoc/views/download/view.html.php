<?php
/**
 * @package Okey DOC 2
 * @copyright Copyright (c) 2017 - 2018 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

JLoader::register('DownloadTrait', JPATH_ADMINISTRATOR.'/components/com_okeydoc/traits/download.php');

/**
 * HTML View class for the Okey DOC 2 component.
 */
class OkeydocViewDownload extends JViewLegacy
{
  use DownloadTrait;

  public function display($tpl = null)
  {
    //$this->loadTemplate('default:tmpl');
    //$this->setLayout('component');
    //$this->render();
    //$tpl = 'component';
    $this->downloadFile(true);
    //parent::display($tpl);
  }
}

