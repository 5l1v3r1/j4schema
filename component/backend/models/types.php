<?php
/**
 * @package 	J4Schema
 * @copyright 	Copyright (c)2011-2014 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 * @since 		1.0
 */

defined('_JEXEC') or die();

class J4schemaModelTypes extends F0FModel
{
	public function &getItemList($overrideLimits = false, $group = '')
	{
	    $format = $this->input->getString('format', '');

		if($format == 'json') $overrideLimits = true;
		return parent::getItemList($overrideLimits, $group);
	}

	function buildQuery($overrideLimits = false)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		//if i'm getting values in json format, probably i need them for the tree
		//so i wipe out the select clause and rebuild it
        $format = $this->input->getString('format', '');

		if($format == 'json')
		{
			//on frontend i really don't know why i have var named 'id' initialized on 'index.php' (!!!)
			$query->select('id_types, ty_children')
				  ->from('#__j4schema_types');

			//check if i'm requesting the root
			$parent = $this->getState('ty_parent', '');
			$query->where("ty_parent = ".$db->quote($parent));
		}
		else
		{
			//@TODO To complete when i'll add edit support for types
			$query = parent::buildQuery($overrideLimits);
		}

		return $query;
	}

	/**
	 * Organize data for tree view
	 *
	 * @see F0FModel::onProcessList()
	 */
	function onProcessList(&$resultArray)
	{
		//organize data only if i'm in a json view
		$format = $this->input->getString('format', '');

		if($format != 'json') return;

		$i = 0;
		foreach($resultArray as $row)
		{
			$return[$i]['property']['name'] = $row->id_types;
			$return[$i]['property']['id'] 	= $row->id_types;

			if($row->ty_children) $return[$i]['children'] = $this->getTypes($row->id_types);

			$i++;
		}

		$resultArray = $return;
	}

	/**
	 * Recursive function to get all the children of a type
	 *
	 * @param 	string $parent Parent type
	 *
	 * @return 	array
	 */
	function getTypes($parent = '')
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
					->select('id_types, ty_children')
					->from('#__j4schema_types')
					->where('ty_parent = '.$db->Quote($parent));

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		if(!$rows) return "";
		else
		{
			$i = 0;
			foreach($rows as $row)
			{
				$return[$i]['property']['name'] = $row->id_types;
				$return[$i]['property']['id'] 	= $row->id_types;

				if($row->ty_children) $return[$i]['children'] = $this->getTypes($row->id_types);

				$i++;
			}
		}

		return $return;
	}

	function getDescr()
	{
		$id_types = $this->input->getString('id_types', '');

		$table = $this->getTable($this->table);
		$table->load($id_types);

		return $table;
	}
}