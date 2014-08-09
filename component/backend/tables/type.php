<?php
/**
 * @package 	J4Schema
 * @copyright 	Copyright (c)2011-2014 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 * @since 		1.0
 */

defined('_JEXEC') or die();

class J4schemaTableType extends F0FTable
{
	function __construct($table, $key, $db)
	{
		parent::__construct($table, 'id_types', $db);
	}
}