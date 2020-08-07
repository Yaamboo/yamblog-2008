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

if(YAMBLOG_SAA_KOMMENTOIDA)
{
	if($_p["sid"] == session_id())
	{
		if($_p["t"] == "u")
		{
			// Uusi kommentti
			
			// Varmistetaan että viime kommentista on yli 30 sekuntia
			if($tietokanta->haeYksi("SELECT id FROM kommentit WHERE paivays >= DATE_SUB(NOW(), INTERVAL 30 SECOND) AND ip = '".$_SERVER['REMOTE_ADDR']."' LIMIT 0,1"))
			{
				header("Location: index.php?sivu=blog&id=".$_p["id"]."&virhe=ALLE_30_SEK_VIIME_KOMMENTISTA");
			}
			else
			{
				// Eri toiminnallisuus kirjautuneille ja ei-kirjautuneille käyttäjille
				if(strlen($_p["teksti"]) > 0)
				{
					if($käyttäjä->onkoKirjautunut())
					{
						$q = "INSERT INTO kommentit(uid, mid, paivays, teksti, ip) VALUES(".$käyttäjä->uid.", ".$_p["id"].", NOW(), '".$_p["teksti"]."', '".$_SERVER['REMOTE_ADDR']."')";
						$id = $tietokanta->tallenna($q);
						header("Location: index.php?sivu=blog&id=".$_p["id"]."#k".$id);
					}
					else
					{
						// Tarkistetaan eka että botti ei ole saanut kolmosta oikein
						if($_p["varmistus"] == "3")
						{
							$q = "INSERT INTO kommentit(uid, mid, paivays, teksti, ip, tunnus, www) VALUES(0, ".$_p["id"].", NOW(), '".$_p["teksti"]."', '".$_SERVER['REMOTE_ADDR']."', '".$_p["tunnus"]."', '".$_p["www"]."')";
							$id = $tietokanta->tallenna($q);
							header("Location: index.php?sivu=blog&id=".$_p["id"]."#k".$id);
						}
						else
						{
							header("Location: index.php?sivu=blog&id=".$_p["id"]."&virhe=BOTTIVARMISTUS_VAARIN");
						}
					}
				}
				else
					header("Location: index.php?sivu=blog&id=".$_p["id"]."&virhe=SISALTO_PUUTTUU");
			}
		}
		else if($_p["t"] == "poista")
		{
			$palautusId = $tietokanta->haeYksi("SELECT mid FROM kommentit WHERE id=".$_p["id"]);
			if($käyttäjä->onkoYlläpitäjä())
			{
				$q = "UPDATE kommentit SET poistettu = 1 WHERE id=".$_p["id"];
				$tietokanta->päivitä($q);
				header("Location: index.php?sivu=blog&id=".$palautusId);
			}
			else
				header("Location: index.php?sivu=blog&id=".$palautusId."&virhe=EI_OIKEUKSIA");
		}
		else if($_p["t"] == "palauta")
		{
			$palautusId = $tietokanta->haeYksi("SELECT mid FROM kommentit WHERE id=".$_p["id"]);
			if($käyttäjä->onkoYlläpitäjä())
			{
				$q = "UPDATE kommentit SET poistettu = NULL WHERE id=".$_p["id"];
				$tietokanta->päivitä($q);
				header("Location: index.php?sivu=blog&id=".$palautusId);
			}
			else
				header("Location: index.php?sivu=blog&id=".$palautusId."&virhe=EI_OIKEUKSIA");
		}
		else
			header("Location: index.php?sivu=blog&id=".$_p["id"]."&virhe=EI_PARAMETREJA");
	}
	else
		header("Location: index.php?sivu=blog&id=".$_p["id"]."&virhe=VAARA_SESSIO_ID");
}
else
	header("Location: index.php?sivu=blog&id=".$_p["id"]."&virhe=KOMMENTOINTI_ESTETTY");
?>