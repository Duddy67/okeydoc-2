<?php
/**
 * @package Okey DOC 2
 * @copyright Copyright (c) 2015 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;
?>


<ol class="nav nav-tabs nav-stacked">
<?php foreach ($this->link_items as &$item) : ?>
	<li>
	  <a href="<?php echo JRoute::_(OkeydocHelperRoute::getDocumentRoute($item->slug, $item->catid, $item->language)); ?>">
		      <?php echo $item->title; ?></a>
	</li>
<?php endforeach; ?>
</ol>

