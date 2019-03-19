<?php
/**
 * @package Okey DOC 2
 * @copyright Copyright (c) 2015 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;


JLoader::register('DownloadTrait', JPATH_ADMINISTRATOR.'/components/com_okeydoc/traits/download.php');

/**
 * HTML View class for the Okey DOC 2 component.
 * Note: This view is only used to download a file by way of a url. No template is
 *       displayed.
 */
class OkeydocViewDownload extends JViewLegacy
{
  use DownloadTrait;

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
    $this->downloadFile(true);
  }
}

