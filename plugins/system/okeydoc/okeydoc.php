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
   * Listener for the `onAfterInitialise` event
   *
   * @return  void
   *
   * @since   1.0
   */
  public function onAfterInitialise()
  {
    // Do something onAfterInitialise
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

    if($component == 'com_categories' && $extension == 'com_okeydoc' && $this->app->isAdmin()) {
      //
      require_once(dirname(__FILE__).'/code/com_categories/views/view.html.php');
    }
  }


  /**
   * Listener for the `onAfterDispatch` event
   *
   * @return  void
   *
   * @since   1.0
   */
  public function onAfterDispatch()
  {
    // Do something onAfterDispatch
  }


  /**
   * Listener for the `onBeforeRender` event
   *
   * @return  void
   *
   * @since   1.0
   */
  public function onBeforeRender()
  {
    // Do something onAfterRender
  }


  /**
   * Listener for the `onAfterRender` event
   *
   * @return  void
   *
   * @since   1.0
   */
  public function onAfterRender()
  {
    // Do something onAfterRender
  }


  /**
   * Listener for the `onBeforeCompileHeader` event
   *
   * @return  void
   *
   * @since   1.0
   */
  public function onBeforeCompileHeader()
  {
    // Do something onAfterRender
  }
}

