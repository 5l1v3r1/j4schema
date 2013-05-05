<?php
/**
 * @package J4Schema
 * @copyright Copyright (c)2011 Davide Tampellini
 * @license GNU General Public License version 3, or later
 * @since 5.0
 */

class J4SchemaHelperBridge
{
	static function getToken()
	{
		if(version_compare(JVERSION, '3.0.0', 'ge')){
			return JFactory::getSession()->getToken();
		}
		else{
			return JUtility::getToken();
		}
	}
}