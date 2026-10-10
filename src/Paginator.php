<?php

declare(strict_types=1);

namespace Forum;

/**
 * Works out which slice of rows a page should load. No database access, so the
 * page number arithmetic can be tested on its own.
 */
class Paginator
{
    public readonly int $total;
    public readonly int $perPage;
    public readonly int $pages;
    public readonly int $page;

    public function __construct(int $total, int $perPage, mixed $requestedPage)
    {
        $this->total = max(0, $total);
        $this->perPage = max(1, $perPage);
        $this->pages = max(1, (int) ceil($this->total / $this->perPage));

        // anything that is not a number counts as page one, and a page past
        // the end lands on the last page instead of showing nothing
        $asked = is_numeric($requestedPage) ? (int) $requestedPage : 1;
        $this->page = min($this->pages, max(1, $asked));
    }

    public function offset(): int
    {
        return ($this->page - 1) * $this->perPage;
    }

    public function hasPrevious(): bool
    {
        return $this->page > 1;
    }

    public function hasNext(): bool
    {
        return $this->page < $this->pages;
    }

    public function previous(): int
    {
        return max(1, $this->page - 1);
    }

    public function next(): int
    {
        return min($this->pages, $this->page + 1);
    }

    /**
     * The page numbers to draw as links, centred on the current page.
     *
     * @return list<int>
     */
    public function window(int $size = 5): array
    {
        $size = max(1, $size);
        $half = intdiv($size, 2);

        $start = max(1, $this->page - $half);
        $end = min($this->pages, $start + $size - 1);
        $start = max(1, $end - $size + 1);

        return range($start, $end);
    }
}
