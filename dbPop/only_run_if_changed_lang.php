<?php
//this file outputs a file full of languages that are copied and pasted into an insert statement
//only run this script if you change the language list; you then need to manually insert the result into the database
$bigString = "Abkhazian
Amharic
Afar
Afrikaans
Akan
Albanian
Arabic
Aragonese
Armenian
Assamese
Avaric
Avestan
Aymara
Azerbaijani
Bambara
Bashkir
Basque
Belarusian
Bengali
Bihari languages
Bislama
Bokmål, Norwegian
Bosnian
Breton
Bulgarian
Burmese
Catalan
Central Khmer
Chamorro
Chechen
Chichewa
Chinese
Church Slavic
Chuvash
Cornish
Corsican
Cree
Croatian
Czech
Danish
Dutch
Dzongkha
English
Esperanto
Estonian
Ewe
Faroese
Fijian
Finnish
French
Fulah
Gaelic
Galician
Ganda
Georgian
German
Greek, Modern
Guarani
Gujarati
Haitian Creole
Hausa
Hebrew
Herero
Hindi
Hiri Motu
Hungarian
Icelandic
Ido
Igbo
Indonesian
Interlingua
Interlingue
Inuktitut
Inupiaq
Irish
Italian
Japanese
Javanese
Kalaallisut
Kannada
Kanuri
Kashmiri
Kazakh
Kikuyu
Kinyarwanda
Kirghiz
Komi
Kongo
Korean
Kuanyama
Kurdish
Lao
Luba-Katanga
Latin
Latvian
Limburgan
Lingala
Lithuanian
Luxembourgish
Macedonian
Malagasy
Malay
Malayalam
Maldivian
Maltese
Manx
Maori
Marathi
Marshallese
Mongolian
Nauru
Navajo
Ndebele, North
Ndebele, South
Ndonga
Nepali
Northern Sami
Norwegian
Nynorsk, Norwegian
Occitan
Ojibwa
Oriya
Oromo
Ossetian
Pali
Panjabi
Persian
Polish
Portuguese
Pushto
Quechua
Romanian
Romansh
Rundi
Russian
Samoan
Sango
Sanskrit
Sardinian
Serbian
Shona
Sichuan Yi
Sindhi
Sinhala
Slovak
Slovenian
Somali
Sotho, Southern
Spanish
Sundanese
Swahili
Swati
Swedish
Tagalog
Tahitian
Tajik
Tamil
Tatar
Telugu
Thai
Tibetan
Tigrinya
Tonga
Tsonga
Tswana
Turkish
Turkmen
Twi
Uighur
Ukrainian
Urdu
Uzbek
Venda
Vietnamese
Volapük
Walloon
Welsh
Western Frisian
Wolof
Xhosa
Yiddish
Yoruba
Zhuang
Zulu";

$text_lines = explode("\n",$bigString);
unset($bigString);

$languages = [];

foreach($text_lines as $line){
	$entry = "('" . $line . "'), ";
	array_push($languages, $entry);
}

$fp = fopen('languages', 'w');
fwrite(  $fp, implode($languages)  );
fclose($fp);

?>
