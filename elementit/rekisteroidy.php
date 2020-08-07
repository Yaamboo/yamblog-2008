<?php
if(!$_p["rekisterointi_valmis"] || $_p["rekisterointi_valmis"] == "false")
{
	?>
	<h2>Rekisteröidy</h2>
	<p>Tämän lomakkeen avulla voit rekisteröityä YamBlogin käyttäjäksi. Rekisteröityneille käyttäjille on 
	tarjolla kaikkea kivaa kommentointimahdollisuuksista kommentointimahdollisuuksiin.</p>
	<?php
}
if($_p["rekisterointi_valmis"] == "true")
{
	
	?>
	<h2>Rekisteröityminen onnistui!</h2>
	<p>Nyt voit kirjautua sisään tunnuksillasi.</p>
	
	<form action="kirjaudu.php" method="post">
	<input type="hidden" name="t" value="sisaan" />
	<input type="hidden" name="sid" value="<?php echo session_id();?>" />
	<table border="0" cellspacing="3" cellpadding="0">
	<tr><th style="text-align: right;">
	Tunnus:</th><td><input type="text" name="tunnus" size="30" />
	</td></tr>
	<tr><th style="text-align: right;">
	Salasana:</th><td><input type="password" name="salasana" size="30" />
	</td></tr>
	<tr><td colspan="2" style="text-align: center">
	<input type="submit" value="Kirjaudu" />
	<input type="reset" value="Tyhjennä" />
	</td></tr></table>
	</form>
	<?php
}
else
{
	if($_p["rekisterointi_epaonnistui"])
	{
		?>
			<h2>Rekisteröityminen epäonnistui!</h2>
		<?php
		if(count($_p["virheet"]) > 0)
		{
			?>
			<p>Seuraavat asiat menivät pieleen:</p>
			<ul>
			<?php
			foreach($_p["virheet"] as $avain => $arvo)
				echo "<li>".$arvo."</li>";
			?>
			</ul>
			<?php
		}
		?>
		<p>Yritä uudelleen.</p>
		<?php
		$korostettavat = $_p["korostettavat"];
		$korostettavatKentät = array("tunnus" => false, "salasana" => false, "sahkoposti" => false, "rek_salasana" => false);
		foreach($korostettavat as $avain => $arvo)
		{
			$korostettavatKentät[$arvo] = true;
		}
	}
?>
	<form action="tallennarekisterointi.php" method="post">
	<input type="hidden" name="a" value="rekisteroidy" />
	<input type="hidden" name="sid" value="<?php echo session_id();?>" />

	<table border="0" cellspacing="3" cellpadding="2">
	<tr><th<?php if($korostettavatKentät["tunnus"] == true) echo " class=\"korostus\"";?>>Tunnus:</th>
		<td><input type="text" name="tunnus" value="<?php if($_p["tunnus"]) echo htmlentities($_p["tunnus"]);?>" size="50" maxlength="100" /></td></tr>
	<tr><th<?php if($korostettavatKentät["salasana"] == true) echo " class=\"korostus\"";?>>Salasana (väh. 6 kirjainta):</th>
		<td><input type="password" name="salasana" size="50" maxlength="255" /></td></tr>
	<tr><th<?php if($korostettavatKentät["salasana"] == true) echo " class=\"korostus\"";?>>Vahvista salasana:</th>
		<td><input type="password" name="salasana2" size="50" maxlength="255" /></td></tr>
	<tr><th<?php if($korostettavatKentät["sahkoposti"] == true) echo " class=\"korostus\"";?>>Sähköpostiosoite:</th>
		<td><input type="text" name="sahkoposti" value="<?php if($_p["email1"]) echo htmlentities($_p["email1"]);?>" size="50" maxlength="255" /></td></tr>
	<tr><th<?php if($korostettavatKentät["sahkoposti"] == true) echo " class=\"korostus\"";?>>Vahvista sähköpostiosoite:</th>
	<td><input type="text" name="sahkoposti2" value="<?php if($_p["email2"]) echo htmlentities($_p["email2"]);?>" size="50" maxlength="255" /></td></tr>
	
	<?php
	if(defined('YAMBLOG_REG_SALASANA'))
	{
		?>
		<tr><td colspan="2"<?php if($korostettavatKentät["rek_salasana"] == true) echo " class=\"korostus\"";?>>
			Rekisteröinti on turvallisuussyistä suojattu salasanalla. Kirjoita salasana alle.</td></tr>
		<tr><td></td>
			<td><input type="password" name="rek_salasana" size="50" maxlength="255" /></td></tr>
		<?php
	}
	?>
	<tr><td colspan="2" style="text-align: center;"><input type="submit" value="Rekisteröidy" />
	<input type="reset" value="Tyhjennä" /></td></tr>
	</table>
	</form>
	
	<?php
}
?>