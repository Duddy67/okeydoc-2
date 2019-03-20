<?php
/**
 * @package Okey DOC 2
 * @copyright Copyright (c) 2018 - 2018 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

// No direct access
defined('_JEXEC') or die('Restricted access');


class plgSystemOkeydoc extends JPlugin
{
  /**
   * Application object.
   *
   * @var    JApplicationCms
   * @since  3.3
   */
  protected $app;


  /**
   * Constructor.
   *
   * @param   object  &$subject  The object to observe.
   * @param   array   $config	An optional associative array of configuration settings.
   *
   * @since   1.0
   */
  public function __construct(&$subject, $config)
  {
    $this->app = JFactory::getApplication();
    // Calling the parent Constructor
    parent::__construct($subject, $config);

    // Do some extra initialisation in this constructor if required
  }


  /**
   * Listener for the `onAfterRoute` event
   *
   * @return  void
   *
   * @since   1.0
   */
  public function onAfterRoute()
  {
    $jinput = $this->app->input;
    $component = $jinput->get('option', '', 'string');
    $extension = $jinput->get('extension', '', 'string');

    // Checks for the OkeyDoc component category list view.
    if($extension == 'com_okeydoc' && $component == 'com_categories' && $this->app->isAdmin()) {
      // Loads the modified php file.
      require_once(dirname(__FILE__).'/code/com_categories/views/view.html.php');
    }
  }
}

