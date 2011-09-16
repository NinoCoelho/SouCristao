<OBJECT CLASSID="CLSID:22D6f312-B0F6-11D0-94AB-0080C74C7E95"
	CODEBASE="http://www.microsoft.com/ntserver/netshow/download/en/nsmp2inf.cab#Version=5,1,51,415"
	type="application/x-oleobject"
	width=300 height=44>
<PARAM NAME="AutoStart" VALUE="true">
<PARAM NAME="FileName" VALUE="chordmidi.php?<?php echo "chord=".urlencode($_GET["chord"])."&tab=".urlencode($_GET["tab"]) ?>">
<PARAM NAME="ControlType" VALUE="1">
<PARAM NAME="Loop" VALUE="false">
<PARAM NAME="ShowControls" VALUE="true">
<EMBED TYPE="video/x-ms-asf-plugin"
	PLUGINSPAGE="http://www.microsoft.com/windows/mediaplayer/download/default.asp"
	SRC="chordmidi.php?<?php echo "chord=".urlencode($_GET["chord"])."&tab=".urlencode($_GET["tab"]) ?>"
		AutoStart="1" ShowControls="1" Loop="0" width=300 height=44>
</OBJECT>
