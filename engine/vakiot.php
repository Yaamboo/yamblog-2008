<?php
if(!defined('YAMBLOG_RUNNING'))
	die("Virhe (mahdollinen hyökkäysyritys)");

define('YAMBLOG_OFFLINE', true);	// Onko blogi käytössä (TODO)
define('YAMBLOG_BASEDIR', "http://www.yaamboo.com/"); // Perushakemisto (/ netissä, täällä /yamblog/)
define('YAMBLOG_FULL_BASEDIR', "/home/yaamboo/public_html/"); // Koko perushakemisto (/home/yaamboo/public_html/ netissä, täällä /var/www/yamblog/)
// SHA1-suojattua...
// huomautus vuodelta 2020: salasana on marsipaani ; sha1 piilottaa sen näkyvistä, ei anna käytännön suojaa
define('YAMBLOG_VIP_SALASANA', "8ffd4479e1807eab1b379840f6d119a718b457de");	// VIP-salasana
define('YAMBLOG_REG_SALASANA', "8ffd4479e1807eab1b379840f6d119a718b457de");	// Rekisteröintisalasana, jos määritelty, rekisteröinti salasanasuojattu

define('YAMBLOG_SAA_KOMMENTOIDA', false); // Onko kommentointi sallittu

?>
