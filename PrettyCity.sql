--
-- База данных: `PrettyCity`
--

-- --------------------------------------------------------

--
-- Структура таблицы `Coords`
--

DROP TABLE IF EXISTS `Coords`;
CREATE TABLE IF NOT EXISTS `Coords` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Address` text NOT NULL,
  `Latitude` float NOT NULL,
  `Longitude` float NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31164 ;

-- --------------------------------------------------------

--
-- Структура таблицы `Data`
--

DROP TABLE IF EXISTS `Data`;
CREATE TABLE IF NOT EXISTS `Data` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `DatabaseID` int(11) NOT NULL,
  `Latitude` double DEFAULT NULL,
  `Longitude` double DEFAULT NULL,
  `String` text NOT NULL,
  `Last_update` date NOT NULL,
  `isNew` tinyint(1) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `DatabaseID` (`DatabaseID`,`Latitude`,`Longitude`,`Last_update`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5094 ;

-- --------------------------------------------------------

--
-- Структура таблицы `Files`
--

DROP TABLE IF EXISTS `Files`;
CREATE TABLE IF NOT EXISTS `Files` (
  `ID` int(11) NOT NULL,
  `Name` text NOT NULL,
  `Url` text NOT NULL,
  `Filename` text NOT NULL,
  `Last_update` date NOT NULL,
  `Min_range` float NOT NULL,
  `Max_range` float NOT NULL,
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `Points`
--

DROP TABLE IF EXISTS `Points`;
CREATE TABLE IF NOT EXISTS `Points` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Num_Latitude` int(11) NOT NULL,
  `Num_Longitude` int(11) NOT NULL,
  `Latitude` double NOT NULL,
  `Longitude` double NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4698 ;

-- --------------------------------------------------------

--
-- Структура таблицы `Results`
--

DROP TABLE IF EXISTS `Results`;
CREATE TABLE IF NOT EXISTS `Results` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ID_Point` int(11) NOT NULL,
  `ID_Dataset` int(11) NOT NULL,
  `ID_Data` int(11) NOT NULL,
  `Result` double NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
