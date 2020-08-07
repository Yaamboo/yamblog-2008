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

function tallennaKuva($id, $kuva, $kuvaIsona = false)
{
	// Tallennetaan mahdollinen kuva
	if($kuva["error"] == UPLOAD_ERR_OK)
	{
		// Tähän väliin tarkistukset että kuva täyttää säädökset...
		if($kuva["type"] == "image/jpeg"
		|| $kuva["type"] == "image/png")
		{
			$polku = YAMBLOG_FULL_BASEDIR."kuvat/".$id."/";
			if(!is_dir($polku))
			{
				mkdir($polku, 0755);
				chmod($polku, 0755);
			}
			if(is_dir($polku))
			{
				
				$loppusijoituspaikka = $polku.basename($kuva["name"]);
				if(move_uploaded_file($kuva["tmp_name"], $loppusijoituspaikka))
				{
					// Tehdään kuvasta myös pienemmät versiot...
					list($leveys, $korkeus, $tyyppi) = getimagesize($loppusijoituspaikka);
					if($tyyppi == IMAGETYPE_JPEG)
					{
						$isonkuvanPolku = $polku."otsikko.jpg";
						$pikkukuvanPolku = $polku."thumb.otsikko.jpg";
					}
					else if($tyyppi == IMAGETYPE_PNG)
					{
						$isonkuvanPolku = $polku."otsikko.png";
						$pikkukuvanPolku = $polku."thumb.otsikko.png";
					}
					if($leveys > 640 || $korkeus > 640)
					{
						$suhde = $leveys/$korkeus;
						
						if($suhde >= 1)
						{
							$uusiLeveys = 640;
							$uusiKorkeus = 640/$suhde;
						}
						else
						{
							$uusiKorkeus = 640;
							$uusiLeveys = 640*$suhde;
						}
						$uusiKuva = imagecreatetruecolor($uusiLeveys, $uusiKorkeus);
						if($tyyppi == IMAGETYPE_JPEG)
							$vanhaKuva = imagecreatefromjpeg($loppusijoituspaikka);
						else if($tyyppi == IMAGETYPE_PNG)
							$vanhaKuva = imagecreatefrompng($loppusijoituspaikka);
						
						imagecopyresampled($uusiKuva, $vanhaKuva, 0, 0, 0, 0, $uusiLeveys, $uusiKorkeus, $leveys, $korkeus);
						
						if($tyyppi == IMAGETYPE_JPEG)
							imagejpeg($uusiKuva, $pikkukuvanPolku, 75);
						else if($tyyppi == IMAGETYPE_PNG)
							imagepng($uusiKuva, $pikkukuvanPolku, 3);
						
					}
					else
					{
						rename($loppusijoituspaikka, $pikkukuvanPolku);
					}
					
					if($kuvaIsona)
						rename($loppusijoituspaikka, $isonkuvanPolku);
					else
						unlink($loppusijoituspaikka);
						
					header("Location: index.php?sivu=blog&id=".$id);
				}
				else
					header("Location: index.php?sivu=blog&id=".$id."&virhe=KUVA_UPLOAD_MOVE_UPLOADED_FILE_FAIL");
			}
			else
				header("Location: index.php?sivu=blog&id=".$id."&virhe=KUVA_UPLOAD_KANSIO_PUUTTUU");
		}
		else
			header("Location: index.php?sivu=blog&id=".$id."&virhe=KUVA_UPLOAD_TYYPPI_VAARA");
	}
	else
		header("Location: index.php?sivu=blog&id=".$id."&virhe=KUVA_UPLOAD_ERROR_".$kuva["error"]);
}

