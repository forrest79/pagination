<?php declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Forrest79\Pagination;

/**
 * @param list<int|NULL> $expected
 * @param list<int|NULL> $actual
 */
function compareArray(array $expected, array $actual, string $errorMessage): void
{
	if ($expected !== $actual) {
		echo $errorMessage . PHP_EOL;
		echo 'Expected: ' . str_replace(PHP_EOL, '', var_export($expected, TRUE));
		echo PHP_EOL;
		echo 'Actual:   ' . str_replace(PHP_EOL, '', var_export($actual, TRUE));
		echo PHP_EOL;
		exit(1);
	}
}

compareArray([1, 2, 3, 4, 5, 6, 7, 8, 9, 10], Pagination\PagesFactory::all(10), 'All pages test failed.');
compareArray([2, 3, 4, 5, 6, 7, 8, 9, 10], Pagination\PagesFactory::all(10, 2), 'All pages test with 2 as first page failed.');

compareArray([1, NULL, 10], Pagination\PagesFactory::neighbour(10, 1, 1), 'Neighbour pages with gap test failed.');
compareArray([1, 10], Pagination\PagesFactory::neighbour(10, 1, 1, 1, FALSE), 'Neighbour pages without gap test failed.');
compareArray([1, 2, NULL, 10], Pagination\PagesFactory::neighbour(10, 1, 3), 'Neighbour pages with step 3 and gap test failed.');
compareArray([1, 2, 10], Pagination\PagesFactory::neighbour(10, 1, 3, 1, FALSE), 'Neighbour pages with step 3 and without gap test failed.');
compareArray([1, 2, 3, 4, NULL, 10], Pagination\PagesFactory::neighbour(10, 1, 5), 'Neighbour pages with step 5 and gap test failed.');
compareArray([1, 2, 3, 4, 10], Pagination\PagesFactory::neighbour(10, 1, 5, 1, FALSE), 'Neighbour pages with step 5 and without gap test failed.');
compareArray([1, NULL, 4, 5, 6, NULL, 10], Pagination\PagesFactory::neighbour(10, 5, 5), 'Neighbour pages with current page 5, step 5 and gap test failed.');
compareArray([1, 4, 5, 6, 10], Pagination\PagesFactory::neighbour(10, 5, 5, 1, FALSE), 'Neighbour pages with current page 5, step 5 and without gap test failed.');
compareArray([1, NULL, 7, 8, 9, 10], Pagination\PagesFactory::neighbour(10, 10, 5), 'Neighbour pages with current page 10, step 5 and gap test failed.');
compareArray([1, 7, 8, 9, 10], Pagination\PagesFactory::neighbour(10, 10, 5, 1, FALSE), 'Neighbour pages with current page 10, step 5 and without gap test failed.');
compareArray([2, NULL, 4, 5, 6, NULL, 10], Pagination\PagesFactory::neighbour(10, 5, 5, 2), 'Neighbour pages with current page 5, step 5, first page 2 and gap test failed.');
compareArray([2, 4, 5, 6, 10], Pagination\PagesFactory::neighbour(10, 5, 5, 2, FALSE), 'Neighbour pages with current page 5, step 5, first page 2 and without gap test failed.');

compareArray([1, NULL, 100], Pagination\PagesFactory::logarithmic(100, 1, 1), 'Logarithmic pages with gap test failed.');
compareArray([1, 100], Pagination\PagesFactory::logarithmic(100, 1, 1, 1, TRUE, FALSE), 'Logarithmic pages without gap test failed.');
compareArray([1, 2, NULL, 100], Pagination\PagesFactory::logarithmic(100, 1, 3), 'Logarithmic pages with step 3 and gap test failed.');
compareArray([1, 2, 100], Pagination\PagesFactory::logarithmic(100, 1, 3, 1, TRUE, FALSE), 'Logarithmic pages with step 3 and without gap test failed.');
compareArray([1, 2, NULL, 6, NULL, 22, NULL, 100], Pagination\PagesFactory::logarithmic(100, 1, 5), 'Logarithmic pages with step 5 and gap test failed.');
compareArray([1, 2, 6, 22, 100], Pagination\PagesFactory::logarithmic(100, 1, 5, 1, TRUE, FALSE), 'Logarithmic pages with step 5 and without gap test failed.');
compareArray([1, NULL, 3, 4, 5, 6, 7, NULL, 11, NULL, 20, NULL, 43, NULL, 100], Pagination\PagesFactory::logarithmic(100, 5, 10), 'Logarithmic pages with current page 5, step 10 and gap test failed.');
compareArray([1, 3, 4, 5, 6, 7, 11, 20, 43, 100], Pagination\PagesFactory::logarithmic(100, 5, 10, 1, TRUE, FALSE), 'Logarithmic pages with current page 5, step 10 and without gap test failed.');
compareArray([1, NULL, 7, NULL, 9, 10, 11, 12, NULL, 16, NULL, 25, NULL, 47, NULL, 100], Pagination\PagesFactory::logarithmic(100, 10, 10), 'Logarithmic pages with current page 10, step 10 and gap test failed.');
compareArray([1, 7, 9, 10, 11, 12, 16, 25, 47, 100], Pagination\PagesFactory::logarithmic(100, 10, 10, 1, TRUE, FALSE), 'Logarithmic pages with current page 10, step 10 and without gap test failed.');
compareArray([2, NULL, 4, 5, 6, 7, NULL, 10, NULL, 15, NULL, 26, NULL, 49, NULL, 100], Pagination\PagesFactory::logarithmic(100, 5, 10, 2), 'Logarithmic pages with current page 5, step 10, first page 2 and gap test failed.');
compareArray([2, 4, 5, 6, 7, 10, 15, 26, 49, 100], Pagination\PagesFactory::logarithmic(100, 5, 10, 2, TRUE, FALSE), 'Logarithmic pages with current page 5, step 10, first page 2 and without gap test failed.');

echo 'All tests passed' . PHP_EOL;
