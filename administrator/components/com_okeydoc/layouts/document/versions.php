<?php
/**
 * @package Okey DOC 2
 * @copyright Copyright (c) 2017 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

defined('JPATH_BASE') or die;


$archives = $displayData['archives'];
$docId = $displayData['doc_id'];
?>

<table class="table">
 <tr>
   <th><?php echo JText::_('COM_OKEYDOC_ARCHIVE_VERSION'); ?></th>
   <th><?php echo JText::_('COM_OKEYDOC_ARCHIVE_SIZE'); ?></th>
   <th><?php echo JText::_('COM_OKEYDOC_ARCHIVE_FILE_TYPE'); ?></th>
   <th><?php echo JText::_('COM_OKEYDOC_ARCHIVE_DOWNLOADS'); ?></th>
   <th><?php echo JText::_('COM_OKEYDOC_ARCHIVE_ARCHIVED'); ?></th>
   <th></th>
 </tr>
 <?php foreach($archives as $key => $archive) : ?>
   <tr>
    <td><?php echo $archive['version']; ?></td>
    <td><?php echo $archive['file_size']; ?></td>
    <td><?php echo $archive['file_icon']; ?></td>
    <td><?php echo $archive['downloads']; ?></td>
    <td><?php echo JHtml::_('date', $archive['archived'], JText::_('DATE_FORMAT_LC2')); ?></td>
    <td><a href="<?php echo JURI::base().'index.php?option=com_okeydoc&view=download&tmpl=component&id='.$docId.'&version='.$archive['version']; ?>" class="btn btn-info" target="_blank">
      <span class="icon-download"></span>&#160;<?php echo JText::_('COM_OKEYDOC_BUTTON_DOWNLOAD'); ?></a></td>
   </tr>
 <?php endforeach; ?>

</table>

