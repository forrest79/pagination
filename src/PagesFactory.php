<?php declare(strict_types=1);

namespace Forrest79\Pagination;

/**
 * Algorithms copied from https://github.com/nikolassv/pagination
 */
class PagesFactory
{
	public const GAP = NULL;


	/**
	 * @return array<int>
	 */
	public static function all(int $lastPage, int $firstPage = 1): array
	{
		return range($firstPage, $lastPage);
	}


	/**
	 * @return array<int|NULL>
	 */
	public static function neighbour(int $lastPage, int $currentPage, int $steps, int $firstPage = 1, bool $addGaps = TRUE): array
	{
		$firstPage = min($currentPage, $firstPage);
		$lastPage = max($currentPage, $lastPage);

		// we have less pages in our list than the maximal number of steps,
		// therefore the pagination will show all the pages.
		if (($lastPage - $firstPage + 1) < $steps) {
			$pages = range($firstPage, $lastPage);
		} else {
			// first, last and current page belong to the pagination in any case
			$pages = array_unique([$firstPage, $lastPage, $currentPage]);
			$steps -= count($pages);

			// calculate how many pages are shown before an after the current page
			$before = (int) ($steps / 2);

			$after = (($steps % 2) === 1) ? $before + 1 : $before;

			// take into account that there may not be enough space before or after the current page to show
			// the same number of pages before and after the current page.
			$spaceBefore = max($currentPage - ($firstPage + 1), 0);
			$spaceAfter = max($lastPage - ($currentPage + 1), 0);

			if ($spaceBefore < $before) {
				$after += $before - $spaceBefore;
				$before = $spaceBefore;
			}

			if ($spaceAfter < $after) {
				$before += $after - $spaceAfter;
				$after = $spaceAfter;
			}

			// push the pages before and after the current page into the pagination elements
			for ($i = $currentPage - $before; $i < $currentPage; $i++) {
				array_push($pages, $i);
			}
			for ($i = $currentPage + 1; $i <= $currentPage + $after; $i++) {
				array_push($pages, $i);
			}
		}
		return self::preparePages($pages, $addGaps);
	}


	/**
	 * @return array<int|NULL>
	 */
	public static function logarithmic(int $lastPage, int $current, int $steps, int $firstPage = 1, bool $forceLinkNextPrev = TRUE, bool $addGaps = TRUE): array
	{
		if (min($firstPage, $lastPage) < 1) {
			throw new \InvalidArgumentException('logarithmic paginations must begin at page 1 or higher');
		}

		if ($steps <= 0) {
			throw new \InvalidArgumentException('number of steps must be bigger than zero');
		}

		$firstPage = min($current, $firstPage);
		$lastPage = max($current, $lastPage);
		$total = $lastPage - $firstPage + 1;
		$head = $current - $firstPage; // number of pages before the current page
		$tail = $lastPage - $current; // number of pages after the current page

		// we have less pages in our list than the maximal number of steps,
		// therefore the pagination will show all the pages.
		if ($total <= $steps) {
			$pages = range($firstPage, $lastPage);
		} else {
			// the first, the last, the current, next and prev page belong to the pagination in any case
			$pages = array_unique([$current, $firstPage, $lastPage]);
			$steps -= count($pages);

			if ($steps > 0) {
				if (($head > 0) && ($tail > 0)) {
					$logFirst = log($head);
					$logSecond = log($tail);
					$scaleStepsFirst = $steps * $logFirst / ($logFirst + $logSecond);
				} else {
					$scaleStepsFirst = ($head <= 0) ? 0 : $steps;
				}

				$scaleStepsFirst = (int) round($scaleStepsFirst);
				$scaleStepsSecond = $steps - $scaleStepsFirst;

				// if the calculatet number of steps does not fit before or after the current element,
				// we re-adjust the division
				if ($scaleStepsFirst > $head) {
					$scaleStepsFirst = $head;
					$scaleStepsSecond = $steps - $scaleStepsFirst;
				}
				if ($scaleStepsSecond > $tail) {
					$scaleStepsSecond = $tail;
					$scaleStepsFirst = $steps - $scaleStepsSecond;
				}

				// if we have at least one page before or after the current page we make sure that we also
				// have at least one link before resp. after the current page
				if ($forceLinkNextPrev && ($steps > 1)) {
					if (($head > 1) && ($scaleStepsFirst === 0)) {
						$scaleStepsFirst++;
						$scaleStepsSecond--;
					}
					if (($tail > 1) && ($scaleStepsSecond === 0)) {
						$scaleStepsFirst--;
						$scaleStepsSecond++;
					}
				}

				$elementsBefore = ($scaleStepsFirst > 0) ? self::getLogSteps($head, $scaleStepsFirst) : [];

				$elementsAfter = ($scaleStepsSecond > 0) ? self::getLogSteps($tail, $scaleStepsSecond) : [];

				foreach ($elementsBefore as $e) {
					array_push($pages, $current - $e);
				}
				foreach ($elementsAfter as $e) {
					array_push($pages, $current + $e);
				}
			}
		}
		return self::preparePages($pages, $addGaps);
	}

	/**
	 * divide a given integer number in a given number of steps on a log scale
	 *
	 * for a given number of steps $s and a given integer $n it will produce a set
	 * of numbers x^0, x^1, x^2, ... , x^($s - 1) and x^($s - 1) = $n.
	 *
	 * this method only returns integer values and always the demanded number of
	 * distinct values. if round(x^1) = round(x^2) or round(x^2) = round(x^3) it will decrease both
	 * the number of steps and the given integer by the same amount and recalculate a new
	 * set. the recalculation is recursive. it will keep track of the depth of recursion
	 * in the parameter $r.
	 *
	 * @param int $n the size of the largest number
	 * @param int $s the size of the set of number
	 * @return array<int> the resultset
	 */
	private static function getLogSteps(int $n, int $s, int $recursionLevel = 0): array
	{
		if (($n < 0) || ($s < 0)) {
			throw new \InvalidArgumentException('expected all arguments to be bigger than zero');
		}

		if ($s < 1) {
			return [];
		}

		// if the largest number is equal or less our number of steps we cannot return
		// a set of $s disctinct integers. instead we return $n distinct integers.
		if ($n <= $s) {
			return range($recursionLevel + 1, $n + $recursionLevel);
		}

		$stepSize = pow($n, (1 / $s));

		// to ensure that round(stepsize ^ i) is a different number for each i stepsize
		// must be bigger than 1.6
		if ($stepSize <= 1.6) {
			return array_merge([$recursionLevel + 1], self::getLogSteps($n - 1, $s - 1, $recursionLevel + 1));
		}

		$resultSet = [];
		for ($i = 0; $i < $s; $i++) {
			array_push($resultSet, (int) round(pow($stepSize, $i) + $recursionLevel));
		}

		return $resultSet;
	}


	/**
	 * @param array<int> $pages
	 * @return array<int|NULL>
	 */
	private static function preparePages(array $pages, bool $addGaps): array
	{
		$pages = array_unique($pages);
		sort($pages);

		if ($addGaps) {
			$pagesWithGap = [];
			foreach ($pages as $i => $page) {
				$pagesWithGap[] = $page;
				$nextPage = $page + 1;
				if (($pages[$i + 1] ?? $nextPage) !== $nextPage) {
					$pagesWithGap[] = self::GAP;
				}
			}
			$pages = $pagesWithGap;
		}

		return $pages;
	}

}
