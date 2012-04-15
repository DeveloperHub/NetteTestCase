<?php
/**
 * Form
 * - helper pro snadnejsi testovani formularu
 *
 * @Date: 09-02-2012
 * @Package: NetteTestCase
 * @author RDPanek <rdpanek@gmail.com> { DeveloperHub
 */

namespace NetteTestCase;

use Nette;

class Form extends \Nette\Object
{
	/**
	 * @var null|\Nette\Application\UI\Form
	 */
	public $form = NULL;

	/**
	 * Instance formulare
	 *
	 * <code>
	 * $this->context->form->setForm( $form );
	 * </code>
	 *
	 * @param \Nette\Application\UI\Form|null $form
	 * @author RDPanek
	 */
	public function setForm( \Nette\Application\UI\Form $form = NULL )
	{
		$this->form = $form;
		return $this;
	}


	/**
	 * Vrati true / false podle toho, zda-li bylo ve formulari nalezene
	 * pozadovane pravidlo
	 *
	 * <code>
	 * $res = $this->context->form->setForm( $form )
	 * ->existsRule( 'email', \Nette\Application\UI\Form::EMAIL );
	 * </code>
	 *
	 * Zakladni validacni pravidla
	 * ---------------------------
	 * Form::FILLED	je prvek vyplněn?
	 * Form::EQUAL	je hodnota rovna uvedené?
	 * Form::IS_IN	testuje, zda hodnota spadá do výčtu
	 * Form::VALID	je prvek vyplněn správně?
	 * Form::MIN_LENGTH	minimální délka
	 * Form::MAX_LENGTH	maximální délka
	 * Form::LENGTH	právě tato délka
	 * Form::EMAIL	je hodnota platná e-mailová adresa?
	 * Form::URL	je hodnota absolutní URL?
	 * Form::PATTERN	test oproti regulárnímu výrazu
	 * Form::INTEGER	je hodnota celočíselná?
	 * Form::FLOAT	je hodnota číslo?
	 * Form::RANGE	je hodnota v daném rozsahu?
	 * Form::MAX_FILE_SIZE	ověřuje maximální velikost souboru
	 * Form::MIME_TYPE	ověření MIME type
	 * Form::IMAGE	ověření, že jde o obrázek JPEG, PNG nebo GIF
	 *
	 * @param null|string $key nazev elementu
	 * @param null|string $rul nazev pravidla
	 * @param null|bool $arg vrati hodnotu aplikovane na pravidlo
	 * @return bool|array|int
	 * @author RDPanek
	 */
	public function existsRule( $key = NULL, $rul = NULL, $arg = NULL )
	{
		foreach ( $this->form[ $key ]->rules->getIterator() as $r )
		{
			// bylo nalezeno validacni pravidlo
			if( $r->operation === $rul )
			{
				if(( $arg === TRUE ))
				{
					return $r->arg;
				}
				return TRUE;
			}
		}

		return FALSE;
	}


	/**
	 * Metoda vraci true nebo false, podle toho, zda-li je ve formulari
	 * nalezena komponenta, odpovidajici $key
	 *
	 * your form
	 * ---------
	 * <code>
	 * $form->addText('age', 'Your age:')
	 * ...
	 * </code>
	 *
	 *
	 * test
	 * ----
	 * <code>
	 * $r = $this->form_case->existsComponent("age");
	 * $this->assertTrue( $r );
	 * </code>
	 *
	 * @param null|string $key nazev komponenty
	 * @return bool
	 * @author RDPanek
	 */
	public function existsComponent( $key = NULL )
	{
		try
		{
			$this->form[$key]->name;
			return TRUE;
		}
		catch (\Exception $e)
		{
			return FALSE;
		}
	}

}
