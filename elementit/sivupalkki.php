<div id="sivupalkki">
<?php
$vuosi = date("Y");
$kuukausi = date("n");
$viikko = 0;

$kuukaudet = array("Tammi", "Helmi", "Maalis", "Huhti", "Touko", "Kesä", "Heinä", "Elo", "Syys", "Loka", "Marras", "Joulu");

?><div id="kalenterit"><table class="kalenteri"><thead><tr><th colspan="8">
		<?php
		$tulostettavaKuukausi = $kuukausi;
		if($tulostettavaKuukausi < 10)
			$tulostettavaKuukausi = "0".$tulostettavaKuukausi;
		echo "<a href=\"$vuosi-$tulostettavaKuukausi\">".$kuukaudet[$kuukausi-1]."kuu</a> ".$vuosi."</th></tr>";
		?><tr><th class="viikko">Vko</th><th>M</th><th>T</th><th>K</th><th>T</th><th>P</th><th>L</th><th>S</th></tr></thead>
		<tbody>
		<?php
		$viikkoja = 0;
		for($i = 1; $i <= date("t", strtotime("$vuosi-$kuukausi-01")); $i++)
		{
			$päiväys = "$vuosi-$kuukausi-$i";
			$päivänNumero = date("w", strtotime($päiväys));
			if($päivänNumero == 0)
				$päivänNumero = 7;
			//echo $päiväys." ".$viikko." ".date("W", strtotime($päiväys));
			if($viikko != date("W", strtotime($päiväys)) || $i == 1)
			{
				// Uusi viikko
				$viikko = date("W", strtotime($päiväys));
				if($i == 1)
				{
					echo "<tr><td class=\"viikko\">".$viikko."</td>";
					// Ekstratarkistukset: montako riviä pitää siirtää...
					if($päivänNumero > 1)
					{
						for($j = 1; $j < $päivänNumero; $j++)
							echo "<td class=\"tyhja\"></td>";
					}
				}
				else
					echo "</tr><tr><td class=\"viikko\">".$viikko."</td>";
				$viikkoja++;
			}
			// Tarkistetaan onko päivälle merkintää...
			$q = "SELECT id FROM merkinta WHERE paivays LIKE \"".date("Y-m-d", strtotime($päiväys))."%\" AND julkaistu = 1";
			if($tietokanta->haeYksi($q))
			{
				?><td class="luettavaa"<?php
				if(date("Y-m-d") == date("Y-m-d", strtotime($päiväys)))
					echo " style=\"outline: 1px #2A2 solid;\""?>>
				<a href="<?php echo date("Y-m-d", strtotime($päiväys));?>"
				onmousemove="naytaKalenterilaatikko('kalenteripaivakuvaus', '<?php echo date("Y-m-d", strtotime($päiväys))?>');"
				onmouseout="piilotaKalenterilaatikko('kalenteripaivakuvaus');">
				<?php
				$luettavaa = true;
			}
			else
			{
				if(date("Y-m-d") == date("Y-m-d", strtotime($päiväys)))
					echo "<td style=\"outline: 1px #2A2 solid;\">";
				else
					echo "<td>";
				$luettavaa = false;
			}
			
			if($i < 10)
				echo "0".$i;
			else
				echo $i;
			
			if($luettavaa == true)
				echo "</a>";
			echo "</td>";
		}
		$kuukausi++;
		if($päivänNumero < 7)
		{
			while($päivänNumero < 7)
			{
				echo "<td class=\"tyhja\"></td>";
				$päivänNumero++;
			}
		}
		?>
		</tr></tbody></table></div><?php
?>

<div id="sivuHaku">
<form action="index.php" method="get"><input type="hidden" name="sivu" value="blog" />
<input type="text" size="15" name="haku" value="" />
<input type="submit" value="Hae!" />
</form>
</div>

	<div id="uusimmatKommentit">
	<a href="rss.php?kommentit=0"><img src="kuvat/rss.png" alt="Kommenttien RSS" title="Kommenttien RSS" style="border: 0; float: right; width: 18px; height: 18px; vertical-align: top; margin: 2px;" /></a>
	<h4>Uusimmat kommentit</h4>
	<?
	// Viimeisimmät kommentit.
	
	$q = "SELECT * FROM kommentit WHERE poistettu IS NULL ORDER BY paivays DESC LIMIT 0,5";
	$tulos = $tietokanta->hae($q);
	if($tulos->onkoRivejä())
	{
		echo "<p>";
		while($tulos->onkoRivejä())
		{
			$kommentti = $tulos->seuraava();
			echo "<a href=\"".$kommentti->mid."/#k".$kommentti->id."\">";
			
			if($kommentti->uid == 0)
			{
				if(strlen($kommentti->tunnus) > 0)
					echo htmlspecialchars($kommentti->tunnus);
				else
					echo "Anonyymi";
			}
			else
			{
				$kirjoittaja = $yleisfunktiot->haeNimi($kommentti->uid);
				if($kirjoittaja)
					echo $kirjoittaja;
				else
				{
					$kirjoittaja = $tietokanta->haeTunnus($kommentti->uid);
					if($kirjoittaja)
						echo $kirjoittaja;
					else
						echo "Anonyymi";
				}
			}
			echo ":</a> ";
			echo htmlspecialchars(substr($kommentti->teksti, 0, 50))."...<br />";
		}
		echo "</p>";
	}
	else
		echo "<p>Ei kommentteja.</p>";
	?>
	</div>
	
