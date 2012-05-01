<?php
/**
 * @package 	J4Schema
 * @category	J4SchemaPro
 * @copyright 	Copyright (c)2011 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

class J4schemaToolbar extends FOFToolbar
{
	function onOverridesBrowse()
	{
		// Set toolbar title
		$subtitle_key = FOFInput::getCmd('option','com_foobar',$this->input).'_TITLE_'.strtoupper(FOFInput::getCmd('view','cpanel',$this->input));
		JToolBarHelper::title(JText::_( FOFInput::getCmd('option','com_foobar',$this->input)).' &ndash; <small>'.JText::_($subtitle_key).'</small>', str_replace('com_', '', FOFInput::getCmd('option','com_foobar',$this->input)));

		$bar = JToolBar::getInstance('toolbar');
		$bar->appendButton('Confirm', JText::_('COM_J4SCHEMA_CONFIRM_OVERRIDES'), 'new-style', JText::_('COM_J4SCHEMA_COPY_OVERRIDES'), 'copyOverrides', false);

		$this->renderSubmenu();
	}
}