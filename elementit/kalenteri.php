<?php
?>
<h2>Kalenteri</h2>
<p>Tämän kalenterin pointtina on mahdollistaa suorat siirtymät jokaiseen päivään jona olen päästänyt
sanansäiläni viuhumaan. Siirtämällä hiiren päivän päälle näet päivän merkintöjen otsikon ja sijainnin.</p>
<p>Blogia voi selata päivittäin tai kuukausittain.</p>
<div id="kalenterit">
<?php
// Luodaan taulukko alkupäivästä (2008-08) tähän päivään...
$vuosi = 2008;
$kuukausi = 6;
$viikko = 0;

$kuukaudet = array("Tammi", "Helmi", "Maalis", "Huhti", "Touko", "Kesä", "Heinä", "Elo", "Syys", "Loka", "Marras", "Joulu");

while($vuosi <= date("Y"))
{
	if($vuosi == date("Y"))
		$lopetuskuukausi = date("n");
	else
		$lopetuskuukausi = 12;
	
	while($kuukausi <= $lopetuskuukausi)
	{
		?><table class="kalenteri"><thead><tr><th colspan="8">
		<?php
		$tulostettavaKuukausi = $kuukausi;
		if($tulostettavaKuukausi < 10)
			$tulostettavaKuukausi = "0".$tulostettavaKuukausi;
		echo "<a href=\"$vuosi-$tulostettavaKuukausi\">".$kuukaudet[$kuukausi-1]."kuu</a> ".$vuosi."</th></tr>";
		?><tr><th class="viikko">Vko</th><th>M</th><th>T</th><th>K</th><th>T</th><th>P</th><th>L</th><th>S</th></tr></thead>
		<tbody>
		<?php
		$viikkoja = 0;
		for($i = 1; $i <= date("t", strtotime("$vuosi-$kuukausi-01")); $i++)
		{
			$päiväys = "$vuosi-$kuukausi-$i";
			$päivänNumero = date("w", strtotime($päiväys));
			if($päivänNumero == 0)
				$päivänNumero = 7;
			//echo $päiväys." ".$viikko." ".date("W", strtotime($päiväys));
			if($viikko != date("W", strtotime($päiväys)) || $i == 1)
			{
				// Uusi viikko
				$viikko = date("W", strtotime($päiväys));
				if($i == 1)
				{
					echo "<tr><td class=\"viikko\">".$viikko."</td>";
					// Ekstratarkistukset: montako riviä pitää siirtää...
					if($päivänNumero > 1)
					{
						for($j = 1; $j < $päivänNumero; $j++)
							echo "<td class=\"tyhja\"></td>";
					}
				}
				else
					echo "</tr><tr><td class=\"viikko\">".$viikko."</td>";
				$viikkoja++;
			}
			// Tarkistetaan onko päivälle merkintää...
			$q = "SELECT id FROM merkinta WHERE paivays LIKE \"".date("Y-m-d", strtotime($päiväys))."%\" AND julkaistu = 1";
			if($tietokanta->haeYksi($q))
			{
				?><td class="luettavaa"<?php
				if(date("Y-m-d") == date("Y-m-d", strtotime($päiväys)))
					echo " style=\"outline: 1px #2A2 solid;\""?>>
				<a href="<?php echo date("Y-m-d", strtotime($päiväys));?>"
				onmousemove="naytaKalenterilaatikko('kalenteripaivakuvaus', '<?php echo date("Y-m-d", strtotime($päiväys))?>');"
				onmouseout="piilotaKalenterilaatikko('kalenteripaivakuvaus');">
				<?php
				$luettavaa = true;
			}
			else
			{
				if(date("Y-m-d") == date("Y-m-d", strtotime($päiväys)))
					echo "<td style=\"outline: 1px #2A2 solid;\">";
				else
					echo "<td>";
				$luettavaa = false;
			}
			
			if($i < 10)
				echo "0".$i;
			else
				echo $i;
			
			if($luettavaa == true)
				echo "</a>";
			echo "</td>";
		}
		$kuukausi++;
		if($päivänNumero < 7)
		{
			while($päivänNumero < 7)
			{
				echo "<td class=\"tyhja\"></td>";
				$päivänNumero++;
			}
		}
		if($viikkoja < 6)
			echo "</tr><tr><td class=\"tyhja\" colspan=\"8\" style=\"color: #DDD;\">.</td>";
		?>
		</tr></tbody></table><?php
	}
	$vuosi++;
	$kuukausi = 1;
}
?>
</div><div id="kalenteripaivakuvaus" style="display: none;"><img src="kuvat/loading.gif" alt="Loading..." /></div>
<img src="kuvat/loading.gif" style="height: 0; width: 0;" alt="Loading..." />