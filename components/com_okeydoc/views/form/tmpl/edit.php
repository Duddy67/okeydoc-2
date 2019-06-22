<?php
/**
 * @package Okey DOC 2
 * @copyright Copyright (c) 2015 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

// No direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tabstate');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

// Create shortcut to parameters.
$params = $this->state->get('params');
$uri = JUri::getInstance();
// Pre builds the download link. 
$downloadLink = JURI::base().'index.php?option=com_okeydoc&view=download&tmpl=component';
?>

<script type="text/javascript">
Joomla.submitbutton = function(task)
{
  if(task == 'document.cancel' || document.formvalidator.isValid(document.id('document-form'))) {
    Joomla.submitform(task, document.getElementById('document-form'));
  }
  else {
    alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
  }
}
</script>

<div class="edit-document <?php echo $this->pageclass_sfx; ?>">
  <?php if($params->get('show_page_heading')) : ?>
    <div class="page-header">
      <h1>
	<?php echo $this->escape($params->get('page_heading')); ?>
      </h1>
    </div>
  <?php endif; ?>

  <form action="<?php echo JRoute::_('index.php?option=com_okeydoc&d_id='.(int)$this->item->id); ?>" 
   method="post" name="adminForm" id="document-form" enctype="multipart/form-data" class="form-validate form-vertical">

      <div class="btn-toolbar">
	<div class="btn-group">
	  <button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('document.save')">
		  <span class="icon-ok"></span>&#160;<?php echo JText::_('JSAVE') ?>
	  </button>
	</div>
	<div class="btn-group">
	  <button type="button" class="btn" onclick="Joomla.submitbutton('document.cancel')">
		  <span class="icon-cancel"></span>&#160;<?php echo JText::_('JCANCEL') ?>
	  </button>
	</div>
	<?php if ($params->get('save_history', 0)) : ?>
	<div class="btn-group">
		<?php echo $this->form->getInput('contenthistory'); ?>
	</div>
	<?php endif; ?>
      </div>

      <fieldset>

	<ul class="nav nav-tabs">
		<li class="active"><a href="#details" data-toggle="tab"><?php echo JText::_('COM_OKEYDOC_TAB_DETAILS') ?></a></li>
		<li><a href="#publishing" data-toggle="tab"><?php echo JText::_('COM_OKEYDOC_TAB_PUBLISHING') ?></a></li>
		<li><a href="#link-document" data-toggle="tab"><?php echo JText::_('COM_OKEYDOC_TAB_DOCUMENT_LINKINGS') ?></a></li>
		<li><a href="#language" data-toggle="tab"><?php echo JText::_('JFIELD_LANGUAGE_LABEL') ?></a></li>
		<li><a href="#metadata" data-toggle="tab"><?php echo JText::_('COM_OKEYDOC_TAB_METADATA') ?></a></li>
	</ul>

	<div class="tab-content">
	    <div class="tab-pane active" id="details">
	      <?php echo $this->form->renderField('title'); 
		    echo $this->form->renderField('alias');
		?>

	      <?php if($this->form->getValue('id') != 0) : // Existing item. ?>
		  <div class="control-group">
		    <div class="control-label">
		      <?php echo JText::_('COM_OKEYDOC_FIELD_DOWNLOAD_LABEL'); ?>
		    </div>
		    <div class="controls">
		      <a href="<?php echo $downloadLink.'&id='.$this->item->id.'&link=component'; ?>" class="btn btn-success" target="_blank">
			<span class="icon-download"></span>&#160;<?php echo JText::_('COM_OKEYDOC_BUTTON_DOWNLOAD'); ?>
		      </a>
		    </div>
		  </div>
		<?php echo $this->form->renderField('file_name'); ?>

		  <?php // Toggle button which hide/show the link method fields to replace the original file. ?>
		  <span class="form-space"></span>
		  <a href="#" id="switch_replace" style="margin-bottom:10px;" class="btn">
		    <span id="replace-title"><?php echo JText::_('COM_OKEYDOC_REPLACE'); ?></span>
		    <span id="cancel-title"><?php echo JText::_('JCANCEL'); ?></span></a>
		  <span class="form-space"></span>
	      <?php endif; ?>

	      <?php
		    echo $this->form->renderField('file_location');
		    echo $this->form->renderField('uploaded_file');
		    echo $this->form->renderField('file_url');

		    if($this->form->getValue('access') == 1) {
		      // This feature is only available with a Public access.
		      echo $this->form->renderField('email_required');
		    }

		    echo $this->form->renderField('author');
		    echo $this->form->renderField('documenttext');
	      ?>
	      </div>

	      <div class="tab-pane" id="publishing">
		<?php echo $this->form->renderField('catid'); ?>
		<?php echo $this->form->renderField('tags'); ?>
		<?php echo $this->form->renderField('access'); ?>

		<?php if($this->item->params->get('access-change')) : ?>
		  <?php echo $this->form->renderField('published'); ?>
		  <?php echo $this->form->renderField('publish_up'); ?>
		  <?php echo $this->form->renderField('publish_down'); ?>
		<?php endif; ?>
	      </div>

	      <div class="tab-pane" id="language">
		<?php echo $this->form->renderField('language'); ?>
	      </div>

	      <div class="tab-pane" id="link-document">
		<?php echo $this->form->renderField('articleids'); ?>

		<?php if(!empty($this->extDocLinkings['article'])) { 
			echo JLayoutHelper::render('document.linkings', array('ext_doc_linkings' => $this->extDocLinkings['article'], 'link_type' => 'article'), JPATH_ADMINISTRATOR.'/components/com_okeydoc/layouts'); 
		      } ?>

		<?php echo $this->form->renderField('contcatids'); ?>

		<?php if(!empty($this->extDocLinkings['category'])) { 
			echo JLayoutHelper::render('document.linkings', array('ext_doc_linkings' => $this->extDocLinkings['category'], 'link_type' => 'category'), JPATH_ADMINISTRATOR.'/components/com_okeydoc/layouts'); 
		      } ?>
		<span class="link-document-space"></span>
	      </div>

	      <div class="tab-pane" id="metadata">
		<?php echo $this->form->renderField('metadesc'); ?>
		<?php echo $this->form->renderField('metakey'); ?>
	      </div>
	    </div>

	    <?php
		  // Hidden input flag to check if a file replacement is required.
		  echo $this->form->getInput('replace_file'); 
		  // In case of file replacement the current file location will be needed.
		  $this->form->setValue('current_file_location', null, $this->item->file_location);
		  echo $this->form->getInput('current_file_location'); 
	      ?>

    <?php echo $this->form->getInput('id'); ?>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="return" value="<?php echo $this->return_page; ?>" />
    <?php if($this->params->get('enable_category', 0) == 1) :?>
      <input type="hidden" name="jform[catid]" value="<?php echo $this->params->get('catid', 1); ?>" />
    <?php endif; ?>
    <?php echo JHtml::_('form.token'); ?>
    </fieldset>
  </form>
</div>

<?php
$doc = JFactory::getDocument();
$doc->addScript(JURI::base().'administrator/components/com_okeydoc/js/document.js');

