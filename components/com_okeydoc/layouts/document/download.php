<?php
/**
 * @package Okey DOC 2
 * @copyright Copyright (c) 2017 - 2018 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

defined('JPATH_BASE') or die;

$params = $displayData['params'];
$item = $displayData['item'];
$view = $displayData['view'];

$target = 'target="blank"';
if(!$params->get('access-view' && $view == 'category')) {
  $target = '';
}
?>

<p class="download">
	<a class="btn btn-success" href="<?php echo $displayData['link']; ?>" <?php echo $target; ?> itemprop="url">
		<span class="icon-download"></span>
		<?php if (!$params->get('access-view') && $view == 'category') :
			echo JText::_('COM_OKEYDOC_REGISTER_TO_DOWNLOAD');
		else :
			echo JText::_('COM_OKEYDOC_DOWNLOAD');
		endif; ?>
	</a>
</p>

