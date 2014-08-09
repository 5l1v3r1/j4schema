<?php
/**
 * @package 	J4Schema
 * @copyright 	Copyright (c)2011-2014 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 * @since 		1.0
 */
// Protect from unauthorized access
defined('_JEXEC') or die();

class J4schemaControllerAttributes extends F0FController
{
	function __construct($config = array())
	{
		parent::__construct($config);

		$this->registerTask('getDescr', 'read');
	}
}