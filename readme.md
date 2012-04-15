Vytvořeno v { DeveloperHub

NetteTestCase
=============
* sada nastroju pro testovani aplikaci v Nette Frameworku
* doporucuji NetteTestCase pouzit jako submodul

Instalace NetteTestCase
-----------------------
* naklonovani externiho repozitare
	$ git submodule add git@github.com:Radim-Daniel-Panek/NetteTestCase.git libs/NetteTestCase
* v rootu projektu se vytvori sobor .gitmodules, ktery predstavuje konfiguracni soubor, v nemz je ulozeno mapovani
	mezi adresou URL projektu a lokalnim podadresarem NetteTestCase
* hostujici projekt je nyni povysen na "super projekt"
* pri klonovani je sice ziskan .gitmodules, ale ne jeho data, je potreba je stahnout
	* $ git submodule init = inicializace lokalniho konfiguracniho souboru
	* $ git submodule update = vyzvednuti vsech dat
* doplnit do config.ini sekci 'console'
* ve skeletonu v adresarich unit a selen jsou v souborech readme pripraveny ukazkove tridy testu


Nastaveni
=========
* obsah adresare skeleton/ prekopirujte do rootu Vasi aplikace
* ziskate 
	* phpunit.xml - konfigurace phpunit
	* tests/ 
		* case/ 
			* selen/ - adresar pro selen testy
			* unit/ - adresar pro integracni a jednotkove testy
		* coverage/ - adresar pro coverage report
		* libs/ - adresar pro tridy tretich stran, nebo jejich uzpusobeni, nebo Vase, ktere vyuzijete jen v testech
		* run.php - spoustec NetteTestCase a PHPUnit frameworku

Spousteni integracnich a jednotkovych testu
===========================================
* $ php tests/run.php
	* bez parametru se spusti vsechny testy
	* s parametrem --group unit pouze unit testy
* pokud pouzivate pre-commit, automaticky se spousteji testy pouze ve skupine unit

Spousteni Selenovych testu
==========================
* stahnete si selenium server http://selenium.googlecode.com/files/selenium-server-standalone-2.20.0.jar
* umistete jej do tests/libs/
* spuste jej java -jar selenium-server-standalone-2.20.0.jar
* nyni muzete spoustet i selenove testy
* php tests/run.php --group selen
* pouziva se WebDriver-PHP https://github.com/chibimagic/WebDriver-PHP/

Spousteni integracnich a unit testu pred commitem
=================================================
* pre-commit prekopirujte z /NetteTestCase/framework/Hooks/pre-commit/ do
  /VasProjekt/.git/hooks/pre-commit
* spustte git commit - spusti se testy a pokud vporadku probehnou, bude se pokracovat v commitovani
* spusteni testu muzete v pre-commit i ovlivnit
