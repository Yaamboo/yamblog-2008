<?php
// Määrittää että järjestelmä on oikeasti pyörinnässä jolloin voidaan käyttää muita sivuja
define("YAMBLOG_RUNNING", true);

// Includetetaan tärkeät roinat
require_once("engine/tietokanta.php");
require_once("engine/kayttaja.php");
require_once("engine/vakiot.php");
require_once("engine/yleiset.php");

// Alustetaan tarpeelliset muuttujat
$tietokanta = new Tietokanta();
$käyttäjä = new Kayttaja($tietokanta);
$yleisfunktiot = new Yleisfunktiot($tietokanta, $käyttäjä);
// $_p on parametrimuuttuja
$_p = array_merge($_GET, $_POST);

// Lähetetään otsikot
header("Content-Type: text/html; charset=UTF-8");

if($_p["sivu"] == "faq")
	$otsikko = "Kukamitähäh?";
else if($_p["sivu"] == "galleria")
	$otsikko = "Galleria";
else if($_p["sivu"] == "linkit")
	$otsikko = "Linkit";
else if($_p["sivu"] == "rekisteroidy")
	$otsikko = "Rekisteröidy";
else if($_p["sivu"] == "tietosuoja")
	$otsikko = "Tietosuojaseloste";
else if($_p["sivu"] == "asetukset")
	$otsikko = "Asetukset";
else if($_p["sivu"] == "kalenteri")
	$otsikko = "Kalenteri";
else
	$otsikko = null;
require("elementit/alku.php");

// SISÄLTÖALUE ALKAA

// Käsitellään virheet
include_once("elementit/virheilmoitus.php");

?>
	<?php
if($_p["sivu"] == "faq")
{
	$tiedosto = "faq.php";
}
else if($_p["sivu"] == "rekisteroidy")
{
	$tiedosto = "rekisteroidy.php";
}
else if($_p["sivu"] == "galleria")
{
	$tiedosto = "galleria.php";
}
else if($_p["sivu"] == "linkit")
	$tiedosto = "linkit.php";
else if($_p["sivu"] == "tietosuoja")
	$tiedosto = "tietosuoja.php";
else if($_p["sivu"] == "asetukset")
	$tiedosto = "asetukset.php";
else if($_p["sivu"] == "kalenteri")
	$tiedosto = "kalenteri.php";
else
{
	// Palautetaan etusivu.
	$tiedosto = "blog.php";
}
if(file_exists("elementit/".$tiedosto))
	include("elementit/".$tiedosto);
else
	echo "<h2>Virhe</h2><p>Sisältötiedostoa ".$tiedosto." ei löydy!</p>";
// SISÄLTÖALUE LOPPUU
require("elementit/loppu.php");
?>