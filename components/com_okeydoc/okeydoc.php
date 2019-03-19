<?php
/**
 * @package Okey DOC 2
 * @copyright Copyright (c) 2015 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

// No direct access.
defined('_JEXEC') or die; 


// Registers the component helper files. They will be loaded automatically later as soon
// as an helper class is instantiate.
JLoader::register('OkeydocHelperRoute', JPATH_SITE.'/components/com_okeydoc/helpers/route.php');
JLoader::register('OkeydocHelperQuery', JPATH_SITE.'/components/com_okeydoc/helpers/query.php');


$controller = JControllerLegacy::getInstance('Okeydoc');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();

