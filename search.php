<? require_once('common.php');

if (!binnen_school()) {
	echo('[]'); // disabled for now
	exit;
}

$safe_week = (int)$_GET['wk'];
if ($safe_week != $_GET['wk']) {
	echo('[]');
	exit;
}

if (config('HIDE_STUDENTS')) {
	echo('[]');
	exit;
}

$week_id = mdb2_single_val("SELECT week_id FROM weken WHERE week = $safe_week");
if (!$week_id) {
	echo('[]');
	exit;
}

$file_id = mdb2_single_val("SELECT file_id FROM roosters WHERE week_id <= $week_id AND wijz_id = 0 ORDER BY rooster_id DESC LIMIT 1");
if (!$file_id) {
	echo('[]');
	exit;
}

$query = <<<EOT
SELECT entities.entity_name id, CONCAT(name, ' (', stamklassen.entity_name, '/', entities.entity_name, ')') value
FROM names
JOIN entities ON names.entity_id = entities.entity_id
JOIN grp2ppl ON ppl_id = names.entity_id
JOIN entities AS stamklassen ON stamklassen.entity_id = lesgroep_id
WHERE file_id_basis = $file_id AND stamklassen.entity_type = 5 AND name LIKE '%%%w%%' LIMIT 15
EOT;

$result = mdb2_query($query, $_GET['term']);

header("Content-Type: application/json; charset=UTF-8");

$array = $result->fetchAll(MDB2_FETCHMODE_ASSOC);

echo(json_encode($array));

?>
