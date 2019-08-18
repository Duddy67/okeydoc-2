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
 * Note: This view is only used to download a file by way of a url. No template is
         displayed.
 */
class OkeydocViewDownload extends JViewLegacy
{
  use DownloadTrait;

  public $item;

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
    $jinput = JFactory::getApplication()->input;
    $docId = (int)$jinput->get('id', 0, 'integer');

    // The document link comes from an external component and an email is required to download the document.
    if($jinput->get('link', '', 'string') == 'external' && $this->isEmailRequired($docId)) {
      $this->item = new JObject;
      $this->item->view = 'external';
      $this->item->email_required = true;
      $this->item->link = JURI::base().'index.php?option=com_okeydoc&view=download&tmpl=component&id='.$docId;

      parent::display($tpl);
    }
    else {
      // Treats the file downloading.
      $this->downloadFile();
    }
  }
}

