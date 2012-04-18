<?php
defined('_JEXEC') or die();

$return['descr'] = $this->item->ty_comment_plain ? $this->item->ty_comment_plain : 'No description provided';
$return['schema'] = $this->item->ty_url;

echo json_encode($return);