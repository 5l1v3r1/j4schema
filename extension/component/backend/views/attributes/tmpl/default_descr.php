<?php
defined('_JEXEC') or die();

$dataTypes = array('text', 'number', 'date', 'duration', 'integer', 'url', 'enum');

$return['descr']  = $this->items[0]->descr ? $this->items[0]->descr : 'No description provided';
$return['schema'] = FOFInput::getVar('id_attributes');

foreach($this->items as $row)
{
	$values[]  = $row->value;
	//property value is not a "fixed" one (ie text, number or date)
	if(!in_array(strtolower($row->value) , $dataTypes ))
	{
		if(J4SCHEMA_PRO)	$values_descr[] = 'a "<span class="expandToType">'.$row->value.'</span>" type';
		else				$values_descr[] = 'a "'.$row->value.'" type';
	}
	else
	{
		$values_descr[] = 'any '.$row->value;
	}
}

$return['value'] 		= $values;
$return['value_descr']	= ucfirst(implode($values_descr, ' or '));

echo json_encode($return);