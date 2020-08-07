<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fi" lang="fi">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>YamBlog<?php if($otsikko) echo " - ".$otsikko;?></title>
<base href="<?php echo YAMBLOG_BASEDIR;?>" />
<link rel="stylesheet" type="text/css" href="style.css" media="screen" />
<link rel="stylesheet" type="text/css" href="tulostus.css" media="print" />
<link rel="alternate" type="application/rss+xml" href="rss" title="YamBlog (RSS 2.0)" />
<link rel="alternate" type="application/rss+xml" href="rss.php?kommentit=0" title="Kommentit" />
<?php
if($_p["sivu"] == "blog" && $_p["id"])
{
	?><link rel="alternate" type="application/rss+xml" href="rss.php?kommentit=<?php echo $_p["id"];?>" title="Kommentit tälle merkinnälle" /><?php
}
?>

<script src="elementit/scriptaculous/prototype.js" type="text/javascript"></script>
<script src="elementit/scriptaculous/scriptaculous.js" type="text/javascript"></script>
<script src="elementit/swfobject.js" type="text/javascript"></script> 
<script src="elementit/skriptit.js" type="text/javascript"></script>
</head>
<body>
<div id="kirjautuminen"><p>
<?php
if($käyttäjä->onkoKirjautunut())
{
	// haetaan käyttäjän tunnus jne
	echo "<span style=\"font-size: large;\">Hei ".$yleisfunktiot->haeNimi($käyttäjä->uid)."!</span><br />";
	?>
	<a href="asetukset">Muokkaa asetuksia</a> |
	<a href="kirjaudu.php?t=ulos&amp;sid=<?php echo session_id();?>">Kirjaudu ulos</a>
	<?php
}
else
{
	?>
	<a href="javascript:naytaKirjautumisruutu('kirjaudu');">Kirjaudu</a> |
	<?php
	if($käyttäjä->onkoTunnistautunut())
	{
		?>
		<a href="javascript:naytaKirjautumisruutu('tunnistaudu');" style="font-weight: bold;">Tunnistautunut</a> |
		<?php
	}
	else
	{
		?>
		<a href="javascript:naytaKirjautumisruutu('tunnistaudu');">Tunnistaudu</a> |
		<?php
	}
	?>
	<a href="rekisteroidy">Rekisteröidy</a> 
	<form action="kirjaudu.php" method="post" style="display: none;" id="kirjautuminen_form">
	<input type="hidden" name="t" value="sisaan" />
	<input type="hidden" name="sid" value="<?php echo session_id();?>" />Tunnus<br />
	<input type="text" name="tunnus" size="30" /><br />
	Salasana<br />
	<input type="password" name="salasana" size="30" /><br />
	<input type="submit" value="Kirjaudu" style="margin-top: 2px;" />
	<input type="reset" value="Tyhjennä" style="margin-top: 2px;" onclick="naytaKirjautumisruutu('kirjaudu');" />
	</form>
	<?php
	if($käyttäjä->onkoTunnistautunut())
	{
		?>
		<form action="kirjaudu.php" method="post" style="display: none;" id="kirjautuminen_tunnistaudu">
		<input type="hidden" name="t" value="tunnistuspois" />
		<input type="hidden" name="sid" value="<?php echo session_id();?>" />Olet tällä hetkellä tunnistautuneena järjestelmässä.<br />
		<input type="submit" value="Unohda minut" style="margin-top: 2px;"/>
		<input type="reset" value="Peruuta" style="margin-top: 2px;" onclick="naytaKirjautumisruutu('tunnistaudu');" />
		</form>
		<?php
	}
	else
	{
		?>
		<form action="kirjaudu.php" method="post" style="display: none;" id="kirjautuminen_tunnistaudu">
		<input type="hidden" name="t" value="tunnistus" />
		<input type="hidden" name="sid" value="<?php echo session_id();?>" />Salasana<br />
		<input type="password" name="koodi" size="30" /><br />
		<input type="submit" value="Tunnistaudu" style="margin-top: 2px;"/>
		<input type="reset" value="Tyhjennä" style="margin-top: 2px;" onclick="naytaKirjautumisruutu('tunnistaudu');" />
		</form>
		<?php
	}
}
?></p>
</div>
<div id="alkuTausta">
	<div id="alku">
		<h1>YamBlog</h1>
	</div>
</div>
<div id="valikkoTausta">
	<div id="valikko">
	<a href="index.php">Blogin etusivu</a>
	<a href="kalenteri">Kalenteri</a>
	<a href="faq">Kukamitähäh?</a>
	<a href="galleria">Galleria</a>
	<a href="linkit">Linkkejä</a>
	</div>
</div>
<div id="sisältöTausta">
	<div id="sisältö">
	
	<div id="apulaatikko_vip" class="apulaatikko" style="display: none;"><img src="kuvat/kysymysmerkki.png" alt="Kysymysmerkki" />
	Koska en halua ihan kaikkea paljastella aina itsestäni ja elämästäni täysin tuntemattomille ihmisille,
	osa tekstistä vaatii tunnistautumisen. Voit tehdä sen joko ylänurkasta mikäli et ole kirjautunut, ja
	kirjautuneille käyttäjille riittää kertomani salasanan syöttäminen asetuksissa (selkeästi sanottu).</div>
	<div id="apulaatikko_vip_on" class="apulaatikko" style="display: none;"><img src="kuvat/kysymysmerkki.png" alt="Kysymysmerkki" />
	Tämä teksti näkyy vain tunnistautuneille käyttäjille.</div>