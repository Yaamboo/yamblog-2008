<?php
// Luodaan tietokantayhteys
define("YAMBLOG_RUNNING", true);
require_once("../../engine/tietokanta.php");
if(!$tietokanta)
	$tietokanta = new Tietokanta();
$_p = array_merge($_GET, $_POST);
// Tulostetaan
if($_p["paiva"])
{
	$q = "SELECT otsikko, paikka FROM merkinta WHERE paivays LIKE \"".$_p["paiva"]."%\" ORDER BY paivays DESC";
	$tulos = $tietokanta->hae($q);
	if($tulos->onkoRivejä())
	{
		while($tulos->onkoRivejä())
		{
			$tiedot = $tulos->seuraava();
			echo "<b>".htmlspecialchars($tiedot->otsikko)."</b><br />";
			if($tiedot->paikka)
			{
				$paikat = explode(";", $tiedot->paikka);
				$i = 0;
				foreach($paikat as $avain => $paikka)
				{ 
					if($i > 0)
						echo "; ";
					echo htmlspecialchars($paikka);
					$i++;
				}
				echo "<br />";
			}
		}
	}
	else
		echo "Ei merkintöjä tälle päivälle?";
}
else
{
	?><img src="kuvat/loading.gif" alt="Loading..." /><?
}
?>