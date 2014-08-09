<?php
/**
 * @package 	J4Schema
 * @copyright 	Copyright (c)2011-2014 Davide Tampellini
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
			// Load the correct version of LiveUpdate (this check is needed for local development only)
            if(file_exists(JPATH_ADMINISTRATOR.'/components/com_j4schema/liveupdate_2.5/liveupdate.php'))
            {
                require_once JPATH_ADMINISTRATOR.'/components/com_j4schema/liveupdate_2.5/liveupdate.php';
            }
            else
            {
                require_once JPATH_ADMINISTRATOR.'/components/com_j4schema/liveupdate/liveupdate.php';
            }


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

        include_once JPATH_ROOT . '/media/akeeba_strapper/strapper.php';
        AkeebaStrapper::bootstrap();

		parent::dispatch();
	}
}