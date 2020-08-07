<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output method="xml"/>

<xsl:template match="/rss/channel">
<html>
<head>
<title><xsl:value-of select="title" /> RSS</title>
<style type="text/css">
@import url("style.css");
</style>
</head>
<body>
<div id="alkuTausta">
	<div id="alku">
		<h1><xsl:value-of select="title" /> RSS</h1>
	</div>
</div>
<div id="valikkoTausta">
	<div id="valikko">
	<a href="index.php">Palaa blogin etusivulle</a>
	</div>
</div>
<div id="sisältöTausta">
	<div id="sisältö">

	<xsl:for-each select="item">
		<div>
		<h2><a href="{guid}" rel="bookmark"><xsl:value-of select="title"/></a></h2>
		<p class="pikkutieto"><xsl:value-of select="pubDate"/></p>
		<p><xsl:value-of select="description" disable-output-escaping="yes"/></p>
		</div>
	</xsl:for-each>

	</div></div>
<div id="loppuTausta">
	<div id="loppu">
		<p>Tämä on RSS-syöte, jota voit seurata selaimessasi tai RSS-lukijassasi.</p>
	</div>
</div>
</body>
</html>
</xsl:template>
</xsl:stylesheet>