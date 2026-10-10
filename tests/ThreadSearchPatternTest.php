<?php

declare(strict_types=1);

namespace Forum\Tests;

use Forum\ThreadRepository;
use PHPUnit\Framework\TestCase;

final class ThreadSearchPatternTest extends TestCase
{
    public function testPlainWordsAreWrappedForAContainsSearch(): void
    {
        $this->assertSame('%prepared statements%', ThreadRepository::likePattern('prepared statements'));
    }

    public function testTheTermIsTrimmed(): void
    {
        $this->assertSame('%mysql%', ThreadRepository::likePattern('  mysql  '));
    }

    public function testPercentIsEscapedSoItSearchesForTheCharacter(): void
    {
        $this->assertSame('%100\\%%', ThreadRepository::likePattern('100%'));
    }

    public function testUnderscoreIsEscaped(): void
    {
        $this->assertSame('%user\\_id%', ThreadRepository::likePattern('user_id'));
    }

    public function testBackslashIsEscapedBeforeTheOthers(): void
    {
        // a single backslash must come out doubled, not as an escape for the
        // character that follows it
        $this->assertSame('%a\\\\b%', ThreadRepository::likePattern('a\\b'));
    }

    public function testAnEscapeSequenceInTheTermStaysLiteral(): void
    {
        $this->assertSame('%\\\\\\%%', ThreadRepository::likePattern('\\%'));
    }

    public function testEmptyTermGivesTheMatchEverythingPattern(): void
    {
        $this->assertSame('%%', ThreadRepository::likePattern('   '));
    }
}
