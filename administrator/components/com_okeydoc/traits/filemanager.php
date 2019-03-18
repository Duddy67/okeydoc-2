<?php
/**
 * @package Okey DOC 2
 * @copyright Copyright (c) 2015 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die; //No direct access to this file.

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');


/**
 * Provides some functions to manage files.
 *
 */

trait FilemanagerTrait
{
  /**
   * Checks that the file pointed by the given url is valid.
   *
   * @param   string  $fileUrl   The file url to check.
   *
   * @return  boolean            True if the file is valid, false otherwise.
   *
   */
  public function checkFileFromUrl($fileUrl)
  {
    // Checks for a valid url and protocole.
    if(!empty($fileUrl) && (!preg_match('#^https?:#', $fileUrl) || !filter_var($fileUrl, FILTER_VALIDATE_URL))) {
      JFactory::getApplication()->enqueueMessage(JText::_('COM_OKEYDOC_WARNING_INVALID_FILE_URL'), 'warning');
      return false;
    }

    // Retrieves the file name from its URL.
    $fileName = basename($fileUrl);

    $fileSettings = $this->getFileSettings();

    // Gets the file extension and convert it to lowercase.
    $ext = strtolower(JFile::getExt($fileName));

    if(empty($ext)) {
      JFactory::getApplication()->enqueueMessage(JText::_('COM_OKEYDOC_NO_EXTENSION'), 'warning');
      return false;
    }

    // Checks if the extension is allowed.
    if(!in_array($ext, $fileSettings['allowed_extensions']) && !$fileSettings['all_files']) {
      JFactory::getApplication()->enqueueMessage(JText::_('COM_OKEYDOC_EXTENSION_NOT_ALLOWED'), 'warning');
      return false;
    }

    // Note: Since the file is not on the current server there is no way to check its size.

    return true;
  }


  /**
   * Checks that the uploaded file is valid.
   *
   * @return  boolean  True if the uploaded file is valid, false otherwise.
   *
   */
  public function checkUploadedFile()
  {
    // Gets the files array.
    $files = JFactory::getApplication()->input->files->get('jform');

    if($files['uploaded_file']['error'] > 0) {
      JFactory::getApplication()->enqueueMessage(JText::_('COM_OKEYDOC_FILES_ERROR_'.$files['uploaded_file']['error']), 'warning');
      return false;
    }

    $fileSettings = $this->getFileSettings();

    // Gets the file extension and convert it to lowercase.
    $ext = strtolower(JFile::getExt($files['uploaded_file']['name']));

    if(empty($ext)) {
      JFactory::getApplication()->enqueueMessage(JText::_('COM_OKEYDOC_NO_EXTENSION'), 'warning');
      return false;
    }

    // Checks if the extension is allowed.
    if(!in_array($ext, $fileSettings['allowed_extensions']) && !$fileSettings['all_files']) {
      JFactory::getApplication()->enqueueMessage(JText::_('COM_OKEYDOC_EXTENSION_NOT_ALLOWED'), 'warning');
      return false;
    }

    // Checks the size of the file.
    if($files['uploaded_file']['size'] > $fileSettings['max_file_size']) {
      JFactory::getApplication()->enqueueMessage(JText::_('COM_OKEYDOC_FILE_SIZE_TOO_LARGE'), 'warning');
      return false;
    }

    return true;
  }


