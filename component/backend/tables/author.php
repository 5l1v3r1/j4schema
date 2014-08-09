<?php
/**
 * @package 	J4Schema
 * @category	J4SchemaPro
 * @copyright 	Copyright (c)2011-2014 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

class J4schemaTableAuthor extends FOFTable
{
	function __construct($table, $key, $db)
	{
		parent::__construct($table, 'id_authors', $db);
	}

	function check()
	{
		$result = true;

		if(empty($this->at_userid))
		{
			$this->setError(JText::_('COM_J4SCHEMA_AUTH_ERR_USERID'));
			$result = false;
		}

		if(empty($this->at_profile))
		{
			$this->setError(JText::_('COM_J4SCHEMA_AUTH_ERR_PROFILE'));
			$result = false;
		}

		//user insert the complete url of profile
		if(strpos($this->at_profile, '/') !== false)
		{
			$this->at_profile = preg_replace('#/0/|[^\d]#', '', $this->at_profile);
		}

		return $result;
	}
}