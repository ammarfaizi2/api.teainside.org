<?php

require __DIR__."/../third_party/tex2png/autoload.php";

use Gregwar\Tex2png\Tex2png;


if (isset($_GET["exp"]) && is_string($_GET["exp"])) {

	$expr = $_GET["exp"];

	if (strpos($expr, "\\int") !== false) {
		$expr = str_replace("\\int ", "\\int\\(pure_space)", $expr);
		$expr = implode("\\;d", preg_split("/(?<=[^\\\\][^c])d/", $expr));
	}

	$expr = str_replace(" ", "\\,", trim($expr));
	$expr = str_replace(
		[
			"\\(pure_space)",
			"π",
			"\\erf"
		],
		[
			" ",
			"\\pi",
			"\,erf"
		],
		$expr
	);

	$d = isset($_GET["d"]) ? (int)$_GET["d"] : 155;
	$hash = sha1($expr.$d);
	$st = new Tex2png($expr, $d);
	if (file_exists(__DIR__."/latex/{$hash}.png")) {
		$st->error = null;
	} else {
		$st->saveTo(__DIR__."/latex/{$hash}.png")->generate();
	}

	if ($st->error) {
		header("Content-Type: application/json");
		echo json_encode(["error" => $st->error->__toString()], JSON_UNESCAPED_SLASHES);
	} else {
		header("Content-Type: image/png");
		readfile(__DIR__."/latex/{$hash}.png");
	}
	exit;
}

print "no_response";
