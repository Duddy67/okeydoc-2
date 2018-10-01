<?php
/**
 * @package Okey DOC 2.x
 * @copyright Copyright (c) 2017 - 2018 Lucas Sanner
 * @license GNU General Public License version 3, or later
 * @contact lucas.sanner@gmail.com
 */


//Since this file is called directly and it doesn't belong to any component, 
//module or plugin, we need first to initialize the Joomla framework in order to use 
//the Joomla API methods.
 
//Initialize the Joomla framework
define('_JEXEC', 1);
//Note: Utterly useless here but it fits the JED expectations.
defined('_JEXEC') or die;
//First we get the number of letters we want to substract from the path.
$length = strlen('media');
//Turn the length number into a negative value.
$length = $length - ($length * 2);
//Builds the path to the website root.
define('JPATH_BASE', substr(dirname(__DIR__), 0, $length));

//Get the required files
require_once (JPATH_BASE.'/includes/defines.php');
require_once (JPATH_BASE.'/includes/framework.php');
//Path to the factory.php file before the 3.8.0 Joomla's version.
$factoryFilePath = '/libraries/joomla/factory.php';
$jversion = new JVersion();
//Check Joomla's version.
if($jversion->getShortVersion() >= '3.8.0') {
  //Set to the file new location.
  $factoryFilePath = '/libraries/src/Factory.php';
}
//We need to use Joomla's database class 
require_once (JPATH_BASE.$factoryFilePath);
//Create the application
$mainframe = JFactory::getApplication('site');

//Get the id number passed through the url.
$jinput = JFactory::getApplication()->input;
$id = $jinput->get('id', 0, 'uint');

if($id) {
  //Retrieve some data from the document. 
  $db = JFactory::getDbo();
  $query = $db->getQuery(true);
  $query->select('published,publish_up,publish_down,access,alias,file_path,file_name,file_type,file_size,file_location')
	->from('#__okeydoc_document')
	->where('id='.$id);
  $db->setQuery($query);
  $document = $db->loadObject();

  //The document is unpublished.
  if($document->published != 1) {
    echo 'This document is currently no available.';
    return;
  }

  //Check the publication date (start and stop) of the document.

  //Get current date and time (equal to NOW() in SQL).
  jimport('joomla.utilities.date');
  $date = new JDate();
  $now = $date->toSQL();

  //A date to stop publishing is set.
  if($document->publish_down != '0000-00-00 00:00:00') {
    //Publication date has expired.
    if(strcmp($now, $document->publish_down) > 0) {
      echo 'The publication date of this document has expired.';
      return;
    }
  }

  //A date to start publishing is set.
  if($document->publish_up != '0000-00-00 00:00:00') {
    if(strcmp($now, $document->publish_up) < 0) {
      //Publication date doesn't have started yet.
      echo 'The publication date of this document doesn\'t have started yet.';
      return;
    }
  }

  //Check the permissions of the user for this document.

  //Get the user's access view.
  $user = JFactory::getUser();

  $accessView = false;
  if(in_array($document->access, $user->getAuthorisedViewLevels())) {
    $accessView = true;
  }

  //The user has the required permission.
  if($accessView) {
    if($document->file_path) {
      //Increment the download counter for this document.
      $query->clear();
      $query->update('#__okeydoc_document')
	    ->set('downloads=downloads+1')
	    ->where('id='.$id);
      $db->setQuery($query);
      $result = $db->query();

      if($document->file_location === 'url') {
	//file_path field contains the whole url to the file.
	$mainframe->redirect($document->file_path);
	return;
      }

      //$component = JComponentHelper::getComponent('com_okeydoc');
      //Build the path to the file.
      $download = JPATH_BASE.'/'.$document->file_path.'/'.$document->file_name;

      if(file_exists($download) === false) {
	echo 'The file cannot be found.';
	return;
      }

      preg_match('#\.([a-z0-9]{1,})$#', $document->file_name, $matches);
      $extension = $matches[1];
      $documentName = $document->alias.'.'.$extension;

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
    else { //The document url is empty.
      echo 'Wrong document url.';
      return;
    }
  }
  else { //The user doesn't have the required permission.
    echo 'You are not allowed to download this document.';
    return;
  }
}
else { //The document id is unset.
  echo 'The document doesn\'t exist.';
}


?>
