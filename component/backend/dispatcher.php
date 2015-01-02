<?php
/**
 * @package 	J4Schema
 * @copyright 	Copyright (c)2011-2014 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 * @since 		1.0
 */

defined('_JEXEC') or die();

class J4schemaDispatcher extends F0FDispatcher
{
	public function dispatch()
	{
        // Control Check
        $view = F0FInflector::singularize($this->input->getCmd('view', $this->defaultView));

        if ($view == 'liveupdate')
        {
            $url = JUri::base() . 'index.php?option=com_j4schema';
            JFactory::getApplication()->redirect($url);

            return;
        }

        include_once JPATH_ROOT . '/media/akeeba_strapper/strapper.php';
        AkeebaStrapper::bootstrap();

		parent::dispatch();
	}
}