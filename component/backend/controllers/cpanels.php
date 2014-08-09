<?php
/**
 * @package 	J4Schema
 * @copyright 	Copyright (c)2011-2014 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 * @since 		1.0
 */
// Protect from unauthorized access
defined('_JEXEC') or die();

class J4schemaControllerCpanels extends FOFController
{
	public function execute($task) {
		if($task != 'reinstalljce') $task = 'browse';
		parent::execute($task);
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