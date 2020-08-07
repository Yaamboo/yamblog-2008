	<div id="pääpalkki">
<?php
include("engine/blogimerkinta.php");

$tulostetaan = 0;
$tulostettavat = array();
if($_p["sivu"] && $_p["id"])
{
	// Haetaan kyseinen blogimerkintä kommentteineen jne.
	if($käyttäjä->onkoYlläpitäjä())
		$q = "SELECT * FROM merkinta WHERE id=".$_p["id"];
	else
		$q = "SELECT * FROM merkinta WHERE id=".$_p["id"]." AND julkaistu=1";
	$tulos = $tietokanta->hae($q);
	if($tulos->onkoRivejä())
	{
		$merkintä = $tulos->seuraava();
		$tulostettavat[] = new Blogimerkinta($merkintä->id, $merkintä->uid, $merkintä->paivays, $merkintä->muokattu, $merkintä->otsikko, $merkintä->teksti, $merkintä->julkaistu, $merkintä->biisi, $merkintä->paikka, $merkintä->galleria);
		$tulostetaan = 1;
	}
	else
	{
		$tulostettavat = null;
		$virhe = "<p>Blogimerkintää ".htmlspecialchars($_p["id"])." ei löytynyt.</p>";
	}
}
else if($_p["paivays"])
{
	// Haetaan blogimerkinnät siltä päivämäärältä
	if(ereg("([0-9]{4})-([0-9]{1,2})(-([0-9]{1,2}))?", $_p["paivays"]))
	{
		if($käyttäjä->onkoYlläpitäjä())
			$q = "SELECT * FROM merkinta WHERE paivays LIKE \"".$_p["paivays"]."%\" ORDER BY paivays DESC";
		else
			$q = "SELECT * FROM merkinta WHERE paivays LIKE \"".$_p["paivays"]."%\" AND julkaistu=1 ORDER BY paivays DESC";
		$tulos = $tietokanta->hae($q);
		if($tulos->onkoRivejä())
		{
			while($tulos->onkoRivejä())
			{
				$merkintä = $tulos->seuraava();
				$tulostettavat[] = new Blogimerkinta($merkintä->id, $merkintä->uid, $merkintä->paivays, $merkintä->muokattu, $merkintä->otsikko, $merkintä->teksti, $merkintä->julkaistu, $merkintä->biisi, $merkintä->paikka, $merkintä->galleria);
				$tulostetaan++;
			}
		}
		else
		{
			$tulostettavat = null;
			$virhe = "<p>Blogimerkintöjä ei löytynyt päivälle ".htmlspecialchars($_p["paivays"]).".</p>";
		}
	}
	else
	{
		$tulostettavat = null;
		$virhe = "<p>Päiväyksen muoto on väärä (toimivat esimerkit: 2008-08 tai 2008-08-30).</p>";
	}
}
else if($_p["haku"])
{
	if($_p["alku"])
		$alku = $_p["alku"];
	else
		$alku = 0;
	// TODO: leiki hakulausekkeella :D
	$hakulause = $_p["haku"];
	if(strlen($hakulause) < 3)
	{
		$virhe = "<p>Hakulauseen täytyy olla vähintään kolme merkkiä pitkä.</p>";
		$tulostettavat = null;
		$ehtotaulu = array("o" => true, "t" => true, "s" => true);
	}
	else
		{
		
		if(is_array($_p["kohde"]))
		{
			$ehdot = "";
			$ehdot2 = "";
			$ehtotaulu = array("o" => false, "t" => false, "s" => false);
			$i = 1;
			foreach($_p["kohde"] as $avain => $arvo)
			{
				if($i > 1)
				{
					$ehdot .= " + ";
					$ehdot2 .= ", ";
				}
				if($arvo == "o")
				{
					$ehtotaulu["o"] = true;
					$ehdot .= "(2 * (MATCH (otsikko) AGAINST ('".$hakulause."' IN BOOLEAN MODE)))";
					$ehdot2 .= "otsikko";
				}
				else if($arvo == "t")
				{
					$ehtotaulu["t"] = true;
					$ehdot .= "(1.5 * (MATCH (teksti) AGAINST ('".$hakulause."' IN BOOLEAN MODE)))";
					$ehdot2 .= "teksti";
				}
				else if($arvo == "s")
				{
					$ehtotaulu["s"] = true;
					$ehdot .= "(1 * (MATCH (paikka) AGAINST ('".$hakulause."' IN BOOLEAN MODE)))";
					$ehdot2 .= "paikka";
				}
				$i++;
			}
		}
		else
		{
			$ehdot = "(2 * (MATCH (otsikko) AGAINST ('".$hakulause."' IN BOOLEAN MODE))) + (1.5 * (MATCH (teksti) AGAINST ('".$hakulause."' IN BOOLEAN MODE))) + (1 * (MATCH (paikka) AGAINST ('".$hakulause."' IN BOOLEAN MODE)))";
			$ehdot2 = "otsikko, teksti, paikka";
			$ehtotaulu = array("o" => true, "t" => true, "s" => true);
		}
		
		if(!$_p["jarjestys"])
			$järjestys = "relevanssi DESC, paivays DESC";
		else
		{
			if($_p["jarjestys"] == "p")
				$järjestys = "paivays ASC";
			else if($_p["jarjestys"] == "p2")
				$järjestys = "paivays DESC";
			else if($_p["jarjestys"] == "a")
				$järjestys = "otsikko ASC, relevanssi DESC, paivays DESC";
			else
				$järjestys = "relevanssi DESC, paivays DESC";
		}
		
		if($käyttäjä->onkoYlläpitäjä())
			$q = "SELECT *, (".$ehdot.") AS relevanssi FROM merkinta WHERE MATCH(".$ehdot2.") AGAINST ('".$hakulause."' IN BOOLEAN MODE) ORDER BY ".$järjestys;
		else
			$q = "SELECT *, (".$ehdot.") AS relevanssi FROM merkinta WHERE MATCH(".$ehdot2.") AGAINST ('".$hakulause."' IN BOOLEAN MODE) AND julkaistu=1 ORDER BY ".$järjestys;
		
		//echo $q;
		$tulos = $tietokanta->hae($q);
		if($tulos->onkoRivejä())
		{
			$relevanssi = array();
			$isoinRelevanssi = 0;
			while($tulos->onkoRivejä())
			{
				$merkintä = $tulos->seuraava();
				$tulostettavat[] = new Blogimerkinta($merkintä->id, $merkintä->uid, $merkintä->paivays, $merkintä->muokattu, $merkintä->otsikko, $merkintä->teksti, $merkintä->julkaistu, $merkintä->biisi, $merkintä->paikka, $merkintä->galleria);
				$relevanssi[$merkintä->id] = $merkintä->relevanssi;
				if($merkintä->relevanssi > $isoinRelevanssi)
					$isoinRelevanssi = $merkintä->relevanssi;
				$tulostetaan++;
			}
		}
		else
		{
			$tulostettavat = null;
			$virhe = "<p>Ei hakutuloksia!</p>";
		}
	}
}
else
{
	// Palautetaan etusivu...
	if($_p["alku"])
		$alku = $_p["alku"];
	else
		$alku = 0;
	if($käyttäjä->onkoYlläpitäjä())
		$q = "SELECT * FROM merkinta ORDER BY paivays DESC LIMIT ".$alku.",5";
	else
		$q = "SELECT * FROM merkinta WHERE julkaistu=1 ORDER BY paivays DESC LIMIT ".$alku.",5";
	$tulos = $tietokanta->hae($q);
	if($tulos->onkoRivejä())
	{
		while($tulos->onkoRivejä())
		{
			$merkintä = $tulos->seuraava();
			$tulostettavat[] = new Blogimerkinta($merkintä->id, $merkintä->uid, $merkintä->paivays, $merkintä->muokattu, $merkintä->otsikko, $merkintä->teksti, $merkintä->julkaistu, $merkintä->biisi, $merkintä->paikka, $merkintä->galleria);
			$tulostetaan++;
		}
	}
	else
	{
		$tulostettavat = null;
		$virhe = "<p>Blogissa ei ole sisältöä.</p>";
	}
}

