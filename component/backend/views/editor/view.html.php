<?php
/**
 * @package 	J4Schema
 * @copyright 	Copyright (c)2011-2014 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 * @since 		1.0
 */

defined('_JEXEC') or die();

class J4schemaViewEditor extends F0FViewHtml
{
	function __construct($config = array())
	{
		$config['helper_path'] = JPATH_COMPONENT_ADMINISTRATOR.'/helpers';

		parent::__construct($config);

		//I add the backend template paths here, so F0F has already did his work
		$this->addTemplatePath(JPATH_COMPONENT_ADMINISTRATOR.'/views/editor/tmpl');
	}

	function display($tpl = null)
	{
		$this->loadHelper('checks');

		$warnings = J4schemaHelperChecks::fullCheck();

		if($warnings)
		{
			$this->warnings = $warnings;
			$tpl = 'warnings';
		}

		parent::display($tpl);
	}

	/**
	 * Override of standard onAdd method, I don't need to query the database
	 *
	 */
	function onRead($tpl = null)
	{
		return true;
	}
}