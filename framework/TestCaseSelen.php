<?php
/**
 * TestCaseSelen
 *
 * @Date: 22-01-2012
 * @Package: NetteTestCase
 * @author RDPanek <rdpanek@gmail.com> { DeveloperHub
 */

namespace NetteTestCase;

class TestCaseSelen extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var null|\SystemContainer
	 */
	public $context = NULL;

	/**
	 * @var obj \WebDriver_Driver
	 */
	private $driver = NULL;

	public function setUp()
	{
		$nt = new NetteTestCase();
		$this->context = $nt->getContext();
		$this->driver = \WebDriver_Driver::InitAtLocal("4444", "firefox");
	}

	/*
	 * Forward calls to main driver
	 */
	public function __call($name, $arguments) {
		if (method_exists($this->driver, $name)) {
			return call_user_func_array(array($this->driver, $name), $arguments);
		} else {
			throw new \Exception("Tried to call nonexistent method $name with arguments:\n" . print_r($arguments, true));
		}
	}

	public function tearDown() {
		if ($this->driver) {
			if ($this->hasFailed()) {
				$this->driver->set_sauce_context("passed", false);
			} else {
				$this->driver->set_sauce_context("passed", true);
			}
			$this->driver->quit();
		}
		parent::tearDown();
	}
}
