<?php
/**
 * @package 	J4Schema
 * @category	J4SchemaPro
 * @copyright 	Copyright (c)2011 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

class J4schemaHelperHtml
{
	public static function getFrontendTemplate()
	{
		$db = JFactory::getDbo();

		//get default template name
		$query = $db->getQuery(true)
					->select('template')
					->from('#__template_styles')
					->where('client_id = 0')
					->where('home = 1');
		return $db->setQuery($query)->loadResult();
	}
}