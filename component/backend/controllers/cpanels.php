<?php
/**
 * @package 	J4Schema
 * @copyright 	Copyright (c)2011-2014 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 * @since 		1.0
 */
// Protect from unauthorized access
defined('_JEXEC') or die();

class J4schemaControllerCpanels extends F0FController
{
	public function execute($task)
    {
        if (!in_array($task, array('updateinfo', 'reinstalljce')))
        {
            $task = 'browse';
        }

		parent::execute($task);
	}

    public function updateinfo()
    {
        /** @var J4schemaModelUpdates $updateModel */
        $updateModel = F0FModel::getTmpInstance('Updates', 'J4schemaModel');
        $updateInfo  = (object)$updateModel->getUpdates();

        $result = '';

        if ($updateInfo->hasUpdate)
        {
            $strings = array(
                'header'  => JText::sprintf('COM_J4SCHEMA_UPDATE_AVAILABLE', $updateInfo->version),
                'button'  => JText::sprintf('COM_J4SCHEMA_CPANEL_MSG_UPDATENOW', $updateInfo->version),
                'infourl' => $updateInfo->infoURL,
                'infolbl' => JText::_('COM_J4SCHEMA_CPANEL_MSG_MOREINFO'),
            );

            $result = <<<ENDRESULT
	<div class="alert alert-warning">
        <strong>{$strings['header']}</strong>

        <a style="margin-left:10px" href="index.php?option=com_installer&view=update" class="btn btn-small btn-primary pull-right">
				{$strings['button']}
        </a>

        <a href="{$strings['infourl']}" target="_blank" class="btn btn-small btn-info pull-right">
            {$strings['infolbl']}
        </a>

        <div class="clearfix"></div>
	</div>
ENDRESULT;
        }

        echo '###' . $result . '###';

        // Cut the execution short
        JFactory::getApplication()->close();
    }

	public function reinstalljce()
	{
		jimport('joomla.filesystem.folder');

		$rc = JFolder::copy(JPATH_ROOT.'/administrator/components/com_j4schema/jce/j4schema',
							JPATH_ROOT.'/components/com_jce/editor/tiny_mce/plugins/j4schema', '', true);

		if($rc){
			$msg = JText::_('COM_J4SCHEMA_JCE_REINSTALL_OK');
		}
		else
		{
			$msg  = JText::_('COM_J4SCHEMA_JCE_REINSTALL_KO');
			$type = 'notice';
		}

		$this->setRedirect('index.php?option=com_j4schema', $msg, $type);
	}
}