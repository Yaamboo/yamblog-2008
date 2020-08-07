CREATE TABLE `galleriakommentit` (
  `id` mediumint(7) NOT NULL,
  `kansio` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `kuva` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `teksti` text COLLATE utf8_swedish_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

CREATE TABLE `kayttajat` (
  `id` mediumint(7) NOT NULL,
  `tunnus` varchar(100) COLLATE utf8_swedish_ci DEFAULT NULL,
  `salasana` varchar(50) COLLATE utf8_swedish_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `yllapitaja` tinyint(1) NOT NULL DEFAULT '0',
  `rek_ip` varchar(16) COLLATE utf8_swedish_ci DEFAULT NULL,
  `rek_aika` datetime DEFAULT NULL,
  `auto_tunnistautuminen` tinyint(1) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

CREATE TABLE `kayttajatiedot` (
  `uid` mediumint(7) NOT NULL,
  `etunimi` varchar(255) DEFAULT NULL,
  `sukunimi` varchar(255) DEFAULT NULL,
  `muoto` tinyint(10) NOT NULL DEFAULT '1',
  `sijainti` varchar(255) DEFAULT NULL,
  `tiedot` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `kirjautuneet` (
  `uid` mediumint(7) DEFAULT NULL,
  `vid` varchar(50) COLLATE utf8_swedish_ci DEFAULT NULL,
  `tunnistautunut` tinyint(1) DEFAULT '0',
  `alku` datetime DEFAULT NULL,
  `loppu` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

CREATE TABLE `kommentit` (
  `id` mediumint(7) NOT NULL,
  `uid` mediumint(7) NOT NULL,
  `mid` mediumint(7) NOT NULL,
  `ip` varchar(15) COLLATE utf8_swedish_ci DEFAULT NULL,
  `paivays` datetime DEFAULT NULL,
  `poistettu` tinyint(1) DEFAULT NULL,
  `teksti` text COLLATE utf8_swedish_ci,
  `tunnus` varchar(100) COLLATE utf8_swedish_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `www` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

CREATE TABLE `merkinta` (
  `id` mediumint(7) NOT NULL,
  `paivays` datetime NOT NULL,
  `muokattu` datetime DEFAULT NULL,
  `uid` mediumint(7) DEFAULT NULL,
  `otsikko` varchar(100) COLLATE utf8_swedish_ci DEFAULT NULL,
  `teksti` text COLLATE utf8_swedish_ci,
  `julkaistu` tinyint(1) DEFAULT NULL,
  `biisi` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `paikka` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `galleria` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Indexes for dumped tables
--

ALTER TABLE `galleriakommentit`
  ADD PRIMARY KEY (`id`);
  
ALTER TABLE `kayttajat`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tunnus` (`tunnus`);

ALTER TABLE `kayttajatiedot`
  ADD PRIMARY KEY (`uid`);

ALTER TABLE `kirjautuneet`
  ADD KEY `uid` (`uid`,`vid`);

ALTER TABLE `kommentit`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `merkinta`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

ALTER TABLE `galleriakommentit`
  MODIFY `id` mediumint(7) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `kayttajat`
  MODIFY `id` mediumint(7) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `kommentit`
  MODIFY `id` mediumint(7) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `merkinta`
  MODIFY `id` mediumint(7) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
