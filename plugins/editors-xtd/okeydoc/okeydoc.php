<?php
// no direct access
defined('_JEXEC') or die;

/**
 * Editor Document button
 *
 * @package		Joomla.Plugin
 * @subpackage	Editors-xtd.article
 * @since 1.5
 */
class plgButtonOkeydoc extends JPlugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = true;


	/**
	 * Display the button
	 *
	 * @return array A three element array of (document_title, document_url, object)
	 */
	function onDisplay($name)
	{
		/*
		 * Javascript to insert the link
		 * View element calls jSelectDocument when an document is clicked
		 * jSelectDocument creates the link tag, sends it to the editor,
		 * and closes the select frame.
		 */
		$js = "function insertDocumentLink(title, downloadURL) {
			var tag = '<a href='+'\"'+downloadURL+'\" target=\"_blank\">'+title+'</a>';
			jInsertEditorText(tag, '".$name."');
			SqueezeBox.close();
		}";

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);

		JHtml::_('behavior.modal');

		/*
		 * Use the built-in element view to select the document.
		 * Currently uses blank class.
		 */
		$link = 'index.php?option=com_okeydoc&amp;view=documents&amp;layout=modal&amp;tmpl=component&amp;function=insertDocumentLink&amp;'.JSession::getFormToken().'=1';

		$button = new JObject();
		$button->set('modal', true);
		$button->set('class', 'btn');
		$button->set('link', $link);
		$button->set('text', JText::_('PLG_DOCUMENT_BUTTON_OKEYDOC'));
		$button->set('name', 'okeydoc');
		$button->set('options', "{handler: 'iframe', size: {x: 770, y: 400}}");

		return $button;
	}
}
