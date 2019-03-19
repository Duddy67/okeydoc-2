<?php
/**
 * @package Okey DOC 2
 * @copyright Copyright (c)2015 - 2019 Lucas Sanner
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;


/**
 * Build 2 input tags relative to the maximum file size allowed.
 * The first tag contains the value set by the user and make sure a proper size
 * format is typed.
 * The second tag displays the maximum size upload (in megabyte) allowed by php.ini. 
 */
class JFormFieldMaxfilesize extends JFormField
{
  protected $type = 'maxfilesize';


  /**
   * Method to get the field options.
   *
   * @return  array  The field option objects.
   */
  protected function getInput()
  {
    // Calculates the maximum size upload (in megabyte) allowed by php.ini. 
    $max_upload = (int)(ini_get('upload_max_filesize'));
    $max_post = (int)(ini_get('post_max_size'));
    $memory_limit = (int)(ini_get('memory_limit'));
    $upload_mb = min($max_upload, $max_post, $memory_limit);

    // Gets some attribute values.
    $default = $this->element->attributes()->default;
    $size = $this->element->attributes()->size;

    // Forces the user to type an integer. If the number is higher than the upload value, it
    // is replaced with the upload value.
    $js = 'var Okeydoc = {'."\n";
    $js .= 'checkFileSize : function(fileSize) {'."\n";
    $js .= '  var regex = /^[0-9]+$/;'."\n";
    $js .= '  fileSize.value = fileSize.value.match(regex);'."\n";
    $js .= '  if(fileSize.value > '.$upload_mb.') {'."\n";
    $js .= '    fileSize.value = 8;'."\n";
    $js .= '  }'."\n";
    $js .= ' }';
    $js .= '}';
    // Places the Javascript function into the html page header.
    $doc = JFactory::getDocument();
    $doc->addScriptDeclaration($js);

    // Builds the input tags.
    $html = '';
    $html .= '<input type="text" name="'.$this->name.'" id="'.$this->id.'" '.
             ' value="'.$this->value.'" default="'.$default.'" size="'.$size.'" onkeyup="javascript:Okeydoc.checkFileSize(this)" />';
    $html .= '<br /><input type="text" name="upload_mb" id="upload_mb" readonly="readonly" '.
             'class="readonly" value="  php.ini upload = '.$upload_mb.'M" />';

    return $html;
  }
}

