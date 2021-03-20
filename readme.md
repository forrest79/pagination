# Pagination

[![License](https://img.shields.io/badge/License-BSD%203--Clause-blue.svg)](https://github.com/forrest79/Pagination/blob/master/license.md)
[![Build](https://github.com/forrest79/Pagination/actions/workflows/build.yml/badge.svg?branch=master)](https://github.com/forrest79/Pagination/actions/workflows/build.yml)

Create pages list for pagination with logarithmic scale, neighbour pages or all pages.

Algorithms are copied from https://github.com/nikolassv/pagination.


## Installation

The recommended way to install Forrest79/Pagination is through Composer:

```sh
composer require forrest79/pagination
```


## How to use it

Just call `PagesFactory::` with pages list you want:

```php
$pages = Forrest79\Pagination\PagesFactory::all(100);
$pages = Forrest79\Pagination\PagesFactory::neighbour(100, 1, 5);
$pages = Forrest79\Pagination\PagesFactory::logarithmic(100, 10, 10);
```

You will get sorted `array` with `integer` pages numbers. For neighbour and logarithmic scale, there are also `NULL` values at place, where is broken pages series, for example: `[1, 2, 3, NULL, 7, 8]`. So you know where print space. You can disable this behavior by settings parameter `$addGaps` to `FALSE`. 

### Example with Nette

Simple using with default paginator:

```php
class Paginator extends Nette\Utils\Paginator
{

	public function pages(): array
	{
		if ($this->getPageCount() === NULL) {
			throw new InvalidArgumentException('We need page count set to generate pages list');
		}
		return Forrest79\Pagination\PagesFactory::logarithmic($this->getPageCount(), $this->getPage(), 10);
	}

}
```

And in `latte`:

```php
<li n:foreach="$paginator->pages() as $page" n:class="$page === $paginator->getPage() ? active, $page === NULL ? disabled">
	{if $page === NULL}
		..
	{else}
		<a n:href="this, page => $page">{$page}</a>
	{/if}
</li>
```
