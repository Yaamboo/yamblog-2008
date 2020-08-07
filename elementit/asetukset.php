<?php
?>
<h2>Asetukset</h2>
<p>Rekisteröityneenä käyttäjänä sinulla on mahdollisuus lisätä järjestelmään avatara, joka näytetään 
viesteissäsi, sekä muokata joitain muitakin asetuksia :)</p>

<?php
if($käyttäjä->onkoKirjautunut())
{
	if($_p["ok"] == "tunnistautuminen")
	{
		?>
		<div class="ilmoitus">
		<p><img src="kuvat/ilmoitusmerkki.png" alt="Huutomerkki" />
		Tunnistautuminen käyttäjällesi on nyt automatisoitu!</p>
		</div>
		<?php
	}
	?>
	<h3>Tunnistautuminen</h3>
	<p>Tunnistautuneet käyttäjät voivat lukea salaisuuksia jotka jäävät näkemättä niiltä, jotka eivät ole
	tunnistautuneet. Tietynlainen "en paljasta elämääni kaikille ihmisille"-systeemi siis.</p>
	<?php
	if($käyttäjä->onkoTunnistautunut())
	{
		echo "<p>Olet tällä hetkellä tunnistautunut ja järjestelmä näyttää automaattisesti sinulle piilotetut tekstit.</p>";
	}
	$q = "SELECT auto_tunnistautuminen FROM kayttajat WHERE id=".$käyttäjä->uid;
	if($tietokanta->haeYksi($q) == 1)
		echo "<p>Automaattinen tunnistautuminen on käytössä tunnuksellasi.</p>";
	else
	{
		?>
		<form action="tallennaasetukset.php">
		<input type="hidden" name="t" value="tunnistaudu" />
		<input type="hidden" name="sid" value="<?php echo session_id();?>" />
		Salasana: <input type="password" name="koodi" />
		<input type="submit" value="Ota automaattinen tunnistautuminen käyttöön" />
		</form>
		<?php
	}
	
	// Tarkistetaan onko käyttäjällä jo valmis avatara käytössään.
	// /kuvat/avatarat/[uid]_[koko].[pääte]
	// Kaksi kuvakokoa: 
	?>
	<h3>Lisää/poista/vaihda avatara</h3>
	<p>Voit siirtää palvelimelle minkälaisen kuvan tahansa (kunhan se on alle 2 megaa iso ja gif-, jpg- tai png-muodossa).
	Kuvasta tehdään enimmillään 24x24, 64x64 ja 128x128 kuvapistettä suuret versiot automaattisesti, 
	ja niitä käytetään eri puolella sivustoa.<br />
	Tai jotain.</p>
	<?php
	if($_p["ok"] == "avatar")
	{
		?>
		<div class="ilmoitus">
		<p><img src="kuvat/ilmoitusmerkki.png" alt="Huutomerkki" />
		Avatara päivitetty onnistuneesti!</p>
		</div>
		<?php
	}
	if($_p["ok"] == "pavatar")
	{
		?>
		<div class="ilmoitus">
		<p><img src="kuvat/ilmoitusmerkki.png" alt="Huutomerkki" />
		Avatara poistettu onnistuneesti!</p>
		</div>
		<?php
	}
	?>
	<table border="0">
	<tr><th>Tämänhetkinen avatara</th><th>Uusi avatara</th></tr>
	<tr><td><?php
	// tarkistetaan onko käyttäjällä avatar...
	if(!$käyttäjä->haeAvatar(24))
		echo "Sinulla ei ole ennestään avataraa.";
	else
	{
		// Esikatsellaan eri kuvakoot
		echo "<img src=\"".$käyttäjä->haeAvatar(24)."?rand=".rand(0,1000)."\" />";
		echo "<img src=\"".$käyttäjä->haeAvatar(64)."?rand=".rand(0,1000)."\" />";
		echo "<img src=\"".$käyttäjä->haeAvatar(128)."?rand=".rand(0,1000)."\" />";
	} 
	?></td>
	<td>
	<form action="tallennaasetukset.php" method="post" enctype="multipart/form-data">
	<input type="hidden" name="t" value="avatar" />
	<input type="hidden" name="sid" value="<?php echo session_id();?>" />
	<input type="hidden" name="MAX_FILE_SIZE" value="2000000" />
	<input type="file" name="kuva" /><br />
	<input type="submit" value="Tallenna uusi avatara" />
	</form>
	<form action="tallennaasetukset.php" method="post">
	<input type="hidden" name="t" value="pavatar" />
	<input type="hidden" name="sid" value="<?php echo session_id();?>" />
	<input type="submit" value="Poista olemassaoleva avatara" />
	</form>
	</td></tr>
	</table>
	
	<h3>Käyttäjätiedot</h3>
	<p>Tämän lomakkeen avulla voit täydentää tietoja itsestäsi, joita sitten näytetään siellä sun täällä.</p>
	<?php
	if($_p["ok"] == "tiedot")
	{
		?>
		<div class="ilmoitus">
		<p><img src="kuvat/ilmoitusmerkki.png" alt="Huutomerkki" />
		Käyttäjätiedot päivitetty onnistuneesti!</p>
		</div>
		<?php
	}
	
	// Haetaan tämänhetkiset tiedot...
	$q = "SELECT * FROM kayttajatiedot WHERE uid=".$käyttäjä->uid;
	$tiedot = $tietokanta->hae($q);
	if($tiedot->onkoRivejä())
		$tiedot = $tiedot->seuraava();
	else
		$tiedot = null;
	?>
	<form action="tallennaasetukset.php" method="post">
	<input type="hidden" name="t" value="tiedot" />
	<input type="hidden" name="sid" value="<?php echo session_id();?>" />
	<table border="0">
	<tr><th>Etunimi</th><td><input type="text" name="etunimi" value="<?php if($tiedot) echo htmlspecialchars($tiedot->etunimi);?>" /></td></tr>
	<tr><th>Sukunimi</th><td><input type="text" name="sukunimi" value="<?php if($tiedot) echo htmlspecialchars($tiedot->sukunimi);?>" /></td></tr>
	<tr><th>Käytä minusta muotoa</th><td><select name="muoto">
	<option value="1"<?php if($tiedot) if($tiedot->muoto == 1) echo " selected=\"selected\"";?>>Tunnus (oletus)</option>
	<option value="2"<?php if($tiedot) if($tiedot->muoto == 2) echo " selected=\"selected\"";?>>Etunimi Sukunimi</option>
	<option value="3"<?php if($tiedot) if($tiedot->muoto == 3) echo " selected=\"selected\"";?>>Etunimi S.</option>
	<option value="4"<?php if($tiedot) if($tiedot->muoto == 4) echo " selected=\"selected\"";?>>Etunimi</option>
	</select>
	<tr><th>Sijainti</th><td><input type="text" name="sijainti" value="<?php if($tiedot) echo htmlspecialchars($tiedot->sijainti);?>" /></td></tr>
	<tr><th>Kuvaus</th><td><textarea name="tiedot" rows="10" cols="50"><?php if($tiedot) echo htmlspecialchars($tiedot->tiedot);?></textarea></td></tr>
	<tr><td colspan="2" style="text-align: center;"><input type="submit" value="Tallenna" /></td></tr>
	</table>
	</form>
	<?php
}
else
{
	?><p>Sinun pitää joko kirjautua tai rekisteröityä muokataksesi asetuksia!</p>
	<?php
}
?>