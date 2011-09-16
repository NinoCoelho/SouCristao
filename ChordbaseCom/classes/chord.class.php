<?php

define("_MAJOR", "M");
define("_MINOR", "m");
define("_DIM", "dim");
define("_SUS", "sus");
define("_AUG", "aum");

require('midi.class.php');

class Chord {

	var $typeConvTable = array(
		_MAJOR => "M",
		_MINOR => "m",
		_DIM => "\xB0",
		_SUS => "sus",
		_AUG => "aum"
		);

	var $chordSignature = "";
	var $chordStringRepresentation = "";
	var $chordHTMLRepresentation = "";

	var $scaleMajor = array(0, 2, 4, 5, 7, 9, 11, 12, 14, 16, 17, 19, 21, 23);
	var $scaleMinor = array(0, 2, 3, 5, 7, 9, 11, 12, 14, 15, 17, 19, 21, 23);
	var $scaleDim = array(0, 2, 3, 5, 6, 8, 9, 11, 12, 14, 15, 17, 18, 20, 21, 24);

	var $mainNote;
	var $category;

	var $type = "";
	var $altNotes = array();
	var $bassNote;

	var $chordComposition;

	var $scale;

	var $isDimSet = false;
	var $isMinorSet = false;
	var $isMajorSet = false;
	var $isSusSet = false;
	var $isAugSet = false;

	var $guitarChordNumberRepresentation = null;
	var $guitarBaseTuneNote = 40;

	function getCode()
	{
		$notes = $this->getChordBasicNotesAsNumbers();
		if ($this->getBassNote() != "")
			$code .= (chr(ord("A")+$note));
		array_multisort($notes);
		$code = "";
		foreach ($notes as $note)
		{
			$code .= (chr(ord("A")+$note));
		}
		return $code;
	}

	function setDim($booleanValue = true)
	{
		$this->isDimSet = $booleanValue;
	}

	function setMinor($booleanValue = true)
	{
		$this->isMinorSet = $booleanValue;
	}

	function setMajor($booleanValue = true)
	{
		$this->isMajorSet = $booleanValue;
	}

	function setSus($booleanValue = true)
	{
		$this->isSusSet = $booleanValue;
	}

	function setAug($booleanValue = true)
	{
		$this->isAugSet = $booleanValue;
	}

	function isDim()
	{
		return $this->isDimSet;
	}

	function isMinor()
	{
		return $this->isMinorSet;
	}

	function isMajor()
	{
		return $this->isMajorSet;
	}

	function isSus()
	{
		return $this->isSusSet;
	}

	function isAug()
	{
		return $this->isAugSet;
	}

	function setMainNote($mainNote) {
		$this->mainNote = $mainNote;
	}

	function getMainNote() {
		return $this->mainNote;
	}

	function setCategory($category) {
		$this->category = $category;
	}

	function getCategory() {
		return $this->category;
	}

	function getAltNotes() {
		return $this->altNotes;
	}

	function getNote($note, $octave = 0) {
		if ($note == "") return -1;
		$noteValue = ($octave * 12) + strpos("C D EF G A B", $note[0]);
		if ($note[1] == '#')
			$noteValue ++;
		elseif ($note[1] == 'b') $noteValue --;
		return $noteValue;
	}

	function setBassNote($bassNote) {
		$this->bassNote = $bassNote;
	}

	function getBassNote() {
		return $this->bassNote;
	}

	function addAltNote($interval, $addAltNote = true) {
		$this->altNotes[count($this->altNotes)] .= $interval;
	}

	function getGuitarChordNumberRepresentation()
	{
		return $this->$guitarChordNumberRepresentation;
	}

	function setMajorChordComposition() {
		$this->setChordComposition(array (0, 4, 7));
		$this->scale = $this->scaleMajor;
	}

	function setMinorChordComposition() {
		$this->setChordComposition(array (0, 3, 7));
		$this->scale = $this->scaleMinor;
	}

	function setDimChordComposition() {
		$this->setChordComposition(array (0, 3, 6));
		$this->scale = $this->scaleDim;
	}

	function setChordComposition($chordComposition) {
		$this->chordComposition = $chordComposition;
	}

	function getChordComposition() {
		return $this->chordComposition;
	}

