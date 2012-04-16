<?php
defined('_JEXEC') or die();

class J4schemaModelAttributes extends FOFModel
{
	function buildQuery($overridelimits)
	{
		$db = $this->getDbo();
		$query = FOFQueryAbstract::getNew($db);

		//if i'm getting values in json format, probably i need them for the tree
		//so i wipe out the select clause and rebuild it
		if(FOFInput::getVar('format') == 'json')
		{
			$query->select('*')
				  ->from('#__j4schema_types')
				  ->leftJoin('#__j4schema_type_prop ON id_type = id_types')
				  ->leftJoin('#__j4schema_properties ON id_properties = id_property');

			//check if i'm requesting the root
			$type = $this->getState('id_types');
			$query->where("id_types = ".$db->quote($type));
		}
		else
		{
			//@TODO To complete when i'll add edit support for attribs
			$query = parent::buildQuery($overrideLimits);
		}

		return $query;
	}

	function onProcessList(&$resultArray)
	{
		if(FOFInput::getVar('format') != 'json') return;

		if($resultArray[0]->id_properties)
		{
			$i = 0;
			$return[0]['property']['name'] = $this->getState('id_types');;
			foreach($rows as $row)
			{
				$child[$i]['property']['name'] = $resultArray->id_properties;
				$i++;
			}

			$return[$level]['children'] = $child;
		}
		else
		{
			$return = array();
		}

		if($rows[0]->ty_parent)
		{
			$return = array_merge($return, $this->getAttrib($resultArray[0]->ty_parent, 1));
		}

		$resultArray = $return;
	}
}