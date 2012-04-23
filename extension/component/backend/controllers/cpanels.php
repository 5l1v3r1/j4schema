<?php
/**
 * @package 	J4Schema
 * @copyright 	Copyright (c)2011 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 * @since 		1.0
 */
// Protect from unauthorized access
defined('_JEXEC') or die();

class J4schemaControllerCpanels extends FOFController
{
	public function execute($task) {
		$task = 'browse';
		parent::execute($task);
	}
}