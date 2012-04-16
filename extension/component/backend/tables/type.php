<?php
defined('_JEXEC') or die();

class J4schemaTableType extends FOFTable
{
	function __construct($table, $key, $db)
	{
		parent::__construct($table, 'id_types', $db);
	}
}