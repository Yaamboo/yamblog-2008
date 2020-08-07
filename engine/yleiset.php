<?php
class Yleisfunktiot
{
	var $tietokanta;
	var $käyttäjä;
	
	function __construct($tietokanta, $käyttäjä)
	{
		$this->tietokanta = $tietokanta;
		$this->käyttäjä = $käyttäjä;
	}
	
	function haeAvatar($koko, $uid)
	{
		$tiedostoPitkä = YAMBLOG_FULL_BASEDIR."kuvat/avatarat/".$uid.".".$koko.".png";
		$tiedostoLyhyt = YAMBLOG_BASEDIR."kuvat/avatarat/".$uid.".".$koko.".png";
		if(file_exists($tiedostoPitkä))
			return $tiedostoLyhyt;
		else
			return null;
	}
	
	function haeNimi($uid)
	{
		$q = "SELECT etunimi, sukunimi, muoto FROM kayttajatiedot WHERE uid=".$uid;
		$tiedot = $this->tietokanta->hae($q);
		if($tiedot->onkoRivejä())
		{
			$tiedot = $tiedot->seuraava();
			$nimi = "";
			if($tiedot->muoto == 1)
				return htmlspecialchars($this->tietokanta->haeYksi("SELECT tunnus FROM kayttajat WHERE uid=".$uid));
			else
			{
				if(strlen($tiedot->etunimi) > 0)
					$nimi .= htmlspecialchars($tiedot->etunimi);
				if(strlen($tiedot->sukunimi) > 0 && ($tiedot->muoto == 2 || $tiedot->muoto == 3))
				{
					if($tiedot->muoto == 2)
						$nimi .= " ".htmlspecialchars($tiedot->sukunimi);
					else
						$nimi .= " ".htmlspecialchars(substr($tiedot->sukunimi,0,1)).".";
				}
				if(strlen($nimi) > 1)
					return $nimi;
				else
					return htmlspecialchars($this->tietokanta->haeYksi("SELECT tunnus FROM kayttajat WHERE uid=".$uid));
			}
		}
		else
			return htmlspecialchars($this->tietokanta->haeYksi("SELECT tunnus FROM kayttajat WHERE id=".$uid));
	}
}
?>
