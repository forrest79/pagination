<?php declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Forrest79\Pagination;

/**
 * @param list<int|null> $expected
 * @param list<int|null> $actual
 */
function compareArray(array $expected, array $actual, string $errorMessage): void
{
	if ($expected !== $actual) {
		echo $errorMessage . PHP_EOL;
		echo 'Expected: ' . str_replace(PHP_EOL, '', var_export($expected, true));
		echo PHP_EOL;
		echo 'Actual:   ' . str_replace(PHP_EOL, '', var_export($actual, true));
		echo PHP_EOL;
		exit(1);
	}
}

compareArray([1, 2, 3, 4, 5, 6, 7, 8, 9, 10], Pagination\PagesFactory::all(10), 'All pages test failed.');
compareArray([2, 3, 4, 5, 6, 7, 8, 9, 10], Pagination\PagesFactory::all(10, 2), 'All pages test with 2 as first page failed.');

compareArray([1, null, 10], Pagination\PagesFactory::neighbour(10, 1, 1), 'Neighbour pages with gap test failed.');
compareArray([1, 10], Pagination\PagesFactory::neighbour(10, 1, 1, 1, false), 'Neighbour pages without gap test failed.');
compareArray([1, 2, null, 10], Pagination\PagesFactory::neighbour(10, 1, 3), 'Neighbour pages with step 3 and gap test failed.');
compareArray([1, 2, 10], Pagination\PagesFactory::neighbour(10, 1, 3, 1, false), 'Neighbour pages with step 3 and without gap test failed.');
compareArray([1, 2, 3, 4, null, 10], Pagination\PagesFactory::neighbour(10, 1, 5), 'Neighbour pages with step 5 and gap test failed.');
compareArray([1, 2, 3, 4, 10], Pagination\PagesFactory::neighbour(10, 1, 5, 1, false), 'Neighbour pages with step 5 and without gap test failed.');
compareArray([1, null, 4, 5, 6, null, 10], Pagination\PagesFactory::neighbour(10, 5, 5), 'Neighbour pages with current page 5, step 5 and gap test failed.');
compareArray([1, 4, 5, 6, 10], Pagination\PagesFactory::neighbour(10, 5, 5, 1, false), 'Neighbour pages with current page 5, step 5 and without gap test failed.');
compareArray([1, null, 7, 8, 9, 10], Pagination\PagesFactory::neighbour(10, 10, 5), 'Neighbour pages with current page 10, step 5 and gap test failed.');
compareArray([1, 7, 8, 9, 10], Pagination\PagesFactory::neighbour(10, 10, 5, 1, false), 'Neighbour pages with current page 10, step 5 and without gap test failed.');
compareArray([2, null, 4, 5, 6, null, 10], Pagination\PagesFactory::neighbour(10, 5, 5, 2), 'Neighbour pages with current page 5, step 5, first page 2 and gap test failed.');
compareArray([2, 4, 5, 6, 10], Pagination\PagesFactory::neighbour(10, 5, 5, 2, false), 'Neighbour pages with current page 5, step 5, first page 2 and without gap test failed.');

compareArray([1, null, 100], Pagination\PagesFactory::logarithmic(100, 1, 1), 'Logarithmic pages with gap test failed.');
compareArray([1, 100], Pagination\PagesFactory::logarithmic(100, 1, 1, 1, true, false), 'Logarithmic pages without gap test failed.');
compareArray([1, 2, null, 100], Pagination\PagesFactory::logarithmic(100, 1, 3), 'Logarithmic pages with step 3 and gap test failed.');
compareArray([1, 2, 100], Pagination\PagesFactory::logarithmic(100, 1, 3, 1, true, false), 'Logarithmic pages with step 3 and without gap test failed.');
compareArray([1, 2, null, 6, null, 22, null, 100], Pagination\PagesFactory::logarithmic(100, 1, 5), 'Logarithmic pages with step 5 and gap test failed.');
compareArray([1, 2, 6, 22, 100], Pagination\PagesFactory::logarithmic(100, 1, 5, 1, true, false), 'Logarithmic pages with step 5 and without gap test failed.');
compareArray([1, null, 3, 4, 5, 6, 7, null, 11, null, 20, null, 43, null, 100], Pagination\PagesFactory::logarithmic(100, 5, 10), 'Logarithmic pages with current page 5, step 10 and gap test failed.');
compareArray([1, 3, 4, 5, 6, 7, 11, 20, 43, 100], Pagination\PagesFactory::logarithmic(100, 5, 10, 1, true, false), 'Logarithmic pages with current page 5, step 10 and without gap test failed.');
compareArray([1, null, 7, null, 9, 10, 11, 12, null, 16, null, 25, null, 47, null, 100], Pagination\PagesFactory::logarithmic(100, 10, 10), 'Logarithmic pages with current page 10, step 10 and gap test failed.');
compareArray([1, 7, 9, 10, 11, 12, 16, 25, 47, 100], Pagination\PagesFactory::logarithmic(100, 10, 10, 1, true, false), 'Logarithmic pages with current page 10, step 10 and without gap test failed.');
compareArray([2, null, 4, 5, 6, 7, null, 10, null, 15, null, 26, null, 49, null, 100], Pagination\PagesFactory::logarithmic(100, 5, 10, 2), 'Logarithmic pages with current page 5, step 10, first page 2 and gap test failed.');
compareArray([2, 4, 5, 6, 7, 10, 15, 26, 49, 100], Pagination\PagesFactory::logarithmic(100, 5, 10, 2, true, false), 'Logarithmic pages with current page 5, step 10, first page 2 and without gap test failed.');

echo 'All tests passed' . PHP_EOL;
