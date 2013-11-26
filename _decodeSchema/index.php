<?php
// ???? HELPER ???
//require_once 'helper.php';

mysql_connect('localhost', 'root', '');
mysql_select_db('sviluppo2_5');

mysql_query('TRUNCATE raw74_j4schema_types');
mysql_query('TRUNCATE raw74_j4schema_type_prop');
mysql_query('TRUNCATE raw74_j4schema_properties');
mysql_query('TRUNCATE raw74_j4schema_prop_values');

// Il file è reperibile a questo indirizzo: http://schema.rdfs.org/all.json
$file = file_get_contents('all.json');
$json = json_decode($file);

#var_dump($json);
#var_dump($json->types);
#var_dump($json->properties);

$types = array_merge((array) $json->types, (array) $json->datatypes);

// PRIMA PASSATA: inserisco tutto direttamente
foreach($json->properties as $prop)
{
	$id 	 		= $prop->id;
	$comment 		= mysql_real_escape_string($prop->comment);
	$comment_plain  = mysql_real_escape_string($prop->comment_plain);
	$ranges  		= '';
	$url 	 		= 'http://schema.org/'.$prop->id;

	$query = "INSERT INTO raw74_j4schema_properties VALUES('$id','$comment','$comment_plain','','$url')";

	if(!mysql_query($query))
	{
		echo "Errore nell'inserire la propriet&agrave;: $id <br />";
		echo mysql_error()."<br />";
	}

	foreach($prop->ranges as $range)
	{
		$query = "INSERT INTO raw74_j4schema_prop_values VALUES('$id','$range', 0)";
		if(!mysql_query($query))
		{
			echo "Errore nell'inserire il valore: $range per la propriet&agrave;: $id <br />";
			echo mysql_error()."<br />";
		}
	}
}

foreach($types as $type)
{
	$id 	 		= $type->id;

	// prendo il parent più specifico. Ad esempio nel caso Thing -> CreativeWork -> WebPage
	// prendo WebPage.
	// 23.08.2012
	// Prima il primo elemento era il più specifico, ora è il contrario
	$ancestors		= $type->ancestors;
	$parent  		= array_pop($ancestors);
	$comment 		= mysql_real_escape_string($type->comment);
	$comment_plain 	= mysql_real_escape_string($type->comment_plain);
	$label	 		= $type->label;
	$url	 		= $type->url;
	$children		= count($type->subtypes);

	$query = "INSERT INTO raw74_j4schema_types VALUES( '$id',
												'$parent',
												'$comment',
												'$comment_plain',
												'$label',
												'$url',
												$children)";
	if(!mysql_query($query))
	{
		echo "Errore nell'inserire il tipo: $id <br />";
		echo "\t ".mysql_error()."<br />";
	}

	foreach($type->properties as $property)
	{
		$query = "INSERT INTO raw74_j4schema_type_prop VALUES('$id', '$property')";

		if(!mysql_query($query))
		{
			echo "Errore nell'inserire la propriet&agrave;: $property per il tipo: $id <br />";
			echo "\t ".mysql_error()."<br />";
		}
	}

	if($type->instances){
		foreach($type->instances as $enum_url)
		{
			$enum = str_replace('http://schema.org/', '', $enum_url);

			$query = "INSERT INTO raw74_j4schema_type_prop VALUES('$id', '$enum')";

			if(!mysql_query($query))
			{
				echo "Errore nell'inserire la propriet&agrave;: $enum (enum) per il tipo: $id <br />";
				echo "\t ".mysql_error()."<br />";
			}

			$query = "INSERT INTO raw74_j4schema_properties VALUES('$enum',
														'',
														'',
														'',
														'$enum_url')";

			if(!mysql_query($query))
			{
				echo "Errore nell'inserire la propriet&agrave; (enum): $enum <br />";
				echo mysql_error()."<br />";
			}

			$query = "INSERT INTO raw74_j4schema_prop_values VALUES('$enum','Enum', 1)";
			if(!mysql_query($query))
			{
				echo "Errore nell'inserire il valore: $range per la propriet&agrave;: $id <br />";
				echo mysql_error()."<br />";
			}
		}
	}
}

unset($json);

$file = file_get_contents('all.json');
$json = json_decode($file);

// SECONDA PASSATA: elimino le proprietà che sono già presente in un type padre dell'elemento corrente
foreach ($json->types as $type)
{
	$toDelete = array();

	if(!$type->ancestors) continue;

	foreach($type->properties as $property)
	{
		$query = "SELECT COUNT(*) FROM raw74_j4schema_type_prop WHERE id_property = '$property' ".
				" AND id_type IN('".implode("','", $type->ancestors)."')";
		$result = mysql_query($query);

		// la proprietà è già stata utilizzata, la segno come da cancellare
		$count = mysql_result($result, 0);
		if($count) $toDelete[] = $property;
	}

	if($toDelete)
	{
		$query = "DELETE FROM raw74_j4schema_type_prop WHERE id_type = '{$type->id}' ".
				" AND id_property IN ('".implode("','", $toDelete)."')";
		mysql_query($query);
	}
}
echo 'Operazione completata';