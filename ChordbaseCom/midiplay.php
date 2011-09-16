<?php

if ($_SERVER['HTTP_REFERER'] != ""
	&& !strstr($_SERVER['HTTP_REFERER'], $_SERVER['SERVER_NAME']))
	die ("REFUSED");

require('classes/midi.class.php');

$params = $_GET;

$instrument = $_GET["instr"];
$chordNotes = explode("_",$_GET["notes"]);

if ($chordNotes)
{
	$midi = new Midi();
	$midi->open(480);
	$midi->setBpm(120);
	$track = $midi->newTrack() - 1;
	$midi->addMsg($track, "0 PrCh ch=1 p=$instrument");

	$time = 20;

	foreach ($chordNotes as $noteDeslocation)
	{
		$midi->addMsg($track, "$time On ch=1 n=$noteDeslocation v=127");
		$time += 20;
	}
	$time += 1000;
	foreach ($chordNotes as $noteDeslocation)
	{
		$midi->addMsg($track, "$time Off ch=1 n=$noteDeslocation v=127");
	}
	foreach ($chordNotes as $noteDeslocation)
	{
		$midi->addMsg($track, "$time On ch=1 n=$noteDeslocation v=127");
		$time += 150;
	}
	$time += 1500;
	foreach ($chordNotes as $noteDeslocation)
	{
		$midi->addMsg($track, "$time Off ch=1 n=$noteDeslocation v=127");
	}

	$midi->addMsg($track, "$time Meta TrkEnd");

	$data = $midi->getMid();
}

header("Content-type: audio/midi");
header("Content-Disposition: attachment; filename=chord.mid");

echo $data;

exit;

?>