<?php

declare(strict_types=1);

namespace Forum\Tests;

use Forum\Paginator;
use PHPUnit\Framework\TestCase;

final class PaginatorTest extends TestCase
{
    public function testCountsPagesAndOffset(): void
    {
        $pagination = new Paginator(45, 10, 3);

        $this->assertSame(5, $pagination->pages);
        $this->assertSame(3, $pagination->page);
        $this->assertSame(20, $pagination->offset());
        $this->assertTrue($pagination->hasPrevious());
        $this->assertTrue($pagination->hasNext());
    }

    public function testEmptyListStillHasOnePage(): void
    {
        $pagination = new Paginator(0, 10, 1);

        $this->assertSame(1, $pagination->pages);
        $this->assertSame(0, $pagination->offset());
        $this->assertFalse($pagination->hasPrevious());
        $this->assertFalse($pagination->hasNext());
    }

    public function testPagePastTheEndLandsOnTheLastPage(): void
    {
        $pagination = new Paginator(25, 10, 99);

        $this->assertSame(3, $pagination->page);
        $this->assertSame(20, $pagination->offset());
    }

    public function testJunkPageNumbersFallBackToOne(): void
    {
        foreach (['abc', '', null, [], '-4', 0] as $requested) {
            $pagination = new Paginator(25, 10, $requested);

            $this->assertSame(1, $pagination->page);
        }
    }

    public function testNumericStringIsAccepted(): void
    {
        $pagination = new Paginator(25, 10, '2');

        $this->assertSame(2, $pagination->page);
    }

    public function testPerPageIsNeverZero(): void
    {
        $pagination = new Paginator(10, 0, 1);

        $this->assertSame(1, $pagination->perPage);
        $this->assertSame(10, $pagination->pages);
    }

    public function testPreviousAndNextStayInRange(): void
    {
        $first = new Paginator(25, 10, 1);
        $last = new Paginator(25, 10, 3);

        $this->assertSame(1, $first->previous());
        $this->assertSame(2, $first->next());
        $this->assertSame(2, $last->previous());
        $this->assertSame(3, $last->next());
    }

    public function testWindowIsCentredAndClamped(): void
    {
        $this->assertSame([1, 2, 3, 4, 5], (new Paginator(100, 10, 1))->window());
        $this->assertSame([3, 4, 5, 6, 7], (new Paginator(100, 10, 5))->window());
        $this->assertSame([6, 7, 8, 9, 10], (new Paginator(100, 10, 10))->window());
    }

    public function testWindowNeverRunsPastTheLastPage(): void
    {
        $this->assertSame([1, 2, 3], (new Paginator(25, 10, 2))->window());
    }
}
