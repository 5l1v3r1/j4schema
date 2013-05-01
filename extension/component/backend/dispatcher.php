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
		if(!class_exists('LiveUpdate'))
		{
			// Load the correct version of LiveUpdate
			if(version_compare(JVERSION, '1.6.0', 'ge')){
				$folder = 'liveupdate_2.5';
			}
			else{
				$folder = 'liveupdate';
			}

			require_once JPATH_ADMINISTRATOR.'/components/com_j4schema/'.$folder.'/liveupdate.php';

			if($this->input instanceof FOFInput) {
			    $view = $this->input->getString('view', '');
			} else {
			    $view = FOFInput::getCmd('view','',$this->input);
			}
			if(($view == 'liveupdate')) {
				LiveUpdate::handleRequest();
				return;
			}
		}

		parent::dispatch();
	}
}