<?php
defined('_JEXEC') or die();

$return['descr'] = $this->item->descr ? $this->item->descr : 'No description provided';
$return['schema'] = $this->item->url;

echo json_encode($return);