<?php

declare(strict_types=1);

namespace Forum\Tests;

use Forum\Csrf;
use PHPUnit\Framework\TestCase;

final class CsrfTest extends TestCase
{
    protected function setUp(): void
    {
        // the class reads $_SESSION directly, so the array alone is enough here
        $_SESSION = [];
        $_POST = [];
    }

    public function testTokenIsStableUntilRotated(): void
    {
        $first = Csrf::token();

        $this->assertSame($first, Csrf::token());
        $this->assertSame(64, strlen($first));

        $this->assertNotSame($first, Csrf::rotate());
    }

    public function testCheckAcceptsTheStoredToken(): void
    {
        $token = Csrf::token();

        $this->assertTrue(Csrf::check($token));
    }

    public function testCheckRejectsWrongEmptyAndNonStringValues(): void
    {
        Csrf::token();

        $this->assertFalse(Csrf::check('not the token'));
        $this->assertFalse(Csrf::check(''));
        $this->assertFalse(Csrf::check(null));
        $this->assertFalse(Csrf::check(['an', 'array']));
    }

    public function testCheckFailsWhenNothingIsStored(): void
    {
        $this->assertFalse(Csrf::check('anything'));
    }

    public function testCheckRequestReadsThePostedField(): void
    {
        $token = Csrf::token();
        $_POST[Csrf::FIELD] = $token;

        $this->assertTrue(Csrf::checkRequest());

        $_POST[Csrf::FIELD] = 'stale';

        $this->assertFalse(Csrf::checkRequest());
    }

    public function testFieldRendersAHiddenInputWithTheToken(): void
    {
        $token = Csrf::token();

        $this->assertSame(
            '<input type="hidden" name="' . Csrf::FIELD . '" value="' . $token . '">',
            Csrf::field()
        );
    }
}
