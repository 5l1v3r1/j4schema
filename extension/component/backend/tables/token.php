<?php
/**
 * @package 	J4Schema
 * @category	J4SchemaPro
 * @copyright 	Copyright (c)2011 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 * @since 		1.0
 */

defined('_JEXEC') or die();

class J4schemaTableToken extends FOFTable
{
	function __construct($table, $key, $db)
	{
		parent::__construct($table, 'id_tokens', $db);
	}

	function check()
	{
		$result = true;

		if(empty($this->to_name))
		{
			$this->setError(JText::_('COM_J4SCHEMA_TOKEN_ERR_NAME'));
			$result = false;
		}

		if(empty($this->to_integration))
		{
			$this->setError(JText::_('COM_J4SCHEMA_TOKEN_ERR_INTEG'));
			$result = false;
		}

		return $result;
	}
}