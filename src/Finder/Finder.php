<?php

namespace Finder;

use SplFileInfo;

class Finder
{
	protected $includes = [];
	protected $excludes = [];

	/**
	 * @param string $regex
	 * @return $this
	 */
	public function includes($regex)
	{
		$this->includes = array_merge($this->includes, (array)$regex);

		return $this;
	}

	/**
	 * @param string $regex
	 * @return $this
	 */
	public function excludes($regex)
	{
		$this->excludes = array_merge($this->excludes, (array)$regex);

		return $this;
	}

	/**
	 * @param $path
	 * @return \SplFileInfo[]
	 */
	public function search($path)
	{
		$includes = $this->includes;
		$excludes = $this->excludes;

		$includesRegex = '`' . implode('|', $includes) . '`S';
		$excludesRegex = '`' . implode('|', $excludes) . '`S';
		// TODO: Cannot do this if | is used to escape spaces <tom@tomrochette.com>
		$path = preg_replace('`(?:\\\\|\/)+`', '/', $path);
		$path = rtrim($path, '/') . '/';

		$fileList = [];
		$directoryQueue = [$path];
		while ($currentDirectory = array_pop($directoryQueue)) {
			foreach ($this->getFilesInPath($currentDirectory) as $file) {
				$file = preg_replace('`(?:\\\\|\/)+`', '/', $file);
				if ($excludes && preg_match($excludesRegex, $file)) {
					continue;
				}

				if (substr($file, -1) === '/') {
					$directoryQueue[] = $file;
					continue; // TODO: Remove continue when we support returning directories <tom@tomrochette.com>
				}

				if ( ! $includes || preg_match($includesRegex, $file)) {
					$fileList[] = new SplFileInfo($file);
				}
			}
		}
		return $fileList;
	}

	/**
	 * Return a list of files for the given path.
	 *
	 * This list contains both files and directories. Directories end with /.
	 *
	 * @param string $path
	 * @return array
	 */
	private function getFilesInPath($path)
	{
		// TODO: Replace with generic "list files in directory", so we can
		// use filesystem libraries to reuse this code on aws s3 for instance <tom@tomrochette.com>
		return $this->getFilesInPathUsingGlob($path);
	}

	private function getFilesInPathUsingGlob($path)
	{
		$from = [ '[', '*', '?'];
		$to = ['[[]', '[*]', '[?]'];
		$path = str_replace($from, $to, $path);
		return glob($path . '*', GLOB_MARK);
	}
}
