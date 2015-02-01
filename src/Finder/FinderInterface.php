<?php

namespace Finder;

use Closure;
use Countable;
use IteratorAggregate;

interface FinderInterface extends IteratorAggregate, Countable
{
	public static function create();

	public function directories();

	public function files();

	public function depth($level);

	public function date($date);

	public function name($pattern);

	public function notName($pattern);

	public function contains($pattern);

	public function notContains($pattern);

	public function path($pattern);

	public function notPath($pattern);

	public function size($size);

	public function exclude($dirs);

	public function ignoreDotFiles($ignoreDotFiles);

	public function ignoreVCS($ignoreVCS);

	public static function addVCSPattern($pattern);

	public function sort(Closure $closure);

	public function sortByName();

	public function sortByType();

	public function sortByAccessedTime();

	public function sortByChangedTime();

	public function sortByModifiedTime();

	public function filter(Closure $closure);

	public function followLinks();

	public function ignoreUnreadableDirs($ignore);

	public function in($dirs);

	public function append($iterator);
}
