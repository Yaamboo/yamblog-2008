<?php
// Määrittää että järjestelmä on oikeasti pyörinnässä jolloin voidaan käyttää muita sivuja
define("YAMBLOG_RUNNING", true);

// Includetetaan tärkeät roinat
require_once("engine/tietokanta.php");
require_once("engine/kayttaja.php");
require_once("engine/vakiot.php");

// Alustetaan tarpeelliset muuttujat
$tietokanta = new Tietokanta();
$käyttäjä = new Kayttaja($tietokanta);

// Lähetetään otsikot
header("Content-Type: text/html; charset=UTF-8");
header("Cache-Control: must-revalidate");
header("Expires: ".date("r", time()-16000));
// TODO: miten otsikko välitetään?
require("elementit/alku.php");

// SISÄLTÖALUE ALKAA
$ok = false;
if($_POST["a"] == "rekisteroidy" && $_POST["sid"] == session_id())
{
	// Tarkistetaan arvot ja tallennetaan rekisteröinti...

	// Apumuuttujia
	$ok = true;
	$virheet = array();
	$korostettavatKentät = array("tunnus" => false, "salasana" => false, "sahkoposti" => false, "rek_salasana" => false);
	
	// Tarkistetaan viive ensiksi.
	$q = "SELECT rek_aika FROM kayttajat WHERE rek_ip = '".$_SERVER["REMOTE_ADDR"]."' ORDER BY rek_aika DESC LIMIT 0,1";
	$tulos = $tietokanta->haeYksi($q);
	// 10 minuutin jäähy
	if($tulos && strtotime($tulos) > time()-600)
	{
		$ok = false;
		$virheet[] = "Tältä tietokoneelta on rekisteröidytty viimeksi alle 10 minuuttia sitten. Odota 10 minuuttia ja yritä uudelleen.";
	}
	
	$tunnus = $_POST["tunnus"];
	$salasana1 = $_POST["salasana"];
	$salasana2 = $_POST["salasana2"];
	$email1 = $_POST["sahkoposti"];
	$email2 = $_POST["sahkoposti2"];
	
	if($ok == true)
	{
		if(defined('YAMBLOG_REG_SALASANA'))
		{
			// Aloitetaan tästä.
			// TODO: salaus?
			if($_POST["rek_salasana"] != "" && $_POST["rek_salasana"] == YAMBLOG_REG_SALASANA)
			{
				$ok = true;
			}
			else
			{
				$ok = false;
				$virheet[] = "Salasanasuojatun rekisteröinnin salasana oli väärä.";
				$korostettavatKentät["rek_salasana"] = true;
			}
		}
		// Tarkistetaan että salasanat ja sähköpostit vastaavat toisiaan
		if($salasana1 != $salasana2)
		{
			$ok = false;
			$virheet[] = "Salasanat eivät vastaa toisiaan";
			$korostettavatKentät["salasana"] = true;
		}
		if(strlen($salasana1) < 6)
		{
			$ok = false;
			$virheet[] = "Salasana on liian lyhyt";
			$korostettavatKentät["salasana"] = true;
		}
		if($email1 != $email2)
		{
			$ok = false;
			$virheet[] = "Sähköpostiosoitteet eivät vastaa toisiaan";
			$korostettavatKentät["sahkoposti"] = true;
		}
		if(!preg_match("/^[-^!#$%&'*+\/=?`{|}~.\w]+@[-a-zA-Z0-9]+(\.[-a-zA-Z0-9]+)+$/",$email1)
		|| !preg_match("/^[-^!#$%&'*+\/=?`{|}~.\w]+@[-a-zA-Z0-9]+(\.[-a-zA-Z0-9]+)+$/",$email2))
		{
			$ok = false;
			$virheet[] = "Sähköpostiosoitteen muoto on väärä";
			$korostettavatKentät["sahkoposti"] = true;
		}
	}
	
	if($ok == true)
	{
		// Tarkistetaan onko tunnus käytössä
		$q = "SELECT id FROM kayttajat WHERE tunnus='".$tunnus."'";
		if($tietokanta->haeYksi($q))
		{
			$ok = false;
			$virheet[] = "Tunnus on jo käytössä!";
			$korostettavatKentät["tunnus"] = true;
		}
		
		// Tarkistetaan onko sähköposti käytössä
		$q = "SELECT id FROM kayttajat WHERE email='".$email1."'";
		if($tietokanta->haeYksi($q))
		{
			$ok = false;
			$virheet[] = "Sähköposti on jo käytössä!";
			$korostettavatKentät["sahkoposti"] = true;
		}
		
		if($ok == true)
		{
			// Tallennetaan uusi käyttäjä tietokantaan!
			$q = "INSERT INTO kayttajat(tunnus, salasana, email, rek_ip, rek_aika) VALUES('".$tunnus."', '".sha1($salasana1)."', '".$email1."', '".$_SERVER["REMOTE_ADDR"]."', NOW())";
			$uid = $tietokanta->tallenna($q);
			if($uid == false)
			{
				$ok = false;
				$virheet = "Tietokantaan tallennus epäonnistui";
			}
			// TODO: Lähetetään sähköpostia...
		}
	}
	
	if($ok == false)
	{
		?>
		<h2>Rekisteröityminen epäonnistui!</h2>
		<?php
		if(count($virheet) > 0)
		{
			?>
			<p>Seuraavat asiat menivät pieleen:</p>
			<ul>
			<?php
			foreach($virheet as $avain => $arvo)
				echo "<li>".$arvo."</li>";
			?>
			</ul>
			<?php
		}
		?>
		<p>Yritä uudelleen.</p>
		<?php
	}
}
else
{
	
	?>
	<h2>Rekisteröidy</h2>
	<p>Tämän lomakkeen avulla voit rekisteröityä YamBlogin käyttäjäksi. Rekisteröityneille käyttäjille on 
	tarjolla kaikkea kivaa kommentointimahdollisuuksista kommentointimahdollisuuksiin.</p>
<?php
}
if($ok == true)
{
	
	?>
	<h2>Rekisteröityminen onnistui!</h2>
	<p>Nyt voit kirjautua sisään tunnuksillasi.</p>
	
	<form action="kirjaudu.php" action="post" style="display: none;">
	<input type="hidden" name="t" value="sisaan" />Tunnus<br />
	<input type="text" name="tunnus" size="30" /><br />
	Salasana<br />
	<input type="password" name="salasana" size="30" /><br />
	<input type="submit" value="Kirjaudu" style="margin-top: 2px;" />
	<input type="reset" value="Tyhjennä" style="margin-top: 2px;" />
	</form>
	<?php
}
else
{
?>
	<form action="rekisteroidy.php" method="post">
	<input type="hidden" name="a" value="rekisteroidy" />
	<input type="hidden" name="sid" value="<?php echo session_id();?>" />

	<table border="0" cellspacing="3" cellpadding="2">
	<tr><th<?php if($korostettavatKentät["tunnus"] == true) echo " class=\"korostus\"";?>>Tunnus:</th>
		<td><input type="text" name="tunnus" value="<?php if($tunnus) echo htmlentities($tunnus);?>" size="50" maxlength="100" /></td></tr>
	<tr><th<?php if($korostettavatKentät["salasana"] == true) echo " class=\"korostus\"";?>>Salasana (väh. 6 kirjainta):</th>
		<td><input type="password" name="salasana" size="50" maxlength="255" /></td></tr>
	<tr><th<?php if($korostettavatKentät["salasana"] == true) echo " class=\"korostus\"";?>>Vahvista salasana:</th>
		<td><input type="password" name="salasana2" size="50" maxlength="255" /></td></tr>
	<tr><th<?php if($korostettavatKentät["sahkoposti"] == true) echo " class=\"korostus\"";?>>Sähköpostiosoite:</th>
		<td><input type="text" name="sahkoposti" value="<?php if($email1) echo htmlentities($email1);?>" size="50" maxlength="255" /></td></tr>
	<tr><th<?php if($korostettavatKentät["sahkoposti"] == true) echo " class=\"korostus\"";?>>Vahvista sähköpostiosoite:</th>
	<td><input type="text" name="sahkoposti2" value="<?php if($email2) echo htmlentities($email2);?>" size="50" maxlength="255" /></td></tr>
	
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
// SISÄLTÖALUE LOPPUU
require("elementit/loppu.php");
?>