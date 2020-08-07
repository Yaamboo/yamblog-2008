# Yamblog (versio 2008-2009)

Tämä on itse php:lla tekemäni blogi, joka oli käytössäni vuosina 2008-2012 (jonka jälkeen migratoin blogin Wordpressille). Suurin osa kehitystyöstä on vuosilta 2008-2009 ja perustuu senaikaiseen osaamiseeni.

Tätä ei ole tarkoitus kehittää sen suuremmin, lähinnä haluan arkistoida tämän muuallekin kuin omiin arkistoihini.

## Taustaa

Olin Belgiassa opiskelijavaihdossa vuosina 2008-2009 ja vaihtoajasta piti kirjoittaa raportti tai pitää blogia. Valitsin jälkimmäisen ja tarvitsin sitä varten blogialustaa. Tuolloin Wordpress oli jo olemassa, muttei ollut itsestäänselvä vaihtoehto.

Olin ylläpitänyt aikanaan Suomipelit.com -verkkosivua ja itseopiskellut PHP-pohjaista web-kehittämistä sitä varten. Päätin harjoittaa osaamistani ja rakentelin sivuston pystyyn. Kehitystyylini oli iteratiivinen (vaikken sitä tuolloin oikein hahmottanutkaan), eli ensin syntyivät perus-blogisisällöt, jota seurasivat mm. kuvagalleriaominaisuudet, videosoittimet, "nyt soi" -musiikkimerkinnät ja rekisteröitymistoiminnot. Jossain välissä olen näköjään RSS-feedinkin jaksanut räpläillä!

## Blogialustan ominaisuudet

Mainittakoon ainakin nämä:

* Blogipostaukset, joiden yhteyteen sai ison aloituskuvan, "nyt soi" ja sijaintimerkinnät sekä kuvia galleriasta
* Blogimerkinnöissä bbcodeja tekstin sisällön muotoiluun sekä mm. videosoitin (en halunnut käyttää Youtubea videoiden jakamiseen), myöhemmin myös Youtube-embeddaus (koska logiikka ei ole vahvuuksiani)
* Kommentit blogipostauksiin
* Kuvagalleria (erillinen blogipostauksista, päiväkohtaiset kuvat ja kuvatekstit)
* Vapaamuotoisempaa sivusisältöä (ks. "elementit" -hakemisto)
* Viimeisemmät kuunnellut kappaleet AudioScrobblerista (nyk. Last.fm, kyseinen rss on poistunut historiaan)
* Yksityisyysominaisuuksia (rekisteröitymissalasana, tietyt asiat blogissa paljastuvat vain rekisteröityneille tai vip-salasanan antajille)

## Jälkipohdintaa

Olen yllättynyt, ettei kukaan käynyt missään vaiheessa "korkkaamassa" blogiani, sen verran tietoturva-aukkoja alustassa taitaa olla. (TODO: luettele aukot)

Jännästi olen tallentanut joitain vakioita vakiot.php -tiedostoon, mutta esim. tietokantayhteys on määritelty tietokanta.php:ssä. Salasanoja on suojattu sha1:llä ilman minkäänlaista vahvennusta suolalla tai muullakaan tavalla (purin vakiot.php:ssä ilmenevän salasanan [tällä työkalulla](https://md5decrypt.net/en/Sha1/) parissa sekunnissa).

Tuolloin en myöskään tuntenut PDO:ta vaan käsittelin mysql-tietokantaa itse wräppäämäni (luultavasti myös aukkoisen) tietokantatoteutuksen kautta. Tietokanta on näköjään sekoitus latin1:ä ja utf8:a, koska miksipä ei...

Sovellus pohjautuu näköjään hitusen olioihin, mutta ei kuitenkaan ole puhtaasti oliorakenteinen. Minkäänlaista templatointia ei myöskään tunneta. Rakenne on pitkälti perinteinen "yksi sivupyyntö, yksi sivu" verrattuna nykyisiin single-page-appeihin, yksi ajax-elementti kuitenkin löytyy (kalenteri).

En myöskään käyttänyt jQueryä muutamissa Javascript-animaatioissa vaan käytössäni oli prototype.js sekä scriptaculous-kirjasto. Historian havinaa!

Ja tosiaan tässähän ei mitään kontteja tai muutakaan erillisiä kehitysympäristöjä tunnettu - tuotannossa on aina ollut hyvä devata.

Ja kielenähän käytämme suomea - kuka nyt englanniksi koodaisi, hyi!

## Lisenssi ja vastuuvapaus

Julkaisen tämän blogialustan lähdekoodin Public Domainiin käyttäen The Unlicense -lisenssiä (ks. [LICENSE](LICENSE)).

**Blogialusta ei ole sellaisenaan tuotantokelpoinen ja luultavasti sisältää useita tietoturva-aukkoja; en siis ota vastuuta jos menet ajamaan tätä tosissasi ja huonosti käy!**

**This blogging platform is not production-safe and I do not recommend using it for anything but learning about my mistakes**
