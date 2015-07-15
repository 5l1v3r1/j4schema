<?php
/**
 * @package 	J4Schema
 * @category	J4SchemaPro
 * @copyright 	Copyright (c)2011-2014 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

class J4schemaHelperSelect
{
	protected static $cache = array();

	public static function integration($id, $selected = null, $attribs = '')
	{
		if(!isset(self::$cache['integration']))
		{
			$opt[] = array('value' => ''		, 	'text' => ' - '.JText::_('COM_J4SCHEMA_SELECT').' - ');
			$opt[] = array('value' => 'joomla'	, 	'text' => 'Joomla!');
			$opt[] = array('value' => 'k2'		, 	'text' => 'K2');
			$opt[] = array('value' => 'virtuemart', 'text' => 'Virtuemart');

			self::$cache['integration'] = $opt;
		}

		return self::genericlist(self::$cache['integration'], $id, $attribs, $selected, $id);
	}

	public static function tokenType($id, $selected = null, $attribs = '')
	{
		if(!isset(self::$cache['tokenType']))
		{
			$opt[] = array('value' => '', 'text' => ' - '.JText::_('COM_J4SCHEMA_SELECT').' - ');
			$opt[] = array('value' => 'date', 'text' => JText::_('COM_J4SCHEMA_DATE'));
			$opt[] = array('value' => 'link', 'text' => 'Link');
			$opt[] = array('value' => 'meta', 'text' => 'Meta');
			$opt[] = array('value' => 'text', 'text' => JText::_('COM_J4SCHEMA_PLAIN_TEXT'));

			self::$cache['tokenType'] = $opt;
		}

		return self::genericlist(self::$cache['tokenType'], $id, $attribs, $selected, $id);
	}

	protected static function genericlist($list, $name, $attribs, $selected, $idTag)
	{
		return JHTML::_('select.genericlist', $list, $name, $attribs, 'value', 'text', $selected, $idTag);
	}
}