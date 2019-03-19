<?php
/**
 * @package Okey DOC 2
 * @copyright Copyright (c) 2015 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

JHtml::_('behavior.framework');

// Create a shortcut for params.
$item = $displayData['item'];
$params = $displayData['params'];
$nowDate = $displayData['now_date'];
?>

<?php if($params->get('show_title') || $item->published == 0 || ($params->get('show_author') && !empty($item->author))) : ?>
  <div class="page-header">
    <?php if($params->get('show_extension_icon')) : ?>
      <img src="media/com_okeydoc/extensions/<?php echo $item->file_icon; ?>" class="file-icon" 
	   alt="<?php echo $item->file_icon; ?>" width="16" height="16" />
    <?php endif; ?>
    <?php if($params->get('show_title')) : ?>
	    <h2>
	      <?php if($params->get('link_title') && $params->get('access-view')) :

		    $link = JRoute::_(OkeydocHelperRoute::getDocumentRoute($item->slug, $item->catid, $item->language));
	      ?>
		<a href="<?php echo $link; ?>">
		      <?php echo $this->escape($item->title); ?></a>
	      <?php else : ?>
		<?php echo $this->escape($item->title); ?>
	      <?php endif; ?>
	    </h2>
    <?php endif; ?>

    <?php if($item->published == 0) : ?>
	    <span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
    <?php endif; ?>
    <?php if($item->published == 2) : ?>
	    <span class="label label-warning"><?php echo JText::_('JARCHIVED'); ?></span>
    <?php endif; ?>
    <?php if (strtotime($item->publish_up) > strtotime($nowDate)) : ?>
	    <span class="label label-warning"><?php echo JText::_('JNOTPUBLISHEDYET'); ?></span>
    <?php endif; ?>
    <?php if ((strtotime($item->publish_down) < strtotime($nowDate)) && $item->publish_down != '0000-00-00 00:00:00') : ?>
	    <span class="label label-warning"><?php echo JText::_('JEXPIRED'); ?></span>
    <?php endif; ?>
  </div>
<?php endif; ?>
