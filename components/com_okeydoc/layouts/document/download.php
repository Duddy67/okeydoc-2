<?php
/**
 * @package Okey DOC 2
 * @copyright Copyright (c) 2015 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

defined('JPATH_BASE') or die;

$item = $displayData['item'];

$elemId = $style = '';
if($item->email_required) {
  // A id is needed as it is used by the JS functions. 
  $elemId = 'id="download-btn"';
  $style = 'style="display:inline;"';
}

$target = 'target="blank"';
if($item->view == 'category' && !$item->params->get('access-view')) {
  // Prevents to open a new tab when redirecting to com_user.
  $target = '';
}

$onclick = '';
if($item->view == 'external') {
  // The link comes from another component and an email is required to download the document.
  // Closes the tab in which the com_okeydoc download view is currently displayed.
  $onclick = 'onclick="window.close();"';
}
?>

  <p class="download" <?php echo $style; ?>>
    <a class="btn btn-success" <?php echo $elemId; ?> href="<?php echo $item->link; ?>"  <?php echo $target.' '.$onclick; ?> itemprop="url">
	    <span class="icon-download"></span>
	    <?php if ($item->view == 'category' && !$item->params->get('access-view')) :
		    echo JText::_('COM_OKEYDOC_REGISTER_TO_DOWNLOAD');
	    else :
		    echo JText::_('COM_OKEYDOC_DOWNLOAD');
	    endif; ?>
    </a>
</p>

