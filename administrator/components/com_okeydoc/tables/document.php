<?php
/**
 * @package Okey DOC 2
 * @copyright Copyright (c) 2015 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
 
// Imports Joomla's table and filesystem libraries.
jimport('joomla.database.table');
jimport('joomla.filesystem.file');
 
JLoader::register('FilemanagerTrait', JPATH_ADMINISTRATOR.'/components/com_okeydoc/traits/filemanager.php');
use Joomla\Registry\Registry;


/**
 * Document table class
 */
class OkeydocTableDocument extends JTable
{
  use FilemanagerTrait;

  public $jform = null;

  /**
   * Constructor
   *
   * @param object Database connector object
   */
  function __construct(&$db) 
  {
    parent::__construct('#__okeydoc_document', 'id', $db);
    // Needed to use the Joomla tagging system with the document items.
    JTableObserverTags::createObserver($this, array('typeAlias' => 'com_okeydoc.document'));
    // Gets the edit form.
    $this->jform = JFactory::getApplication()->input->post->get('jform', array(), 'array');
  }


  /**
   * Overloaded bind function to pre-process the params.
   *
   * @param   mixed  $array   An associative array or object to bind to the JTable instance.
   * @param   mixed  $ignore  An optional array or space separated list of properties to ignore while binding.
   *
   * @return  boolean  True on success.
   *
   * @see     JTable:bind
   * @since   1.5
   */
  public function bind($array, $ignore = '')
  {
    if(isset($array['params']) && is_array($array['params'])) {
      // Converts the params field to a string.
      $registry = new JRegistry;
      $registry->loadArray($array['params']);
      $array['params'] = (string) $registry;
    }

    if(isset($array['metadata']) && is_array($array['metadata'])) {
      $registry = new JRegistry;
      $registry->loadArray($array['metadata']);
      $array['metadata'] = (string) $registry;
    }

    // Searches for the {readmore} tag and split the text up accordingly.
    if(isset($array['documenttext'])) {
      $pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
      $tagPos = preg_match($pattern, $array['documenttext']);

      if($tagPos == 0) {
	$this->intro_text = $array['documenttext'];
	$this->full_text = '';
      }
      else {
	// Splits documenttext field data in 2 parts with the "readmore" tag as a separator.
	// Note: The "readmore" tag is not included in either part.
	list($this->intro_text, $this->full_text) = preg_split($pattern, $array['documenttext'], 2);
      }
    }

    // Bind the rules. 
    if(isset($array['rules']) && is_array($array['rules'])) {
      $rules = new JAccessRules($array['rules']);
      $this->setRules($rules);
    }

    return parent::bind($array, $ignore);
  }


  /**
   * Overrides JTable::store to set modified data and user id.
   *
   * @param   boolean  $updateNulls  True to update fields even if they are null.
   *
   * @return  boolean  True on success.
   *
   * @since   11.1
   */
  public function store($updateNulls = false)
  {
    // Gets the current date and time (UTC).
    $now = JFactory::getDate()->toSql();
    $user = JFactory::getUser();

    if($this->id) { // Existing item
      $this->modified = $now;
      $this->modified_by = $user->get('id');
    }
    else {
      // New document. A document created and created_by field can be set by the user,
      // so we don't touch either of these if they are set.
      if(!(int)$this->created) {
	$this->created = $now;
      }

      if(empty($this->created_by)) {
	$this->created_by = $user->get('id');
      }
    }

    // Sets the alias of the document.
    
    // Creates a sanitized alias, (see stringURLSafe function for details).
    $this->alias = JFilterOutput::stringURLSafe($this->alias);
    // In case no alias has been defined, create a sanitized alias from the title field.
    if(empty($this->alias)) {
      $this->alias = JFilterOutput::stringURLSafe($this->title);
    }

    // Verify that the alias is unique
    $table = JTable::getInstance('Document', 'OkeydocTable', array('dbo', $this->getDbo()));

    if($table->load(array('alias' => $this->alias, 'catid' => $this->catid)) && ($table->id != $this->id || $this->id == 0)) {
      $this->setError(JText::_('COM_OKEYDOC_DATABASE_ERROR_DOCUMENT_UNIQUE_ALIAS'));
      return false;
    }

    if($this->access != 1) {
      // This feature is only available with a Public access.
      $this->email_required = 0;
    }

    // In case of archiving the data of the current file has to be saved before being updated.
    if($this->jform['archive_file'] == 1) {
      $archive = array();
      $archive['file_name'] = $this->file_name;
      $archive['file_type'] = $this->file_type;
      $archive['file_size'] = $this->file_size;
      $archive['file_path'] = $this->file_path;
      $archive['file_icon'] = $this->file_icon;
      $archive['downloads'] = $this->downloads;
    }

    // Now that the item data is ok let's move to the file data.
    // N.B: The file validity has been already checked in the model. 
    if(!$this->setFileData()) {
      return false;
    }

    if($this->jform['archive_file'] == 1) {
      $model = JModelLegacy::getInstance('document', 'OkeydocModel');
      // Saves the data of the previous file version.
      $model->archiveFile($this->id, $archive);
      // Resets the downloads for the brand new file version.
      $this->downloads = 0;
    }

    return parent::store($updateNulls);
  }


