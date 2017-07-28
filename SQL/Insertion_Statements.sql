
INSERT INTO tbl_Countries(Name) VALUES ('Canada'), ('United States'), ('England'), ('France'), ('Germany');

INSERT INTO tbl_Commitments(Commitment) VALUES ('Casual'), ('Regular'), ('Hardcore');

INSERT INTO tbl_Archetypes(Archetype) VALUES ('Organization'), ('Corporation'), ('PMC'), ('Faith'), ('Syndicate');

INSERT INTO tbl_Fluencies(Language) VALUES ('Abkhazian'), ('Amharic'), ('Afar'), ('Afrikaans'), ('Akan'), ('Albanian'), ('Arabic'), ('Aragonese'), ('Armenian'), ('Assamese'), ('Avaric'), ('Avestan'), ('Aymara'), ('Azerbaijani'), ('Bambara'), ('Bashkir'), ('Basque'), ('Belarusian'), ('Bengali'), ('Bihari languages'), ('Bislama'), ('Bokmål, Norwegian'), ('Bosnian'), ('Breton'), ('Bulgarian'), ('Burmese'), ('Catalan'), ('Central Khmer'), ('Chamorro'), ('Chechen'), ('Chichewa'), ('Chinese'), ('Church Slavic'), ('Chuvash'), ('Cornish'), ('Corsican'), ('Cree'), ('Croatian'), ('Czech'), ('Danish'), ('Dutch'), ('Dzongkha'), ('English'), ('Esperanto'), ('Estonian'), ('Ewe'), ('Faroese'), ('Fijian'), ('Finnish'), ('French'), ('Fulah'), ('Gaelic'), ('Galician'), ('Ganda'), ('Georgian'), ('German'), ('Greek, Modern'), ('Guarani'), ('Gujarati'), ('Haitian Creole'), ('Hausa'), ('Hebrew'), ('Herero'), ('Hindi'), ('Hiri Motu'), ('Hungarian'), ('Icelandic'), ('Ido'), ('Igbo'), ('Indonesian'), ('Interlingua'), ('Interlingue'), ('Inuktitut'), ('Inupiaq'), ('Irish'), ('Italian'), ('Japanese'), ('Javanese'), ('Kalaallisut'), ('Kannada'), ('Kanuri'), ('Kashmiri'), ('Kazakh'), ('Kikuyu'), ('Kinyarwanda'), ('Kirghiz'), ('Komi'), ('Kongo'), ('Korean'), ('Kuanyama'), ('Kurdish'), ('Lao'), ('Luba-Katanga'), ('Latin'), ('Latvian'), ('Limburgan'), ('Lingala'), ('Lithuanian'), ('Luxembourgish'), ('Macedonian'), ('Malagasy'), ('Malay'), ('Malayalam'), ('Maldivian'), ('Maltese'), ('Manx'), ('Maori'), ('Marathi'), ('Marshallese'), ('Mongolian'), ('Nauru'), ('Navajo'), ('Ndebele, North'), ('Ndebele, South'), ('Ndonga'), ('Nepali'), ('Northern Sami'), ('Norwegian'), ('Nynorsk, Norwegian'), ('Occitan'), ('Ojibwa'), ('Oriya'), ('Oromo'), ('Ossetian'), ('Pali'), ('Panjabi'), ('Persian'), ('Polish'), ('Portuguese'), ('Pushto'), ('Quechua'), ('Romanian'), ('Romansh'), ('Rundi'), ('Russian'), ('Samoan'), ('Sango'), ('Sanskrit'), ('Sardinian'), ('Serbian'), ('Shona'), ('Sichuan Yi'), ('Sindhi'), ('Sinhala'), ('Slovak'), ('Slovenian'), ('Somali'), ('Sotho, Southern'), ('Spanish'), ('Sundanese'), ('Swahili'), ('Swati'), ('Swedish'), ('Tagalog'), ('Tahitian'), ('Tajik'), ('Tamil'), ('Tatar'), ('Telugu'), ('Thai'), ('Tibetan'), ('Tigrinya'), ('Tonga'), ('Tsonga'), ('Tswana'), ('Turkish'), ('Turkmen'), ('Twi'), ('Uighur'), ('Ukrainian'), ('Urdu'), ('Uzbek'), ('Venda'), ('Vietnamese'), ('Volapük'), ('Walloon'), ('Welsh'), ('Western Frisian'), ('Wolof'), ('Xhosa'), ('Yiddish'), ('Yoruba'), ('Zhuang'), ('Zulu');

-- for faster loading, the app no longer queries the DB for this list, and the DB no longer saves default image locations
-- *** IF THESE CHANGE, update them in LoadViewService.js AND the corresponding default image location ***
INSERT INTO tbl_Activities(Activity) VALUES 
('Bounty Hunting'), 
('Engineering'), 
('Exploration'), 
('Freelancing'), 
('Infiltration'), 
('Piracy'), 
('Resources'), 
('Scouting'), 
('Security'), 
('Smuggling'), 
('Social'), 
('Trading'), 
('Transport');

-- degragment to cluster on indexes
ALTER TABLE tbl_Countries ENGINE=INNODB;
ALTER TABLE tbl_Commitments ENGINE=INNODB;
ALTER TABLE tbl_Archetypes ENGINE=INNODB;
-- ALTER TABLE tbl_ExclusiveOrgs ENGINE=INNODB;
ALTER TABLE tbl_Fluencies ENGINE=INNODB;
ALTER TABLE tbl_Activities ENGINE=INNODB;

