<?php
defined('_JEXEC') or die();

class J4schemaModelTypes extends FOFModel
{
	function buildQuery($overrideLimits = false)
	{
		$db = $this->getDbo();
		$query = FOFQueryAbstract::getNew($db);

		//if i'm getting values in json format, probably i need them for the tree
		//so i wipe out the select clause and rebuild it
		if(FOFInput::getVar('format') == 'json')
		{
			$query = parent::buildQuery(true);

			$query->clear('select')
				  ->clear('order')
				  ->select('id_types, ty_children');

			//check if i'm requesting the root
			$parent = $this->getState('ty_parent');
			if(empty($parent))	$query->where("ty_parent = ''");
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
					->from('#__j4s_types')
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
}