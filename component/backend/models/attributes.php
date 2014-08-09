<?php
/**
 * @package 	J4Schema
 * @copyright 	Copyright (c)2011-2014 Davide Tampellini
 * @license 	GNU General Public License version 3, or later
 * @since 		1.0
 */

defined('_JEXEC') or die();

class J4schemaModelAttributes extends F0FModel
{
	public function buildQuery($overrideLimits = false)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		//if i'm getting values in json format, probably i need them for the tree
        $format = $this->input->getString('format', '');


		if($format == 'json')
		{
			$query->select('*')
				  ->from('#__j4schema_types')
				  ->leftJoin('#__j4schema_type_prop ON id_type = id_types')
				  ->leftJoin('#__j4schema_properties ON id_properties = id_property');

			//check if i'm requesting the root
			$type = $this->getState('id', '');
			$query->where("id_types = ".$db->quote($type));
		}
		else
		{
			//@TODO To complete when i'll add edit support for attribs
			$query = parent::buildQuery($overrideLimits);
		}

		return $query;
	}

	/**
	 * Organize data, so I can manage it better to create the Attrib tree.
	 * Inside there is a call to a recursive function {@see J4schemaModelAttributes::getAttrib} to fetch
	 * parents attribs, too
	 *
	 * @see F0FModel::onProcessList()
	 */
	function onProcessList(&$resultArray)
	{
	    $format = $this->input->getString('format', '');

		if($format != 'json') return;

		// put every property inside an associative array
		// $return[$level]['property']['name']  - Name of the attrib
		// $return[$level]['children']			- Attribs of parent type
		if($resultArray[0]->id_properties)
		{
			$i = 0;
			$return[0]['property']['name'] = $this->getState('id');
			foreach($resultArray as $row)
			{
				$child[$i]['property']['name'] = $row->id_properties;
				$i++;
			}

			$return[0]['children'] = $child;
		}
		else
		{
			$return = array();
		}

		//if type has a parent, let's get its attribs
		if($resultArray[0]->ty_parent)
		{
			$return = array_merge($return, $this->getAttrib($resultArray[0]->ty_parent, 1));
		}

		//reverse the array (so first the selected one and then parents) and flag
		//the first node to be expanded
		if(is_array($return) && count($return))
		{
			array_reverse($return);
			$return[0]['children'][0]['property']['expandTo'] = true;
		}

		$resultArray = $return;
	}

	/**
	 * Recursive function to get all parent attribs, organizing them into levels
	 *
	 * @param string  $type		Type to search for
	 * @param int	  $level	Level of nesting
	 */
	protected function getAttrib($type, $level = 0)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
				 ->select('*')
				 ->from('#__j4schema_types')
				 ->leftJoin('#__j4schema_type_prop ON id_type = id_types')
				 ->leftJoin('#__j4schema_properties ON id_properties = id_property')
				 ->where('id_types = '.$db->quote($type));
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		if(!$rows) return "";
		elseif($rows[0]->id_properties)
		{
			$i = 0;
			$return[$level]['property']['name'] = $type;
			foreach($rows as $row)
			{
				$child[$i]['property']['name'] = $row->id_properties;
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
			$return = array_merge($return, $this->getAttrib($rows[0]->ty_parent, $level + 1));
		}

		return $return;
	}

	function getDescr()
	{
		$db = JFactory::getDbo();

		$id_prop = $this->input->getString('id_attributes', '');

		$query = $db->getQuery(true)
					->select('pr_comment_plain as descr, pv_value as value')
					->from('#__j4schema_properties')
					->innerJoin('#__j4schema_prop_values ON id_properties = pv_id_properties')
					->where('id_properties = '.$db->quote($id_prop));
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		return $rows;
	}

	function &getItemList($overrideLimits = false, $group = '')
	{
		//if i'm getting values using json, i don't need the pagination
        $format = $this->input->getString('format', '');

		if($format == 'json')	$overrideLimits = true;

		return parent::getItemList($overrideLimits, $group);
	}
}