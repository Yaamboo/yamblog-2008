<?php
class Kuvagalleria
{
	var $pääkansio;		// Pääkansio (/kuvagalleria)
	var $polku;			// Polku, jossa ollaan (esim. 2008-11-11)
	
	var $tiedostot;
	
	function __construct($pääkansio, $polku)
	{
		$this->pääkansio = $pääkansio;
		$this->polku = $polku;
		
		$this->tiedostot = $this->haeHakemistonTiedostot($this->polku);
	}
	
		
	function haeHakemistonTiedostot($polku = null)
	{
		// Haetaan hakemisto missä ollaan...
		
		if(!$polku)
			$polku = $this->polku;
		
		if(!is_dir($polku))
			return null;
		
		// Avataan hakemisto
		$tiedostot = scandir($polku);
		// Asetetaan muuttujia...
		$palautettavatTiedostot = array();
		$kuvienMäärä = 0;
		
		// Haetaan kaikki .jpg-päätteiset tiedostot hakemistossa, missä ei ole nimessä sanaa _thumb ja tallennetaan
		// ne taulukkoon.
		foreach($tiedostot as $avain => $tiedosto)
		{
			if((substr($tiedosto, -4) == ".jpg" || substr($tiedosto, -4) == ".gif" 
				|| substr($tiedosto, -4) == ".png" || substr($tiedosto, -4) == ".JPG"
				|| substr($tiedosto, -4) == ".GIF" || substr($tiedosto, -4) == ".PNG")
				&& substr($tiedosto, 0, 1) != "."
				&& substr($tiedosto, 0, 6) != "thumb.")
			{
				$palautettavatTiedostot[$kuvienMäärä] = $tiedosto;
				$kuvienMäärä++;
			}
			
		}
		
		sort($palautettavatTiedostot);
		// Palautetaan tiedostolistaus.
		return $palautettavatTiedostot;
	}
	
	function haeSeuraavaKuva($nykKuva)
	{
		return $this->haeKuva($nykKuva, 1);
		/*
		//$tiedostolistaus = $this->haeHakemistonTiedostot($kansio);
		$tiedostolistaus = $this->tiedostot;
		$kuvanNumero = array_search($nykKuva, $tiedostolistaus);
		$kuvanNumero++;
		
		return $tiedostolistaus[$kuvanNumero];
		*/
	}
	
	function haeEdellinenKuva($nykKuva)
	{
		return $this->haeKuva($nykKuva, -1);
		/*
		//$tiedostolistaus = $this->haeHakemistonTiedostot($kansio);
		$tiedostolistaus = $this->tiedostot;
		$kuvanNumero = array_search($nykKuva, $tiedostolistaus);
		$kuvanNumero--;
		
		return $tiedostolistaus[$kuvanNumero];
		*/
	}
	
	function haeKuva($nykKuva, $pos)
	{
		$kuvanNumero = array_search($nykKuva, $this->tiedostot);
		$kuvanNumero = $kuvanNumero+$pos;
		return $this->tiedostot[$kuvanNumero];
	}
	
	function palautaKoko($koko)
	{
		if($koko < 1000)
			return $koko." t";
		else if($koko >= 1000 && $koko < 1000000)
			return round(($koko / 1024), 1)." Kt";
		else if($koko >= 1000000 && $koko < 1000000000)
			return round(($koko / (1024 * 1024 ) ), 1)." Mt";
		else
			return round(($koko / (1024 * 1024 * 1024 ) ), 1)." Gt";
	}
	
	function palautaMetadata($tiedosto)
	{
		$palautus = "";
		
		if(function_exists('exif_read_data'))
		{
			if(file_exists($tiedosto) && exif_imagetype($tiedosto) == IMAGETYPE_JPEG)
			{
				$exif = exif_read_data($tiedosto);
				if($exif["Model"])
					$palautus .= "<b>Kamera:</b><br />".$exif["Model"]."<br />";
				if($exif["DateTime"])
					$palautus .= "<b>Kuvattu:</b><br />".$exif["DateTime"]."<br />";
				if($exif["ExposureTime"])
					$palautus .= "<b>Valotusaika:</b><br />".$exif["ExposureTime"]."<br />";
				if($exif["COMPUTED"]["ApertureFNumber"])
					$palautus .= "<b>Aukko:</b><br />".$exif["COMPUTED"]["ApertureFNumber"]."<br />";
				
			}
			else
				return false;
		}
		else
			return false;
		
		return $palautus;
	}
	
	function palautaKansionNimi($teksti)
	{
		// Muunnetaan ainakin aluksi alaviivat välilyönneiksi.
		$teksti = str_replace("_", " ", $teksti);
		return $teksti;
	}
	
	function luoPikkukuva($kuva, $kansio)
	{
		$polku = YAMBLOG_FULL_BASEDIR.$this->pääkansio."/".basename($kansio)."/";
		if(file_exists($polku.$kuva))
		{
			if(substr(basename($kuva), 0, 6) <> "thumb.")
			{
				// Luodaan thumbnaili...
				$pikkukuvanPolku = $polku."thumb.".basename($kuva);
				$isonkuvanPolku = $polku.basename($kuva);
				
				
				list($leveys, $korkeus, $tyyppi) = getimagesize($isonkuvanPolku);
				
				if($leveys > 150 || $korkeus > 150)
				{
					$suhde = $leveys/$korkeus;
					
					if($suhde >= 1)
					{
						$uusiLeveys = 150;
						$uusiKorkeus = 150/$suhde;
					}
					else
					{
						$uusiKorkeus = 150;
						$uusiLeveys = 150*$suhde;
					}
					
					if($tyyppi == IMAGETYPE_GIF)
					{
						$vanhaKuva = imagecreatefromgif($isonkuvanPolku);
					}
					else if($tyyppi == IMAGETYPE_JPEG)
					{
						$vanhaKuva = imagecreatefromjpeg($isonkuvanPolku);
					}
					else if($tyyppi == IMAGETYPE_PNG)
					{
						$vanhaKuva = imagecreatefrompng($isonkuvanPolku);
					}
					$uusiKuva = imagecreatetruecolor($uusiLeveys, $uusiKorkeus);
					imagecopyresampled($uusiKuva, $vanhaKuva, 0, 0, 0, 0, $uusiLeveys, $uusiKorkeus, $leveys, $korkeus);
				}
				else
				{
					if($tyyppi == IMAGETYPE_GIF)
					{
						$uusiKuva = imagecreatefromgif($isonkuvanPolku);
					}
					else if($tyyppi == IMAGETYPE_JPEG)
					{
						$uusiKuva = imagecreatefromjpeg($isonkuvanPolku);
					}
					else if($tyyppi == IMAGETYPE_PNG)
					{
						$uusiKuva = imagecreatefrompng($isonkuvanPolku);
					}
					
				} 
				if($tyyppi == IMAGETYPE_GIF)
				{
					imagegif($uusiKuva, $pikkukuvanPolku);
				}
				else if($tyyppi == IMAGETYPE_JPEG)
				{
					imagejpeg($uusiKuva, $pikkukuvanPolku, 75);
				}
				else if($tyyppi == IMAGETYPE_PNG)
				{
					imagepng($uusiKuva, $pikkukuvanPolku, 3);
				}
				
			}
		}
	}
		
}
?>
