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

if($käyttäjä->onkoYlläpitäjä())
{
	if($_p["sid"] == session_id())
	{
		if($_p["t"] == "u")
		{
			// Uusi kommentti
			$q = "INSERT INTO galleriakommentit(kansio, kuva, teksti) VALUES('".$_p["kansio"]."', '".$_p["kuva"]."', '".$_p["teksti"]."')";
			$tietokanta->tallenna($q);
			header("Location: index.php?sivu=galleria&kansio=".$_p["kansio"]."&image=".$_p["kuva"]);
		}
		else if($_p["t"] == "m")
		{
			// Muokkaus
			$q = "UPDATE galleriakommentit SET teksti='".$_p["teksti"]."' WHERE kansio='".$_p["kansio"]."' AND kuva='".$_p["kuva"]."'";
			$tietokanta->päivitä($q);
			header("Location: index.php?sivu=galleria&kansio=".$_p["kansio"]."&image=".$_p["kuva"]);
		}
		else if($_p["t"] == "p")
		{
			$q = "DELETE FROM galleriakommentit WHERE kansio='".$_p["kansio"]."' AND kuva='".$_p["kuva"]."'";
			$tietokanta->poista($q);
			header("Location: index.php?sivu=galleria&kansio=".$_p["kansio"]."&image=".$_p["kuva"]);
		}
		else
			header("Location: index.php?virhe=EI_PARAMETREJA");
	}
	else
		header("Location: index.php?virhe=VAARA_SESSIO_ID");
}
else
	header("Location: index.php?virhe=EI_OIKEUKSIA");
?>