<?php
/**
 * @package 	J4Schema
 * @copyright 	Copyright (c)2011-2014 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 * @since 		1.0
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

if(file_exists(JPATH_ROOT.'/media/com_j4schema/js/pro.js'))	define('J4SCHEMA_PRO', 1);
else														define('J4SCHEMA_PRO', 0);

if(!J4SCHEMA_PRO)
{
	$jlang = JFactory::getLanguage();
	$jlang->load('com_j4schema', JPATH_ADMINISTRATOR, 'en-GB', true);
	$jlang->load('com_j4schema', JPATH_ADMINISTRATOR, null, true);

	echo '<h2>'.JText::_('COM_J4SCHEMA_BACKEND_ONLY').'</h2>';
	return;
}

require_once JPATH_COMPONENT_SITE.'/j4schema_frontend.php';