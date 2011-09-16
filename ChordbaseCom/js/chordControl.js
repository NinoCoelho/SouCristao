document.write("<div style='display: hidden; height:1;' id='midiDivObj'></div>");
GUITAR = "G";
KEYBOARD = "K";

selectedInstrument = GUITAR;

keysBaseURL = "components/com_chordbase/keys/";
fretsBaseURL = "components/com_chordbase/guitarTabs/";
midiPlayURL = "components/com_chordbase/midiplay.php";
guitarMidiPlayURL = "components/com_chordbase/guitarmidiplay.php";


chordQty = 0;
allKeysHTML = "<small>Clique para tocar</small><table border=0 cellpadding=5 cellspacing=0><tr>";
allFretsHTML = "<small>Clique para tocar / CTRL+Clique para variações</small><table border=0 cellpadding=5 cellspacing=0><tr>";

chords = new Array();

function updateSelectedInstrument()
{
	if (document.all["keyboard-page"].style.display == "none")
		selectedInstrument = GUITAR;
	else
		selectedInstrument = KEYBOARD;
}

function getChordHint(chordHTML)
{
	chord = getChord(chordHTML);
	updateSelectedInstrument();
	if (selectedInstrument == KEYBOARD)
	{
		return chord.getKeysImageHTML();
	}
	else
	{
		return chord.getFretsImageHTML();
	}
}

function playChordMidi(chordHTML)
{
	if (selectedInstrument == KEYBOARD)
	{
		playKeyboardNotes(chordHTML)
	}
	else
	{
		playGuitarNotes(chordHTML);
	}
}

function playKeyboardNotes(chordHTML)
{
	chord = getChord(chordHTML);
	midiPlay(keysBaseURL+chord.bass+"_"+chord.keys+".mid");
}

function handleGuitarClick(chordHTML)
{
	if (event.ctrlKey) showGuitarVariations(chordHTML);
	else playGuitarNotes(chordHTML);
}

function showGuitarVariations(chordHTML)
{
	chord = getChord(chordHTML);

	html = "";

	qty = 0;
	for(i=0; i<chord.guitarCodes.length; i++)
	{
		if (i != chord.selectedGuitarCode)
		{
			if (qty++ % 3 == 0) html += "<br>";
			html += "<img src=\""+chord.getFretsImageURL(chord.guitarCodes[i])+"\" valign=bottom hspace=\"5\" vspace=\"5\" onclick=\"selectGuitarVariation('"+chordHTML+"', "+i+")\">";
		}
	}

	if (html != "")
		overlib(html, HAUTO, VAUTO, STICKY, CAPTION, "Selecione a variação", CLOSETEXT, "[X]", CLOSECOLOR, "#AA0000", CLOSECLICK);
}

function selectGuitarVariation(chordHTML, variation)
{
	chord = getChord(chordHTML);
	chord.selectGuitarCode(variation);
	document.all[chord.asHTML].src = chord.getFretsImageURL();
	nd(); nd();
	playGuitarNotes(chordHTML);
}

function playGuitarNotes(chordHTML)
{
	chord = getChord(chordHTML);
	midiURL = fretsBaseURL+chord.getSelectedGuitarCode()+".mid";
	midiPlay(midiURL);
}

function midiPlay(midiURL)
{
	midiDivObj.innerHTML =
	"<OBJECT CLASSID=\"CLSID:22D6f312-B0F6-11D0-94AB-0080C74C7E95\""
	+"	CODEBASE=\"http://www.microsoft.com/ntserver/netshow/download/en/nsmp2inf.cab#Version=5,1,51,415\""
	+"	type=\"application/x-oleobject\""
	+"	width=300 height=44 STYLE=\"display: none;\">"
	+"<PARAM NAME=\"AutoStart\" VALUE=\"true\">"
	+"<PARAM NAME=\"FileName\" VALUE=\""+midiURL+"\">"
	+"<PARAM NAME=\"ControlType\" VALUE=\"1\">"
	+"<PARAM NAME=\"Loop\" VALUE=\"false\">"
	+"<PARAM NAME=\"ShowControls\" VALUE=\"true\">"
	+"<EMBED TYPE=\"video/x-ms-asf-plugin\" STYLE=\"display: none;\""
	+"	PLUGINSPAGE=\"http://www.microsoft.com/windows/mediaplayer/download/default.asp\""
	+"	SRC=\""+midiURL+"\""
	+"		AutoStart=\"1\" ShowControls=\"1\" Loop=\"0\" width=300 height=44>"
	+"</EMBED>"
	+"</OBJECT>";
}

function getChordList(instrument)
{
	return (instrument == GUITAR ? allFretsHTML : allKeysHTML) + "</tr></table>";
}

function addChord(chord)
{
	chords[chord.asHTML.toUpperCase()] = chord;

	if (chordQty++ % 5 == 0)
	{
		allKeysHTML += "</tr><tr>";
		allFretsHTML += "</tr><tr>";
	}

	allKeysHTML += "<td>"+chord.getKeysImageHTML()+"</td>";
	allFretsHTML += "<td>"+chord.getFretsImageHTML()+"</td>";
}

function getChord(chord)
{
	return chords[chord.toUpperCase()];
}

function Chord(chordText, asHTML, keys, bass, keysNotes)
{
	this.chordText = chordText;
	this.asHTML = asHTML;
	this.keys = keys;
	this.bass = bass;
	this.keysNotes = keysNotes;
	this.guitarCodes = new Array();
	this.selectedGuitarCode = -1;

	this.addGuitarCode = function (guitarCode)
	{
		if (this.guitarCodes.length == 0)
			this.selectGuitarCode(0);
		this.guitarCodes[this.guitarCodes.length] = guitarCode;
	}

	this.getSelectedGuitarCode = function ()
	{
		return this.guitarCodes[this.selectedGuitarCode];
	}

	this.selectGuitarCode = function (guitarCodeIndex)
	{
		this.selectedGuitarCode = guitarCodeIndex;
	}

	this.getKeysImageURL = function ()
	{
		return keysBaseURL+this.bass+"_"+this.keys+".png";
	}

	this.getKeysImageHTML = function (attributes)
	{
		return "<span style='cursor:hand;' onclick='playKeyboardNotes(\""+this.asHTML+"\")'><strong>"+this.asHTML+"</strong><br><img src='"+this.getKeysImageURL()+"' "+(attributes != undefined ? attributes : "")+"></span>";
	}

	this.writeKeysImage = function (attributes)
	{
		document.write(this.getKeysImageHTML());
	}

	this.getFretsImageURL = function (code)
	{
		if (this.getSelectedGuitarCode() || code)
			return fretsBaseURL+(code?code:this.getSelectedGuitarCode())+".png";
		else
			return "(Inexistente no dicionário do sistema)";
	}

	this.getFretsImageHTML = function (attributes)
	{
		if (this.getSelectedGuitarCode())
			return "<span style='cursor:hand;' onclick='handleGuitarClick(\""+this.asHTML+"\")'><strong>"+this.asHTML+"</strong><br><img id='"+this.asHTML+"' src='"+this.getFretsImageURL()+"' "+(attributes != undefined ? attributes : "")+"></span>";
		else
			return "<strong>"+this.asHTML+"</strong><br>Inexistente<br>no dicionário<br>do sistema";
	}

	this.writeFretsImage = function (attributes)
	{
		document.write(this.getFretsImageHTML());
	}

}		