if($_p["haku"])
{
	$hakulauseke = $_p["haku"];
	?>
	<h2>Haku</h2>
	<p class="pikkutieto">Voit rajata hakua +/- -operaattoreilla, esim. "+omena -banaani" palauttaa tekstit joissa esiintyy omena, mutta ei banaani. Myöskin osittaisia sanoja voit hakea tähtimerkillä ("auto*" palauttaa autossa, autolla, autoon jne). <a href="http://dev.mysql.com/doc/refman/6.0/en/fulltext-boolean.html">Lisätietoja...</a></p>
	<form action="index.php" method="get">
	<input type="hidden" name="sivu" value="blog" />
	<table border="0" cellspacing="0" cellpadding="2">
	<tbody>
	<tr><td colspan="4">
	<input type="text" size="40" name="haku" value="<?php echo htmlspecialchars($hakulauseke);?>" />
	<input type="submit" value="Hae" /></td></tr>
	<?php if(!$_p["kohde"] && !$_p["jarjestys"])
	{
		?>
		</tbody><tbody id="hakuLisävalinnatNappi">
		<tr><td colspan="4">
		<p class="pikkutieto"><a href="#" onclick="new Effect.toggle('hakuLisävalinnat', 'appear', {duration: 0.5}); $('hakuLisävalinnatNappi').style.display = 'none'; return false;">Tarkempi haku...</a></p>
		</td></tr>
		</tbody>
		<tbody id="hakuLisävalinnat" style="display: none;">
		<?php
	}
	?>
	<tr><td>
		Hae
	</td><td>
		<label><input type="checkbox" name="kohde[]" value="o"<?php if($ehtotaulu["o"]) echo " checked=\"checked\"";?> /> Otsikosta</label>
	</td><td>
		<label><input type="checkbox" name="kohde[]" value="t"<?php if($ehtotaulu["t"]) echo " checked=\"checked\"";?> /> Tekstistä</label>
	</td><td>
		<label><input type="checkbox" name="kohde[]" value="s"<?php if($ehtotaulu["s"]) echo " checked=\"checked\"";?> /> Sijaintitiedoista</label>
	</td></tr>
	<tr><td colspan="3">
	Järjestä 
	<select name="jarjestys">
		<option value="r"<?php if($_p["jarjestys"] == "r" || !$_p["jarjestys"]) echo " selected=\"selected\"";?>>relevanssin mukaan (oletus)</option>
		<option value="p"<?php if($_p["jarjestys"] == "p") echo " selected=\"selected\"";?>>päiväyksen mukaan vanhin ensin</option>
		<option value="p2"<?php if($_p["jarjestys"] == "p2") echo " selected=\"selected\"";?>>päiväyksen mukaan uusin ensin</option>
		<option value="a"<?php if($_p["jarjestys"] == "a") echo " selected=\"selected\"";?>>aakkosjärjestykseen</option>
	</select>
	</td></tr>
	</tbody>
	</table>
	</form>  
	<?php
}

