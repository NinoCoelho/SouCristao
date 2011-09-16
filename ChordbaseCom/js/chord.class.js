function lTrim(textToTrim)
{
	while (textToTrim != ""
		&& (textToTrim.charCodeAt(0) <= 32
			|| textToTrim.charCodeAt(0) > 127) )
		textToTrim = textToTrim.substring(1);

	return textToTrim;
}

function rTrim(textToTrim)
{
	while (textToTrim != ""
		&& (textToTrim.charCodeAt(textToTrim.length-1) <= 32
			|| textToTrim.charCodeAt(textToTrim.length-1) > 127) )
		textToTrim = textToTrim.substring(0, textToTrim.length-1);

	return textToTrim;
}

function allTrim(textToTrim)
{
	return lTrim(rTrim(textToTrim));
}

function Chord(chord)
{

	this.toString = function()
	{
		return this.mainNote+this.signature
			+ (this.addedNotes
				? "(" + this.addedNotes + ")"
				: "")
			+ (this.bassNote
				? "/" + this.bassNote
				: "")
			;
	
	}

	this.toHTML = function()
	{
		return this.mainNote
			+ (this.signature == "74"
				? "<SUP>7</SUP><SUB>4</SUB>"
				: this.signature)
			+ (this.addedNotes
				? "(" + this.addedNotes + ")"
				: "")
			+ (this.bassNote
				? "/" + this.bassNote
				: "")
			;
	}

	this.setMainNote = function(mainNote)
	{
		this.mainNote = mainNote;
	}
	
	this.getMainNote = function()
	{
		return this.mainNote;
	}
	
	this.setBassNote = function(bassNote)
	{
		this.bassNote = bassNote;
	}
	
	this.getBassNote = function()
	{
		return this.bassNote;
	}
	
	this.setSignature = function(signature)
	{
		this.signature = signature;
	}
	
	this.getSignature = function()
	{
		return this.signature;
	}
	
	this.parseChord = function(chord)
	{
		altsAlternativas = " #b+-";
		altsPreferidos = " #b#b";
	
		index = 0;
	
		while (chord != "" && "ABCDEFG".indexOf(chord.charAt(0)) == -1)
			chord = chord.substring(1);
	
		this.error = (chord == "");
	
		if (this.error) return;
	
		mainNote = chord.charAt(index++);
		
		altsAlternativas = " #b+-";
		altsPreferidos = " #b#b";
	
		if (chord.charAt(index) && altsAlternativas.indexOf(chord.charAt(index)) > 0)
			mainNote += altsPreferidos.charAt(altsAlternativas.indexOf(chord.charAt(index++)));
	
		this.setMainNote(mainNote);
	
		indexBass = chord.indexOf("/");
	
		if (indexBass > 0)
		{
			bassNote = chord.charAt(indexBass+1);
	
			this.error = "ABCDEFG".indexOf(bassNote.charAt(0)) == -1;
	
			if (chord.length > indexBass+2 && altsAlternativas.indexOf(chord.charAt(indexBass+2)) > 0)
				bassNote += altsPreferidos.charAt(altsAlternativas.indexOf(chord.charAt(indexBass+2)));
	
			this.setBassNote(bassNote);
		}
	
		switch (chord.charAt(index))
		{
			case "m":
				this.setSignature("m"); 
				if (chord.substr(index, 5).toUpperCase() == "MINOR")
					index += 4;
				index++;
				if (chord.charAt(index) == "7" &&
					" +JM".indexOf(chord.charAt(index+1)) < 1)
				{
					this.setSignature("m7"); 
					index++;
				}
				break;
			case "7":
				index++;
				if (chord.charAt(index) &&
					" +JM".indexOf(chord.charAt(index)) > 0)
				{
					this.setSignature("7M"); 
					if (chord.substr(index, 5).toUpperCase() == "MAJOR")
						index += 4;
					index ++;
				}
				else
				{
					this.setSignature("7"); 
				}
				break;
			case "\xB0":
			case "D":
			case "d":
			case "0":
			case "o":
				if (chord.substr(index, 3).toUpperCase() == "DIM")
					index += 2;
				index++;				
				this.setSignature("DIM");
				break;
		}
		
		searchFor = " +-b#1234567890";
	
		alt = "";
	
		while (index < chord.length)
		{
			if (this.bassNote && chord.charAt(index) == "/")
				break;
	
			if (altsAlternativas.indexOf(chord.charAt(index)) > 0)
			{
				alt = altsPreferidos.charAt(altsAlternativas.indexOf(chord.charAt(index)));
				index++;
			}
	
			if ("1234567890".indexOf(chord.charAt(index)) != -1)
			{
				noteToAdd = chord.charAt(index);
				index++;
	
				if (noteToAdd == 1
					&& index < chord.length 
					&& chord.charAt(index) < 4)
				{
					noteToAdd += chord.charAt(index);
					index++;
				}
	
				if (noteToAdd == 4 && this.getSignature() == "7")
					this.setSignature("74");
				else this.addedNotes += alt + noteToAdd;
			} else {
				if (chord.substr(index, 3).toUpperCase() == "SUS")
				{
					index += 2;
					if (chord.charAt(index+1) == "4") index++;
						this.setSignature("74");
					
				}
				else if ("(ABCDEFG) ".indexOf(chord.charAt(index)) == -1)
					this.error = true;
				
				index++;
			}
			alt = "";
		}
	}

	this.error = false;
	this.mainNote;
	this.signature = "";	
	this.addedNotes = "";	
	this.bassNote;
	
	this.guitarChordTab = "";
	this.guitarChordFret = 0;
	this.guitarChordFingers = "";
	
	this.parseChord(chord);
}