<div id="satunnainenKuva">
<h4>Satunnainen kuva</h4>
<?php
// Arvotaan ekana hakemisto...

// Tämä määrittää hakemiston josta kuvat luetaan...
$_pääkansio = "kuvagalleria";

$polku = $_pääkansio;

if(is_dir($polku))
{
	$hakemistot = scandir($polku);
	
	// Apumuuttuja
	$hakemistot2 = array();
	foreach($hakemistot as $avain => $arvo)
	{
		if(substr($arvo, 0, 1) <> "." && is_dir($polku."/".$arvo))
			$hakemistot2[] = $polku."/".$arvo;
	}
	$hakemistot = $hakemistot2;
	
	// 5 kertaa yritetään hakea kuvaa, jos sattuu arpomaan tyhjän kansion.
	// Mikäli kuva löytyy, voidaan suoraan heivata yritysmäärä viiteen. 
	$yritykset = 0;
	while($yritykset < 5)
	{
		$kansio = $hakemistot[array_rand($hakemistot, 1)];
		// Katsotaan kansion sisälle...
		$tiedostot = scandir($kansio);
		$tiedostot2 = array();
		$kuvienMäärä = 0;
		foreach($tiedostot as $avain => $tiedosto)
		{
			if((substr($tiedosto, -4) == ".jpg" || substr($tiedosto, -4) == ".gif" 
				|| substr($tiedosto, -4) == ".png" || substr($tiedosto, -4) == ".JPG"
				|| substr($tiedosto, -4) == ".GIF" || substr($tiedosto, -4) == ".PNG")
				&& substr($tiedosto, 0, 1) != "."
				&& substr($tiedosto, 0, 6) != "thumb.")
			{
				$tiedostot2[] = array("kansio" => basename($kansio), "tiedosto" => $tiedosto, "thumb" => $kansio."/thumb.".$tiedosto);
				$kuvienMäärä++;
			}
		}
		if($kuvienMäärä > 0)
		{
			$kuva = $tiedostot2[array_rand($tiedostot2, 1)];
			?><p><a href="index.php?sivu=galleria&amp;kansio=<?php echo $kuva["kansio"];?>&amp;image=<?php echo $kuva["tiedosto"];?>">
			<img src="<?php echo $kuva["thumb"];?>" alt="Satunnainen kuva" /></a>
			<?php
			// Haetaan myös kommentti jos sellainen on
			$q = "SELECT * FROM galleriakommentit WHERE kansio='".$kuva["kansio"]."' AND kuva='".$kuva["tiedosto"]."'";
			$tiedot = $tietokanta->hae($q);
			if($tiedot->onkoRivejä())
			{
				$tiedot = $tiedot->seuraava();
				echo "<br />".nl2br(htmlspecialchars($tiedot->teksti));
			}
			echo "</p>";
			$yritykset = 5;
		}
		else
			$yritykset++;
	}
}
else
	echo "Ei kuvakansiota?";

?>
</div>
	
<div id="uusimmatBiisit">
<h4>Nyt soi</h4>
<p style="margin-bottom: 0;">
<?php
// Haetaan viimeisimmät biisit
// Huomio vuodelta 2020: tätä ei enää tarjota :(
$url= "http://ws.audioscrobbler.com/1.0/user/Yaamboo/recenttracks.rss";

// Avataan yhteys tiedostoon...
if(!($yhteys = fopen($url, "r"))) {
    echo "Virhe yhdistäessä palvelimeen";
    fclose($yhteys);
}
else
{
	$data = "";
	while (!feof($yhteys)) {
	  $data .= fread($yhteys, 8192);
	}
	// Suljetaan yhteys
	fclose($yhteys);
	
	// Parseroidaan
	$xml = new SimpleXMLElement($data);
	$biisit = $xml->channel->item;
	if($biisit)
	{
		$i = 1;
		foreach($biisit as $avain => $arvo)
		{
			echo "<a href=\"".htmlspecialchars($arvo->link)."\">".htmlspecialchars($arvo->title)."</a><br />";
			echo "<span style=\"font-size: x-small;\">".gmdate("j.n.Y H:i", strtotime($arvo->pubDate))."</span><br />";
			$i++;
			if($i == 6)
				break;
		}
	}
	else
		echo "Ei kappaletietoja<br />";
	
	echo "</p><p style=\"text-align: right; margin-top: 0;\"><a href=\"".htmlspecialchars($xml->channel->link)."\">Lisää...</a></span>";
}
?></p>
</div>

	<div id="kalenteripaivakuvaus" style="display: none;"><img src="kuvat/loading.gif" alt="Loading..." /></div>
<img src="kuvat/loading.gif" style="height: 0; width: 0;" alt="Loading..." />
</div>
