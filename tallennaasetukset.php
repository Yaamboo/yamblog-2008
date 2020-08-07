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

if($käyttäjä->onkoKirjautunut())
{
	if($_p["sid"] == session_id())
	{
		if($_p["t"] == "avatar")
		{
			// Tarkistetaan ensin missä muodossa avatar tulee...
			if($_FILES["kuva"]["error"] == UPLOAD_ERR_OK)
			{
				// Siirretään originaali kansioon, sitten tehdään siitä pikkukuvat ja tuhotaan alkuperäinen
				$polku = YAMBLOG_FULL_BASEDIR."kuvat/avatarat/";
				
				$pikkukuvanPolku = $polku.$käyttäjä->uid.".24.png";
				$keskikokoisenkuvanPolku = $polku.$käyttäjä->uid.".64.png";
				$isonkuvanPolku = $polku.$käyttäjä->uid.".128.png";
				$alkupkuvanPolku = $polku.$käyttäjä->uid.".".basename($_FILES["kuva"]["name"]);
				if(move_uploaded_file($_FILES["kuva"]["tmp_name"], $alkupkuvanPolku))
				{
					
					list($leveys, $korkeus, $tyyppi) = getimagesize($alkupkuvanPolku);
					$suhde = $leveys/$korkeus;
					/*if($leveys > 16 || $korkeus > 16)
					{	
						if($suhde >= 1)
						{
							$uusiLeveys1 = 16;
							$uusiKorkeus1 = 16/$suhde;
						}
						else
						{
							$uusiKorkeus1 = 16;
							$uusiLeveys1 = 16*$suhde;
						}
					}
					else
					{
						$uusiKorkeus1 = $korkeus;
						$uusiLeveys1 = $leveys;
					}
					
					if($leveys > 64 || $korkeus > 64)
					{	
						if($suhde >= 1)
						{
							$uusiLeveys2 = 64;
							$uusiKorkeus2 = 64/$suhde;
						}
						else
						{
							$uusiKorkeus2 = 64;
							$uusiLeveys2 = 64*$suhde;
						}
					}
					else
					{
						$uusiKorkeus2 = $korkeus;
						$uusiLeveys2 = $leveys;
					}
					if($leveys > 64 || $korkeus > 64)
					{*/	
					if($suhde >= 1)
					{
						$uusiLeveys1 = 24;
						$uusiKorkeus1 = 24/$suhde;
						$uusiLeveys2 = 64;
						$uusiKorkeus2 = 64/$suhde;
						$uusiLeveys3 = 128;
						$uusiKorkeus3 = 128/$suhde;
					}
					else
					{
						$uusiKorkeus1 = 24;
						$uusiLeveys1 = 24*$suhde;
						$uusiKorkeus2 = 64;
						$uusiLeveys2 = 64*$suhde;
						$uusiKorkeus3 = 128;
						$uusiLeveys3 = 128*$suhde;
					}
					
					if($tyyppi == IMAGETYPE_GIF)
					{
						$vanhaKuva = imagecreatefromgif($alkupkuvanPolku);
					}
					else if($tyyppi == IMAGETYPE_JPEG)
					{
						$vanhaKuva = imagecreatefromjpeg($alkupkuvanPolku);
					}
					else if($tyyppi == IMAGETYPE_PNG)
					{
						$vanhaKuva = imagecreatefrompng($alkupkuvanPolku);
					}
					$uusiKuva1 = imagecreatetruecolor($uusiLeveys1, $uusiKorkeus1);
					$uusiKuva2 = imagecreatetruecolor($uusiLeveys2, $uusiKorkeus2);
					$uusiKuva3 = imagecreatetruecolor($uusiLeveys3, $uusiKorkeus3);
					imagecopyresampled($uusiKuva1, $vanhaKuva, 0, 0, 0, 0, $uusiLeveys1, $uusiKorkeus1, $leveys, $korkeus);
					imagecopyresampled($uusiKuva2, $vanhaKuva, 0, 0, 0, 0, $uusiLeveys2, $uusiKorkeus2, $leveys, $korkeus);
					imagecopyresampled($uusiKuva3, $vanhaKuva, 0, 0, 0, 0, $uusiLeveys3, $uusiKorkeus3, $leveys, $korkeus);
					
					imagepng($uusiKuva1, $pikkukuvanPolku, 0);
					imagepng($uusiKuva2, $keskikokoisenkuvanPolku, 0);
					imagepng($uusiKuva3, $isonkuvanPolku, 0);
					
					unlink($alkupkuvanPolku);
					
					header("Location: index.php?sivu=asetukset&ok=avatar&rand=".rand(0,1000));
				}
				else
					header("Location: index.php?sivu=asetukset&virhe=KUVA_UPLOAD_ERROR_7");
			}
			else
				header("Location: index.php?sivu=asetukset&virhe=KUVA_UPLOAD_ERROR_".$_FILES["kuva"]["error"]);
		}
		else if($_p["t"] == "pavatar")
		{
			// Yksinkertainen on usein kaunista
			$polku = YAMBLOG_FULL_BASEDIR."kuvat/avatarat/";
			$koot = array(24,64,128);
			foreach($koot as $avain => $koko)
			{
				if(file_exists($polku.$käyttäjä->uid.".".$koko.".png"))
					unlink($polku.$käyttäjä->uid.".".$koko.".png");
			}
			header("Location: index.php?sivu=asetukset&ok=pavatar");
		}
		else if($_p["t"] == "tiedot")
		{
			if($tietokanta->haeYksi("SELECT uid FROM kayttajatiedot WHERE uid=".$käyttäjä->uid))
			{
				// Muokataan vanhaa
				$q = "UPDATE kayttajatiedot SET etunimi='".$_p["etunimi"]."', sukunimi='".$_p["sukunimi"]."', muoto=".$_p["muoto"].", sijainti='".$_p["sijainti"]."', tiedot='".$_p["tiedot"]."' WHERE uid=".$käyttäjä->uid;
				if($tietokanta->päivitä($q))
					header("Location: index.php?sivu=asetukset&ok=tiedot");
				else
					header("Location: index.php?sivu=asetukset&virhe=TIETOKANTA_TALLENNUS_EPAONNISTUI");
			}
			else
			{
				// Tehdään uusi
				$q = "INSERT INTO kayttajatiedot(uid, etunimi, sukunimi, muoto, sijainti, tiedot) VALUES(".$käyttäjä->uid.", '".$_p["etunimi"]."', '".$_p["sukunimi"]."', ".$_p["muoto"].", '".$_p["sijainti"]."', '".$_p["tiedot"]."')";
				if($tietokanta->tallenna($q) > 0)
					header("Location: index.php?sivu=asetukset&ok=tiedot");
				else
					header("Location: index.php?sivu=asetukset&virhe=TIETOKANTA_TALLENNUS_EPAONNISTUI");
			}
		}
		else if($_p["t"] == "tunnistaudu")
		{
			if(sha1($_p["koodi"]) == YAMBLOG_VIP_SALASANA)
			{
				// Muutetaan nolla ykköseksi
				$q = "UPDATE kayttajat SET auto_tunnistautuminen=1 WHERE id=".$käyttäjä->uid;
				$tietokanta->päivitä($q);
				$q = "UPDATE kirjautuneet SET tunnistautunut=1 WHERE uid=".$käyttäjä->uid." AND vid='".$käyttäjä->vid."'";
				$tietokanta->päivitä($q);
				header("Location: index.php?sivu=asetukset&ok=tunnistautuminen");
			}
		}
		else
			header("Location: index.php?sivu=asetukset&virhe=EI_PARAMETREJA");
	}
	else
		header("Location: index.php?sivu=asetukset&virhe=VAARA_SESSIO_ID");
}
else
	header("Location: index.php?virhe=EI_KIRJAUTUNUT");
?>