	function getChordBasicNotesAsNumbers() {
		$basicComposition = $this->getChordComposition();

		$baseNote = $this->getNote($this->getMainNote());

		foreach ($basicComposition as $key => $note)
		{
			$basicComposition[$key] = ($note+$baseNote)%12;
		}
		array_multisort($basicComposition);
		return array_unique($basicComposition);
	}

	function getChordNotesAsNumbers($octave = 5, $bassOctave = 4) {
		$chordComposition = $this->getChordComposition();

		$baseNote = $this->getNote($this->getMainNote(), $octave -1);

		if ($this->getBassNote()) {
			$bassNote = $this->getNote($this->bassNote, $bassOctave -1);
			if ($chordComposition[0] % 12 == $bassNote % 12)
				$chordComposition[0] += 12;
		} else {
			$bassNote = $baseNote - (($octave - $bassOctave) * 12);
			if ($chordComposition[0] == 0)
				$chordComposition[0] = 12;
		}

		foreach ($chordComposition as $index => $desloc)
			$chordComposition[$index] += $baseNote;

		array_unshift($chordComposition, $bassNote);
		array_multisort($chordComposition);

		return $chordComposition;
	}

	function getPianoChordComposition($octave = 5) {
		$chordComposition = $this->getChordComposition();

		$baseNote = $this->getNote($this->getMainNote(), $octave-1);

		$baseMainNote = $this->getNote($this->getMainNote());

		foreach ($chordComposition as $index => $desloc)
		{
			if ($chordComposition[$index] > 15)
				$chordComposition[$index] -= 12;
			if ($baseMainNote < 5 && $index < count($chordComposition)/2)
				$chordComposition[$index] += 12;
			$chordComposition[$index] += $baseNote;
		}
		array_multisort($chordComposition);

		return $chordComposition;
	}

	function getPianoChordCompositionWithBass($octave = 5, $bassOctave = 4) {
		$chordComposition = $this->getPianoChordComposition($octave = 5);

		$baseNote = $this->getNote($this->getMainNote(), $octave -1);

		if ($this->getBassNote()) {
			$bassNote = $this->getNote($this->bassNote, $bassOctave -1);
			if ($chordComposition[0] % 12 == $bassNote % 12)
				$chordComposition[0] += 12;
		} else {
			$bassNote = $baseNote - (($octave - $bassOctave) * 12);
			if ($chordComposition[0] == 0)
				$chordComposition[0] = 12;
		}

		array_unshift($chordComposition, $bassNote);

		return $chordComposition;
	}

	function getChordSignature() {
		return $this->chordSignature;
	}

	function getChordAsString() {
		return $this->chordStringRepresentation;
	}

	function getChordAsHTML() {
		return $this->chordHTMLRepresentation;
	}

	function initChordStringRepresentation()
	{
		$altNotes = $this->getAltNotes();

		if ($this->isMinor())
			$chordSignature .=
				$this->typeConvTable[_MINOR];

		if ($this->isDim())
			$chordSignature .=
				$this->typeConvTable[_DIM];

		if ($this->isMajor())
		{
			if (in_array("7", $altNotes))
			{
				$chordSignature .= "7";
				$altNotes[array_search ( "7", $altNotes)] = FALSE;
				$altNotes = array_filter($altNotes);
			}
			$chordSignature .=
				$this->typeConvTable[_MAJOR];
		}

		foreach (array("7","4","9","6") as $comp)
		{
			if (in_array($comp, $altNotes))
			{
				$chordSignature .= $comp;
				$altNotes[array_search ( $comp, $altNotes)] = FALSE;
				$altNotes = array_filter($altNotes);
				break;
			}
		}

		if ($this->isSus())
		{
			$chordSignature .=
				$this->typeConvTable[_SUS];
			foreach (array("2","4") as $comp)
			{
				if (in_array($comp, $altNotes))
				{
					$chordSignature .= $comp;
					$altNotes[array_search ( $comp, $altNotes)] = FALSE;
					$altNotes = array_filter($altNotes);
					break;
				}
			}
		}

		$alts = trim(implode("",$altNotes));
		if ($alts != "")
			$addictional = "($alts)";

		$this->chordSignature =
			$chordSignature
			.$addictional;

		$this->chordStringRepresentation =
			$this->getMainNote()
			.$chordSignature
			.$addictional
			.($this->getBassNote() != ""
				? "/" . $this->getBassNote() : "");

		$this->chordHTMLRepresentation =
			$this->getMainNote()
			.$chordSignature
			."<sup>$addictional</sup>"
			.($this->getBassNote() != ""
				? "/" . $this->getBassNote() : "");
	}

