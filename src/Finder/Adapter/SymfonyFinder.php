<?php

namespace Finder\Adapter;

use ArrayIterator;
use Closure;
use Finder\Comparator\DateComparator;
use Finder\Comparator\NumberComparator;
use Finder\FilenameMatch;
use Finder\Finder;
use Finder\FinderInterface;
use LogicException;
use SplFileInfo;

class SymfonyFinder  implements FinderInterface
{
	const ONLY_FILES = 1;
	const ONLY_DIRECTORIES = 2;

	const IGNORE_VCS_FILES = 1;
	const IGNORE_DOT_FILES = 2;

	protected $finder;

	protected $dirs;

	protected $mode = 0;
	protected $ignore = 0;
	protected $depths = [];
	protected $dates = [];
	protected $sizes = [];
	protected $contains = [];
	protected $notContains = [];
	protected $ignoreUnreadableDirs = true;
	protected $followLinks = false;
	protected $filters = [];
	protected $sort;

	protected static $vcsPatterns;

	public function __construct()
	{
		$this->ignore = static::IGNORE_VCS_FILES | static::IGNORE_DOT_FILES;

		$this->finder = new Finder();
	}

	public static function create()
	{
		return new static();
	}

	// TODO: Support this <tom@tomrochette.com>
	public function directories()
	{
		$this->mode = static::ONLY_DIRECTORIES;

		return $this;
	}

	// TODO: Support this <tom@tomrochette.com>
	public function files()
	{
		$this->mode = static::ONLY_FILES;

		return $this;
	}

	// TODO: Support this <tom@tomrochette.com>
	public function depth($level)
	{
		$this->depths[] = new NumberComparator($level);

		return $this;
	}

	// TODO: Support this <tom@tomrochette.com>
	public function date($date)
	{
		$this->dates[] = new DateComparator($date);

		return $this;
	}

	// TODO: Accept globs/strings/regexes <tom@tomrochette.com>
	// TODO: Support arrays <tom@tomrochette.com>
	public function name($pattern)
	{
		$regex = FilenameMatch::translate($pattern);
		$this->finder->includes($regex);

		return $this;
	}

	public function notName($pattern)
	{
		$regex = FilenameMatch::translate($pattern);
		$this->finder->excludes($regex);

		return $this;
	}

	// TODO: Support this <tom@tomrochette.com>
	public function contains($pattern)
	{
		$this->contains[] = $pattern;

		return $this;
	}

	// TODO: Support this <tom@tomrochette.com>
	public function notContains($pattern)
	{
		$this->notContains[] = $pattern;

		return $this;
	}

	public function path($pattern)
	{
		$regex = FilenameMatch::translate($pattern);
		$this->finder->includes($regex);

		return $this;
	}

	public function notPath($pattern)
	{
		$regex = FilenameMatch::translate($pattern);
		$this->finder->excludes($regex);

		return $this;
	}

	// TODO: Support this <tom@tomrochette.com>
	public function size($size)
	{
		$this->sizes[] = new NumberComparator($size);

		return $this;
	}

	public function exclude($dir)
	{
		$regex = preg_replace('/(?:\\|\/)*$/', DIRECTORY_SEPARATOR, $dir);
		$this->finder->excludes($regex);

		return $this;
	}

	// TODO: Support this <tom@tomrochette.com>
	public function ignoreDotFiles($ignoreDotFiles)
	{
		if ($ignoreDotFiles) {
			$this->ignore = $this->ignore | static::IGNORE_DOT_FILES;
		} else {
			$this->ignore = $this->ignore & ~static::IGNORE_DOT_FILES;
		}

		return $this;
	}

	// TODO: Support this <tom@tomrochette.com>
	public function ignoreVCS($ignoreVCS)
	{
		if ($ignoreVCS) {
			$this->ignore = $this->ignore | static::IGNORE_VCS_FILES;
		} else {
			$this->ignore = $this->ignore & ~static::IGNORE_VCS_FILES;
		}

		return $this;
	}

	// TODO: Support this <tom@tomrochette.com>
	public static function addVCSPattern($patterns)
	{
		foreach ((array)$patterns as $pattern) {
			self::$vcsPatterns[] = $pattern;
		}

		self::$vcsPatterns = array_unique(self::$vcsPatterns);
	}

	// TODO: Support this <tom@tomrochette.com>
	public function sort(Closure $closure)
	{
		$this->sort = $closure;

		return $this;
	}

	// TODO: Support this <tom@tomrochette.com>
	public function sortByName()
	{
		return $this;
	}

	// TODO: Support this <tom@tomrochette.com>
	public function sortByType()
	{
		return $this;
	}

	// TODO: Support this <tom@tomrochette.com>
	public function sortByAccessedTime()
	{
		return $this;
	}

	// TODO: Support this <tom@tomrochette.com>
	public function sortByChangedTime()
	{
		return $this;
	}

	// TODO: Support this <tom@tomrochette.com>
	public function sortByModifiedTime()
	{
		return $this;
	}

	// TODO: Support this <tom@tomrochette.com>
	public function filter(Closure $closure)
	{
		$this->filters[] = $closure;

		return $this;
	}

	// TODO: Support this <tom@tomrochette.com>
	public function followLinks()
	{
		$this->followLinks = true;

		return $this;
	}

	// TODO: Support this <tom@tomrochette.com>
	public function ignoreUnreadableDirs($ignore = true)
	{
		$this->ignoreUnreadableDirs = (bool)$ignore;

		return $this;
	}

	public function in($dirs)
	{
		foreach ((array)$dirs as $dir) {
			$this->dirs[] = $dir;
		}

		return $this;
	}

	// TODO: Support this <tom@tomrochette.com>
	public function append($iterator)
	{
	}

	public function getIterator()
	{
		if (count($this->dirs) === 0) {
			throw new LogicException('You must call in() method before iterating over a Finder.');
		}

		if (count($this->dirs) === 1) {
			$files = $this->finder->search($this->dirs[0]);
			return new ArrayIterator($files);
		}

		$iterator = new \AppendIterator();
		foreach ($this->dirs as $dir) {
			$iterator->append(new ArrayIterator($this->finder->search($dir)));
		}

		return $iterator;
	}

	public function count()
	{
		return iterator_count($this->getIterator());
	}}
