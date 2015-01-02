<?php
/**
 * @package J4Schema
 * @copyright Copyright (c)2011-2014 Davide Tampellini
 * @license GNU General Public License version 3, or later
 * @since 5.0
 */

class J4SchemaHelperBridge
{
	static function mootools()
	{
		if(version_compare(JVERSION, '3.0', 'ge')){
			JHTML::_('behavior.framework', true);
		}
		else{
			JHTML::_('behavior.mootools');
		}
	}

	static function getToken()
	{
		if(version_compare(JVERSION, '3.0', 'ge')){
			return JFactory::getSession()->getToken();
		}
		else{
			return JUtility::getToken();
		}
	}
}