  /**
   * Loads a file on the server and return an array filled with the file data. 
   *
   * @return  array  An array filled with the file data.  
   *
   */
  public function uploadFile()
  {
    // Array to store the file data. Set up an error index for a possible error message.
    $file = array('error' => '');
    // Gets the files array.
    $files = JFactory::getApplication()->input->files->get('jform');

    // Checks if the file exists and if no error occurs.
    if($files['uploaded_file']['error'] == 0) {
      // Gets the file extension and convert it to lowercase.
      $ext = strtolower(JFile::getExt($files['uploaded_file']['name']));

      $db = JFactory::getDbo();
      $query = $db->getQuery(true);

      $count = 1;
      while($count > 0) {
	// Creates an unique id for this file.
	$fileName = uniqid();
	$fileName = $fileName.'.'.$ext;

	// To ensure it is unique check against the database.
	// If the id is not unique the loop goes on and a new id is generated.
	$query->clear();
	$query->select('COUNT(*)')
	      ->from('#__okeydoc_document')
	      ->where('file_name='.$db->Quote($fileName));
	$db->setQuery($query);
	$count = (int)$db->loadResult();
      }

      // Note: So far the document root directory is unchangeable but who knows in a futur version..
      $docRootDir = 'media/com_okeydoc/files';

      // Create a table containing all data about the file.
      $file['file_name'] = $fileName;
      $file['file_type'] = $files['uploaded_file']['type'];
      $file['file_size'] = $files['uploaded_file']['size'];
      $file['file_path'] = $docRootDir;

      // To obtain the appropriate icon file name, we get the file extension then we concatenate it with .gif.
      // If the extension doesn't have any appropriate extension icon, we display the generic icon.
      $fileSettings = $this->getFileSettings();

      if(!in_array($ext, $fileSettings['extensions_list'])) {
	$file['file_icon'] = 'generic.gif';
      }
      else {
	$file['file_icon'] = $ext.'.gif';
      }

      // Moves the file on the server.
      if(!JFile::upload($files['uploaded_file']['tmp_name'], JPATH_ROOT.'/'.$docRootDir.'/'.$file['file_name'])) {
	$file['error'] = 'COM_OKEYDOC_FILE_TRANSFER_ERROR';
	return file;
      }

      // File transfert has been successful.
      return $file;
    }
    // The upload of the file has failed.
    else { 
      // Returns the error which has occured.
      switch ($files['uploaded_file']['error']) { 
        case 1:
	  $file['error'] = 'COM_OKEYDOC_FILES_ERROR_1';
	  break;
	case 2:
	  $file['error'] = 'COM_OKEYDOC_FILES_ERROR_2';
	  break;
	case 3:
	  $file['error'] = 'COM_OKEYDOC_FILES_ERROR_3';
	  break;
	case 4:
	  $file['error'] = 'COM_OKEYDOC_FILES_ERROR_4';
	  break;
      }

      return $file;
    }
  }


  /**
   * Returns the settings of the component relating to files.
   *
   * @return  array    The settings of the component.
   *
   */
  public function getFileSettings()
  {
    $fileSettings = array();
    // Gets the component parameters:
    $params = JComponentHelper::getParams('com_okeydoc');
    //- The allowed extensions table
    $fileSettings['allowed_extensions'] = explode(';', $params->get('allowed_extensions'));
    // - The available extension icons table
    $fileSettings['extensions_list'] = explode(';', $params->get('extensions_list'));
    //- Allow or not all types of file. 
    $fileSettings['all_files'] = $params->get('all_files');
    //- The authorised file size (in megabyte) for upload. 
    $maxFileSize = $params->get('max_file_size');
    // Converts in byte. 
    $fileSettings['max_file_size'] = $maxFileSize * 1048576;

    return $fileSettings;
  }


  /**
   * Returns the number of files associated to a given category
   *
   * @param   integer  $catid     The id of the category.
   *
   * @return  integer             The number of files in the given category.
   *
   */
  public function getNumberOfFiles($catid)
  {
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $query->select('COUNT(*)')
	  ->from('#__okeydoc_document')
	  ->where('catid='.(int)$catid);
    $db->setQuery($query);

    return $db->loadResult();
  }


  /**
   * Converts the number of bytes to kilo or mega bytes.
   *
   * @param   integer  $fileUrl   The number of bytes to convert.
   *
   * @return  array               The outcomes of the conversion.
   *
   */
  public function byteConverter($nbBytes)
  {
    $conversion = array();
    // Converts to kilobyte.
    if($nbBytes > 1023 && $nbBytes < 1048576) {  
      $result = $nbBytes / 1024;
      $conversion['result'] = round($result, 2);
      $conversion['multiple'] = 'KILOBYTE';
    }
    // Converts to megabyte.
    elseif($nbBytes > 1048575) { 
      $result = $nbBytes / 1048576;
      $conversion['result'] = round($result, 2);
      $conversion['multiple'] = 'MEGABYTE';
    }
    // No convertion.
    else { 
      $conversion['result'] = $nbBytes;
      $conversion['multiple'] = 'BYTE';
    }

    return $conversion;
  }
}