  /**
   * Sets the file data according to the file location. 
   * Uploads/removes files if necessary.
   *
   * @return boolean  True if everything went fine, false otherwise. 
   */
  protected function setFileData()
  {
    // Gets the edit form.
    $jform = JFactory::getApplication()->input->post->get('jform', array(), 'array');

    // In case of replacement without file archiving, the current file has to be deleted from the server.
    if($jform['replace_file'] == 1 && $jform['current_file_location'] == 'server' && !$jform['archive_file']) {
      // Warning: Don't ever use the JFile delete function cause if a problem occurs with
      // the file, the returned value is undefined (nor boolean or whatever). 
      // Sticking to the unlink() PHP function which is safer.
      if(!unlink(JPATH_ROOT.'/media/com_okeydoc/files/'.$this->file_name)) {
	$data->setError(JText::sprintf('COM_OKEYDOC_FILE_COULD_NOT_BE_DELETED', $this->file_name));
	return false;
      }

      //TODO: Determine whether the download variable has to be reset.
    }

    if((!$this->id || $jform['replace_file'] == 1) && $jform['file_location'] == 'server') {
      // Uploads the file on the server.
      $file = $this->uploadFile();

      // Checks for error.
      if(!empty($file['error'])) {
	$this->setError(JText::_($file['error']));
	return false;
      }

      // Sets the file variables.
      $this->file_name = $file['file_name'];
      $this->file_type = $file['file_type'];
      $this->file_size = $file['file_size'];
      $this->file_path = $file['file_path'];
      $this->file_icon = $file['file_icon'];
    }
    elseif((!$this->id || $jform['replace_file'] == 1) && $jform['file_location'] == 'url') {
      // Retrieves the file name from its URL
      $fileName = basename($jform['file_url']);
      // Gets the file extension and convert it to lowercase.
      $ext = strtolower(JFile::getExt($fileName));
      $fileIcon = 'generic.gif';
      $fileSettings = $this->getFileSettings();

      if(in_array($ext, $fileSettings['extensions_list'])) {
	$fileIcon = $ext.'.gif';
      }

      // Sets the file variables.
      $this->file_name = $fileName;
      //TODO: Ensure that "application" is always used regardless of the extension.
      $this->file_type = 'application/'.$ext;
      // As the file is not on the server its size is unknown.
      $this->file_size = 'unknown';
      $this->file_path = $jform['file_url'];
      $this->file_icon = $fileIcon;
    }

    return true;
  }


  /**
   * Method to return the title to use for the asset table.
   *
   * @return  string
   *
   * @since   11.1
   */
  protected function _getAssetTitle()
  {
    return $this->title;
  }


  /**
   * Method to compute the default name of the asset.
   * The default name is in the form table_name.id
   * where id is the value of the primary key of the table.
   *
   * @return  string
   *
   * @since   11.1
   */
  protected function _getAssetName()
  {
    $k = $this->_tbl_key;
    return 'com_okeydoc.document.'.(int) $this->$k;
  }


  /**
   * We provide our global ACL as parent
   * @see JTable::_getAssetParentId()
   */

  //Note: The component categories ACL override the items ACL, (whenever the ACL of a
  //      category is modified, changes are spread into the items ACL).
  //      This is the default com_content behavior. see: libraries/legacy/table/content.php
  protected function _getAssetParentId(JTable $table = null, $id = null)
  {
    $assetId = null;

    // This is a document under a category.
    if($this->catid) {
      // Build the query to get the asset id for the parent category.
      $query = $this->_db->getQuery(true)
              ->select($this->_db->quoteName('asset_id'))
              ->from($this->_db->quoteName('#__categories'))
              ->where($this->_db->quoteName('id').' = '.(int) $this->catid);

      // Get the asset id from the database.
      $this->_db->setQuery($query);

      if($result = $this->_db->loadResult()) {
        $assetId = (int) $result;
      }
    }

    // Return the asset id.
    if($assetId) {
      return $assetId;
    }
    else {
      return parent::_getAssetParentId($table, $id);
    }
  }
}

