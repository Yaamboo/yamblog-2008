<?php
// Määrittää että järjestelmä on oikeasti pyörinnässä jolloin voidaan käyttää muita sivuja
define("YAMBLOG_RUNNING", true);

// Includetetaan tärkeät roinat
require_once("engine/tietokanta.php");
require_once("engine/vakiot.php");

require_once("engine/tekstielementti.php");
require_once("engine/yleiset.php");
$tekstielementti = new Tekstielementti();

// Alustetaan tarpeelliset muuttujat
$tietokanta = new Tietokanta();
$yleisfunktiot = new Yleisfunktiot($tietokanta, null);
// $_p on parametrimuuttuja
$_p = array_merge($_GET, $_POST);

// Lähetetään otsikot
header("Content-Type: text/xml; charset=UTF-8");

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
echo "<?xml-stylesheet href=\"http://www.yaamboo.com/rss.xsl\" type=\"text/xsl\" media=\"screen\"?>\r\n";
?><rss version="2.0"
	xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
	<?php
	if(isset($_p["kommentit"]))
		echo "<title>YamBlog - Kommentit</title>";
	else
		echo "<title>YamBlog</title>";
	?>
		<link>http://www.yaamboo.com/</link>
		<language>fi-FI</language>
<?php
if(isset($_p["kommentit"]))
{
	?>
		<atom:link href="http://www.yaamboo.com/rss/?kommentit" rel="self" type="application/rss+xml" />
		<description>Yamblog - kommentit</description>
	<?php
	if($_p["kommentit"] > 0)
		$q = "SELECT * FROM kommentit WHERE mid=".$_p["kommentit"]." AND poistettu IS NULL ORDER BY paivays DESC LIMIT 0,10";
	else
		$q = "SELECT * FROM kommentit WHERE poistettu IS NULL ORDER BY paivays DESC LIMIT 0,10";
	$tulos = $tietokanta->hae($q);
	if($tulos->onkoRivejä())
	{
		while($tulos->onkoRivejä())
		{
			$kommentti = $tulos->seuraava();
			?>
			<item>
				<title><?php
				if($kommentti->uid == 0)
				{
					if(strlen($kommentti->tunnus) > 0)
						echo htmlspecialchars($kommentti->tunnus);
					else
						echo "Anonyymi";
				}
				else
				{
					$kirjoittaja = $yleisfunktiot->haeNimi($kommentti->uid);
					if($kirjoittaja)
						echo $kirjoittaja;
					else
					{
						$kirjoittaja = $tietokanta->haeTunnus($kommentti->uid);
						if($kirjoittaja)
							echo $kirjoittaja;
						else
							echo "Anonyymi";
					}
				}
				?>: kommentti <?php echo $kommentti->id;?></title>
				<link>http://www.yaamboo.com/<?php echo $kommentti->mid."/#k".$kommentti->id;?></link>
				<guid>http://www.yaamboo.com/<?php echo $kommentti->mid."/#k".$kommentti->id;?></guid>
				<description><![CDATA[<?php echo nl2br(htmlspecialchars($kommentti->teksti));?>]]></description>
				<pubDate><?php echo gmdate("r", strtotime($kommentti->paivays));?></pubDate>
			</item>
			<?php
		}
	}
}
else
{
	?>
		<atom:link href="http://www.yaamboo.com/rss" rel="self" type="application/rss+xml" />
		<description>Epämääräistä horinaa maailmalta.</description>
	<?php
	$q = "SELECT * FROM merkinta WHERE julkaistu=1 ORDER BY paivays DESC LIMIT 0,10";
	$tulos = $tietokanta->hae($q);
	if($tulos->onkoRivejä())
	{
		while($tulos->onkoRivejä())
		{
			$merkintä = $tulos->seuraava();
			?>
			<item>
				<title><![CDATA[<?php echo htmlspecialchars($merkintä->otsikko);?>]]></title>
				<link>http://www.yaamboo.com/<?php echo $merkintä->id."/".urlencode($merkintä->otsikko);?></link>
				<guid>http://www.yaamboo.com/<?php echo $merkintä->id."/".urlencode($merkintä->otsikko);?></guid>
				<description><![CDATA[<?php echo $tekstielementti->muotoile($merkintä->teksti, false);?>]]></description>
				<pubDate><?php echo gmdate("r", strtotime($merkintä->paivays));?></pubDate>
				<?php
				// Tarkistetaan onko otsikkokuvaa.
				$polku = YAMBLOG_FULL_BASEDIR."kuvat/".$merkintä->id."/";
				$päätteet = array("jpg", "png");
				foreach($päätteet as $avain => $pääte)
				{
					$otsikkokuva = $polku."thumb.otsikko.".$pääte;
					if(is_file($otsikkokuva))
					{
						echo "<enclosure url=\"http://www.yaamboo.com/kuvat/".$merkintä->id."/".basename($otsikkokuva)."\" length=\"".filesize($otsikkokuva)."\" type=\"".mime_content_type($otsikkokuva)."\" />";
					}
				}
				
				?>
			</item>
	<?php
		}
	}
}
?>
	</channel>
</rss>