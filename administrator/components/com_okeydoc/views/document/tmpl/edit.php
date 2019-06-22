<?php
/**
 * @package Okey DOC 2
 * @copyright Copyright (c) 2015 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

// No direct access
defined( '_JEXEC' ) or die; 

JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');


// Prevent params layout (layouts/joomla/edit/params.php) to display twice some fieldsets.
$this->ignore_fieldsets = array('details', 'permissions', 'jmetadata');
$canDo = OkeydocHelper::getActions($this->state->get('filter.category_id'));
// Pre builds the download link. 
$downloadLink = JURI::base().'index.php?option=com_okeydoc&view=download&tmpl=component';
?>

<script type="text/javascript">
Joomla.submitbutton = function(task)
{
  if(task == 'document.cancel' || document.formvalidator.isValid(document.getElementById('document-form'))) {
    Joomla.submitform(task, document.getElementById('document-form'));
  }
}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_okeydoc&view=document&layout=edit&id='.(int) $this->item->id); ?>" 
 method="post" name="adminForm" id="document-form" enctype="multipart/form-data" class="form-validate">

  <?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

  <div class="form-horizontal">

    <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>

    <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('COM_OKEYDOC_TAB_DETAILS')); ?>

      <div class="row-fluid">
	<div class="span9">
	    <div class="form-vertical">

	      <?php if($this->form->getValue('id') != 0) : // Existing item. ?>
		  <div class="control-group">
		    <div class="control-label">
		      <?php echo JText::_('COM_OKEYDOC_FIELD_DOWNLOAD_LABEL'); ?>
		    </div>
		    <div class="controls">
		      <a href="<?php echo $downloadLink.'&id='.$this->item->id; ?>" class="btn btn-success" target="_blank">
			<span class="icon-download"></span>&#160;<?php echo JText::_('COM_OKEYDOC_BUTTON_DOWNLOAD'); ?>
		      </a>
		    </div>
		   </div>

		    <?php echo $this->form->getControlGroup('file_name'); ?>
		    <?php // Toggle button which hide/show the link method fields to replace the original file. ?>
		    <a href="#" id="switch_replace" style="margin-bottom:10px;" class="btn">
		      <span id="replace-title"><?php echo JText::_('COM_OKEYDOC_REPLACE'); ?></span>
		      <span id="cancel-title"><?php echo JText::_('JCANCEL'); ?></span></a>
	      <?php endif; ?>

	      <?php
		    echo $this->form->renderField('file_location');
		    echo $this->form->renderField('uploaded_file');
		    echo $this->form->renderField('archive_file');
		    echo $this->form->renderField('file_url');

		    if($this->form->getValue('access') == 1) {
		      // This feature is only available with a Public access.
		      echo $this->form->renderField('email_required');
		    }

		    echo $this->form->renderField('author');
		    echo $this->form->renderField('documenttext');
		?>
	    </div>
	</div>
	<div class="span3">
	  <?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
	</div>
      </div>
      <?php echo JHtml::_('bootstrap.endTab'); ?>

      <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'attachment', JText::_('COM_OKEYDOC_TAB_DOCUMENT_LINKINGS', true)); ?>
      <div class="row-fluid form-horizontal-desktop">
	<div class="span6">
	  <?php echo $this->form->renderField('articleids'); ?>

	  <?php if(!empty($this->extDocLinkings['article'])) { 
		  echo JLayoutHelper::render('document.linkings', array('ext_doc_linkings' => $this->extDocLinkings['article'], 'link_type' => 'article')); 
		} ?>

	  <?php echo $this->form->renderField('contcatids'); ?>

	  <?php if(!empty($this->extDocLinkings['category'])) { 
		  echo JLayoutHelper::render('document.linkings', array('ext_doc_linkings' => $this->extDocLinkings['category'], 'link_type' => 'category')); 
		} ?>
	</div>
      </div>
      <?php echo JHtml::_('bootstrap.endTab'); ?>

      <?php if(!empty($this->archives)) : ?>
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'versions', JText::_('COM_OKEYDOC_TAB_VERSIONS', true)); ?>
	<div class="row-fluid form-horizontal-desktop">
	  <?php echo JLayoutHelper::render('document.versions', array('archives' => $this->archives, 'doc_id' => $this->item->id)); ?>
	</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
      <?php endif; ?>

      <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('JGLOBAL_FIELDSET_PUBLISHING', true)); ?>
      <div class="row-fluid form-horizontal-desktop">
	<div class="span6">
	  <?php echo JLayoutHelper::render('joomla.edit.publishingdata', $this); ?>
	</div>
	<div class="span6">
	  <?php echo JLayoutHelper::render('joomla.edit.metadata', $this); ?>
	</div>
      </div>
      <?php echo JHtml::_('bootstrap.endTab'); ?>

      <?php echo JLayoutHelper::render('joomla.edit.params', $this); ?>

      <?php if($canDo->get('core.admin')) : ?>
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'permissions', JText::_('COM_OKEYDOC_TAB_PERMISSIONS', true)); ?>
		<?php echo $this->form->getInput('rules'); ?>
		<?php echo $this->form->getInput('asset_id'); ?>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
      <?php endif; ?>
  </div>

  <?php
	// Hidden input flag to check if a file replacement is required.
	echo $this->form->getInput('replace_file'); 
	// In case of file replacement the current file location will be needed.
	$this->form->setValue('current_file_location', null, $this->item->file_location);
	echo $this->form->getInput('current_file_location'); 
    ?>
  <input type="hidden" name="task" value="" />
  <?php echo JHtml::_('form.token'); ?>
</form>

<?php
$doc = JFactory::getDocument();
$doc->addScript(JURI::base().'components/com_okeydoc/js/document.js');

