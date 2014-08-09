<?php
/**
 * @package 	J4Schema
 * @category	J4SchemaPro
 * @copyright 	Copyright (c)2011-2014 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

class J4schemaHelperHtml
{
	public static function createPublishIcon($published, $i)
	{
		if(version_compare(JVERSION, '1.6.0', 'ge')){
			return JHtml::_('grid.published', $published, $i);
		}
		else{
			$dummy = new stdClass();
			$dummy->published = $published;
			return JHtml::_('grid.published', $dummy, $i);
		}
	}

	public static function getFrontendTemplate()
	{
		$db = JFactory::getDbo();

		if(version_compare(JVERSION, '1.6.0', 'ge')){
			//get default template name
			$query = FOFQueryAbstract::getNew()
						->select('template')
						->from('#__template_styles')
						->where('client_id = 0')
						->where('home = 1');
			$db->setQuery($query);
			return $db->loadResult();
		}
		else{
			$query = FOFQueryAbstract::getNew()
						->select('template')
						->from('#__templates_menu')
						->where('client_id = 0');
			$db->setQuery($query);
			return $db->loadResult();
		}
	}
}