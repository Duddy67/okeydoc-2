<?php
/**
 * @package Okey DOC 2
 * @copyright Copyright (c) 2016 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die; //No direct access to this file.

/**
 * Provides some utility functions relating to the file downloading.
 *
 */

trait DownloadTrait
{
  public function downloadFile($isAdmin = false)
  {
    // Gets the data passed through the url query.
    $jinput = JFactory::getApplication()->input;
    // The document id.
    $id = $jinput->get('id', 0, 'uint');
    // The version number of the file archive to download.
    $version = $jinput->get('version', 0, 'uint');
    // The origin of the link which calls this script. 
    $link = $jinput->get('link', 'external', 'string');

    // Gets the user's access view.
    $user = JFactory::getUser();
    //file_put_contents('debog_file.txt', print_r($user, true));

    if($id) {
      // Retrieves some data from the document. 
      $db = JFactory::getDbo();
      $query = $db->getQuery(true);

      // Sets the query according to the type of file to download.

      if($version) { // A previous version of the current file.
	$query->select('a.file_path,a.file_name,a.file_type,a.file_size,a.archived,d.title,d.access')
	      ->from('#__okeydoc_archive AS a')
	      ->join('LEFT', '#__okeydoc_document AS d ON d.id=a.doc_id')
	      ->where('a.doc_id='.$id)
	      ->where('a.version='.$version);
      }
      else { // The current file.
	$query->select('published,publish_up,publish_down,access,title,file_path,file_name,file_type,file_size,file_location')
	      ->from('#__okeydoc_document')
	      ->where('id='.$id);
      }

      $db->setQuery($query);
      $document = $db->loadObject();

      // Checks the publication and publication dates (start and stop) of the document.
      // Note: Those checkings are not taken in account if the file is downloaded from 
      //       the admin part (back-end).

      // The document is unpublished.
      if(!$isAdmin && $document->published != 1) {
	echo '<div class="alert alert-no-items">'.JText::_('COM_OKEYDOC_DOCUMENT_UNAVAILABLE').'</div>';
	return;
      }

      // Gets the current date and time (UTC).
      $now = JFactory::getDate()->toSql();

      // A date to stop publishing is set.
      if(!$isAdmin && $document->publish_down != '0000-00-00 00:00:00') {
	// Publication date has expired.
	if(strcmp($now, $document->publish_down) > 0) {
	  echo '<div class="alert alert-no-items">'.JText::_('COM_OKEYDOC_DOCUMENT_EXPIRED').'</div>';
	  return;
	}
      }

      // A date to start publishing is set.
      if(!$isAdmin && $document->publish_up != '0000-00-00 00:00:00') {
	if(strcmp($now, $document->publish_up) < 0) {
	  // Publication date doesn't have started yet.
	  echo '<div class="alert alert-no-items">'.JText::_('COM_OKEYDOC_DOCUMENT_NOT_PUBLISHED_YET').'</div>';
	  return;
	}
      }

      // Check the permissions of the user for this document.

      $accessView = false;
      if(in_array($document->access, $user->getAuthorisedViewLevels())) {
	$accessView = true;
      }

      // The user has the required permission.
      if($accessView) {
	if($document->file_path) {
	  if(!$version) {
	    // Increment the download counter for this document.
	    // Note: Previous file versions are not incremented.
	    $query->clear();
	    $query->update('#__okeydoc_document')
		  ->set('downloads=downloads+1')
		  ->where('id='.$id);
	    $db->setQuery($query);
	    $db->execute();
	  }

	  if($document->file_location === 'url') {
	    // Redirects with the url to the file.
	    JFactory::getApplication()->redirect($document->file_path);
	    return;
	  }

	  // Builds the path to the file.
	  $download = JPATH_ROOT.'/'.$document->file_path.'/'.$document->file_name;

	  if(file_exists($download) === false) {
	    echo '<div class="alert alert-no-items">'.JText::_('COM_OKEYDOC_FILE_NOT_FOUND').'</div>';
	    return;
	  }

	  // Gets the file extension from the (real) file name.
	  preg_match('#\.([a-z0-9]{1,})$#', $document->file_name, $matches);
	  $extension = $matches[1];

	  // Builds the file name from the title.
	  $documentName = JFilterOutput::stringURLSafe($document->title);

	  if($version) {
	    // Appends the version number to the file name.
	    $documentName = $documentName.'-[version-'.$version.']';
	  }

	  $documentName = $documentName.'.'.$extension;

	  header('Content-Description: File Transfer');
	  header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
	  header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');   // Date in the past
	  header('Content-type: '.$document->file_type);
	  header('Content-Transfer-Encoding: binary');
	  header('Content-length: '.$document->file_size);
	  header("Content-Disposition: attachment; filename=\"".$documentName."\"");
	  ob_clean();
	  flush();
	  readfile($download);

	  exit;
	} 
	else { // The document url is empty.
	  echo '<div class="alert alert-no-items">'.JText::_('COM_OKEYDOC_WRONG_DOCUMENT_URL').'</div>';
	  return;
	}
      }
      else { // The user doesn't have the required permission.
	echo '<div class="alert alert-no-items">'.JText::_('COM_OKEYDOC_DOWNLOAD_UNAUTHORISED').'</div>';
	return;
      }
    }
    else { // The document id is unset.
      echo '<div class="alert alert-no-items">'.JText::_('COM_OKEYDOC_DOCUMENT_DOES_NOT_EXIST').'</div>';
    }

  }
}

