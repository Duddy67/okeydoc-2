<?php
/**
 * @package Okey DOC 2
 * @copyright Copyright (c) 2017 - 2018 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */


defined( '_JEXEC' ) or die; // No direct access
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.framework', true);
JHtml::_('formbehavior.chosen', 'select');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
require_once JPATH_ROOT.'/components/com_okeydoc/helpers/route.php';

$app = JFactory::getApplication();

if($app->isSite()) {
  JSession::checkToken('get') or die(JText::_('JINVALID_TOKEN'));
}

$jinput = JFactory::getApplication()->input;
$function = $jinput->get('function', 'selectDocument');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>

<form action="<?php echo JRoute::_('index.php?option=com_okeydoc&view=documents&layout=modal&tmpl=component&function='.$function.'&'.JSession::getFormToken().'=1');?>" method="post" name="adminForm" id="adminForm" class="form-inline">

  <fieldset class="filter clearfix">
    <div class="btn-toolbar">
      <div class="btn-group pull-left">
	      <label for="filter_search">
		      <?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>
	      </label>
      </div>
      <div class="btn-group pull-left">
	      <input type="text" name="filter_search" id="filter_search" value="<?php echo
	      $this->escape($this->state->get('filter.search')); ?>" size="30"
	      title="<?php echo JText::_('COM_OKEYDOC_FILTER_SEARCH_DESC'); ?>" />
      </div>
      <div class="btn-group pull-left">
	      <button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>" data-placement="bottom">
		      <span class="icon-search"></span><?php echo '&#160;' . JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
	      <button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" data-placement="bottom" onclick="document.id('filter_search').value='';this.form.submit();">
		      <span class="icon-remove"></span><?php echo '&#160;' . JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
      </div>
	<div class="clearfix"></div>
    </div>
    <hr class="hr-condensed" />
    <div class="filters pull-left">
      <?php if ($this->state->get('filter.forcedLanguage')) : ?>
      <select name="filter_category_id" class="input-medium" onchange="this.form.submit()">
	<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
	<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_okeydoc', array('filter.language' => array('*', $this->state->get('filter.forcedLanguage')))), 'value', 'text', $this->state->get('filter.category_id'));?>
      </select>
      <input type="hidden" name="forcedLanguage" value="<?php echo $this->escape($this->state->get('filter.forcedLanguage')); ?>" />
      <input type="hidden" name="filter_language" value="<?php echo $this->escape($this->state->get('filter.language')); ?>" />
      <?php else : ?>
      <select name="filter_category_id" class="input-medium" onchange="this.form.submit()">
	<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
	<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_okeydoc'), 'value', 'text', $this->state->get('filter.category_id'));?>
      </select>
      <select name="filter_tag" class="input-medium" onchange="this.form.submit()">
	<option value=""><?php echo JText::_('JOPTION_SELECT_TAG');?></option>
	<?php echo JHtml::_('select.options', JHtml::_('tag.options', 'com_okeydoc'), 'value', 'text', $this->state->get('filter.tag'));?>
      </select>
      <select name="filter_language" class="input-medium" onchange="this.form.submit()">
	<option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
	<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'));?>
      </select>
      <?php endif; ?>
    </div>
  </fieldset>

  <table class="table table-striped table-condensed">
    <thead>
      <tr>
	<th class="title">
		<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'd.title', $listDirn, $listOrder); ?>
	</th>
	<th width="15%" class="nowrap hidden-phone">
		<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
	</th>
	<th width="15%" class="nowrap hidden-phone">
		<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
	</th>
	<th width="15%" class="nowrap hidden-phone">
		<?php echo JHtml::_('grid.sort', 'JDATE', 'd.created', $listDirn, $listOrder); ?>
	</th>
	<th width="1%" class="nowrap hidden-phone">
		<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'd.id', $listDirn, $listOrder); ?>
	</th>
      </tr>
    </thead>
    <tfoot>
      <tr>
	<td colspan="5">
	  <?php echo $this->pagination->getListFooter(); ?>
	</td>
      </tr>
    </tfoot>
    <tbody>
    <?php foreach($this->items as $i => $item) : ?>
	    <?php if($item->language && JLanguageMultilang::isEnabled()) {
		    $tag = strlen($item->language);
		    if($tag == 5) {
		      $lang = substr($item->language, 0, 2);
		    }
		    elseif($tag == 6) {
		      $lang = substr($item->language, 0, 3);
		    }
		    else {
		      $lang = "";
		    }
	    }
	    elseif(!JLanguageMultilang::isEnabled()) {
	      $lang = "";
	    }
	    ?>
      <tr class="row<?php echo $i % 2; ?>">
	      <td class="has-context">
		<div class="pull-left">
          <?php if($function == 'insertDocumentLink') : ?>
		  <a href="javascript:void(0)" onclick="if (window.parent)
		  window.parent.<?php echo $this->escape($function);?>('<?php echo $this->escape(addslashes($item->title)); ?>','<?php echo JUri::root().'index.php?option=com_okeydoc&tmpl=component&view=download&link=external&id='.$item->id; ?>');"><?php echo $this->escape($item->title); ?></a>
          <?php else : ?>
		  <a href="javascript:void(0)" onclick="if (window.parent)
		  window.parent.<?php echo $this->escape($function);?>('<?php echo $item->id; ?>','<?php echo $this->escape(addslashes($item->title)); ?>','<?php echo $item->catid; ?>');"><?php echo $this->escape($item->title); ?></a>
          <?php endif; ?>
		  <span class="small break-word">
		    <?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
		  </span>
		  <div class="small">
		    <?php echo JText::_('JCATEGORY') . ": ".$this->escape($item->category_title); ?>
		  </div>
		</div>
	      </td>
	      <td  class="small hidden-phone">
		<?php echo $this->escape($item->access_level); ?>
	      </td>
	      <td  class="small hidden-phone">
		<?php if ($item->language == '*'):?>
			<?php echo JText::alt('JALL', 'language'); ?>
		<?php else:?>
			<?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
		<?php endif;?>
	      </td>
	      <td  class="small hidden-phone">
		<?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC4')); ?>
	      </td>
	      <td class="center">
		<?php echo (int) $item->id; ?>
	      </td>
      </tr>
    <?php endforeach; ?>
      </tbody>
    </table>

  <div>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
    <?php echo JHtml::_('form.token'); ?>
  </div>
</form>
