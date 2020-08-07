<?php
require_once("tekstielementti.php");

class Blogimerkinta extends Tekstielementti
{
	var $id;
	var $uid;
	var $päiväys;
	var $muokattu;
	var $otsikko;
	var $teksti;
	var $julkaistu;
	var $biisi;
	var $paikka;
	var $galleria;
	
	function __construct($id, $uid, $päiväys, $muokattu = null, $otsikko, $teksti, $julkaistu = 0, $biisi = null, $paikka = null, $galleria = null)
	{
		$this->id = $id;
		$this->uid = $uid;
		$this->päiväys = strtotime($päiväys);
		if(!$muokattu)
			$this->muokattu = null;
		else
			$this->muokattu = strtotime($muokattu);
		$this->otsikko = $otsikko;
		$this->teksti = $teksti;
		if($julkaistu == 1)
			$this->julkaistu = true;
		else
			$this->julkaistu = false;
		$this->biisi = $biisi;
		$this->paikka = $paikka;
		if($galleria != "")
			$this->galleria = $galleria;
		else
			$this->galleria = null;
	}
	
	function tulostaOtsikko()
	{
		return htmlspecialchars($this->otsikko);
	}
	
	function tulostaMerkintä($lyhennelmä = false, $käsitteleTagit = true, $tunnistautunut = false)
	{
		// TODO: Tekstin käsittely...
		if($lyhennelmä == true)
		{
			// Pätkäistään...
			if(strlen($this->teksti) <= 750)
				$teksti = $this->teksti;
			else
			{
				$vaihtoehdot = array(" ", ".", "\n", "\r", "\t", "?", ",", "!", ":", ";", "(", ")", "[");
				$paikka = strlen($this->teksti);
				foreach($vaihtoehdot as $avain => $merkki)
				{
					$merkinPaikka = strpos($this->teksti, $merkki, 750);
					if($merkinPaikka < $paikka && $merkinPaikka > 750)
						$paikka = $merkinPaikka;
				}
				if(!$paikka)
					$paikka = strlen($this->teksti);
				$teksti = substr($this->teksti, 0, $paikka);
			}
			if($käsitteleTagit == false)
				return htmlspecialchars($teksti);
			else
			{
				if(strlen($this->teksti) <= 750)
					return $this->muotoile($teksti, $tunnistautunut);
				else
					return $this->muotoile($teksti, $tunnistautunut)."...";
			}
		}
		else
		{
			if($käsitteleTagit == false)
				return htmlspecialchars($this->teksti);
			else
				return $this->muotoile($this->teksti, $tunnistautunut);
		}
	}
	
	function tulostaPäiväys()
	{
		return gmdate("j.n.Y H:i", $this->päiväys);
	}
	
	function tulostaMuokkaus()
	{
		if($this->muokattu)
			return gmdate("j.n.Y H:i", $this->muokattu);
		else
			return null;
	}
	
	function haeKommenttitieto($tietokanta)
	{
		$kommentteja = $tietokanta->haeYksi("SELECT COUNT(*) FROM kommentit WHERE mid=".$this->id." AND POISTETTU IS NULL");
		if($kommentteja > 0)
		{
			$palautus = $kommentteja." kommentti";
			if($kommentteja > 1)
				$palautus .= "a";
		}
		else
			$palautus = "Ei kommentteja";
		
		return $palautus;
	}
	
	function haeMuokkaimenKorkeus()
	{
		if(strlen($this->teksti) <= 1000)
			return 20;
		else if(strlen($this->teksti) <= 2000)
			return 30;
		else
			return 40;
	}
	
	function haeBiisilinkki()
	{
		$palautus = "";
		list($artisti, $biisi) = explode(" - ", $this->biisi);
		if($artisti && $biisi)
			$palautus = "<a href=\"http://www.last.fm/music/".urlencode($artisti)."/_/".urlencode($biisi)."\">".htmlspecialchars($this->biisi)."</a>";
		else
			$palautus = htmlspecialchars($this->biisi);
		return $palautus;
	}
	
	function haePaikkalinkki()
	{
		$palautus = "";
		$paikat = explode(";", $this->paikka);
		$i = 0;
		foreach($paikat as $avain => $paikka)
		{ 
			if($i > 0)
				$palautus .= "; ";
			$palautus .= "<a href=\"http://maps.google.fi/maps?q=".urlencode($paikka)."\">".htmlspecialchars($paikka)."</a>";
			$i++;
		}
		return $palautus;
	}
}
?>
