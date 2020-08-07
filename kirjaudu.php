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

// Verrataan tietoja horsmiin.
if($_p["t"] == "sisaan")
{
	// Tarkistetaan onko sessio oikea
	if($_p["sid"] == session_id())
	{
		$tunnus = $_p["tunnus"];
		$salasana = $_p["salasana"];
		
		$q = "SELECT id FROM kayttajat WHERE tunnus='".$tunnus."' AND salasana='".sha1($salasana)."'";
		$tulos = $tietokanta->haeYksi($q);
		if($tulos)
		{
			// Säädetään kakut sen mukaisiksi
			
			// Tehdään uniikki varmistus-id, joka on voimassa tietyn ajan
			$ok = false;
			while($ok == false)
			{
				$vid_plaintext = "";
				while(strlen($vid_plaintext) < 30)
				{
					$vid_plaintext .= chr(rand(32,126));
				}
				$vid_salattu = sha1($vid_plaintext);
				// Tarkistetaan onko kellään muulla tuollaista varmistinta
				$q = "SELECT vid FROM kirjautuneet WHERE vid='".$vid_salattu."'";
				if(!$tietokanta->haeYksi($q))
					$ok = true;
			}
			// Kirjataan tietokantaan ja kakkuihin...
			$q = "INSERT INTO kirjautuneet(uid, vid, alku, loppu) VALUES (".$tulos.", '".$vid_salattu."', NOW(), DATE_ADD(NOW(), INTERVAL 6 MONTH))";
			if($tietokanta->tallenna($q) > 0)
			{
				$loppu = strtotime($tietokanta->haeYksi("SELECT loppu FROM kirjautuneet WHERE uid='".$tulos."' AND vid='".$vid_salattu."' ORDER BY loppu DESC LIMIT 0,1"));
				setcookie("uid", $tulos, $loppu, "/");
				setcookie("vid", $vid_salattu, $loppu, "/");
				
				// TODO: palauta sille sivulle josta kirjauduttiin
				header("Location: index.php");
			}
			else
			{
				// TODO: Virheilmoitus
//				header("Location: index.php?virhe=TIETOKANTA_TALLENNUS_EPAONNISTUI");
			}
		}
		else
		{
			// TODO: Virheilmoitus
			header("Location: index.php?virhe=TUNNUS_TAI_SALASANA_VAARIN");
		}
	}
	else
	{
		header("Location: index.php?virhe=SESSIO_VAARIN");
	}
}
else if($_p["t"] == "ulos")
{
	// Tarkistetaan onko sessio oikea
	if($_p["sid"] == session_id())
	{
		$q = "DELETE FROM kirjautuneet WHERE uid='".$käyttäjä->uid."' AND vid='".$käyttäjä->vid."'";
		$tietokanta->poista($q);
		// Poistetaan kakut
		setcookie("uid", "", time()-3600, "/");
		setcookie("vid", "", time()-3600, "/");
		header("Location: index.php");
	}
	else
	{
		header("Location: index.php?virhe=VAARA_SESSIO_ID");
	}
}
else if($_p["t"] == "tunnistus")
{
	// Tunnistautuminen...
	if($_p["sid"] == session_id())
	{
		// Verrataan salasanaa tunnistautumissalasanaan
		if(sha1($_p["koodi"]) == YAMBLOG_VIP_SALASANA)
		{
			// Tunniste ok...
			// Tehdään uniikki varmistus-id, joka on voimassa tietyn ajan
			$ok = false;
			while($ok == false)
			{
				$vid_plaintext = "";
				while(strlen($vid_plaintext) < 30)
				{
					$vid_plaintext .= chr(rand(32,126));
				}
				$vid_salattu = sha1($vid_plaintext);
				// Tarkistetaan onko kellään muulla tuollaista varmistinta
				$q = "SELECT vid FROM kirjautuneet WHERE vid='".$vid_salattu."'";
				if(!$tietokanta->haeYksi($q))
					$ok = true;
			}
			// Kirjataan tietokantaan ja kakkuihin...
			$q = "INSERT INTO kirjautuneet(uid, vid, tunnistautunut, alku, loppu) VALUES (0, '".$vid_salattu."', 1, NOW(), DATE_ADD(NOW(), INTERVAL 1 HOUR))";
			if($tietokanta->tallenna($q) > 0)
			{
				setcookie("uid", 0, 0, "/");
				setcookie("vid", $vid_salattu, 0, "/");
				
				// TODO: palauta sille sivulle josta kirjauduttiin
				header("Location: index.php");
			}
		}
		else
			header("Location: index.php?virhe=VAARA_TUNNISTUS_SALASANA");
	}
	else
		header("Location: index.php?virhe=VAARA_SESSIO_ID");
}
else if($_p["t"] == "tunnistuspois")
{
	// Tunnistautuminen norjaan...
	if($_p["sid"] == session_id())
	{
		$q = "DELETE FROM kirjautuneet WHERE uid='".$käyttäjä->uid."' AND vid='".$käyttäjä->vid."'";
		$tietokanta->poista($q);
		// Poistetaan kakut
		setcookie("uid", "", time()-3600, "/");
		setcookie("vid", "", time()-3600, "/");
		header("Location: index.php");
	}
	else
		header("Location: index.php?virhe=VAARA_SESSIO_ID");
}
else
{
	header("Location: index.php?virhe=EI_PARAMETREJA");
}
?>
