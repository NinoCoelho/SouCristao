<?php
/**
* @version $Id: brazilian_portuguese.php,v 1.1 2005/04/05 22:30:20 nino Exp $
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
DEFINE("_CHORDS_INSTRUCTIONS","(&lt;acorde&gt; : &lt;digitação&gt;)");
DEFINE("_COPYRIGHTS","Direitos Autorais");
DEFINE("_KEY","Tom");
DEFINE("_KEYWORDS","Palavras Chave");
DEFINE("_KEYWORDS_INSTRUCTIONS","(separadas por vírgula, e sem espaços)");
DEFINE("_NEW","Novo");
DEFINE("_NEW_CATEGORY","- Nova Categoria -");
DEFINE("_NEW_WRITER","- Novo Autor -");
DEFINE("_NOTES","Anotações");
DEFINE("_SAVE_SONG","Salvar Música");
DEFINE("_SIG","Andamento");
DEFINE("_SONG","Música");
DEFINE("_SONGLIST","Lista de Músicas");
DEFINE("_SONG_BODY","Corpo da Música");
DEFINE("_SUBMITTED_BY","Enviado por");
DEFINE("_TITLE","Título");
DEFINE("_LYRIC","Letra");
DEFINE("_VIEWS","Acessos");
DEFINE("_WRITER","Autor");
DEFINE("_WRITERS","Autores");
DEFINE("_CHORDPRO_INSTRUCTIONS_TITLE","Instruções");
DEFINE("_CHORDPRO_INSTRUCTIONS","Separe os acordes com [], exemplo:<br>" .
		"Eu <strong>[A]</strong>canto um <strong>[F#m]</strong>novo ca<strong>[D]</strong>nto<br>" .
		"<br>Diretivas:<br>" .
		"<ul><li><strong>{soc}</strong> - Indica o início do coro da música" .
		"<li><strong>{eoc}</strong> - Indica onde termina o coro da música</li>" .
		"<li><strong>{sot}</strong> - Indica o início de uma tablatura" .
		"<li><strong>{eot}</strong> - Indica onde termina a tablatura</li>" .
		"<li><strong>{c: Comentário}</strong> - Texto de comentário ou instruções</li>" .
		"<li><strong>{gc: Comentário para intrumentista}</strong> - Texto de comentário ou instruções para o instrumentista.</li>" .
		"</ul><br>Somente uma diretiva é permitida por linha. Linhas iniciadas com # conterão comentários que não serão exibidos na música. Você pode utilizar este tipo de marca para incluir informações adicionais que deseja deixar registrado no site");

DEFINE("_CHORDPRO_CM_SOC","{soc}{eoc}");
DEFINE("_CHORDPRO_CM_SOT","{sot}{eot}");
DEFINE("_CHORDPRO_CM_C", "{c: }");
DEFINE("_CHORDPRO_CM_GC","{gc: }");
DEFINE("_CHORDPRO_CM_CHORD","   []   ");
DEFINE("_CHORDPRO_CM_COMENT","#");

// Category
DEFINE("_CATEGORY","Categoria");
DEFINE("_CATEGORYLIST","Lista de Categorias");

// General
DEFINE("_HELP","Ajuda");
DEFINE("_ID","Id");
DEFINE("_PUBLISHED","Publicada");
DEFINE("_SHOW","Exibir");
DEFINE("_HIDE","Ocultar");
DEFINE("_PRINTER_FRIENDLY","Versão para Impressão");
DEFINE("_TRANSPOSE","Alterar Tom");
DEFINE("_ALL","Todas");
DEFINE("_ADD_SONG","Adicionar Música");
DEFINE("_PUBLISHED","Publicadas");
DEFINE("_UNPUBLISHED","Não Publicadas");
DEFINE("_PERMISSION_DENIED","Você não tem permissão para isto!");
DEFINE("_SEARCH","Procurar");
DEFINE("_DISPLAY","Exibir #");
DEFINE("_NEW_MUSIC_MESSAGE","A música enviada aparecerá no site assim que aprovada pelo administrador.<br>Muito obrigado pela colaboração.");
DEFINE("_TEXT_CONV", "Converter Texto Comum para o formato do site");
?>