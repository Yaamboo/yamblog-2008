<?php
class Tekstielementti
{
	function __construct()
	{
		
	}
	
	function muotoile($teksti, $tunnistautunut = false)
	{
		// Tehdään muotoilut tekstiin...	
		
		$teksti = str_replace("<", "&lt;", $teksti);
		$teksti = str_replace(">", "&gt;", $teksti);
		
		// Lisätään välilyönti alkuun ja loppuun niin päästään helpommalla
		$teksti = " ".$teksti;
		
		// Ensin selataan kaikki tagit ja niiden vastakappaleet, jos jotain puuttuu laitetaan loppuun sulku
		$paikka = false;
		$tagit = array("url", "b", "i", "u", "vip", "img", "youtube", "list", "*", "quote", "video");
		$avoimetTagit = array();
		$videoidenmäärä = 0;
		while($paikka < strlen($teksti))
		{
			// Etsitään [-merkki
			$taginAvaus = strpos($teksti, "[", $paikka);
			if($taginAvaus !== false)
			{
				// Katsotaan mitä sen jälkeen tulee...
				if(substr($teksti, $taginAvaus+1, 1) == "/")
				{
					// Sulkutagi...
					$sulkutagi = true;
				}
				else
					$sulkutagi = false;
				foreach($tagit as $avain => $tagi)
				{
					if($sulkutagi == true)
					{
						if(substr($teksti, $taginAvaus+2, strlen($tagi)) == $tagi)
						{
							$avoimetTagit[$tagi]--;
							// echo "/".$tagi." ".$taginAvaus." ".$paikka." <br />";
						}
					}
					else
					{
						if(substr($teksti, $taginAvaus+1, strlen($tagi)) == $tagi)
						{
							$avoimetTagit[$tagi]++;
							// echo $tagi." ".$taginAvaus." ".$paikka." <br />";
						}
						if($tagi == "video")
							$videoidenmäärä++;
					}
				}
				
				$paikka = $taginAvaus+2;
			}
			else
				$paikka = strlen($teksti);
		}
		// Suljetaan avoimet tagit...
		foreach($avoimetTagit as $tagi => $määrä)
		{
			if($määrä > 0)
			{
				$teksti .= " [/".$tagi."]";
				$avoimetTagit[$tagi]--;
			}
		}
		
		// Lisätään välilyönti alkuun ja loppuun niin päästään helpommalla
		$teksti = $teksti." ";
		
		// Tehdään urleista linkkejä...
		$url = "((ht|f)tps?\:\/\/)([a-zA-Z0-9\:\/\-\+\?\&\.\=\_\~\#\%]*)";
		// [url=osoite]tekstiä[/url]
		$teksti = preg_replace("/([[:space:][:punct:]]+)\[url\=(".$url.")\](.+?)\[\/url\]([[:space:][:punct:]]+)/is", "$1<a href=\"$2\">$6</a>$7", $teksti);
		// [url]osoite[/url]
		$teksti = preg_replace("/([[:space:][:punct:]]+)(\[url\])(".$url.")(\[\/url\])([[:space:][:punct:]]+)/is", "$1<a href=\"$3\">$3</a>$8", $teksti);
		// osoite
		$teksti = preg_replace("/([[:space:]]+)(".$url.")([[:space:]]+)/is", "$1<a href=\"$2\">$2</a>$6", $teksti);
		
		// Lihavointi...
		$teksti = preg_replace("/\[b\](.+?)\[\/b\]/is", "<b>$1</b>", $teksti);
		// Kursiivi...
		$teksti = preg_replace("/\[i\](.+?)\[\/i\]/is", "<i>$1</i>", $teksti);
		// Alleviivaus....
		$teksti = preg_replace("/\[u\](.+?)\[\/u\]/is", "<u>$1</u>", $teksti);
		
		// Vippiteksti
		if($tunnistautunut)
			$teksti = preg_replace("/\[vip\](.+?)\[\/vip\]/is", "<span class=\"vippiteksti\" onmousemove=\"naytaApulaatikko('vip_on');\" onmouseout=\"piilotaApulaatikko('vip_on');\">$1</span>", $teksti);
		else
			$teksti = preg_replace("/\[vip\](.+?)\[\/vip\]/is", "<span class=\"infotext\" onmousemove=\"naytaApulaatikko('vip');\" onmouseout=\"piilotaApulaatikko('vip');\">[Tämä osa tekstistä näkyy vain tunnistautuneille käyttäjille]</span>", $teksti);
		
		// Kuva
		$teksti = preg_replace("/\[img([ ]?(.+?))?\](".$url.")\[\/img\]/is", "<img src=\"$3\"$1 />", $teksti);
		
		// Juutuupi
		$youtubeKoodi = '</p><div class="youtube"><object width="704" height="425"><param name="movie" value="http://www.youtube.com/v/$1&amp;hl=en&amp;fs=1&amp;rel=0"></param><param name="allowFullScreen" value="true"></param><embed src="http://www.youtube.com/v/$1&amp;hl=en&amp;fs=1&amp;rel=0" type="application/x-shockwave-flash" allowfullscreen="true" width="704" height="425"></embed></object><br /><a href="http://youtube.com/watch?v=$1&amp;fmt=18">Korkeatarkkuuksinen versio</a></div><p>';
		$teksti = preg_replace("/\[youtube\](.+?)\[\/youtube\]/is", "$youtubeKoodi", $teksti);
		
		// Video
		if($videoidenmäärä > 0)
		{
			$i = 0;
			while($videoidenmäärä > 0)
			{
				$videoKoodi = '	<div id="videoContainer_'.$this->id.'_'.$i.'" class="videoContainer">' .
						'<img src="video/$1.jpg"/>' .
						'Tämän videon katseleminen vaatii <a href="http://get.adobe.com/flashplayer/">Flash Playerin</a>.</div>' .
						'<script type="text/javascript">' .
							'var s1 = new SWFObject("elementit/player.swf","ply_'.$this->id.'_'.$i.'","704","400","9","#FFFFFF");' .
							's1.addParam("allowfullscreen","true");' .
							's1.addParam("allowscriptaccess","always");' .
							's1.addParam("flashvars","file=../video/$1.mp4&image=video/$1.jpg&controlbar=over&backcolor=E5EAE5&frontcolor=444444&lightcolor=ffffff&screencolor=000000");' .
							's1.write("videoContainer_'.$this->id.'_'.$i.'");' .
						'</script>';
				$teksti = preg_replace("/\[video\](.+?)\[\/video\]/is", "$videoKoodi", $teksti, 1);
				$videoidenmäärä--;
				$i++;
			}
		}
		
		// Lainaus...
		$quotekoodi = "</p><div class=\"lainaus\"><b>Lainaus:</b><br />$1</div><p>";
		$quote2koodi = "</p><div class=\"lainaus\"><b>$1 kirjoitti:</b><br />$2</div><p>";
		$teksti = preg_replace("/\[quote\](.+?)\[\/quote\]/is", "$quotekoodi", $teksti);
		$teksti = preg_replace("/\[quote=\"([a-zA-Z0-9\:\/\-\+\?\&\.\=\_\~\#]*)\"\](.+?)\[\/quote\]/is", "$quote2koodi", $teksti);
		
		// Listat...
		$teksti = preg_replace("/\[list=(1|A|a|I|i)+\](.+?)\[\/list\]/is", "<ol type=\"$1\">$2</ol>", $teksti);
		$teksti = preg_replace("/\[list\](.+?)\[\/list\]/is", "<ul>$1</ul>", $teksti);
		$teksti = preg_replace("/\[\*\](.+?)\[\/\*\]/is", "<li>$1</li>", $teksti);
		
		// Poistetaan alussa lisätyt välit...
		$teksti = substr($teksti, 1, -1);
		$teksti = nl2br($teksti);
		return $teksti;
	}
}
?>