	function updateChordComposition()
	{
		$comp = $this->getChordComposition();
		foreach ($this->getAltNotes() as $alt)
		{
			if ($alt == "7")
				$comp[count($comp)] = ($this->isMajor()? 11 : 10);
			elseif (($alt == "4" || $alt == "2") && $this->isSus())
				$comp[1] = $this->scale[$alt-1];
			else {
				$desloc = ($alt[0]=="#"?1:($alt[0]=="b"?-1:0));
				if ($desloc != 0) $alt = substr($alt,1);
				$comp[$alt == 5? 2: count($comp)] =
					$this->scale[$alt-1]+$desloc;
			}
		}
		array_multisort($comp);
		$this->setChordComposition(array_unique($comp));
	}

	function retrieveGuitarTabFromDatabase()
	{
		global $database;

		$query = "SELECT chord "
			. "\n FROM #__cb_chords"
			. "\n WHERE baseNote = ". ($this->getNote($this->getMainNote()))
			. "\n AND " . ($this->getBassNote() ? "bassNote = " . ($this->getNote($this->getBassNote())) : "bassNote = -1")
			. "\n AND (BINARY signature = '" . ($this->getChordSignature()) . "'" . ($this->getChordSignature() && $this->getChordSignature() != "" ? ")" : " OR signature IS NULL)")
			. "\n ORDER BY `order`";

		$database->setQuery( $query );

		$this->guitarChordNumberRepresentation = $database->loadResultArray();

	}

	function checkAndCreateGuitarTabsImages()
	{
		global $mosConfig_absolute_path;
		foreach ($this->guitarChordNumberRepresentation as $chordCode)
		{
			$imageFilePath = "$mosConfig_absolute_path/components/com_chordbase/guitarTabs/$chordCode.png";
			if (!file_exists($imageFilePath))
			{
				extractTabAndFingers($chordCode, $tab, $fingers, $fret, $pestanaFret, $firstCord, $toques, $min, $max);

				$font_number = 3;
				$fret_font_number = 2;

				$cordDistance = 15;
				$fretHeight = 15;
				$pointSize = 13;
				$marginSup = 1;
				$xSize = $fretHeight*0.4;
				$marginBottom = $xSize+3;
				$marginLeft = (imagefontwidth($font_number)*2.5)+$pointSize/2;

				$im = @imagecreate(
					$cordDistance*5+$marginLeft,
					$fretHeight*5+$marginSup+$marginBottom)
					or die("Cannot Initialize new GD image stream");

				$marginLeft-=$pointSize/2; // Decrease left marging to draw properly

				// Paints image background
				$white_color = imagecolorallocate($im, 255, 255, 255);
				$fret_color = imagecolorallocate($im, 0, 0, 0);
				$noSoundThickness = 3;
				$nosound_color = imagecolorallocate($im, 200, 50, 50);
				$line_color = imagecolorallocate($im, 0, 0, 0);
				$trasteThickness = 4;
				$traste_color = imagecolorallocate($im, 0, 100, 0);
				$transparentColor = imagecolorallocate($im, 200, 200, 52);

				for ($i=0; $i<6; $i++)
				{
					if ($i==0) imagesetthickness($im, 3);

					// Horizontal lines
					imageline($im, $marginLeft, $marginSup+$fretHeight*$i, $cordDistance*5+$marginLeft, $marginSup+$fretHeight*$i, $line_color);

					if ($i==0) imagesetthickness($im, 1);

					// Vertical lines
					imageline($im, $marginLeft+$cordDistance*$i, $marginSup, $marginLeft+$cordDistance*$i, $marginSup+$fretHeight*5, $line_color);
				}

				// Transparent Color
				imagefill($im, 0,0, $transparentColor);
				imagefill($im, $marginLeft+$cordDistance*5+1, 0, $transparentColor);
				imagecolortransparent ($im, $transparentColor);

				if ($pestanaFret)
				{
					imagesetthickness($im, $trasteThickness);
					imageline($im,
						$marginLeft+(--$firstCord*$cordDistance)-3,
						$marginSup+$fretHeight*($pestanaFret-1)+$fretHeight/2,
						$marginLeft+$cordDistance*5+3,
						$marginSup+$fretHeight*($pestanaFret-1)+$fretHeight/2,
						$traste_color);
					imagesetthickness($im, 1);
				} else $traste = 0;

				// Points
				$bass = true;
				for ($cord=0; $cord < strlen($tab); $cord++)
				{
					$fretNum = $tab[$cord];
					if ($fretNum && $fretNum != "x")
					{
						if (!($pestanaFret && $fingers[$cord] == "1"))
						{
							imagefilledellipse($im,
								$marginLeft+$cord*$cordDistance,
								$marginSup+$fretHeight*($fretNum-1)+$fretHeight/2,
								$pointSize, $pointSize, $line_color);

							if ($fingers && $fingers[$cord] != '.')
							{
								imagestring($im, $font_number,
									$marginLeft+$cord*$cordDistance-imagefontwidth($font_number)/3,
									$marginSup+$fretHeight*($fretNum-1)+$fretHeight/2-imagefontheight($font_number)/1.8,
									$fingers[$cord], $white_color);
							}

						}

					}

					if ($fretNum == "x")
					{
						imagesetthickness($im, $noSoundThickness);
						imageline($im,
							$marginLeft+$cordDistance*$cord-$xSize/2,
							$fretHeight*5+3,
							$marginLeft+$cordDistance*$cord+$xSize/2,
							$fretHeight*5+3+$xSize,
							$nosound_color);
						imageline($im,
							$marginLeft+$cordDistance*$cord+$xSize/2,
							$fretHeight*5+3,
							$marginLeft+$cordDistance*$cord-$xSize/2,
							$fretHeight*5+3+$xSize,
							$nosound_color);
						imagesetthickness($im, 1);
					}
					else
					{

						if ($bass)
							imagefilledellipse($im,
								$marginLeft+$cord*$cordDistance,
								$fretHeight*5+3+$xSize/2,
								$xSize, $xSize, $line_color);
						else
							imageellipse($im,
								$marginLeft+$cord*$cordDistance,
								$fretHeight*5+3+$xSize/2,
								$xSize, $xSize, $line_color);

						$bass = false;
					}
				}

				if ($fret != "1")
				{
					imagestring($im, $fret_font_number,
						0,
						$marginSup+1,
						$fret, $fret_color);
				}

				imagepng($im, $imageFilePath);
				imagedestroy($im);
			}
		}
	}

