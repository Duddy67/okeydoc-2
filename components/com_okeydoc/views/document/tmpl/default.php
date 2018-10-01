<?php
/**
 * @package Okey DOC 2
 * @copyright Copyright (c) 2017 - 2018 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

// Create some shortcuts.
$params = $this->item->params;
$item = $this->item;
?>

<div class="item-page<?php echo $this->pageclass_sfx; ?>" itemscope itemtype="http://schema.org/Document">
  <?php if($item->params->get('show_page_heading')) : ?>
    <div class="page-header">
      <h1><?php echo $this->escape($params->get('page_heading')); ?></h1>
    </div>
  <?php endif; ?>

  <?php echo JLayoutHelper::render('document.title', array('item' => $item, 'params' => $params, 'now_date' => $this->nowDate)); ?>

  <?php echo JLayoutHelper::render('document.icons', array('item' => $this->item, 'user' => $this->user, 'uri' => $this->uri)); ?>

  <?php $useDefList = ($params->get('show_modify_date') || $params->get('show_publish_date') || $params->get('show_create_date')
		       || $params->get('show_hits') || $params->get('show_category') || $params->get('show_parent_category')
		       || $params->get('show_author') ); ?>

  <?php if ($useDefList) : ?>
    <?php echo JLayoutHelper::render('document.info_block', array('item' => $item, 'params' => $params)); ?>
  <?php endif; ?>

  <?php if($item->params->get('show_intro')) : ?>
    <?php echo $item->intro_text; ?>
  <?php endif; ?>

  <?php if(!empty($item->full_text)) : ?>
    <?php echo $item->full_text; ?>
  <?php endif; ?>

  <?php if($params->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
	  <?php $this->item->tagLayout = new JLayoutFile('joomla.content.tags'); 
		echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
  <?php endif; ?>
</div>
