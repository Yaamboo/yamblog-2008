function naytaKirjautumisruutu(naytettava)
{
	if(naytettava == "kirjaudu")
	{
		if($("kirjautuminen_form").style.display == "none")
		{
			if($("kirjautuminen_tunnistaudu").style.display != "none")
				$("kirjautuminen_tunnistaudu").blindUp({duration: 0.2});
			$("kirjautuminen_form").blindDown({duration: 0.2});
		}
		else
			$("kirjautuminen_form").blindUp({duration: 0.2});
	}
	else if(naytettava == "tunnistaudu")
	{
		if($("kirjautuminen_tunnistaudu").style.display == "none")
		{
			if($("kirjautuminen_form").style.display!="none")
				$("kirjautuminen_form").blindUp({duration: 0.2});
			$("kirjautuminen_tunnistaudu").blindDown({duration: 0.2});
		}
		else
			$("kirjautuminen_tunnistaudu").blindUp({duration: 0.2});
	}
}

function naytaBlogimerkintaMuokkain(id)
{
	if($("blogimerkinta_"+id+"_muokkain").style.display == "none")
	{
		$("blogimerkinta_"+id).blindUp({duration: 0.2});
		$("blogimerkinta_"+id+"_muokkain").blindDown({duration: 0.2});
	}
	else
	{
		$("blogimerkinta_"+id+"_muokkain").blindUp({duration: 0.2});
		$("blogimerkinta_"+id).blindDown({duration: 0.2});
	}
}

function naytaBlogimerkintaPoisto(id)
{
	if($("blogimerkinta_"+id+"_poisto").style.display == "none")
	{
		$("blogimerkinta_"+id).fade({to: 0.3});
		$("blogimerkinta_"+id+"_poisto").blindDown({duration: 0.2});
	}
	else
	{
		$("blogimerkinta_"+id+"_poisto").blindUp({duration: 0.2});
		$("blogimerkinta_"+id).appear({from: 0.3, to: 1.0});
	}
}

function naytaBlogikommenttiMuokkain(id)
{
	if($("blogikommentti_"+id+"_muokkain").style.display == "none")
	{
		$("blogikommentti_"+id).blindUp({duration: 0.2});
		$("blogikommentti_"+id+"_muokkain").blindDown({duration: 0.2});
	}
	else
	{
		$("blogikommentti_"+id+"_muokkain").blindUp({duration: 0.2});
		$("blogikommentti_"+id).blindDown({duration: 0.2});
	}
}

function naytaKuvakommenttiMuokkain()
{
	if($("kuvakommenttimuokkain").style.display == "none")
	{
		$("kuvakommenttimuokkain").blindDown({duration: 0.2});
	}
	else
	{
		$("kuvakommenttimuokkain").blindUp({duration: 0.2});
	}
}

function naytaApulaatikko(id)
{
	$("apulaatikko_"+id).style.left=event.clientX+15;
	$("apulaatikko_"+id).style.top=event.clientY+10;
	$("apulaatikko_"+id).appear({duration: 0.2});
}

function piilotaApulaatikko(id)
{
	$("apulaatikko_"+id).fade({duration: 0.5});
//	$("apulaatikko_"+id).style.display="none";
}

function naytaKalenterilaatikko(id, paiva)
{
	// Haetaan tarvittavat matskut
	new Ajax.Updater(id, "elementit/ajax/a_kalenteri.php?paiva="+paiva);
	$(id).style.left=event.clientX+15;
	$(id).style.top=event.clientY+10;
	$(id).style.display='block';
}

function piilotaKalenterilaatikko(id)
{
	$(id).style.display='none';
	new Ajax.Updater(id, "elementit/ajax/a_kalenteri.php");
}