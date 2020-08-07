<?php
if(!defined('YAMBLOG_RUNNING'))
	die("Virhe (mahdollinen hyökkäysyritys)");

class Kayttaja
{
	var $uid;
	var $vid;
	var $tietokanta;
	var $käyttäjätiedot;
	var $tunnistautunut;
	
	function __construct($tietokanta)
	{
		$this->tietokanta = $tietokanta;
		session_start();
		// Verrataan mahdollisia kakkuja tietokannassa oleviin käyttäjätietoihin.
		if($_COOKIE["vid"])
		{
			// Palaava käyttäjä? Tarkistetaan onko varmistus-hash sama
			$uid = $_COOKIE["uid"];
			$vid = $_COOKIE["vid"];
			$q = "SELECT * FROM kirjautuneet WHERE uid='".$uid."' AND vid='".$vid."' ORDER BY loppu DESC LIMIT 0,1";
			$tulos = $this->tietokanta->hae($q);
			if($tulos->onkoRivejä())
			{
				// Tarkistetaan onko voimassa...
				$tiedot = $tulos->seuraava();
				$alku = strtotime($tiedot->alku);
				$loppu = strtotime($tiedot->loppu);
				if($alku <= time() && time() <= $loppu)
				{
					$this->uid = $tiedot->uid;
					$this->vid = $tiedot->vid;
					if($this->uid > 0)
					{
						// Säädetään loppu 6 kk päähän...
						$q = "UPDATE kirjautuneet SET loppu=DATE_ADD(NOW(), INTERVAL 6 MONTH) WHERE uid='".$this->uid."' AND vid='".$this->vid."'";
						if($this->tietokanta->päivitä($q))
						{
							$loppu = strtotime($this->tietokanta->haeYksi("SELECT loppu FROM kirjautuneet WHERE uid='".$this->uid."' AND vid='".$this->vid."' ORDER BY loppu DESC LIMIT 0,1"));
							setcookie("uid", $this->uid, $loppu, "/");
							setcookie("vid", $this->vid, $loppu, "/");
						}
						
						// Haetaan myös käyttäjätiedot...
						$q = "SELECT * FROM kayttajatiedot WHERE uid=".$this->uid;
						
						// Automaattitunnistautuminen?
						$q = "SELECT auto_tunnistautuminen FROM kayttajat WHERE id=".$this->uid;
						$tunnistus = $tietokanta->haeYksi($q);
						if($tunnistus == 1)
							$this->tunnistautunut = true;
						else
							$this->tunnistautunut = true;
					}
					else
					{
						if($tiedot->tunnistautunut == 1)
							$this->tunnistautunut = true;
						else
							$this->tunnistautunut = false;
					}
					
				}
				else
				{
					// Tuhotaan cookiet
					setcookie("uid", "", time()-3600, "/");
					setcookie("vid", "", time()-3600, "/");
					$this->tunnistautunut = false;
				}
			}
			else
			{
				// Vääriä informaatioita kakuissa.
				$this->uid = null;
				$this->vid = null;
				$this->tunnistautunut = false;
				// Tuhotaan cookiet
				setcookie("uid", "", time()-3600, "/");
				setcookie("vid", "", time()-3600, "/");
			}
		}
		else
		{
			$this->uid = null;
			$this->vid = null;
			$this->tunnistautunut = false;
		}
	}
	
	function onkoKirjautunut()
	{
		if($this->uid && $this->vid)
			return true;
		else
			return false;
	}
	
	function haeTunnus()
	{
		if($this->uid && $this->vid)
			return $this->tietokanta->haeYksi("SELECT tunnus FROM kayttajat WHERE id =".$this->uid);
		else
			return null;
	}
	
	function onkoYlläpitäjä()
	{
		if($this->uid && $this->vid)
		{
			if($this->tietokanta->haeYksi("SELECT yllapitaja FROM kayttajat WHERE id=".$this->uid) == 1)
				return true;
			else
				return false;
		}
		else
			return false;
	}
	
	function haeAvatar($koko)
	{
		$tiedostoPitkä = YAMBLOG_FULL_BASEDIR."kuvat/avatarat/".$this->uid.".".$koko.".png";
		$tiedostoLyhyt = YAMBLOG_BASEDIR."kuvat/avatarat/".$this->uid.".".$koko.".png";
		if(file_exists($tiedostoPitkä))
			return $tiedostoLyhyt;
		else
			return null;
	}
	
	function onkoTunnistautunut()
	{
		return $this->tunnistautunut;
	}
}
?>