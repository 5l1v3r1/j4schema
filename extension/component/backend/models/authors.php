<?php
/**
 * @package 	J4Schema
 * @category	J4SchemaPro
 * @copyright 	Copyright (c)2011 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

class J4schemaModelAuthors extends FOFModel
{
	public function buildQuery($overrideLimits)
	{
		$db = JFactory::getDbo();

		$query = FOFQueryAbstract::getNew($db)
					->select('authors.*, users.*, COUNT(content.id) as articles')
					->from('#__j4schema_authors authors')
					->innerJoin('#__users users ON users.id = at_userid')
					->leftJoin('#__content content ON created_by = at_userid')
					->group('at_userid');

		$order = $this->getState('filter_order', 'name', 'cmd');
		if(!in_array($order, array_keys($this->getTable()->getData()))) $order = 'name';

		$dir = $this->getState('filter_order_Dir', 'ASC', 'cmd');
		$query->order($order.' '.$dir);

		return $query;
	}

	public function synchAuthors()
	{
		//@TODO Insert only users with edit permissions
		//(check on installed components)
		$db = JFactory::getDbo();

		$query = FOFQueryAbstract::getNew($db)
					->select('at_userid')
					->from('#__j4schema_authors');
		$authors = $db->setQuery($query)->loadColumn();
		$authors[] = 0;

		$query = 'INSERT INTO '.$db->quoteName('#__j4schema_authors').
				 ' SELECT NULL, id, "" FROM '.$db->quoteName('#__users').
					' WHERE id NOT IN('.implode(',', $authors).')';

		$result = $db->setQuery($query)->query();

		return $result;
	}
}