function textToChordPro(text)
{
	htmlMusic = "";
	chordProText = "";

	lines = text.split("\n");

	chordLine = false;

	rowIndex = 0;
	
	var chordElements;
	var lyricElements;

	while (rowIndex < lines.length)
	{
		lineText = rTrim(lines[rowIndex]);
		lineTextOrig = lineText;

		if (rowIndex+1 < lines.length)
			lyricLineText = rTrim(lines[rowIndex+1]);
		else lyricLineText = "";

		lyricLineTextOrig = lyricLineText;

		chordElements = new Array();
		lyricElements = new Array();

		lineSize = lineText.length;

		chordLine = lineText != "";

		lastLyricFragment = "";

		while (lineSize > 0)
		{
			lineText = rTrim(lineText);
			lastSpace = lineText.lastIndexOf(" ");

			colspanRow1 = false;
			if (lastSpace == -1)
			{
				middleWord = " ,.;:?!".indexOf(lyricLineText.charAt(lyricLineText.length-1)) == -1;
				
				fragment = lineText;
				lyricFragment = lyricLineText;
				lineSize = 0;
			}
			else
			{
				fragment = lineText.substring(lastSpace);
				lineText = lineText.substring(0, lastSpace);

				middleWord = 
					" ,.;:?!".indexOf(lyricLineTextOrig.charAt(lyricLineText.length)) == -1
					&& " ,.;:?!".indexOf(lyricLineTextOrig.charAt(lyricLineText.length-1)) == -1;
				
				lyricFragment = lyricLineText.substring(lastSpace+1);
				lyricLineText = lyricLineText.substring(0, lastSpace+1);

				lineSize = lineText.length;
				lastLyricFragment = lyricFragment;
			}

			firstlyricFragment = lyricFragment.length == "";
			
			if (allTrim(fragment) != "")
			{
				chordText = allTrim(fragment);
				chord = new Chord(chordText);
				chordText = chord.toHTML();
				if (allTrim(fragment) == "(")
					chordText = ""
				else {
					if (chord.error) {
						chordLine = false;
						chordText = allTrim(fragment);
					}
				}
			} else chordText = "";

			chordElements.unshift(chordText);
			lyricElements.unshift(lyricFragment);
		}

		rowIndex++;

		if (chordLine) 
		rowIndex++;
		for (var index = 0; index < chordElements.length; index++)
		{
			chordProText += 
				(chordElements[index] == ""
					? ""
					: "["+chordElements[index]+"]"
				)
				+lyricElements[index];
		}

		chordProText += "\n";
	}
	return chordProText;
}