	function checkAndCreateGuitarTabsMidiFile()
	{
		global $mosConfig_absolute_path;

		$baseStringNotes = array (40, 45, 50, 55, 59, 64);

		$instrument = 24;

		foreach ($this->guitarChordNumberRepresentation as $chordCode)
		{
			$midiFilePath = "$mosConfig_absolute_path/components/com_chordbase/guitarTabs/$chordCode.mid";

			if (!file_exists($midiFilePath))
			{
				$hexTab = str_pad(dechex($chordCode),6, "f", STR_PAD_LEFT);
				$chordNotes = array();

				for ($corda = 0; $corda <= 5; $corda++)
				{
					if ($hexTab[$corda] != "f")
					{
						$chordNotes[count($chordNotes)] = hexdec($hexTab[$corda]) + $baseStringNotes[$corda];
					}
				}

				if (count($chordNotes) > 0)
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

					$midi->saveMidFile($midiFilePath);
				}
			}
		}

	}

	function checkAndCreateKeysImages()
	{
		global $mosConfig_absolute_path;

		if ($this->getBassNote() != "")
			$bass = $this->getNote($this->getBassNote());
		else $bass = $this->getNote($this->getMainNote());

		$chordKeys = $this->getChordBasicNotesAsNumbers();

		$imageFilePath = "$mosConfig_absolute_path/components/com_chordbase/keys/".$bass."_".implode("_",$chordKeys).".png";

		if (!file_exists($imageFilePath))
		{
			$chordKeys[count($chordKeys)] = $bass;

			$keyboardWidth = 93;
			$keyboardHeight = 40;
			$keyWidth = ($keyboardWidth-1)/7;
			$blackKeyHeight = $keyboardHeight * 0.7;
			$pointSize = $keyWidth * 0.5;

			$im = @imagecreate($keyboardWidth, $keyboardHeight)
				or die("Cannot Initialize new GD image stream");

			// Paints image background
			$white_color = imagecolorallocate($im, 255, 255, 255);
			$black_color = imagecolorallocate($im, 0, 0, 0);
			$bass_color = imagecolorallocate($im, 0, 150, 0);
			$press_color = imagecolorallocate($im, 150, 150, 255);
			$line_color = imagecolorallocate($im, 0, 0, 0);

			// Top/Bottom lines
			imagerectangle($im, 0, 0, $keyboardWidth-1, $keyboardHeight-1, $line_color);

			// White keys
			$count = 1;
			for ($pos=0; $pos < $keyboardWidth-$keyWidth; $pos+=$keyWidth)
			{
				imageline($im, $pos, ($count++ == 4 ? 0 : $blackKeyHeight), $pos, $keyboardHeight, $line_color);
			}

			// Black keys
			$count = 1;
			for ($pos=$keyWidth/2; $pos < $keyboardWidth-$keyWidth; $pos+=$keyWidth)
			{
				if ($count++ != 3)
				{
					imagerectangle($im, $pos+$keyWidth*0.8, 0, $pos+$keyWidth-$keyWidth*0.8, $blackKeyHeight, $line_color);
				}
			}

			for ($key=0; $key<12; $key++)
			{
				if ($bass == $key)
					$pointColor = $bass_color;
				elseif (in_array($key, $chordKeys))
					$pointColor = $press_color;
				elseif ($key == 1 || $key == 3 || $key == 6 || $key == 8 || $key == 10)
				{
					$pointColor = $black_color;
				}
				else
				{
					$pointColor = $white_color;
				}

				imagefill ($im, ($keyWidth/2)*($key+1)+($key>4?$keyWidth/2:0), 1, $pointColor);

			}

			imagepng($im, $imageFilePath);
			imagedestroy($im);
		}
	}

	function checkAndCreateKeysMidiFile()
	{
		global $mosConfig_absolute_path;

		if ($this->getBassNote() != "")
			$bass = $this->getNote($this->getBassNote());
		else $bass = $this->getNote($this->getMainNote());

		$chordKeys = $this->getChordBasicNotesAsNumbers();

		$imageFilePath = "$mosConfig_absolute_path/components/com_chordbase/keys/".$bass."_".implode("_",$chordKeys).".mid";

		$chordNotes = $this->getChordNotesAsNumbers();

		$instrument = 1; // Piano

		if (!file_exists($imageFilePath) && $chordNotes)
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

			$midi->saveMidFile($imageFilePath);
		}

	}

	function Chord($chord)
	{
		$chord = trim($chord);

		$mainNote = getRegexAndRemove("/^([A-G][#,b]?)/", $chord);
		$this->setMainNote($mainNote);

		$bassNote = getRegexAndRemove("/\/([A-G][#,b]?)/", $chord,1);
		if ($bassNote != "")
			$this->setBassNote($bassNote);

		// Switch b and # to + and - because we are going to
		// get the strings
		$chord = preg_replace(
			"/(b)([1-9])/",
			"*\$2",
			$chord
		);

		$isMajor = false;
		$isMinor = false;
		$isDim = false;
		$isSus = false;

		while (($str = getNextString($chord)) != "")
		{
			$str = "|$str|";
			$isMajor = $isMajor || (strstr("|major|maj|J|M|",$str));
			$isMinor = $isMinor || (strstr("|minor|MINOR|min|MIN|m|",$str));
			$isDim = $isDim || (strstr("|DIM|dim|o|°|º|\xB0|",$str));
			$isSus = $isSus || (strstr("|SUS|sus|",$str));
			$isAug = $isAug || (strstr("|aug|AUG|",$str));
		}

		$this->setMajor($isMajor);
		$this->setMinor($isMinor);
		$this->setDim($isDim);
		$this->setSus($isSus);
		$this->setAug($isAug);

		// Switch back from * to b
		$chord = preg_replace(
			"/(\*)([1-9])/",
			"b\$2",
			$chord
		);

		// Set as major as default, but don't change signature
		$this->setMajorChordComposition();

		if ($isMinor)
			$this->setMinorChordComposition();
		else if ($isDim)
			$this->setDimChordComposition();

		if ($isAug) $this->addAltNote("#5");

		while (($alt = getNextAlt($chord)) != "")
		{
			if ($alt == "#7")
			{
				$alt = "7";
				$this->setMajor($isMajor=true);
			}
			if ($alt == "2" && !$isSus) $alt = "9";
			$this->addAltNote($alt);
		}

		if ($isSus  &&
			!in_array("4",$this->altNotes) &&
			!in_array("2",$this->altNotes))
			$this->addAltNote("4");

		if ($isMajor && in_array("7",$this->altNotes))
			$this->setMajor();

		$this->altNotes = array_unique($this->altNotes);
		$this->initChordStringRepresentation();
		$this->updateChordComposition();

		if (defined( '_VALID_MOS' ))
		{
			$this->retrieveGuitarTabFromDatabase();
			$this->checkAndCreateGuitarTabsImages();
			$this->checkAndCreateGuitarTabsMidiFile();
			$this->checkAndCreateKeysImages();
			$this->checkAndCreateKeysMidiFile();
		}

	}
}

