<?php

namespace Finder\Test;

use Finder\Finder;
use PHPUnit\Framework\TestCase;

class FinderTest extends TestCase
{
	protected function fixtures($path = '')
	{
		return __DIR__.'/Fixtures/'.$path;
	}

	public function testBasicFind()
	{
		$files = (new Finder)->search($this->fixtures());
		$this->assertCount(14, $files);
	}

	public function testFindWithIncludes()
	{
		$files = (new Finder)->includes('A\/B\/C')->search($this->fixtures());
		$this->assertCount(2, $files);
	}

	public function testFindWithExcludes()
	{
		$files = (new Finder)->excludes('A\/B\/C')->search($this->fixtures());
		$this->assertCount(12, $files);
	}

	public function testFindWithIncludesAndExcludes()
	{
		$files = (new Finder)->includes('A\/B\/C')->excludes('A\/B\/C')->search($this->fixtures());
		$this->assertCount(0, $files);

		$files = (new Finder)->excludes('A\/B\/C')->includes('A\/B\/C')->search($this->fixtures());
		$this->assertCount(0, $files);
	}
}
