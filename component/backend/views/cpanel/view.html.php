<?php
/**
 * @package 	J4Schema
 * @copyright 	Copyright (c)2011-2014 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 * @since 		1.0
 */

defined('_JEXEC') or die();

class J4schemaViewCpanel extends F0FViewHtml
{
    var $updatePluginCheck;

	public function onDisplay($tpl = null)
	{
        // Run the automatic update site refresh
        /** @var J4schemaModelUpdates $updateModel */
        $updateModel = F0FModel::getTmpInstance('Updates', 'J4schemaModel');
        $updateModel->refreshUpdateSite();

        $this->updatePluginCheck = $this->getModel()->checkUpdatePlugin();

		return;
	}
}