function checkRegexAndRemove($regex, &$text)
{
	if (preg_match($regex, $text, $result))
	{
		$text = preg_replace($regex,"", $text);
		return true;
	}
	return false;
}

function getRegexAndRemove($regex, &$text, $index=0)
{
	if (preg_match($regex, $text, $result))
	{
		$text = preg_replace($regex,"", $text);
		return $result[$index];
	}
	return "";
}

function getNextAlt(&$text)
{
	$match = preg_match("'([#,b]?[1][1-5][\-,+]?|[#,b]?[2-9][\-,+]?)'",$text,$result);
	if ($match)
	{
		$result = $result[1];
		$text = str_replace($result,"",$text);
		$result = preg_replace("/(\d)(\D)/","$2$1",$result);
		$result = preg_replace(
			array("/\-/","/\+/"),
			array("b","#"),
			$result
		);
	} else $result = "";

	return $result;
}

function getNextString(&$text)
{
	$match = preg_match("/[\d,\(]*([a-z,º,°]*)/i",$text,$result);
	if ($match)
	{
		$result = $result[1];
		$text = str_replace($result,"",$text);
	} else $result = "";
	return $result;
}

function extractTabAndFingers($num, &$tab, &$fingers, &$fret, &$pestanaFret, &$firstCord, &$toques, &$min, &$max)
{

	$arrAcordeOrig = $arrAcorde = str_pad(dechex($num),6, "f", STR_PAD_LEFT);

	$tab = "111111";
	$fingers = "111111";

	$min = 100;
	$max = $toques = 0;

	for ($n = 0; $n < strlen($arrAcorde); $n++)
	{
		$arrAc = $arrAcorde[$n];
		if ($arrAc != "f")
		{
			$arrAc = hexdec($arrAcorde[$n]);

			if ($arrAc != "0" && $arrAc < $min) $min = $arrAc;
			if ($arrAc > $max) $max = $arrAc;
			if ($arrAc != "0") $toques ++;
			else $tab[$n] = 0;
		}
	}

	$primeiroTraste = $min;

	if ($max <=5) $min = 1;

	$fret = $min;

	$dedo = 1;

	$pestanaFret = 0;
	$firstCord = 0;

	for ($traste = $min; $traste <= $min + 4; $traste++)
	{
		$pestana = false;
		$trasteVazio = true;
		for ($corda = 0; $corda <= 5; $corda++)
		{

			$arrAc = hexdec($arrAcorde[$corda]);
			if ($arrAc == $traste || $pestana)
			{
				$trasteVazio = false;
				if (($toques > 4 && $traste == $primeiroTraste) || $pestana)
				{
					if (!$pestana) $dedo ++;
					$pestana = true;
					if ($pestanaFret == 0)
						$pestanaFret = $traste-$min+1;
				}
				else
				{
					if ($fingers[$corda] == "1")
						$fingers[$corda] = $dedo++;

					if ($tab[$corda] == "1")
						$tab[$corda] = $traste-$min+1;
				}

				if ($firstCord == 0)
					$firstCord = $corda+1;
			}
		}

		if ($trasteVazio && $dedo > 1 && $dedo < 3 && $toques < 4)
			$dedo ++;

	}

	for ($corda = 0; $corda <= 5; $corda++)
	{
		if ($arrAcordeOrig[$corda] == "f")
			$tab[$corda] = "x";
	}

}


//$chord = new Chord("Am(M7)");
//echo $chord->chordStringRepresentation;

?>
