<?php
/**
 * @package Okey DOC 2
 * @copyright Copyright (c) 2015 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

defined('JPATH_BASE') or die;
?>

<button type="button" class="btn" data-dismiss="modal">
	<?php echo JText::_('JCANCEL'); ?>
</button>
<?php echo JLayoutHelper::render('document.download', array('item' => $displayData)); ?>

