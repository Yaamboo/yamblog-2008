<?php
if(!defined('YAMBLOG_RUNNING'))
	die("Virhe (mahdollinen hyökkäysyritys)");

class Hakutulos
{
	var $hakutulos;
	var $rivimäärä;
	var $nykyinen;
	var $virhe;
	
	function __construct($tulos)
	{
		if($tulos)
		{
			$this->hakutulos = $tulos;
			$this->rivimäärä = mysql_num_rows($tulos);
			$this->nykyinen = 0;
			$this->virhe = null;
		}
		else
		{
			$this->hakutulos = null;
			$this->rivimäärä = 0;
			$this->nykyinen = 0;
			$this->virhe = mysql_error();
		}
	}
	
	function onkoRivejä()
	{
		if($this->nykyinen < $this->rivimäärä) return true;
		else return false;
	}

	function seuraava()
	{
		$this->nykyinen++;
		return mysql_fetch_object($this->hakutulos);
	}

	function rivimäärä()
	{
		return $this->rivimäärä;
	}
	
	function haeVirhe()
	{
		return "SQL: ".$this->virhe;
	}
}

class Tietokanta
{
	var $yhteys;
	
	function __construct()
	{
		// Luodaan tietokantayhteys
		$yhteys = mysql_connect("localhost", "username", "password");
		if(!$yhteys)
			die("SQL: ei tietokantayhteyttä!");
		else
		{
			$this->yhteys = $yhteys;
			if(!mysql_select_db("yaamboo_yamblog"))
				die("SQL: ".mysql_error());
			//echo "Tietokantayhteys ok!";
		}
	}
	
	function __destruct()
	{
		if($this->yhteys)
		{
			mysql_close($this->yhteys);
			//echo "Tietokantayhteys tuhottu!";
		}
		else
		{
			//echo "Ei tuhottavaa tietokantayhteyttä!";
		}
	}
	
	function hae($q)
	{
		if($q && $this->yhteys)
		{
			$tulos = mysql_query($q, $this->yhteys);
			return new Hakutulos($tulos);
		}
		else
			return false;
	}
	
	function haeYksi($q)
	{
		if($q && $this->yhteys)
		{
			$tulos = mysql_query($q, $this->yhteys);
			if($tulos)
				if(mysql_num_rows($tulos)>0)
					return mysql_result($tulos,0);
				else
					return false;
			else
				return false;
		}
		else
			return false;
	}
	
	function tallenna($q)
	{
		if($q && $this->yhteys)
		{
			$tulos = mysql_query($q, $this->yhteys);
			if($tulos)
			{
				$id = mysql_insert_id($this->yhteys);
				if($id > 0)
					return $id;
				else
					return true;
			}
			else
				return false;
		}
		else
			return false;
	}
	
	function päivitä($q)
	{
		if($q && $this->yhteys)
		{
			$tulos = mysql_query($q, $this->yhteys);
			if($tulos)
				return true;
			else
				return false;
		}
		else
			return false;
	}
	
	function poista($q)
	{
		if($q && $this->yhteys)
		{
			$tulos = mysql_query($q, $this->yhteys);
			if($tulos)
				return true;
			else
				return false;
		}
		else
			return false;
	}
	
	// Yleisfunktioita
	function haeTunnus($id)
	{
		$tunnus = $this->haeYksi("SELECT tunnus FROM kayttajat WHERE id = ".$id);
		if($tunnus)
			return $tunnus;
		else
			return null;
	}
}
?>
