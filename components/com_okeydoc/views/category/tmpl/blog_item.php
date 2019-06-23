<?php
/**
 * @package Okey DOC 2
 * @copyright Copyright (c) 2015 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;
JHtml::_('behavior.framework');

// Create shortcut for params.
$params = $this->item->params;
// Needed in the download layout.
$this->item->view = 'category'; 
?>

<div class="document-item">
  <?php echo JLayoutHelper::render('document.title', array('item' => $this->item, 'params' => $params, 'now_date' => $this->nowDate)); ?>

  <?php echo JLayoutHelper::render('document.icons', array('item' => $this->item, 'user' => $this->user, 'uri' => $this->uri)); ?>

  <?php $useDefList = ($params->get('show_modify_date') || $params->get('show_publish_date') || $params->get('show_create_date')
		       || $params->get('show_hits') || $params->get('show_category') || $params->get('show_parent_category')
		       || $params->get('show_author') ); ?>

  <?php if ($useDefList) : ?>
    <?php echo JLayoutHelper::render('document.info_block', array('item' => $this->item, 'params' => $params)); ?>
  <?php endif; ?>

  <?php echo $this->item->intro_text; ?>

  <?php if($params->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
	  <?php $this->item->tagLayout = new JLayoutFile('joomla.content.tags'); 
		echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
  <?php endif; ?>

  <?php if(($params->get('show_readmore') && !empty($this->item->full_text)) || $params->get('show_download')) :
	  if(!$params->get('access-view')) { // Redirects the user to the login page.
	    $menu = JFactory::getApplication()->getMenu();
	    $active = $menu->getActive();
	    $itemId = $active->id;
	    $comUserLink = new JUri(JRoute::_('index.php?option=com_users&view=login&Itemid='.$itemId, false));
	    $comUserLink->setVar('return', base64_encode(JRoute::_(OkeydocHelperRoute::getDocumentRoute($this->item->slug, $this->item->catid, $this->item->language), false)));
	  }

          // Computes the Readmore and Download links.
	  if($params->get('show_readmore') && !empty($this->item->full_text)) {     
	    if($params->get('access-view')) {
	      $link = JRoute::_(OkeydocHelperRoute::getDocumentRoute($this->item->slug, $this->item->catid, $this->item->language));
	    }
	    else {
	      $link = $comUserLink;
	    }

	    echo JLayoutHelper::render('document.readmore', array('item' => $this->item, 'params' => $params, 'link' => $link)); 
	  }

	  if($params->get('show_download')) {     
	    if($params->get('access-view')) {
	      $this->item->link = JURI::base().'index.php?option=com_okeydoc&view=download&tmpl=component&id='.$this->item->id;
	    }
	    else {
	      $this->item->link = $comUserLink;
	    }

	    if($this->item->email_required) { // Shows the button which displays the modal window. ?>

	      <button type="button" data-toggle="modal" onclick="jQuery( '#collapseModal' ).modal('show'); return true;" class="btn btn-success">
	      <span class="icon-download" aria-hidden="true"></span>
	      <?php echo JText::_('COM_OKEYDOC_DOWNLOAD'); ?></button>
	      <?php echo JHtml::_('bootstrap.renderModal', 'collapseModal',
				  array('title' => JText::_('COM_OKEYDOC_MESSAGE_EMAIL_REQUIRED'),
					'footer' => JLayoutHelper::render('document.email_modal_footer',
				  $this->item)), JLayoutHelper::render('document.email_modal_body')); ?>

      <?php }
	    // Shows the regular download button.
	    else {
	      echo JLayoutHelper::render('document.download', array('item' => $this->item)); 
	    }
	  }
   endif; ?>
</div>

