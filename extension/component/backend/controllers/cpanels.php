<?php

// Protect from unauthorized access
defined('_JEXEC') or die();

class J4schemaControllerCpanels extends FOFController
{
	public function execute($task) {
		$task = 'browse';
		parent::execute($task);
	}
}