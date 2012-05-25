<?php
/**
 * @package 	J4Schema
 * @copyright 	Copyright (c)2011 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 * @since 		1.0
 */

defined('_JEXEC') or die();

class J4schemaModelTypes extends FOFModel
{
	public function &getItemList($overrideLimits = false, $group = '')
	{
		if(FOFInput::getVar('format') == 'json') $overrideLimits = true;
		return parent::getItemList($overrideLimits, $group);
	}

	function buildQuery($overrideLimits = false)
	{
		$db = $this->getDbo();
		$query = FOFQueryAbstract::getNew($db);

		//if i'm getting values in json format, probably i need them for the tree
		//so i wipe out the select clause and rebuild it
		if(FOFInput::getVar('format') == 'json')
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
	 * @see FOFModel::onProcessList()
	 */
	function onProcessList(&$resultArray)
	{
		//organize data only if i'm in a json view
		if(FOFInput::getVar('format') != 'json') return;

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

		$query = FOFQueryAbstract::getNew($db)
					->select('id_types, ty_children')
					->from('#__j4schema_types')
					->where('ty_parent = '.$db->Quote($parent));

		$rows = $db->setQuery($query)->loadObjectList();

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
		$id_types = FOFInput::getVar('id_types');
		$table = $this->getTable($this->table);
		$table->load($id_types);

		return $table;
	}
}