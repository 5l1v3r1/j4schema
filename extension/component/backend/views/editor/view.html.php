<?php
defined('_JEXEC') or die();

class J4schemaViewEditor extends FOFViewHtml
{
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