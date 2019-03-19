<?php
/**
 * @package Okey DOC 2
 * @copyright Copyright (c) 2015 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

// No direct access to this file.
defined('_JEXEC') or die; 

JLoader::register('FilemanagerTrait', JPATH_ADMINISTRATOR.'/components/com_okeydoc/traits/filemanager.php');


class OkeydocModelDocument extends JModelAdmin
{
  use FilemanagerTrait;

  // Prefix used with the controller messages.
  protected $text_prefix = 'COM_OKEYDOC';

  /**
   * Returns a Table object, always creating it.
   *
   * @param   string  $type    The table type to instantiate
   * @param   string  $prefix  A prefix for the table class name. Optional.
   * @param   array   $config  Configuration array for model. Optional.
   *
   * @return  JTable    A database object
   */
  public function getTable($type = 'Document', $prefix = 'OkeydocTable', $config = array()) 
  {
    return JTable::getInstance($type, $prefix, $config);
  }


  /**
   * Method to get the record form.
   *
   * @param   array    $data      Data for the form.
   * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
   *
   * @return  JForm|boolean  A JForm object on success, false on failure
   *
   * @since   1.6
   */
  public function getForm($data = array(), $loadData = true) 
  {
    $form = $this->loadForm('com_okeydoc.document', 'document', array('control' => 'jform', 'load_data' => $loadData));

    if(empty($form)) {
      return false;
    }

    return $form;
  }


  /**
   * Method to get the data that should be injected in the form.
   *
   * @return  mixed  The data for the form.
   *
   * @since   1.6
   */
  protected function loadFormData() 
  {
    // Checks the session for previously entered form data.
    $data = JFactory::getApplication()->getUserState('com_okeydoc.edit.document.data', array());

    if(empty($data)) {
      $data = $this->getItem();
    }

    return $data;
  }


  /**
   * Method to get a single record.
   *
   * @param   integer  $pk  The id of the primary key.
   *
   * @return  mixed  Object on success, false on failure.
   */
  public function getItem($pk = null)
  {
    if($item = parent::getItem($pk)) {
      // Gets both intro_text and full_text together as documenttext
      $item->documenttext = trim($item->full_text) != '' ? $item->intro_text."<hr id=\"system-readmore\" />".$item->full_text : $item->intro_text;

      // Gets tags for this item.
      if(!empty($item->id)) {
	$item->tags = new JHelperTags;
	$item->tags->getTagIds($item->id, 'com_okeydoc.document');
      }
    }

    return $item;
  }


  /**
   * Prepare and sanitise the table data prior to saving.
   *
   * @param   JTable  $table  A JTable object.
   *
   * @return  void
   *
   * @since   1.6
   */
  protected function prepareTable($table)
  {
    // Sets the publish date to now
    if($table->published == 1 && (int)$table->publish_up == 0) {
      $table->publish_up = JFactory::getDate()->toSql();
    }

    if($table->published == 1 && intval($table->publish_down) == 0) {
      $table->publish_down = $this->getDbo()->getNullDate();
    }
  }


  /**
   * Loads ContentHelper for filters before validating data.
   *
   * @param   object  $form   The form to validate against.
   * @param   array   $data   The data to validate.
   * @param   string  $group  The name of the group(defaults to null).
   *
   * @return  mixed  Array of filtered data if valid, false otherwise.
   *
   * @since   1.1
   */
  public function validate($form, $data, $group = null)
  {
    if($data['id'] == 0 || $data['replace_file'] == 1) {
      // Checks the file is valid according to its location.
      if($data['file_location'] == 'url' && !$this->checkFileFromUrl($data['file_url'])) {
	return false;
      }
      elseif($data['file_location'] == 'server' && !$this->checkUploadedFile()) {
	return false;
      }
    }

    return parent::validate($form, $data, $group);
  }


  /**
   * Saves the manually set order of records.
   *
   * @param   array    $pks    An array of primary key ids.
   * @param   integer  $order  +1 or -1
   *
   * @return  mixed
   *
   * @since   12.2
   */
  public function saveorder($pks = null, $order = null)
  {
    // Hands over to the parent function.
    return parent::saveorder($pks, $order);
  }


  public function getExternalDocumentLinkings($pk = null)
  {
    $pk = (!empty($pk)) ? $pk : (int)$this->getState($this->getName().'.id');

    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $linkings = array('article' => array(), 'category' => array());

    $query->select('d.item_id, d.item_type, a.title AS article_title, c.title AS category_title')
          ->from('#__okeydoc_document_linking AS d')
          ->join('LEFT', '#__content AS a ON a.id=d.item_id')
          ->join('LEFT', '#__categories AS c ON c.id=d.item_id')
          ->where('d.doc_id='.(int)$pk.' AND d.linking_type="external"')
          ->order('d.item_type ASC');
    $db->setQuery($query);
    $results = $db->loadAssocList();

    foreach($results as $result) {
      if($result['item_type'] == 'article') {
	$linkings['article'][] = $result;
      }
      else {
	$linkings['category'][] = $result;
      }
    }

    return $linkings;
  }


  /**
   * Returns the archived files (ie: versions) of a given document.
   *
   * @param   integer  $pk    The primary key id of the document.
   *
   * @return  array           The archived files of the document. 
   */
  public function getArchives($pk = null)
  {
    $pk = (!empty($pk)) ? $pk : (int)$this->getState($this->getName().'.id');

    $db = JFactory::getDbo();
    $query = $db->getQuery(true);

    $query->select('file_size, file_type, file_icon, downloads, version, archived')
          ->from('#__okeydoc_archive')
          ->where('doc_id='.(int)$pk)
          ->order('version ASC');
    $db->setQuery($query);
    $archives = $db->loadAssocList();

    // Adds the conversion size data for files uploaded on the server.
    foreach($archives as $key => $archive) {
      if($archive['file_size'] != 'unknown') {
	$archives[$key]['conversion'] = $this->byteConverter($archive['file_size']);
      }
    }

    return $archives;
  }


  /**
   * Stores the newly archived files of a given document.
   *
   * @param   integer  $pk         The primary key id of the document.
   * @param   array    $archive    The data of the archived file.
   *
   * @return  void
   */
  public function archiveFile($pk, $archive)
  {
    // Gets the current date and time (UTC).
    $now = JFactory::getDate()->toSql();

    $db = JFactory::getDbo();
    $query = $db->getQuery(true);

    // Computes the file version number against the number of archives already present in
    // the table.
    $query->select('COUNT(*)')
          ->from('#__okeydoc_archive')
          ->where('doc_id='.(int)$pk);
    $db->setQuery($query);
    $version = (int)$db->loadResult() + 1;

    // Adds the new file archive.
    $columns = array('doc_id', 'archived', 'file_name', 'file_type', 'file_size', 'file_path', 'file_icon', 'version', 'downloads');
    $values = $pk.','.$db->Quote($now).','.$db->Quote($archive['file_name']).','.$db->Quote($archive['file_type']).','.
              $db->Quote($archive['file_size']).','.$db->Quote($archive['file_path']).','.$db->Quote($archive['file_icon']).
              ','.$version.','.$archive['downloads'];

    $query->clear();
    $query->insert('#__okeydoc_archive')
	  ->columns($columns)
	  ->values($values);
    $db->setQuery($query);
    $db->execute();
  }
}

