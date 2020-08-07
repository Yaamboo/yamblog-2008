<?php
if($_p["virhe"])
{
	$virhe = $_p["virhe"];
	$virheilmoitukset = array(
		"TIETOKANTA_TALLENNUS_EPAONNISTUI" => "Tallennus tietokantaan epäonnistui. Voit yrittää uudelleen, mutta jos ongelma pysyy entisellään, ilmoita siitä.",
		"TUNNUS_TAI_SALASANA_VAARIN" => "Kirjoitit käyttäjätunnuksen tai salasanan väärin. Ole hyvä ja yritä uudelleen.",
		"VAARA_TUNNISTUS_SALASANA" => "Kirjoitit tunnistus-salasanan väärin. Ole hyvä ja yritä uudelleen.",
		"EI_KIRJAUTUNUT" => "Yritit tehdä kirjautumattomana kirjautuneen käyttäjän toimintoja. Kirjaudu ensin, yritä sitten uudelleen.",
		"VAARA_SESSIO_ID" => "Virheellinen sessio-id toiminnon suorittamiseen. Ole hyvä ja yritä uudelleen.",
		"EI_PARAMETREJA" => "Virheelliset parametrit, nyt et tainnut tehdä jotain ihan oikein. Tai sitten koodissa on iso bugi jossain.",
		"EI_OIKEUKSIA" => "Sinulla ei ole oikeuksia suorittaa tätä toimintoa.",
		"KOMMENTOINTI_ESTETTY" => "Kommentointi on tällä hetkellä poissa käytöstä. Ole hyvä ja yritä uudelleen sitten kun se on taas mahdollista.",
		"SISALTO_PUUTTUU" => "Jätit täyttämättä yhden tai useamman pakollisen kentän. Ole hyvä ja yritä uudelleen.",
		"ALLE_30_SEK_VIIME_KOMMENTISTA" => "Kirjoitit viime kommenttisi alle 30 sekuntia sitten. Ole hyvä ja yritä uudelleen kun tuo aika on kulunut.",
		"BOTTIVARMISTUS_VAARIN" => "Et täyttänyt oikein kenttää, jolla varmistetaan sinun olevan oikea ihminen. Ole hyvä ja yritä uudelleen. Mikäli et osaa täyttää ko. kenttää, ota yhteyttä.",
		"KUVA_UPLOAD_ERROR_1" => "Virhe kuvan siirrossa: Kuva on liian suuri (exceeds upload_max_filesize). Yritä uudelleen pienemmällä kuvalla.",
		"KUVA_UPLOAD_ERROR_2" => "Virhe kuvan siirrossa: Kuva on liian suuri (exceeds MAX_FILE_SIZE). Yritä uudelleen pienemmällä kuvalla.",
		"KUVA_UPLOAD_ERROR_3" => "Virhe kuvan siirrossa: Kuvaa ei saatu kokonaisena perille. Yritä uudelleen.",
		"KUVA_UPLOAD_ERROR_4" => "Virhe kuvan siirrossa: Ei tiedostoa jota siirtää",
		"KUVA_UPLOAD_ERROR_6" => "Virhe kuvan siirrossa: Väliaikaistiedostojen kansio puuttuu.",
		"KUVA_UPLOAD_ERROR_7" => "Virhe kuvan siirrossa: Kirjoitus levylle epäonnistui.",
		"KUVA_UPLOAD_ERROR_8" => "Virhe kuvan siirrossa: File upload stopped by extension. (Wha?)",
		"KUVA_UPLOAD_KANSIO_PUUTTUU" => "Virhe kuvan siirrossa: Kansio puuttuu.",
		"KUVA_UPLOAD_TYYPPI_VAARA" => "Virhe kuvan siirrossa: Tiedostotyyppi on väärä.",
		"KUVA_UPLOAD_MOVE_UPLOADED_FILE_FAIL" => "Virhe kuvan siirrossa: move_uploaded_file() feilasi..."
	);
	?>
	<div id="virheilmoitus">
	<p><img src="kuvat/huutomerkki.png" alt="Huutomerkki" />
	Virhe: <?php
	if($virheilmoitukset[$virhe])
		echo $virheilmoitukset[$virhe];
	else
		echo "Tuntematon virheilmoitus ;)";
	?></p>
	</div>
	<?php
}
?>
