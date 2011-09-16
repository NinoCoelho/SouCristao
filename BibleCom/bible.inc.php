<?php
/**
* @version $id$
* @package MOS_BIBLE
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

global $mosConfig_live_site;

/**
* Convert bible references on text to links with hints
*/

function textBibleReferencesToLinks( $text ) {
	global $mosConfig_absolute_path, $mosConfig_live_site, $database;
	static $books = "Gn|Êx|Lv|Nm|Dt|Js|Jz|Rt|1Sm|1 Sm|ISm|I Sm|2Sm|2 Sm|IISm|II Sm|1Rs|1 Rs|IRs|I Rs|2Rs|2 Rs|IIRs|II Rs|1Cr|1 Cr|ICr|I Cr|2Cr|2 Cr|IICr|II Cr|Ed|Ne|Et|Jó|Sl|Pv|Ec|Ct|Is|Jr|Lm|Ez|Dn|Os|Jl|Am|Ob|Jn|Mq|Na|He|Sf|Ag|Zc|Ml|Mt|Mc|Lc|Jo|At|Rm|1Co|1 Co|ICo|I Co|2Co|2 Co|IICo|II Co|Gl|Ef|Fp|Cl|1Ts|1 Ts|ITs|I Ts|2Ts|2 Ts|IITs|II Ts|1Tm|1 Tm|ITm|I Tm|2Tm|2 Tm|IITm|II Tm|Tt|Fm|Hb|Tg|1Pe|1 Pe|IPe|I Pe|2Pe|2 Pe|IIPe|II Pe|1Jo|1 Jo|IJo|I Jo|2Jo|2 Jo|IIJo|II Jo|3Jo|3 Jo|IIIJo|III Jo|Jd|Ap";

	$regex = "#((". $books .")[.]*\s*(\d+)((\s|:)*)(\d*)((\d|:|-|,|\s)*))#is";

	// perform the replacement
	return preg_replace_callback( $regex, 'textBibleReferencesToLinks_replacer', $text );
}
/**
* Replaces the matched tags
* @param array An array of matches (see preg_match_all)
* @return string
*/
function textBibleReferencesToLinks_replacer( &$matches ) {
	global $database, $mosConfig_live_site;
	static $bookCodes = array(
		"Gn" => 1,
		"Êx" => 2,
		"Lv" => 3,
		"Nm" => 4,
		"Dt" => 5,
		"Js" => 6,
		"Jz" => 7,
		"Rt" => 8,
		"1Sm" => 9,
		"1 Sm" => 9,
		"ISm" => 9,
		"I Sm" => 9,
		"2Sm" => 10,
		"2 Sm" => 10,
		"IISm" => 10,
		"II Sm" => 10,
		"1Rs" => 11,
		"1 Rs" => 11,
		"IRs" => 11,
		"I Rs" => 11,
		"2Rs" => 12,
		"2 Rs" => 12,
		"IIRs" => 12,
		"II Rs" => 12,
		"1Cr" => 13,
		"1 Cr" => 13,
		"ICr" => 13,
		"I Cr" => 13,
		"2Cr" => 14,
		"2 Cr" => 14,
		"IICr" => 14,
		"II Cr" => 14,
		"Ed" => 15,
		"Ne" => 16,
		"Et" => 17,
		"Jó" => 18,
		"Sl" => 19,
		"Pv" => 20,
		"Ec" => 21,
		"Ct" => 22,
		"Is" => 23,
		"Jr" => 24,
		"Lm" => 25,
		"Ez" => 26,
		"Dn" => 27,
		"Os" => 28,
		"Jl" => 29,
		"Am" => 30,
		"Ob" => 31,
		"Jn" => 32,
		"Mq" => 33,
		"Na" => 34,
		"He" => 35,
		"Sf" => 36,
		"Ag" => 37,
		"Zc" => 38,
		"Ml" => 39,
		"Mt" => 40,
		"Mc" => 41,
		"Lc" => 42,
		"Jo" => 43,
		"At" => 44,
		"Rm" => 45,
		"1Co" => 46,
		"1 Co" => 46,
		"ICo" => 46,
		"I Co" => 46,
		"2Co" => 47,
		"2 Co" => 47,
		"IICo" => 47,
		"II Co" => 47,
		"Gl" => 48,
		"Ef" => 49,
		"Fp" => 50,
		"Cl" => 51,
		"1Ts" => 52,
		"1 Ts" => 52,
		"ITs" => 52,
		"I Ts" => 52,
		"2Ts" => 53,
		"2 Ts" => 53,
		"IITs" => 53,
		"II Ts" => 53,
		"1Tm" => 54,
		"1 Tm" => 54,
		"ITm" => 54,
		"I Tm" => 54,
		"2Tm" => 55,
		"2 Tm" => 55,
		"IITm" => 55,
		"II Tm" => 55,
		"Tt" => 56,
		"Fm" => 57,
		"Hb" => 58,
		"Tg" => 59,
		"1Pe" => 60,
		"1 Pe" => 60,
		"IPe" => 60,
		"I Pe" => 60,
		"2Pe" => 61,
		"2 Pe" => 61,
		"IIPe" => 61,
		"II Pe" => 61,
		"1Jo" => 62,
		"1 Jo" => 62,
		"IJo" => 62,
		"I Jo" => 62,
		"2Jo" => 63,
		"2 Jo" => 63,
		"IIJo" => 63,
		"II Jo" => 63,
		"3Jo" => 64,
		"3 Jo" => 64,
		"IIIJo" => 64,
		"III Jo" => 64,
		"Jd" => 65,
		"Ap" => 66);

	if ($bookCodes[$matches[2]])
	{
		$database->setQuery( "SELECT bookId, ordering, bookName, location, qtdChapters "
			. "\nFROM #__bible_book"
			. "\nwhere ordering = '{$bookCodes[$matches[2]]}'"
		);

		$database->loadObject($book);

		$capitulo = trim($matches[3]);
		$versiculo = trim($matches[6]);
		$final = trim($matches[7]);

		$windowTitle = "$book->bookName $capitulo";

		if ($final == "") $final = $versiculo;

		if (preg_match("/\-[1-9]*/",$final))
			$final = substr($final,1);

		if (is_numeric($capitulo) && is_numeric($versiculo) && is_numeric($final))
		{
			$database->setQuery( "SELECT verse, verseText "
				. "\nFROM #__bible"
				. "\nwhere bookOrdering = '{$bookCodes[$matches[2]]}'"
				. "\nand chapter = $capitulo"
				. "\nand verse between $versiculo and $final"
				. "\norder by verse"
				);

			$verses = $database->loadObjectList();

			$windowTitle .= ":$versiculo"
				. ($versiculo==$final?"":"-$final");

			foreach ($verses as $verse)
			{
				$bibleHTMLText .= ($bibleHTMLText == ""?"":"<br>")
					 . ($versiculo==$final?"":"<sup>($verse->verse)</sup>") 
					 . "$verse->verseText";
			}
		}

		if ($bibleHTMLText == "")
			$bibleHTMLText = "Clique para consultar";

		$url = sefRelToAbs("$mosConfig_live_site/index2.php?option=bible&Itemid=71&task=viewBook&id={$bookCodes[$matches[2]]}&limitstart=" . ($matches[3]-1) . "#{$matches[6]}");

		$bibleHTMLText = addslashes($bibleHTMLText);

		return "<a onMouseOver=\"return overlib('$bibleHTMLText', CAPTION, '$windowTitle', WIDTH, 350, BELOW, RIGHT);\" onMouseOut=\"return nd();\" href=\"$url\" target=biblia>{$matches[0]}</a>";

	}
	else return $matches[0];
}
?>
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:10000;"></div>
<script language="Javascript" src="<?php echo $mosConfig_live_site;?>/includes/js/overlib_mini.js"></script>

