<?php
/**
 * @package 	J4Schema
 * @category	J4SchemaPro
 * @copyright 	Copyright (c)2011 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

class J4schemaModelTokens extends FOFModel
{
	public function buildQuery($overrideLimits)
	{
		$db = JFactory::getDbo();

		$query = FOFQueryAbstract::getNew($db)
					->select('*')
					->from('#__j4schema_tokens');

		$order = $this->getState('filter_order', 'to_name', 'cmd');
		if(!in_array($order, array_keys($this->getTable()->getData()))) $order = 'to_name';

		$dir = $this->getState('filter_order_Dir', 'ASC', 'cmd');
		$query->order($order.' '.$dir);

		return $query;
	}
}