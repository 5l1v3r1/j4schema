<?php
defined('_JEXEC') or die();

class J4schemaViewTypes extends FOFViewJson
{
	/**
	 * Since I'm using more than a JSON layout, I have to override the standard onRead method,
	 * so I can set the correct $tpl to use
	 *
	 * @see FOFViewHtml::onRead()
	 */
	function onRead($tpl = null)
	{
		$layout = FOFInput::getVar('layout');

		switch ($layout)
		{
			case 'default_descr':
				$layout = 'default';
				$tpl = 'descr';
			break;
		}

		return parent::onRead($tpl);
	}
}