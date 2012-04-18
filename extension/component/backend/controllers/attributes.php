<?php

// Protect from unauthorized access
defined('_JEXEC') or die();

class J4schemaControllerAttributes extends FOFController
{
	function __construct($config = array())
	{
		parent::__construct($config);

		$this->registerTask('getDescr', 'read');
	}
}