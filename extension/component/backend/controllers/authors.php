<?php
/**
 * @package 	J4Schema
 * @category	J4SchemaPro
 * @copyright 	Copyright (c)2011 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

class J4schemaControllerAuthors extends FOFController
{
	public function synchAuthors()
	{
		$model = $this->getThisModel();

		if($model->synchAuthors())
		{
			$msg = JText::_('COM_J4SCHEMA_SYNCH_AUTH_OK');
		}
		else
		{
			$msg = JText::_('COM_J4SCHEMA_SYNCH_AUTH_ERR');
			$type = 'error';
		}

		$this->setRedirect('index.php?option=com_j4schema&view=authors', $msg, $type);
	}
}