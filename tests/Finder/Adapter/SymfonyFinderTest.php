<?php

namespace Finder\Test\Adapter;

use Finder\Adapter\SymfonyFinder;

class SymfonyFinderTest extends \PHPUnit_Framework_TestCase {
	protected function fixtures($path = '')
	{
		return __DIR__.'/../Fixtures/'.$path;
	}

	public function testCreate()
	{
		$finder = SymfonyFinder::create();
		$this->assertInstanceOf('\Finder\Adapter\SymfonyFinder', $finder);
		$this->assertInstanceOf('\Finder\FinderInterface', $finder);
	}

	public function testBasicFind()
	{
		$files = SymfonyFinder::create()
			->in($this->fixtures());
		$this->assertCount(14, $files);
	}

	public function testPath()
	{
		$files = SymfonyFinder::create()
			->in($this->fixtures())
			->path('A/B/C');
		$this->assertCount(2, $files);
	}

	public function testNotPath()
	{
		$files = SymfonyFinder::create()
			->in($this->fixtures())
			->notPath('A/B/C');
		$this->assertCount(12, $files);
	}

	public function testPathAndNotPath()
	{
		$files = SymfonyFinder::create()
			->in($this->fixtures())
			->path('A/B/C')
			->notPath('A/B/C');
		$this->assertCount(0, $files);
	}

	public function testName()
	{
		$files = SymfonyFinder::create()
			->in($this->fixtures())
			->name('.txt');
		$this->assertCount(4, $files);
	}

	public function testNotName()
	{
		$files = SymfonyFinder::create()
			->in($this->fixtures())
			->notName('.txt');
		$this->assertCount(10, $files);
	}

	public function testNameAndPath()
	{
		$files = SymfonyFinder::create()
			->in($this->fixtures())
			->path('with space')
			->name('.txt');
		$this->assertCount(1, $files);
	}

	public function testNotNameAndPath()
	{
		$files = SymfonyFinder::create()
			->in($this->fixtures())
			->path('one')
			->notName('.neon');
		$this->assertCount(1, $files);
	}
}
