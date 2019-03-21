<?php
/**
 * @package Okey DOC 2
 * @copyright Copyright (c) 2015 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

defined('JPATH_BASE') or die;


$doc = JFactory::getDocument();
$doc->addStyleSheet(JURI::base().'components/com_okeydoc/css/okeydoc.css');
?>

<div class="okeydoc-document-list">
  <table class="table-striped okeydoc-table">
   <tr>
     <th><?php echo JText::_('JGLOBAL_TITLE'); ?></th>
     <th><?php echo JText::_('COM_OKEYDOC_ARCHIVE_SIZE'); ?></th>
     <th><?php echo JText::_('COM_OKEYDOC_ARCHIVE_FILE_TYPE'); ?></th>
     <th></th>
   </tr>
  <?php foreach($displayData as $document) : ?>
	 <tr>
           <td class="document-title"><?php echo $document->title; ?></td>
           <td class="center"><?php echo $document->file_size; ?></td>
           <td class="center"><?php echo '<img src="'.JURI::base().'media/com_okeydoc/extensions/'.$document->file_icon.'" />'; ?></td>
	   <td class="download-button"><a href="<?php echo JURI::base().'index.php?option=com_okeydoc&view=download&tmpl=component&id='.$document->id; ?>" class="btn btn-info" target="_blank">
	    <span class="icon-download"></span>&#160;<?php echo JText::_('COM_OKEYDOC_BUTTON_DOWNLOAD'); ?></a></td>
          </tr>
  <?php endforeach; ?>
  </table>
</div>

