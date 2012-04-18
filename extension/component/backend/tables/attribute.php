<?php
defined('_JEXEC') or die();

class J4schemaTableAttribute extends FOFTable
{
	function __construct($table, $key, $db)
	{
		parent::__construct('#__j4schema_properties', 'id_properties', $db);
	}
}