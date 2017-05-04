<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, minimum-scale=1, initial-scale=1, user-scalable=yes">

	<title>grain-read-more demo</title>

	<script src="../bower_components/webcomponentsjs/webcomponents-lite.js"></script>

	<style>
		body {
			margin: 0;
		}
	</style>

</head>
<body>
<?php
	define('BOWER_PATH', __DIR__ . '/../bower_components');

	require_once (__DIR__ . '/../OptionsClass.php');
	$options = new OptionsClass([
		'a' => [
			'aa' => 'aa-value'
		],
		'b' => [
			'ba' => 'ba-value',
			'bb' => 'bb-value'
		]
	]);
	var_dump($options);
	var_dump($options->getOption('b.bb'));

?>
</body>
</html>