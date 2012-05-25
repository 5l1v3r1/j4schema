<?php
/**
 * @package 	J4Schema
 * @copyright 	Copyright (c)2011 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 * @since 		1.0
 */

defined('_JEXEC') or die();

class J4schemaDispatcher extends FOFDispatcher
{
	public function dispatch()
	{
		// Handle Live Update requests
		if(!class_exists('LiveUpdate')) {
			require_once JPATH_ADMINISTRATOR.'/components/com_j4schema/liveupdate/liveupdate.php';
			if((FOFInput::getCmd('view','',$this->input) == 'liveupdate')) {
				LiveUpdate::handleRequest();
				return;
			}
		}

		parent::dispatch();
	}
}