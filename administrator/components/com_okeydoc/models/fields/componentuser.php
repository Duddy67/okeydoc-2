<?php
/**
 * @package Okey DOC 2
 * @copyright Copyright (c) 2015 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

defined('JPATH_BASE') or die;


class JFormFieldComponentuser extends JFormFieldList
{
  /**
   * The form field type.
   *
   * @var		string
   * @since   1.6
   */
  protected $type = 'Componentuser';
  protected $exceptions = array('deliverypoint' => 'delivery_point','pricerule' => 'price_rule','paymentmode' => 'payment_mode');

  /**
   * Method to get the field options.
   *
   * @return  array  The field option objects.
   */
  public function getOptions()
  {
    // Gets the item name from the form filter name. 
    preg_match('#^com_okeydoc\.([a-zA-Z0-9_-]+)\.filter$#', $this->form->getName(), $matches);
    $itemName = $matches[1];

    // We need the item name in the singular in order to build the SQL table name.

    // countries, currencies etc...
    if(preg_match('#ies$#', $itemName)) { 
      $itemName = preg_replace('#ies$#', 'y', $itemName);
    }
    // taxes, boxes etc...
    elseif(preg_match('#xes$#', $itemName)) { 
      $itemName = preg_replace('#es$#', '', $itemName);
    }
    // Regular plurials.
    else { 
      $itemName = preg_replace('#s$#', '', $itemName);
    }

    // Note: Some SQL table names are separated with underscore.
    if(array_key_exists($itemName, $this->exceptions)) {
      $itemName = $this->exceptions[$itemName];
    }

    $options = OkeydocHelper::getUsers($itemName);

    return  array_merge(parent::getOptions(), $options);
  }
}

