<?php
/**
 * TestCase 
 *
 * @Date: 13-01-2012
 * @Package: NetteTestCase
 * @author RDPanek <rdpanek@gmail.com> { DeveloperHub
 */

namespace NetteTestCase;

require_once 'NetteTestCase.php';

class TestCase extends \PHPUnit_Framework_TestCase
{
	public $context = NULL;

	public function setUp()
	{
		$nt = new NetteTestCase;
		$this->context = $nt->getContext();
	}

}