if($tulostetaan == 0)
{
	echo "<h2>Virhe</h2>".$virhe;
}
else
{
	foreach($tulostettavat as $avain => $merkintä)
	{
		if($käyttäjä->onkoYlläpitäjä())
		{
			?>
			<div id="blogimerkinta_<?php echo $merkintä->id;?>_poisto" style="display: none; border: 1px #A00 solid; background-color: #F0C0D0; margin-bottom: 1em; padding: 0.5em;">
				<p>Oletko täysin varma että haluat poistaa merkinnän <i><?php echo $merkintä->tulostaOtsikko();?></i> (id <?php echo $merkintä->id;?>)?</p>
				<form action="tallennamerkinta.php" method="post">
				<input type="hidden" name="t" value="poista" />
				<input type="hidden" name="sid" value="<?php echo session_id();?>" />
				<input type="hidden" name="id" value="<?php echo $merkintä->id;?>" />
				<input type="submit" value="Poista" /><input type="reset" value="Peruuta" onclick="naytaBlogimerkintaPoisto(<?php echo $merkintä->id;?>);" />
				</form>
			</div>
			<?php
		}
		?>
		<div id="blogimerkinta_<?php echo $merkintä->id;?>" class="blogimerkinta">
		<h2><a href="<?php echo $merkintä->id."/".urlencode($merkintä->otsikko)?>"><?php echo $merkintä->tulostaOtsikko();?></a></h2>
		<p class="pikkutieto"><?php
		if($merkintä->uid > 0)
		{
			if($yleisfunktiot->haeAvatar(24, $merkintä->uid))
				echo "<img src=\"".$yleisfunktiot->haeAvatar(24, $merkintä->uid)."\" style=\"border: 0; margin-right: 0.5em; vertical-align: middle;\" alt=\"Avatara\"/>";
				$kirjoittaja = $yleisfunktiot->haeNimi($merkintä->uid);
				if($kirjoittaja)
					echo $kirjoittaja;
				else
				{
					$kirjoittaja = $tietokanta->haeTunnus($merkintä->uid);
					if($kirjoittaja)
						echo $kirjoittaja;
					else
						echo "Anonyymi";
				}
		}
		else
		{
			$kirjoittaja = $tietokanta->haeTunnus($merkintä->uid);
			if($kirjoittaja)
				echo $kirjoittaja;
			else
				echo "Anonyymi";
		}
		echo " | ".$merkintä->tulostaPäiväys();
		if ($merkintä->muokattu)
			echo " (muokattu ".$merkintä->tulostaMuokkaus().")";
		?><?php
		if($käyttäjä->onkoYlläpitäjä() && !$merkintä->julkaistu)
			echo " | <a href=\"tallennamerkinta.php?t=j&amp;id=".$merkintä->id."&amp;sid=".session_id()."\" title=\"Julkaise!\" style=\"color: #f00;\">Julkaisematon</a>";
		if($käyttäjä->onkoYlläpitäjä() && !$_p["haku"])
		{
			echo "<span class=\"muokkausnappi\">";
			echo "<a href=\"javascript:naytaBlogimerkintaMuokkain(".$merkintä->id.");\" id=\"blogimerkinta_".$merkintä->id."_muokkaa\">Muokkaa</a>";
			echo " | <a href=\"javascript:naytaBlogimerkintaPoisto(".$merkintä->id.");\" id=\"blogimerkinta_".$merkintä->id."_poista\">Poista</a>";
			echo "</span>";
		}
		?></p>
		
		
		<?php
		if(!$_p["haku"])
		{
			// Tarkistetaan onko otsikkokuvaa.
			$polku = YAMBLOG_FULL_BASEDIR."kuvat/".$merkintä->id."/";
			$päätteet = array("jpg", "png");
			foreach($päätteet as $avain => $pääte)
			{
				$otsikkokuva = $polku."thumb.otsikko.".$pääte;
				$otsikkokuvaIso = $polku."otsikko.".$pääte;
				if(is_file($otsikkokuva))
				{
					echo "<div class=\"blogimerkintäOtsikkokuva\">";
					if(is_file($otsikkokuvaIso))
						echo "<a href=\"kuvat/".$merkintä->id."/".basename($otsikkokuvaIso)."\">";
					echo "<img src=\"kuvat/".$merkintä->id."/".basename($otsikkokuva)."\" alt=\"".$merkintä->tulostaOtsikko()."\"/>";
					if(is_file($otsikkokuvaIso))
						echo "</a><br />Klikkaa isommaksi";
					echo "</div>";
				}
			}
			
			?>
			<p><?php
			if($_p["sivu"] && $_p["id"])
				echo $merkintä->tulostaMerkintä(false, true, $käyttäjä->onkoTunnistautunut());
			else
			{
				echo $merkintä->tulostaMerkintä(true, true, $käyttäjä->onkoTunnistautunut());
				if(strlen($merkintä->teksti) > 500)
				{
					?>
					<br />[<?php echo "<a href=\"".$merkintä->id."/".urlencode($merkintä->otsikko)."\">";?>Lisää...</a>]
					<?php
				}
			}
			?>
			</p>
			<?php if(($merkintä->biisi || $merkintä->paikka) && ($_p["sivu"] && $_p["id"]))
			{
				?>
				<p class="pikkutieto">
				<?php
				if($merkintä->biisi)
					echo "Nyt soi <i>".$merkintä->haeBiisilinkki()."</i><br />";
				if($merkintä->paikka)
					echo "Sijainti: ".$merkintä->haePaikkalinkki()."<br />";
				?>
				</p>
				<?php
			}
			?>
			
			<?php
			
			// Jos on määritelty galleriakansio, linkitetään siihen...
			if($merkintä->galleria && $_p["sivu"] && $_p["id"])
			{
				$_pääkansio = "kuvagalleria";
				$polku = $_pääkansio."/".basename($merkintä->galleria);
				
				if(is_dir($polku))
				{
					include_once("engine/kuvagalleria.php");
					$galleria = new Kuvagalleria($_pääkansio, $polku);
					
					$tiedostot = $galleria->haeHakemistonTiedostot($polku);
					$kuvienMäärä = count($tiedostot);
					if($kuvienMäärä > 5)
						$raja = 5;
					else
						$raja = $kuvienMäärä;
					?>
					<table border="0" style="margin-left: auto; margin-right: auto; margin-top: 0.5em; margin-bottom: 0.5em; border: 1px #343 solid; background-color: #F5FAF5; padding: 3px;">
					<tr><th colspan="<?php echo $raja;?>">Aiheeseen liittyviä kuvia galleriassa:</th></tr>
					<tr height="165" valign="bottom">
					<?php
					for($i = 0; $i < $raja; $i++)
					{
						$kuvanNimi = $tiedostot[$i];
					
						// Haetaan kuvasta turhaa tietoa kuten koko :)
						$tiedostonKoko = filesize($polku."/".$kuvanNimi);
						list($leveys, $korkeus, $tyyppi, $attr) = getimagesize($polku."/".$kuvanNimi);
						
						// Haetaan thumbnaili....
						if(!file_exists($polku."/thumb.".$kuvanNimi))
							$galleria->luoPikkukuva($kuvanNimi, basename($merkintä->galleria));
						
						?>
						<td width="150" style="text-align: center; vertical-align: middle;">
						
						<a href="index.php?sivu=galleria&amp;kansio=<?php echo basename($polku);?>&amp;image=<?php echo $kuvanNimi;?>"><img src="<?php echo $polku;?>/thumb.<?php echo $kuvanNimi;?>" border="0" alt="<?php echo $kuvanNimi;?>" /></a>
							
						</td>
						<?php
					}
					?>
					</tr><tr><td colspan="<?php echo $raja;?>" style="text-align: right;"><a href="index.php?sivu=galleria&amp;kansio=<?php echo basename($merkintä->galleria);?>">Selaa kansiota...</a></td></tr></table>
					<?php
				}
			}
			
			?>
			
			<p class="pikkutieto"><?php
			echo "<a href=\"".$merkintä->id."/".urlencode($merkintä->otsikko)."#kommentit\">".$merkintä->haeKommenttitieto($tietokanta)."</a>";
			echo " | <a href=\"".$merkintä->id."/".urlencode($merkintä->otsikko)."\">#</a>";
			?></p>
			<?php
		}
		else // haku
		{
			echo "<p class=\"pikkutieto\">Relevanssi: ".round((($relevanssi[$merkintä->id] / $isoinRelevanssi)*100), 0)."%</p>";
		}
		?>
		</div>
		<?php
		if($käyttäjä->onkoYlläpitäjä() && !$_p["haku"])
		{
			// Tulostetaan piilotettu muokkauslaatikko...
			?><div id="blogimerkinta_<?php echo $merkintä->id;?>_muokkain" style="display: none; margin-bottom: 1em;">
				<form action="tallennamerkinta.php" method="post" enctype="multipart/form-data">
				<input type="hidden" name="t" value="pm" />
				<input type="hidden" name="sid" value="<?php echo session_id();?>" />
				<input type="hidden" name="id" value="<?php echo $merkintä->id;?>" />
				<input type="text" name="otsikko" value="<?php echo $merkintä->tulostaOtsikko();?>" style="font-size: x-large; font-weight: bold;" size="40" /><br />
				Vaihda kuva tai lisää uusi:<br />
				<input type="hidden" name="MAX_FILE_SIZE" value="2000000" />
				<input type="file" name="kuva" />
				<input type="checkbox" name="kuva_isona" id="kuva_isona_<?php echo $merkintä->id?>" /> <label for="kuva_isona_<?php echo $merkintä->id?>">Tee kuvasta linkki isompaan versioon (vain alle 640px leveät kuvat)</label>
				<?php
				$polku = YAMBLOG_FULL_BASEDIR."kuvat/".$merkintä->id."/";
				$päätteet = array("jpg", "png");
				foreach($päätteet as $avain => $pääte)
				{
					$otsikkokuva = $polku."thumb.otsikko.".$pääte;
					if(is_file($otsikkokuva))
					{
						?><br /><input type="checkbox" name="kuva_poista" id="kuva_poista_<?php echo $merkintä->id;?>"/> <label for="kuva_poista_<?php echo $merkintä->id?>">Poista vanha kuva</label><?php
					}
				}
				?><br />
				<textarea name="teksti" cols="80" rows="<?php echo $merkintä->haeMuokkaimenKorkeus();?>"><?php echo $merkintä->tulostaMerkintä(false, false);?></textarea><br />
				Nyt soi <img src="kuvat/kysymysmerkki.png" alt="Kysymysmerkki" class="kysymys" onmousemove="naytaApulaatikko('biisimerkinta_<?php echo $merkintä->id;?>');" onmouseout="piilotaApulaatikko('biisimerkinta_<?php echo $merkintä->id;?>');" style="vertical-align: middle;" />
				<div id="apulaatikko_biisimerkinta_<?php echo $merkintä->id;?>" class="apulaatikko" style="display: none;"><img src="kuvat/kysymysmerkki.png" alt="Kysymysmerkki" /> Esim. Nobuo Uematsu - Final Fantasy</div> : <input type="text" name="biisi" value="<?php echo htmlspecialchars($merkintä->biisi);?>" size="50" maxlength="255" /><br />
				Kaupunki, maa <img src="kuvat/kysymysmerkki.png" alt="Kysymysmerkki" class="kysymys" onmousemove="naytaApulaatikko('paikkamerkinta_<?php echo $merkintä->id;?>');" onmouseout="piilotaApulaatikko('paikkamerkinta_<?php echo $merkintä->id;?>');" style="vertical-align: middle;" />
				<div id="apulaatikko_paikkamerkinta_<?php echo $merkintä->id;?>" class="apulaatikko" style="display: none;"><img src="kuvat/kysymysmerkki.png" alt="Kysymysmerkki" /> Voit kirjoittaa useampiakin kuin yhden paikan laittamalla puolipisteen paikannimien väliin (esim. Jyväskylä, Suomi;Oslo, Norja)</div> : <input type="text" name="paikka" value="<?php echo htmlspecialchars($merkintä->paikka);?>" size="50" maxlength="255" /><br />
				Liitä kansio galleriasta: <select name="galleria"><option value="">Älä liitä</option><option value="" style="background-color: #999;">---</option><?php
				// Luetaan gallerian näkyvät kansiot ja heitetään niistä lista
				$polku = "kuvagalleria";
				$tiedostot = scandir($polku);
				foreach($tiedostot as $avain => $kansio)
				{
					if(substr($kansio,0,1) != "." && is_dir($polku."/".$kansio))
					{
						echo "<option value=\"".basename($kansio)."\"";
						if(basename($kansio) == $merkintä->galleria)
							echo " selected=\"selected\"";
						echo ">".basename($kansio)."</option>";
					}
				}
				?></select><br />
				<input type="submit" value="Tallenna" /><input type="reset" value="Peruuta" onclick="naytaBlogimerkintaMuokkain(<?php echo $merkintä->id;?>);" />
				</form>
			</div>
			<?php

		}
		
		// Tähän väliin kuvasysteemi?
		
		
		
		if($_p["sivu"] && $_p["id"])
		{
			
			
			// Haetaan tietokannasta kommentit..
			?>
			<a name="kommentit"></a>
			<h2>Kommentit</h2>
			<?php
			if($käyttäjä->onkoYlläpitäjä())
				$q = "SELECT * FROM kommentit WHERE mid=".$merkintä->id." ORDER BY paivays ASC";
			else
				$q = "SELECT * FROM kommentit WHERE mid=".$merkintä->id." AND poistettu IS NULL ORDER BY paivays ASC";
			$tulos = $tietokanta->hae($q);
			if($tulos->onkoRivejä())
			{
				while($tulos->onkoRivejä())
				{
					$kommentti = $tulos->seuraava();
					?>
					<div id="blogikommentti_<?php echo $kommentti->id;?>" class="blogikommentti"<?php
					if($kommentti->poistettu)
						echo "style=\"color: #AAA;\"";
					?>>
					<a name="k<?php echo $kommentti->id;?>"></a><p class="kommentti">
					<?php
					if($kommentti->uid > 0)
						if($yleisfunktiot->haeAvatar(64, $kommentti->uid))
							echo "<img src=\"".$yleisfunktiot->haeAvatar(64, $kommentti->uid)."\" style=\"float: left; border: 0; margin: 0.5em;\" alt=\"Avatara\" />"
					?><strong>
					<?php
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
					echo "</strong>";
					
					if($kommentti->uid > 0)
					{
						// Haetaan tietokannasta nettisivutiedot... 
					}
					else
					{
						if($kommentti->www)
						{
							if($kommentti->www)
								echo " (<a href=\"".htmlspecialchars($kommentti->www)."\">Kotisivu</a>) ";
						}
					}
					
					echo " kirjoitti ".gmdate("j.n.Y H:i", strtotime($kommentti->paivays));
					if($kommentti->poistettu)
						echo " (poistettu)";
					?>
					<br />
					<?php echo nl2br(htmlspecialchars($kommentti->teksti));?></p>
					<?
					if($käyttäjä->onkoYlläpitäjä())
					{
						echo "<p class=\"pikkutieto\">";
						if(!$kommentti->poistettu)
							echo "<a href=\"tallennakommentti.php?t=poista&amp;id=".$kommentti->id."&amp;sid=".session_id()."\" title=\"Poista\">Poista kommentti</a>";
						else
							echo "<a href=\"tallennakommentti.php?t=palauta&amp;id=".$kommentti->id."&amp;sid=".session_id()."\" title=\"Palauta\">Palauta kommentti</a>";
						if($kommentti->ip)
							echo " | ".$kommentti->ip;
						else
							echo " | Ip-osoite ei tiedossa";
						echo "</p>";
					}
					?>
					</div>
					<?php
				}
			}
			else
				echo "<p>Ei kommentteja!</p>";
			
			if(YAMBLOG_SAA_KOMMENTOIDA)
			{
				?>
				<div id="blogikommentti_0">
				<a href="javascript:naytaBlogikommenttiMuokkain(0)">Lisää kommentti...</a>
				</div>
				<div id="blogikommentti_0_muokkain" style="display: none;">
				<h2>Lisää kommentti</h3>
				<p>Pakolliset kentät on merkitty tähdellä (*).</p>
				<form action="tallennakommentti.php" method="post">
				<input type="hidden" name="t" value="u" />
				<input type="hidden" name="id" value="<?php echo $merkintä->id;?>" />
				<input type="hidden" name="sid" value="<?php echo session_id();?>" />
				<table border="0" cellspacing="3" cellpadding="2">
				<tr><th>Tunnus</th><td>
				<?php
				if($käyttäjä->onkoKirjautunut())
				{
					echo $käyttäjä->haeTunnus();
					?>
					<?php
				}
				else
				{
					?><input type="text" name="tunnus" size="40" maxlength="255" />
					</td></tr>
					<tr><th>WWW-sivu</th><td>
					<input type="text" name="www" size="40" maxlength="255" />
					</td></tr>
					<tr><th>Varmistus *</th><td>
					Kirjoita alle, paljonko 1+2 on. <img src="kuvat/kysymysmerkki.png" alt="Kysymysmerkki" class="kysymys" onmousemove="naytaApulaatikko('varmistin');" onmouseout="piilotaApulaatikko('varmistin');" />
					<div id="apulaatikko_varmistin" class="apulaatikko" style="display: none;"><img src="kuvat/kysymysmerkki.png" alt="Kysymysmerkki" /> Tämän avulla järjestelmä tietää sinun olevan lihaa ja verta oleva ihminen eikä spämmirobotti.<br />Kirjoita vastaus esitettyyn kysymykseen.</div>
					<br />
					<input type="text" name="varmistus" size="40" maxlength="40" />
					<?php
				}
				?>
				</td></tr>
				<tr><th>Kommentti *</th><td>
				<textarea name="teksti" cols="60" rows="10"></textarea>
				</td></tr>
				<tr><td colspan="2" style="text-align: center;">
				<input type="submit" value="Tallenna" /><input type="reset" value="Peruuta" onclick="naytaBlogikommenttiMuokkain(0);" />
				</td>
				</table>
				</form>
				
				</div>
				<?php
			}
			else
				echo "<p>Blogin kommentointi on estetty.</p>";
		}
	}
}
if($käyttäjä->onkoYlläpitäjä() && !$_p["sivu"] && !$_p["id"])
{
	// Tulostetaan piilotettu uuden merkinnän tekolaatikko...
	?>
	<div id="blogimerkinta_0">
	<a href="javascript:naytaBlogimerkintaMuokkain(0);">Lisää uusi merkintä...</a>
	</div>
	<div id="blogimerkinta_0_muokkain" style="display: none; margin-bottom: 1em;">
	<form action="tallennamerkinta.php" method="post" enctype="multipart/form-data">
	<input type="hidden" name="t" value="uu" />
	<input type="hidden" name="sid" value="<?php echo session_id();?>" />
	<input id="blogimerkinta_0_muokkain_otsikko" type="text" name="otsikko" value="Uusi merkintä" onfocus="this.style.color='#000';if(this.value=='Uusi merkintä') this.value='';" style="color: #999; font-size: x-large; font-weight: bold;" size="40" /><br />
	<input type="hidden" name="MAX_FILE_SIZE" value="2000000" />
	<input type="file" name="kuva" />
	<input type="checkbox" name="kuva_isona" id="kuva_isona" /> <label for="kuva_isona">Tee kuvasta linkki isompaan versioon  (vain alle 640px leveät kuvat)</label><br />
	<textarea id="blogimerkinta_0_muokkain_teksti" name="teksti" cols="80" rows="10" style="color: #999;" onfocus="this.style.color='#000';if(this.value=='Kirjoita jotain...') this.value='';">Kirjoita jotain...</textarea><br />
	Nyt soi <img src="kuvat/kysymysmerkki.png" alt="Kysymysmerkki" class="kysymys" onmousemove="naytaApulaatikko('biisimerkinta_0');" onmouseout="piilotaApulaatikko('biisimerkinta_0');" style="vertical-align: middle;" />
	<div id="apulaatikko_biisimerkinta_0" class="apulaatikko" style="display: none;"><img src="kuvat/kysymysmerkki.png" alt="Kysymysmerkki" /> Esim. Nobuo Uematsu - Final Fantasy</div> : <input type="text" name="biisi" value="" size="50" maxlength="255" /><br />
	Kaupunki, maa <img src="kuvat/kysymysmerkki.png" alt="Kysymysmerkki" class="kysymys" onmousemove="naytaApulaatikko('paikkamerkinta_0');" onmouseout="piilotaApulaatikko('paikkamerkinta_0');" style="vertical-align: middle;" />
	<div id="apulaatikko_paikkamerkinta_0" class="apulaatikko" style="display: none;"><img src="kuvat/kysymysmerkki.png" alt="Kysymysmerkki" /> Voit kirjoittaa useampiakin kuin yhden paikan laittamalla puolipisteen paikannimien väliin (esim. Jyväskylä, Suomi;Oslo, Norja)</div> : <input type="text" name="paikka" value="" size="50" maxlength="255" /><br />
	Liitä kansio galleriasta: <select name="galleria"><option value="">Älä liitä</option><option value="" style="background-color: #999;">---</option><?php
	// Luetaan gallerian näkyvät kansiot ja heitetään niistä lista
	$polku = "kuvagalleria";
	$tiedostot = scandir($polku);
	foreach($tiedostot as $avain => $kansio)
	{
		if(substr($kansio,0,1) != "." && is_dir($polku."/".$kansio))
		{
			echo "<option value=\"".basename($kansio)."\">".basename($kansio)."</option>";
		}
	}
	?></select><br />
	<input type="submit" value="Tallenna" />
	<input type="reset" value="Peruuta" onclick="naytaBlogimerkintaMuokkain(0);
												$('blogimerkinta_0_muokkain_otsikko').style.color='#999';
												$('blogimerkinta_0_muokkain_teksti').style.color='#999';" />
	</form>
	</div>
	<?php
}
if(!$_p["sivu"] && !$_p["id"])
{
	?>
	<div id="selaaMerkintöjä">
	<?php
	
			
	// Linkki vanhempiin merkintöihin...
	if($_p["alku"])
	{
		$uusiAlku = $_p["alku"]+5;
		$edellinenSivu = $_p["alku"]-5;
		
	}
	else
		$uusiAlku = 5;
	
	if($_p["alku"] && $tietokanta->haeYksi("SELECT id FROM merkinta LIMIT ".$edellinenSivu.",1"))
	{
		echo "<a href=\"index.php?alku=".$edellinenSivu."\" style=\"float: left;\">Uudemmat merkinnät</a>";
	}	
	if($tietokanta->haeYksi("SELECT id FROM merkinta LIMIT ".$uusiAlku.",1"))
		echo "<a href=\"index.php?alku=".$uusiAlku."\" style=\"float: right;\">Vanhemmat merkinnät</a>";
	echo "</div>";
	
	?>

	<?php
}
?>
</div><!-- pääpalkki -->
<?php include_once("sivupalkki.php");?>
</div>