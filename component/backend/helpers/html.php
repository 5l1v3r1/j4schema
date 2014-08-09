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
        return JHtml::_('grid.published', $published, $i);
	}

	public static function getFrontendTemplate()
	{
		$db = JFactory::getDbo();

        //get default template name
        $query = $db->getQuery(true)
                    ->select('template')
                    ->from('#__template_styles')
                    ->where('client_id = 0')
                    ->where('home = 1');
        $db->setQuery($query);

        return $db->loadResult();
	}
}