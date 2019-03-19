<?php
/**
 * @package Okey DOC 2
 * @copyright Copyright (c) 2015 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

defined('JPATH_BASE') or die;
?>

<dl class="article-info muted">

  <?php if($displayData['params']->get('show_author') && !empty($displayData['item']->author )) : ?>
    <dd class="createdby" itemprop="author" itemscope itemtype="http://schema.org/Person">
	<?php $author = '<span itemprop="name">'.$displayData['item']->author.'</span>'; ?>
	<?php echo JText::sprintf('COM_OKEYDOC_WRITTEN_BY', $author); ?>
    </dd>
  <?php endif; ?>

  <?php if($displayData['params']->get('show_parent_category') && !empty($displayData['item']->parent_slug)) : ?>
    <dd class="parent-category-name">
      <?php $title = $this->escape($displayData['item']->parent_title); ?>
      <?php if($displayData['params']->get('link_parent_category')) : ?>
	<?php $url = '<a href="' . JRoute::_(OkeydocHelperRoute::getCategoryRoute($displayData['item']->parent_slug)).'" itemprop="genre">'.$title.'</a>'; ?>
	<?php echo JText::sprintf('COM_OKEYDOC_PARENT', $url); ?>
      <?php else : ?>
	<?php echo JText::sprintf('COM_OKEYDOC_PARENT', '<span itemprop="genre">'.$title.'</span>'); ?>
    <?php endif; ?>
    </dd>
  <?php endif; ?>

  <?php if($displayData['params']->get('show_category')) : ?>
    <dd class="category-name">
      <?php $title = $this->escape($displayData['item']->category_title); ?>
      <?php if ($displayData['params']->get('link_category') && $displayData['item']->catslug) : ?>
	<?php $url = '<a href="'.JRoute::_(OkeydocHelperRoute::getCategoryRoute($displayData['item']->catslug)).'" itemprop="genre">'.$title.'</a>'; ?>
	<?php echo JText::sprintf('COM_OKEYDOC_CATEGORY', $url); ?>
      <?php else : ?>
	<?php echo JText::sprintf('COM_OKEYDOC_CATEGORY', '<span itemprop="genre">'.$title.'</span>'); ?>
      <?php endif; ?>
    </dd>
  <?php endif; ?>

  <?php if($displayData['params']->get('show_publish_date')) : ?>
    <dd class="published">
      <span class="icon-calendar"></span>
      <time datetime="<?php echo JHtml::_('date', $displayData['item']->publish_up, 'c'); ?>" itemprop="datePublished">
	<?php echo JText::sprintf('COM_OKEYDOC_PUBLISHED_DATE_ON', JHtml::_('date', $displayData['item']->publish_up, JText::_('DATE_FORMAT_LC3'))); ?>
      </time>
    </dd>
  <?php endif; ?>

  <?php if($displayData['params']->get('show_create_date')) : ?>
      <dd class="create">
	<span class="icon-calendar"></span>
	<time datetime="<?php echo JHtml::_('date', $displayData['item']->created, 'c'); ?>" itemprop="dateCreated">
	  <?php echo JText::sprintf('COM_OKEYDOC_CREATED_DATE_ON', JHtml::_('date', $displayData['item']->created, JText::_('DATE_FORMAT_LC3'))); ?>
	</time>
	</dd>
  <?php endif; ?>

  <?php if($displayData['params']->get('show_modify_date') && (int)$displayData['item']->modified != 0) : ?>
    <dd class="modified">
      <span class="icon-calendar"></span>
      <time datetime="<?php echo JHtml::_('date', $displayData['item']->modified, 'c'); ?>" itemprop="dateModified">
	<?php echo JText::sprintf('COM_OKEYDOC_LAST_UPDATED', JHtml::_('date', $displayData['item']->modified, JText::_('DATE_FORMAT_LC3'))); ?>
      </time>
      </dd>
  <?php endif; ?>

  <?php if($displayData['params']->get('show_hits')) : ?>
    <dd class="hits">
    <span class="icon-eye-open"></span>
    <meta itemprop="interactionCount" content="UserPageVisits:<?php echo $displayData['item']->hits; ?>" />
      <?php echo JText::sprintf('COM_OKEYDOC_DOCUMENT_HITS', $displayData['item']->hits); ?>
    </dd>
  <?php endif; ?>

  <?php if($displayData['params']->get('show_file_name')) :
          $fileName = $displayData['item']->file_name;
          if($displayData['item']->file_location == 'server') {
	    preg_match('#\.([a-z0-9]+)$#', $displayData['item']->file_name, $matches);
	    $fileName = JFilterOutput::stringURLSafe($displayData['item']->title).'.'.$matches[1];
	  }
    ?>
    <dd class="file">
    <span class="icon-file"></span>
    <meta itemprop="interactionCount" content="UserPageVisits:<?php echo $fileName; ?>" />
      <?php echo JText::sprintf('COM_OKEYDOC_FILE_NAME', $fileName); ?>
    </dd>
  <?php endif; ?>

  <?php if($displayData['params']->get('show_file_size')) :
          if($displayData['item']->file_size == 'unknown') {
	    $fileSize = JText::_('COM_OKEYDOC_UNKNOWN');
	  }
          else {
	    $conversion = $displayData['item']->conversion;
	    $fileSize = JText::sprintf('COM_OKEYDOC_BYTE_CONVERTER_'.$conversion['multiple'], $conversion['result']);
	  }
    ?>
    <dd class="file">
    <span class="icon-file"></span>
      <?php echo JText::sprintf('COM_OKEYDOC_FILE_SIZE', $fileSize); ?>
    </dd>
  <?php endif; ?>

  <?php if($displayData['params']->get('show_file_type')) : ?>
    <dd class="file">
    <span class="icon-file"></span>
      <?php echo JText::sprintf('COM_OKEYDOC_FILE_TYPE', $displayData['item']->file_type); ?>
    </dd>
  <?php endif; ?>

  <?php if($displayData['params']->get('show_file_author')) : ?>
    <dd class="file">
    <span class="icon-user"></span>
      <?php echo JText::sprintf('COM_OKEYDOC_FILE_AUTHOR', $displayData['item']->file_author); ?>
    </dd>
  <?php endif; ?>

  <?php if($displayData['params']->get('show_downloads')) : ?>
    <dd class="download">
    <span class="icon-download"></span>
      <?php echo JText::sprintf('COM_OKEYDOC_FILE_DOWNLOADS', $displayData['item']->downloads); ?>
    </dd>
  <?php endif; ?>
</dl>

