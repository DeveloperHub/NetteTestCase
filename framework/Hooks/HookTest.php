<?php

namespace NetteTestCase\Framework;


/**
 * Trida zajistuje spousteni testu, vraci jejich vysledek a logickou
 * hodnotu 1 nebo 0 pro zastaveni nebo pokracovani prikazu v Gitu
 * 
 * @author RDPanek { DeveloperHub
 */
final class HookTest
{

	/**
	 * Uchovava stav pribehu kodu a nastavuje status na 0 nebo 1
	 * @author RDPanek
	 */
	private $status = NULL;

	/**
	 * Nazev projektu
	 * @author RDPanek
	 * basename( getcwd() )
	 */
	private $projectName = "";

	/**
	 * Cesta k binarce php
	 * @author RDPanek
	 */
	private $phpPath = "php";

	/**
	 * Nastaveni PHPunit pro prikazovou radku
	 * @author RDPanek
	 */ 
	private $options = NULL;


	/**
	 * Spustit PHPUnit a vrati status s pripadnym vypisem chyb
	 * 
	 * @author RDPanek
	 */
	public function run()
	{
		$this->setLine();
		if( !$this->isWindows() ) echo "\033[32;7m";
		echo "----- Spoustim testy -----" . $this->setLine();
		if( !$this->isWindows() ) echo "\033[0m";

		$this->runPHPUnit();

		$this->returnStatus();
		
	}

	/**
	 * Umoznuje nastavit cestu k php.
	 * Defaultne se predpoklada pritomnost php v promennym prostredi
	 * 
	 * @author RDPanek
	 */
	public function setPath( $php_path = "php" )
	{
		$this->phpPath = $php_path;
	}


	/**
	 * Umoznuje definovat nastaveni behu PHPUnit
	 * 
	 * @author RDPanek
	 */
	public function setOptions( $options = NULL )
	{
		$this->options = " " . $options;
	}


	/**
	 * Spusti PHPUnit
	 * 
	 * @author RDPanek
	 */
	private function runPHPUnit()
	{
		// spusti PHPUnit
		exec($this->phpPath . ' tests/run.php' . $this->options, $output, $returnCode);

		$this->isAnyTestFail( $returnCode );
		$this->catchAnyErrors( $output );
	}


	/**
	 * Podle kodu, ktery vratil PHPUnit vypise, jest-li nastala 
	 * chyba a nastavi status na 1
	 * 
	 * @author RDPanek
	 */
	private function isAnyTestFail( $returnCode = NULL )
	{
		// pokud v testech nevznikla zadna chyba, bude navratova hodnota 0
		if( $returnCode !== 0 )
		{
			$this->setLine();
			if( !$this->isWindows() ) echo "\033[101;37m";
			echo  ">> Nektery z testu neprosel" . $this->setLine();
			if( !$this->isWindows() ) echo "\033[0m";

			// navratova hodnota 1 zpusobi zastaveni provadene operace v Gitu
		    $this->status = 1;
		}
	}


	/**
	 * Zachyti pripadne chyby a vypise je
	 * 
	 * @author RDPanek
	 */
	private function catchAnyErrors( $output = NULL )
	{
		$i = 0;
		// doslo k nejake zmene statusu nejakeho issues na Githubu?
		foreach( $output as $val )
		{

			/************ Zachytavani udalosti pro Github *************/
			$pattern_github = '/^Updating GitHub.+/';
			preg_match($pattern_github, $val, $matches_github, PREG_OFFSET_CAPTURE);
			
			if( count($matches_github) )
			{
				echo '>> ' . $matches_github[0][0] . $this->setLine();
			}



			/*********** Zachytavani chyb ****************************/
			$pattern_github = '/.+\).+/';
			preg_match($pattern_github, $val, $matches_error, PREG_OFFSET_CAPTURE);

			if( count($matches_error) )
			{
				$this->setLine();
				echo $matches_error[0][0] . $this->setLine();
				echo ' - ' . $output[++$i] . $this->setLine();
				echo " -------------------------------------" . $this->setLine();
				
				$i--;

			}

			$i++;

		}
	}


	/**
	 * Vrati status pro Git
	 * 
	 * @author RDPanek
	 */
	private function returnStatus()
	{
		if( $this->status === NULL )
		{
			echo '>> Vsechny testy pro ' . $this->projectName . ' prosly v poradku. ' 
					. $this->setLine();
			
			echo '----- Provadena operace v Gitu nyni pokracuje. -----' 
					. $this->setLine();
			
			$this->setLine(2);
			exit(0);
		}

		$this->setLine();
		if( !$this->isWindows() ) echo "\033[101;37m";
		echo "----- Provadena operace v Gitu byla unkoncena. -----"; 
		if( !$this->isWindows() ) echo "\033[0m";
		$this->setLine(2);

		exit(1);
	}

	/**
	 * Vraci logickou hodnotu, podle toho, zda-li tento script 
	 * bezi na windows
	 * 
	 * @author RDPanek
	 * @return bool TRUE|FALSE
	 */
	private function isWindows()
	{
		return PHP_OS == "WINNT"?TRUE:FALSE;
	}

	/**
	 * Nastavi novy radek
	 * 
	 * @author RDPanek
	 */
	private function setLine( $count = 1 )
	{
		for ( $i = 0;$i<$count;$i++ )
		{
			echo PHP_EOL;
		}
	}

}
