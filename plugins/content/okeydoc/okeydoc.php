<?php
/**
 * @package Okey DOC 2
 * @copyright Copyright (c) 2018 - 2018 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
// Import the JPlugin class
jimport('joomla.plugin.plugin');


class plgContentOkeydoc extends JPlugin
{

  public function onContentPrepare($context, &$data, &$params, $page)
  {
  }


  public function onContentAfterTitle($context, &$data, &$params, $limitstart)
  {
  }


  public function onContentBeforeDisplay($context, &$data, &$params, $limitstart)
  {
  }


  public function onContentAfterDisplay($context, &$data, &$params, $limitstart)
  {
  }


  public function onContentBeforeSave($context, $data, $isNew)
  {
    return true;
  }


  public function onContentAfterSave($context, $data, $isNew)
  {
  }


  public function onContentBeforeDelete($context, $data)
  {
    return true;
  }


  public function onContentAfterDelete($context, $data)
  {
  }


  public function onContentPrepareForm($form, $data)
  {
    return true;
  }


  public function onContentPrepareData($context, $data)
  {
    return true;
  }


  public function onContentChangeState($context, $pks, $value)
  {
    return true;
  }
}