if($käyttäjä->onkoYlläpitäjä())
{
	if($_p["sid"] == session_id())
	{
		if($_p["t"] == "j")
		{
			// Julkaistaan artikkeli
			if($_p["id"])
			{
				$q = "UPDATE merkinta SET julkaistu=1 WHERE id=".$_p["id"];
				$tietokanta->päivitä($q);
			}
			header("Location: index.php?sivu=blog&id=".$_p["id"]);
		}
		else if($_p["t"] == "pm")
		{
			// Pikamuokkain, päivitetään vain otsikko&teksti
			if($_p["id"])
			{
				$id = $_p["id"];
				if($tietokanta->haeYksi("SELECT julkaistu FROM merkinta WHERE id=".$_p["id"]))
					$q = "UPDATE merkinta SET otsikko='".$_p["otsikko"]."', teksti='".$_p["teksti"]."', biisi='".$_p["biisi"]."', paikka='".$_p["paikka"]."', galleria='".$_p["galleria"]."', muokattu=NOW() WHERE id=".$id;
				else
					$q = "UPDATE merkinta SET otsikko='".$_p["otsikko"]."', teksti='".$_p["teksti"]."', biisi='".$_p["biisi"]."', paikka='".$_p["paikka"]."', galleria='".$_p["galleria"]."' WHERE id=".$id;
				$tietokanta->päivitä($q);
				if($_FILES["kuva"] && $_FILES["kuva"]["error"] != UPLOAD_ERR_NO_FILE)
				{
					if($_p["kuva_isona"])
						$kuvaIsona = true;
					else
						$kuvaIsona = false;
					tallennaKuva($id, $_FILES["kuva"], $kuvaIsona);
				}
				else if($_p["kuva_poista"])
				{
					// Poistetaan wanha kuva
					$polku = YAMBLOG_FULL_BASEDIR."kuvat/".$id."/";
					$päätteet = array("jpg", "png");
					foreach($päätteet as $avain => $pääte)
					{
						if(file_exists($polku."otsikko.".$pääte))
							unlink($polku."otsikko.".$pääte);
						if(file_exists($polku."thumb.otsikko.".$pääte))
							unlink($polku."thumb.otsikko.".$pääte);
					}
					rmdir($polku);
					header("Location: index.php?sivu=blog&id=".$_p["id"]);
				}
				else
					header("Location: index.php?sivu=blog&id=".$_p["id"]);
			}
			else
				header("Location: index.php?sivu=blog");
		}
		else if($_p["t"] == "uu")
		{
			if($_p["galleria"] != "")
				$q = "INSERT INTO merkinta(otsikko, teksti, biisi, paikka, galleria, uid, paivays) VALUES('".$_p["otsikko"]."', '".$_p["teksti"]."', '".$_p["biisi"]."', '".$_p["paikka"]."', '".$_p["galleria"]."', ".$käyttäjä->uid.", NOW())";
			else
				$q = "INSERT INTO merkinta(otsikko, teksti, biisi, paikka, uid, paivays) VALUES('".$_p["otsikko"]."', '".$_p["teksti"]."', '".$_p["biisi"]."', '".$_p["paikka"]."', ".$käyttäjä->uid.", NOW())";
			$id = $tietokanta->tallenna($q);
			if($id)
			{
				if($_FILES["kuva"])
				{
					if($_p["kuva_isona"])
						$kuvaIsona = true;
					else
						$kuvaIsona = false;
					tallennaKuva($id, $_FILES["kuva"], $kuvaIsona);
				}
				else
					header("Location: index.php?sivu=blog&id=".$id);
			}
			else
				header("Location: index.php?virhe=TIETOKANTA_TALLENNUS_EPAONNISTUI");
		}
		else if($_p["t"] == "poista")
		{
			$q = "DELETE FROM merkinta WHERE id=".$_p["id"];
			$tietokanta->poista($q);
			// Poistetaan wanha kuva
			$polku = YAMBLOG_FULL_BASEDIR."kuvat/".$_p["id"]."/";
			$päätteet = array("jpg", "png");
			foreach($päätteet as $avain => $pääte)
			{
				if(file_exists($polku."otsikko.".$pääte))
					unlink($polku."otsikko.".$pääte);
				if(file_exists($polku."thumb.otsikko.".$pääte))
					unlink($polku."thumb.otsikko.".$pääte);
			}
			rmdir($polku);
			header("Location: index.php");
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