<?php
// On muuten ihan hirveetä jaskaa tää koodi :D

require_once("engine/kuvagalleria.php");
// Tämä määrittää hakemiston josta kuvat luetaan...
$_pääkansio = "kuvagalleria";

if($_p["kansio"])
	$polku = $_pääkansio."/".basename($_p["kansio"]);
else
	$polku = $_pääkansio;

$galleria = new Kuvagalleria($_pääkansio, $polku);

if($_p["image"])
{
	
	// Haetaan kuvan tietoja...
	$kuvanNimi = $_p["image"];
	?>
	<div id="pääpalkki">
	<?php
	echo "<h2><a href=\"index.php?sivu=galleria\">Galleria</a> &gt; <a href=\"index.php?sivu=galleria&amp;kansio=".htmlspecialchars(basename($polku))."\">".htmlspecialchars(basename($polku))."</a> &gt; ".htmlspecialchars(basename($kuvanNimi))."</h2>"; 
	
	$kuvaOlemassa = file_exists($polku."/".htmlspecialchars(basename($kuvanNimi)));
	if($kuvaOlemassa)
	{
		
		?>
		<div id="galleriakuva">
		<?php
		list($leveys, $korkeus, $tyyppi, $attr) = getimagesize($polku."/".$kuvanNimi);	
		
		$maksimileveys = 720;	// Kuinka leveä kuva saa olla
		
		if($leveys > $maksimileveys)
		{
			$suhde = $leveys/$korkeus;
			
			if($suhde >= 1)
			{
				$uusiLeveys = $maksimileveys;
				$uusiKorkeus = $maksimileveys/$suhde;
			}
			else
			{
				$uusiKorkeus = $maksimileveys;
				$uusiLeveys = $maksimileveys*$suhde;
			}
		}
		else
		{
			$uusiLeveys = $leveys;
			$uusiKorkeus = $korkeus;
		}
		
		echo "<a href=\"index.php?sivu=galleria&amp;kansio=".htmlspecialchars(basename($polku))."&image=".$galleria->haeSeuraavaKuva($kuvanNimi)."\"><img src=\"".$polku."/".htmlspecialchars(basename($kuvanNimi))."\" border=\"0\" width=\"".$uusiLeveys."\" height=\"".$uusiKorkeus."\" /></a>";
		
		//echo "<br /><a href=\"".$polku."/".htmlspecialchars(basename($kuvanNimi))."\" style=\"font-size: small;\">Täysikokoinen kuva</a>";
		// Tulostetaan metadata
	
		?>
		</div>
		<div id="galleriakuvaselain">
		<table border="0" cellspacing="0" cellpadding="0">
		<tr>
		<?php
		for($i=-2; $i <= 2; $i++)
		{
			
			if($i <> 0)
			{
				echo "<td style=\"width: 165px;\">";
				if($galleria->haeKuva($kuvanNimi, $i))
					echo "<a href=\"index.php?sivu=galleria&amp;kansio=".htmlspecialchars(basename($polku))."&image=".$galleria->haeKuva($kuvanNimi, $i)."\"><img src=\"".$polku."/thumb.".htmlspecialchars(basename($galleria->haeKuva($kuvanNimi, $i)))."\" alt=\"".htmlspecialchars(basename($galleria->haeKuva($kuvanNimi, $i)))."\" /></a>";
				else
					echo "-";
			}
			else
				echo "<td style=\"width: 2px; background-color: #BBB;\"> ";
			echo "</td>";
		}
		?>
		</tr>
		<tr><td>&lt;&lt;</td><td>&lt;</td><td style="width: 2px; background-color: #BBB;"> </td><td>&gt;</td><td>&gt;&gt;</td></tr>
		</table>
		</div>
		<?php
		/*
		echo "<p style=\"text-align: center\"><a href=\"index.php?sivu=galleria&amp;kansio=".htmlspecialchars(basename($polku))."&image=".$galleria->haeEdellinenKuva($kuvanNimi)."\">Edellinen</a> | ";
		echo "<a href=\"index.php?sivu=galleria&amp;kansio=".htmlspecialchars(basename($polku))."&image=".$galleria->haeSeuraavaKuva($kuvanNimi)."\">Seuraava</a></p>";
		*/
		?>
		
		<?php
		
		// Haetaan kuvan kommentti jos sellainen on
		$q = "SELECT * FROM galleriakommentit WHERE kansio='".basename($_p["kansio"])."' AND kuva='".basename($kuvanNimi)."'";
		$tiedot = $tietokanta->hae($q);
		if($tiedot->onkoRivejä())
		{
			$tiedot = $tiedot->seuraava();
			$kuvaus = nl2br(htmlspecialchars($tiedot->teksti));
			if($käyttäjä->onkoYlläpitäjä())
			{
				// Tulostetaan muokkaus/poistoruutu...
				?>
				Ylläpitäjä: <a href="javascript:naytaKuvakommenttiMuokkain()" style="font-size: small;">Muokkaa kuvausta...</a>
				<div id="kuvakommenttimuokkain" style="display: none; margin-bottom: 1em;">
				<form action="tallennakuvakommentti.php" method="post">
				<input type="hidden" name="t" value="m" />
				<input type="hidden" name="sid" value="<?php echo session_id();?>" />
				<input type="hidden" name="kansio" value="<?php echo basename($_p["kansio"]);?>" />
				<input type="hidden" name="kuva" value="<?php echo basename($kuvanNimi);?>" />
				<textarea name="teksti" rows="5" cols="60"><?php echo htmlspecialchars($tiedot->teksti);?></textarea><br />
				<input type="submit" value="Tallenna" /><input type="button" value="Peruuta" onclick="naytaKuvakommenttiMuokkain();" />
				</form>
				<form action="tallennakuvakommentti.php" method="post">
				<input type="hidden" name="t" value="p" />
				<input type="hidden" name="sid" value="<?php echo session_id();?>" />
				<input type="hidden" name="kansio" value="<?php echo basename($_p["kansio"]);?>" />
				<input type="hidden" name="kuva" value="<?php echo basename($kuvanNimi);?>" />
				<input type="submit" value="Poista" />
				</form>
				</div>
				<?php
			}
		}
		else
		{
			// Tulostetaan lisäämisjuttu, jos käyttäjä superi...
			if($käyttäjä->onkoYlläpitäjä())
			{
				?>
				<a href="javascript:naytaKuvakommenttiMuokkain()">Lisää kommentti kuvaan...</a>
				<div id="kuvakommenttimuokkain" style="display: none; margin-bottom: 1em;">
				<form action="tallennakuvakommentti.php" method="post">
				<input type="hidden" name="t" value="u" />
				<input type="hidden" name="sid" value="<?php echo session_id();?>" />
				<input type="hidden" name="kansio" value="<?php echo basename($_p["kansio"]);?>" />
				<input type="hidden" name="kuva" value="<?php echo basename($kuvanNimi);?>" />
				<textarea name="teksti" rows="5" cols="60"></textarea><br />
				<input type="submit" value="Tallenna" /><input type="button" value="Peruuta" onclick="naytaKuvakommenttiMuokkain();" />
				</form>
				</div>
				<?php
			}
		}
		
		?>
		<?php
	}
	else
		echo "<p><b>Virhe:</b> Tiedostoa ei löytynyt!</p>";
	?>
	</div>
	<div id="sivupalkki">
	<?php
	if($_p["image"])
	{
		if($kuvaOlemassa)
		{
			echo "<div>";
			echo "<h4>Kuvaus</h4>";
			echo "<p style=\"font-size: 110%;\">";
			if($kuvaus)
				echo $kuvaus;
			else
				echo "(Ei kuvausta)";
			echo "</p><p><a href=\"".$polku."/".htmlspecialchars(basename($kuvanNimi))."\" style=\"font-size: small;\">Näytä täysikokoinen kuva</a>";
			echo "</p>";
			echo "</div>";
			echo "<div><h4>Metadata</h4><p>";
			echo "<b>Tiedostonimi:</b><br />".$kuvanNimi."<br />";
			echo "<b>Tiedostokoko:</b><br /> ".$galleria->palautaKoko(filesize($polku."/".htmlspecialchars(basename($kuvanNimi))))." (".filesize($polku."/".htmlspecialchars(basename($kuvanNimi)))." t)<br />";
			echo "<b>Resoluutio:</b><br />".$leveys."x".$korkeus."<br />";
			$metadata = $galleria->palautaMetadata($polku."/".htmlspecialchars(basename($kuvanNimi)));
			if($metadata)
			{
				echo $metadata;
			}
			echo "</p></div>";
			// Katsotaan liittyykö joku merkintä tähän...
			$q = "SELECT id, otsikko, paivays FROM merkinta WHERE galleria = '".basename($polku)."'";
			$tulos = $tietokanta->hae($q);
			if($tulos->onkoRivejä())
			{
				$tiedot = $tulos->seuraava();
				echo "<div><h4>Lue myös</h4><p><a href=\"".$tiedot->id."/".urlencode($tiedot->otsikko)."\">".htmlspecialchars($tiedot->otsikko)."</a>  (".date("d.m.Y", strtotime($tiedot->paivays)).")</p></div>";
			}
		}
		echo "<div><a href=\"index.php?sivu=galleria&amp;kansio=".htmlspecialchars(basename($polku))."\">Palaa tiedostoluetteloon</a></div>";
	}
	else
	{
		
	}
	?>
</div>
	<?php
}
else
{
	$tiedostolistaus = $galleria->haeHakemistonTiedostot($polku);
	
	if(is_dir($polku) && $_p["kansio"])
	{
		if($tiedostolistaus)
		{
			$kuvienMäärä = count($tiedostolistaus);
			
			echo "<h2><a href=\"index.php?sivu=galleria\">Galleria</a> &gt; ".$galleria->palautaKansionNimi(htmlspecialchars(basename($polku)))."</h2>";
			
			// Katsotaan liittyykö joku merkintä tähän...
			$q = "SELECT id, otsikko FROM merkinta WHERE galleria = '".basename($polku)."'";
			$tulos = $tietokanta->hae($q);
			if($tulos->onkoRivejä())
			{
				$tiedot = $tulos->seuraava();
				echo "<p>Lue myös: <a href=\"".$tiedot->id."/".urlencode($tiedot->otsikko)."\">".htmlspecialchars($tiedot->otsikko)."</a></p>";
			}
			
			echo "<p>".$kuvienMäärä." kuva";
			if($kuvienMäärä <> 1)
				echo "a";
			echo " kansiossa<br />Klikkaa kuvaa nähdäksesi sen isompana.</p>";
			
			echo "<table border=\"0\" style=\"margin-left: auto; margin-right: auto;\">";
			
			for($i = 0; $i < $kuvienMäärä; $i++)
			{
				
				$kuvanNimi = $tiedostolistaus[$i];
				
				// Haetaan kuvasta turhaa tietoa kuten koko :)
				$tiedostonKoko = filesize($polku."/".$kuvanNimi);
				list($leveys, $korkeus, $tyyppi, $attr) = getimagesize($polku."/".$kuvanNimi);
				
				// Haetaan thumbnaili....
				if(!file_exists($polku."/thumb.".$kuvanNimi))
					$galleria->luoPikkukuva($kuvanNimi, basename($_p["kansio"]));
				
				// Tulostetaan hienosti kuvat taulukkoon... :)
				if($kuvaRivillä == 0)
					echo "<tr height=\"165\" valign=\"bottom\">";
				echo "<td width=\"150\" style=\"background-color: #EFF0EF; text-align: center;\">";
				
				echo "<a href=\"index.php?sivu=galleria&amp;kansio=".basename($polku)."&amp;image=".$kuvanNimi."\"><img src=\"".$polku."/thumb.".$kuvanNimi."\" border=\"0\" /></a>";
				echo "<span style=\"color: #777777; font-size: x-small;\"><br />".$kuvanNimi."<br />".$leveys."x".$korkeus.", ";
				echo $galleria->palautaKoko($tiedostonKoko)."<br /></span>";
					
				echo "</td>";
				if($kuvaRivillä == 5)
				{
					echo "</tr>";
					$kuvaRivillä = 0;
				}
				else
					$kuvaRivillä++;
				
			}
			
			echo "</table>";
			echo "<p><a href=\"index.php?sivu=galleria\">Selaa galleriaa</a></p>";
		}
		else
		{
			echo "<h2><a href=\"index.php?sivu=galleria\">Galleria</a> &gt; ".htmlspecialchars(basename($polku))."</h2>";
			echo "<p>Hakemistossa ei ole tiedostoja.</p>";
			echo "<p><a href=\"index.php?sivu=galleria\">Selaa galleriaa</a></p>";
		}
	}
	else
	{
		$polku = $_pääkansio;
		// Haetaan kaikki ennalta määritellyn hakemiston alla olevat kansiot :)
		?>
				
		<h2>Galleria</h2>
		<p>Katsele kuvia maailmalta. Alle on listattu kaikki gallerian kansiot.</p>
		
		<?php
		echo "\n<table border=\"0\" style=\"margin-left: auto; margin-right: auto;\">";
		$kuvarivillä = 0;
		$tiedostot = scandir($polku);
		foreach($tiedostot as $avain => $kansio)
		{
			if(substr($kansio,0,1) != "." && is_dir($polku."/".$kansio))
			{
				if($kuvaRivillä == 0)
					echo "<tr height=\"165\" valign=\"bottom\" align=\"left\">";
				echo "<td width=\"150\" style=\"background-color: #EFF0EF; text-align: center;\">";

				// Haetaan esimerkkikuva kansion sisältä.
				$polku2 = $polku."/".$kansio;
				$hakemisto2 = opendir($polku2);
				$ok = false;
				while (false !== ($tiedosto = readdir($hakemisto2)) && $ok === false)
				{ 
					if((substr($tiedosto, -4) == ".jpg" || substr($tiedosto, -4) == ".gif" 
						|| substr($tiedosto, -4) == ".png" || substr($tiedosto, -4) == ".JPG"
						|| substr($tiedosto, -4) == ".GIF" || substr($tiedosto, -4) == ".PNG")
						&& substr ($tiedosto, 0, 6) <> "thumb.")
					{
						$ok = true;
						break;
					}
				}
				
				if($ok == true)
				{
					
					list($leveys, $korkeus, $tyyppi, $attr) = getimagesize($polku2."/".$tiedosto);
					
					// Haetaan thumbnaili....
					if(!file_exists($polku2."/thumb.".$tiedosto))
						$galleria->luoPikkukuva($tiedosto, $kansio);
				
					echo "<a href=\"index.php?sivu=galleria&amp;kansio=".$kansio."\"><img src=\"".$polku2."/thumb.".$tiedosto."\" border=\"0\" /></a><br />";
					
				}
				else
					echo "<p style=\"color: #777777; font-size: x-small;\">Kansio on tyhjä</p>";
				echo "<a href=\"index.php?sivu=galleria&amp;kansio=".$kansio."\">".$galleria->palautaKansionNimi($kansio)."</a>";
				echo "</td>";
				if($kuvaRivillä == 5)
				{
					echo "</tr>";
					$kuvaRivillä = 0;
				}
				else
					$kuvaRivillä++;
			}
		}
		echo "</table>";
	}
}

?>