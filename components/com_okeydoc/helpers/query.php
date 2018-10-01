<?php
/**
 * @package Okey DOC 2
 * @copyright Copyright (c) 2017 - 2018 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

/**
 * Okey DOC 2 Component Query Helper
 *
 * @static
 * @package     Joomla.Site
 * @subpackage  com_okeydoc
 * @since       1.5
 */
class OkeydocHelperQuery
{
  /**
   * Translate an order code to a field for primary category ordering.
   *
   * @param   string	$orderby	The ordering code.
   *
   * @return  string	The SQL field(s) to order by.
   * @since   1.5
   */
  public static function orderbyPrimary($orderby)
  {
    //Set the prefix to use according to the view.
    $view = JFactory::getApplication()->input->get('view');
    $prefix = 'ca.'; //Use okeydoc table.
    if($view == 'tag') {
      $prefix = 'd.'; //Use mapping table.
    }

    switch ($orderby)
    {
      case 'alpha' :
	      $orderby = $prefix.'path, ';
	      break;

      case 'ralpha' :
	      $orderby = $prefix.'path DESC, ';
	      break;

      case 'order' :
	      $orderby = $prefix.'lft, ';
	      break;

      default :
	      $orderby = '';
	      break;
    }

    return $orderby;
  }

  /**
   * Translate an order code to a field for secondary category ordering.
   *
   * @param   string	$orderby	The ordering code.
   * @param   string	$orderDate	The ordering code for the date.
   *
   * @return  string	The SQL field(s) to order by.
   * @since   1.5
   */
  public static function orderbySecondary($orderby, $orderDate = 'created')
  {
    $queryDate = self::getQueryDate($orderDate);

    //Set the prefix to use according to the view.
    $view = JFactory::getApplication()->input->get('view');
    $prefix = 'd.'; //Use okeydoc table.
    if($view == 'tag') {
      $prefix = 'tm.'; //Use mapping table.
    }

    switch ($orderby)
    {
      case 'date' :
	      $orderby = $queryDate;
	      break;

      case 'rdate' :
	      $orderby = $queryDate.' DESC ';
	      break;

      case 'alpha' :
	      $orderby = 'd.title';
	      break;

      case 'ralpha' :
	      $orderby = 'd.title DESC';
	      break;

      case 'order' :
	      $orderby = $prefix.'ordering';
	      break;

      case 'rorder' :
	      $orderby = $prefix.'ordering DESC';
	      break;

      case 'author' :
	      $orderby = 'author';
	      break;

      case 'rauthor' :
	      $orderby = 'author DESC';
	      break;

      default :
	      $orderby = $prefix.'ordering';
	      break;
    }

    return $orderby;
  }

  /**
   * Translate an order code to a field for primary category ordering.
   *
   * @param   string	$orderDate	The ordering code.
   *
   * @return  string	The SQL field(s) to order by.
   * @since   1.6
   */
  public static function getQueryDate($orderDate)
  {
    $db = JFactory::getDbo();

    switch($orderDate) {
      case 'modified' :
	      $queryDate = ' CASE WHEN d.modified = '.$db->quote($db->getNullDate()).' THEN d.created ELSE d.modified END';
	      break;

      // use created if publish_up is not set
      case 'published' :
	      $queryDate = ' CASE WHEN d.publish_up = '.$db->quote($db->getNullDate()).' THEN d.created ELSE d.publish_up END ';
	      break;

      case 'created' :
      default :
	      $queryDate = ' d.created ';
	      break;
    }

    return $queryDate;
  }

  /**
   * Method to order the intro documents array for ordering
   * down the columns instead of across.
   * The layout always lays the introtext documents out across columns.
   * Array is reordered so that, when documents are displayed in index order
   * across columns in the layout, the result is that the
   * desired document ordering is achieved down the columns.
   *
   * @param   array    &$documents   Array of intro text documents
   * @param   integer  $numColumns  Number of columns in the layout
   *
   * @return  array  Reordered array to achieve desired ordering down columns
   *
   * @since   1.6
   */
  public static function orderDownColumns(&$documents, $numColumns = 1)
  {
    $count = count($documents);

    // Just return the same array if there is nothing to change
    if($numColumns == 1 || !is_array($documents) || $count <= $numColumns) {
      $return = $documents;
    }
    // We need to re-order the intro documents array
    else {
      // We need to preserve the original array keys
      $keys = array_keys($documents);

      $maxRows = ceil($count / $numColumns);
      $numCells = $maxRows * $numColumns;
      $numEmpty = $numCells - $count;
      $index = array();

      // Calculate number of empty cells in the array

      // Fill in all cells of the array
      // Put -1 in empty cells so we can skip later
      for($row = 1, $i = 1; $row <= $maxRows; $row++) {
	for($col = 1; $col <= $numColumns; $col++) {
	  if($numEmpty > ($numCells - $i)) {
	    // Put -1 in empty cells
	    $index[$row][$col] = -1;
	  }
	  else {
	    // Put in zero as placeholder
	    $index[$row][$col] = 0;
	  }

	  $i++;
	}
      }

      // Layout the documents in column order, skipping empty cells
      $i = 0;

      for($col = 1; ($col <= $numColumns) && ($i < $count); $col++) {
	for($row = 1; ($row <= $maxRows) && ($i < $count); $row++) {
	  if($index[$row][$col] != - 1) {
	    $index[$row][$col] = $keys[$i];
	    $i++;
	  }
	}
      }

      // Now read the $index back row by row to get documents in right row/col
      // so that they will actually be ordered down the columns (when read by row in the layout)
      $return = array();
      $i = 0;

      for($row = 1; ($row <= $maxRows) && ($i < $count); $row++) {
	for($col = 1; ($col <= $numColumns) && ($i < $count); $col++) {
	  $return[$keys[$i]] = $documents[$index[$row][$col]];
	  $i++;
	}
      }
    }

    return $return;
  }
}

