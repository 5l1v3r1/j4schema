<?php
/**
 * @package 	J4Schema
 * @category	J4SchemaPro
 * @copyright 	Copyright (c)2011 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 */

class J4schemaViewTokens extends FOFViewHtml
{
	function onDisplay($tpl = null)
	{
		//Override of standard onDisplay method to prevent wrong filter order / direction
		// when they are not set
		parent::onDisplay($tpl);

		$model = $this->getModel();

		$this->lists->set('order',		$model->getState('filter_order', 'to_name', 'cmd'));
		$this->lists->set('order_Dir',	$model->getState('filter_order_Dir', 'ASC', 'cmd'));

		return true;
	}
}