<?php
/**
 * @package Okey DOC 2
 * @copyright Copyright (c) 2015 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

defined('JPATH_BASE') or die;


$extDocLinkings = $displayData['ext_doc_linkings'];
$linkType = $displayData['link_type'];
?>

<div class="document-linkings">
  <table class="table-striped linking-table">
   <tr>
     <th><?php echo JText::_('JGLOBAL_TITLE'); ?></th>
     <th><?php echo JText::_('JGRID_HEADING_ID'); ?></th>
   </tr>
   <?php foreach($extDocLinkings as $extDocLinking) : ?>
     <tr>
      <td class="item-title"><?php echo $extDocLinking[$linkType.'_title']; ?></td>
      <td class="item-id"><?php echo $extDocLinking['item_id']; ?></td>
     </tr>
   <?php endforeach; ?>
  </table>
</div>
