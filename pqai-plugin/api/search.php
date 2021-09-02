<?php

$query = $_GET['query'];
$token = 'test_token_asdf77bc3a9f'; // API Access token
$n = 100;  // No of results to be requested
$data = "[]";

if (!isset($query) or empty($query) or strlen($query) > 1000 or strlen($query) < 5) {
	echo $data;
} else {
	$url = 'https://api.projectpq.ai/search/102?q=' .  urlencode($query) . '&token=' . $token . '&n=' . $n;
	$contents = file_get_contents($url);
	if ($contents !== false) {
		$data = $contents;
	}
	echo $data;
}

?>