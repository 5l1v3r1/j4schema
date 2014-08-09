<?php
/**
 * @package 	J4Schema
 * @copyright 	Copyright (c)2011-2014 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 * @since 		1.0
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

class J4schemaControllerEditor extends F0FController
{
	/**
	 * I don't need to fetch data from database
	 *
	 * @see F0FController::read()
	 */
	public function read()
	{
		// Set the layout to item, if it's not set in the URL
		if(is_null($this->layout)) $this->layout = 'item';

		// Display
		$this->display(in_array('read', $this->cacheableTasks));
	}
}