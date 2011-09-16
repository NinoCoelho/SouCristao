<?php
/**
* @version $Id: english.php,v 1.1 2005/04/05 22:30:20 nino Exp $
* @package ChordBase
* @Copyright (C) 2003-2004 ChrodBase by Jonathan Felchlin
* @Email jonathan@chordbase.com
* @ All rights reserved
* @ ChordBase is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
**/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

// Song
DEFINE("_BPM","Tempo");
DEFINE("_CATEGORY","Categoria");
DEFINE("_CHORDS","Acordes");
DEFINE("_CHORDS_INSTRUCTIONS","(&lt;acorde&gt; : &lt;digita��o&gt;)");
DEFINE("_COPYRIGHTS","Direitos Autorais");
DEFINE("_KEY","Key");
DEFINE("_KEYWORDS","Palavras Chave");
DEFINE("_KEYWORDS_INSTRUCTIONS","(separadas por v�rgula, e sem espa�os)");
DEFINE("_NEW","Novo");
DEFINE("_NEW_CATEGORY","- Nova Categoria -");
DEFINE("_NEW_WRITER","- Novo Autor -");
DEFINE("_NOTES","Anota��es");
DEFINE("_SAVE_SONG","Salvar M�sica");
DEFINE("_SIG","Andamento");
DEFINE("_SONG","Song");
DEFINE("_SONGLIST","Song List");
DEFINE("_SONG_BODY","Corpo da M�sica");
DEFINE("_SUBMITTED_BY","Enviado por");
DEFINE("_TITLE","Title");
DEFINE("_LYRIC","Lyrics");
DEFINE("_VIEWS","Views");
DEFINE("_WRITER","Writer");
DEFINE("_WRITERS","Writers");
DEFINE("_CHORDPRO_INSTRUCTIONS_TITLE","Instru��es");
DEFINE("_CHORDPRO_INSTRUCTIONS","Separe os acordes com [], exemplo:<br>" .
		"Eu <strong>[A]</strong>canto um <strong>[F#m]</strong>novo ca<strong>[D]</strong>nto<br>" .
		"<br>Diretivas:<br>" .
		"<ul><li><strong>{soc}</strong> - Indica o in�cio do coro da m�sica" .
		"<li><strong>{eoc}</strong> - Indica onde termina o coro da m�sica</li>" .
		"<li><strong>{sot}</strong> - Indica o in�cio de uma tablatura" .
		"<li><strong>{eot}</strong> - Indica onde termina a tablatura</li>" .
		"<li><strong>{c: Coment�rio}</strong> - Texto de coment�rio ou instru��es</li>" .
		"<li><strong>{gc: Coment�rio para intrumentista}</strong> - Texto de coment�rio ou instru��es para o instrumentista.</li>" .
		"</ul><br>Somente uma diretiva � permitida por linha. Linhas iniciadas com # conter�o coment�rios que n�o ser�o exibidos na m�sica. Voc� pode utilizar este tipo de marca para incluir informa��es adicionais que deseja deixar registrado no site");

DEFINE("_CHORDPRO_CM_SOC","{soc}{eoc}");
DEFINE("_CHORDPRO_CM_SOT","{sot}{eot}");
DEFINE("_CHORDPRO_CM_C", "{c: }");
DEFINE("_CHORDPRO_CM_GC","{gc: }");
DEFINE("_CHORDPRO_CM_CHORD","[]");
DEFINE("_CHORDPRO_CM_COMENT","#");

// Category
DEFINE("_CATEGORY","Category");
DEFINE("_CATEGORYLIST","Category List");

// General
DEFINE("_HELP","Help");
DEFINE("_ID","ID");
DEFINE("_PUBLISHED","Published");
DEFINE("_SHOW","Exibir");
DEFINE("_HIDE","Ocultar");
DEFINE("_PRINTER_FRIENDLY","Vers�o para Impress�o");
DEFINE("_TRANSPOSE","Alterar Tom");
DEFINE("_ALL","Todas");
DEFINE("_ADD_SONG","Adicionar M�sica");
DEFINE("_PUBLISHED","Publicadas");
DEFINE("_UNPUBLISHED","N�o Publicadas");
DEFINE("_PERMISSION_DENIED","Voc� n�o tem permiss�o para isto!");
DEFINE("_SEARCH","Procurar");
DEFINE("_DISPLAY","Exibir #");
DEFINE("_NEW_MUSIC_MESSAGE","A m�sica enviada aparecer� no site assim que aprovada pelo administrador.<br>Muito obrigado pela colabora��o.");
DEFINE("_TEXT_CONV", "Converter Texto Comum para o formato do site");
?>