<?php
/**
 * @package Okey DOC 2
 * @copyright Copyright (c) 2015 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;


// This template is used only to display the email modal form when
// an email is required to download the document.

echo JHtml::_('bootstrap.renderModal', 'collapseModal',
	      array('title' => JText::_('COM_OKEYDOC_MESSAGE_EMAIL_REQUIRED'),
		    'footer' => JLayoutHelper::render('document.email_modal_footer',
	      $this->item)), JLayoutHelper::render('document.email_modal_body')); 
?>

<script type="text/javascript">
  jQuery( '#collapseModal' ).modal('show'); 
</script>

<?php
// Loads the required scripts.
$doc = JFactory::getDocument();
$doc->addScript(JURI::base().'components/com_okeydoc/js/emailrequired.js');
