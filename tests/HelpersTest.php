<?php

declare(strict_types=1);

namespace Forum\Tests;

use PHPUnit\Framework\TestCase;

final class HelpersTest extends TestCase
{
    public function testTagsAreEscaped(): void
    {
        $this->assertSame(
            '&lt;script&gt;alert(1)&lt;/script&gt;',
            e('<script>alert(1)</script>')
        );
    }

    public function testBothQuoteStylesAreEscaped(): void
    {
        // single quotes matter too, because attributes in the templates use them
        $this->assertSame('&quot;a&quot; and &#039;b&#039;', e('"a" and \'b\''));
    }

    public function testAmpersandIsEscapedOnce(): void
    {
        $this->assertSame('a &amp; b', e('a & b'));
    }

    public function testNumbersAndNullBecomeStrings(): void
    {
        $this->assertSame('12', e(12));
        $this->assertSame('', e(null));
    }

    public function testInvalidUtf8IsSubstitutedInsteadOfReturningEmpty(): void
    {
        $this->assertNotSame('', e("bad \xB1\x31 bytes"));
    }
}
