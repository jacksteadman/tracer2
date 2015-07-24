#!/usr/bin/php
<?php
ini_set('date.timezone', 'UTC');

$action = [
	'category' => 'test',
	'name' => 'test_action',
	'data' => [ 'foo' => 'bar', 'baz' => 'foo' ],
	'tags' => [ 'tag1', 'tag2', 'tag3' ],
	'sources' => [ 'source1' ],
];


echo json_encode($action);
