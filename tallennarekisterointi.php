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
// $_p on parametrimuuttuja
$_p = array_merge($_GET, $_POST);


$ok = false;
if($_p["a"] == "rekisteroidy" && $_p["sid"] == session_id())
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
	
	$tunnus = $_p["tunnus"];
	$salasana1 = $_p["salasana"];
	$salasana2 = $_p["salasana2"];
	$email1 = $_p["sahkoposti"];
	$email2 = $_p["sahkoposti2"];
	
	if($ok == true)
	{
		if(defined('YAMBLOG_REG_SALASANA'))
		{
			// Aloitetaan tästä.
			// TODO: salaus?
			if($_p["rek_salasana"] != "" && sha1($_p["rek_salasana"]) == YAMBLOG_REG_SALASANA)
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
		// palautetaan virhe...
		$sivu= "Location: index.php?sivu=rekisteroidy&rekisterointi_epaonnistui=true&tunnus=".$tunnus."&email1=".$email1."&email2=".$email2;
		foreach($virheet as $avain => $arvo)
			$sivu .= "&virheet[]=".$arvo;
		foreach($korostettavatKentät as $avain => $arvo)
		{
			if($arvo = true)
				$sivu .= "&korostettavat[]=".$avain;
		}
		header($sivu);
	}
	else
		header("Location: index.php?sivu=rekisteroidy&rekisterointi_valmis=true");
}
?>