<?php

declare(strict_types=1);

namespace Forum\Tests;

use Forum\Validator;
use PHPUnit\Framework\TestCase;

final class ValidatorTest extends TestCase
{
    public function testGoodInputPasses(): void
    {
        $validator = new Validator([
            'username' => 'aditya_19',
            'email' => 'aditya@example.com',
            'password' => 'longenough1',
            'password_confirm' => 'longenough1',
        ]);

        $validator->username('username')
            ->email('email')
            ->password('password')
            ->sameAs('password_confirm', 'password', 'The two passwords do not match.');

        $this->assertTrue($validator->passes());
        $this->assertSame([], $validator->errors());
        $this->assertNull($validator->firstError());
    }

    public function testTextIsTrimmedAndKept(): void
    {
        $validator = new Validator(['title' => '  How do I index a table?  ']);
        $validator->text('title', 'Title', 5, 190);

        $this->assertTrue($validator->passes());
        $this->assertSame('How do I index a table?', $validator->value('title'));
    }

    public function testTextTooShortFails(): void
    {
        $validator = new Validator(['title' => 'hey']);
        $validator->text('title', 'Title', 5, 190);

        $this->assertFalse($validator->passes());
        $this->assertSame('Title needs at least 5 characters.', $validator->errors()['title']);
    }

    public function testMissingTextIsRequired(): void
    {
        $validator = new Validator([]);
        $validator->text('body', 'Message', 10, 5000);

        $this->assertSame('Message is required.', $validator->errors()['body']);
    }

    public function testArrayInputIsTreatedAsEmpty(): void
    {
        $validator = new Validator(['title' => ['not', 'a', 'string']]);
        $validator->text('title', 'Title', 5, 190);

        $this->assertFalse($validator->passes());
        $this->assertSame('', $validator->value('title'));
    }

    public function testFirstMessagePerFieldWins(): void
    {
        $validator = new Validator(['title' => '']);
        $validator->text('title', 'Title', 5, 190);
        $validator->fail('title', 'Something else about the title.');

        $this->assertSame('Title is required.', $validator->errors()['title']);
    }

    public function testBadEmailFails(): void
    {
        $validator = new Validator(['email' => 'aditya@@example']);
        $validator->email('email');

        $this->assertSame('Enter a valid email address.', $validator->errors()['email']);
    }

    public function testUsernameRejectsSpacesAndSymbols(): void
    {
        $validator = new Validator(['username' => 'adi tagwale!']);
        $validator->username('username');

        $this->assertFalse($validator->passes());
    }

    public function testUsernameRejectsTooShort(): void
    {
        $validator = new Validator(['username' => 'ab']);
        $validator->username('username');

        $this->assertFalse($validator->passes());
    }

    public function testPasswordKeepsOuterSpaces(): void
    {
        // a trim here would count this as 3 characters and refuse a valid password
        $validator = new Validator(['password' => '    abc    ']);
        $validator->password('password');

        $this->assertTrue($validator->passes());
    }

    public function testShortPasswordFails(): void
    {
        $validator = new Validator(['password' => 'abc']);
        $validator->password('password');

        $this->assertSame('Password needs at least 8 characters.', $validator->errors()['password']);
    }

    public function testSameAsFailsOnMismatch(): void
    {
        $validator = new Validator(['password' => 'one', 'password_confirm' => 'two']);
        $validator->sameAs('password_confirm', 'password', 'The two passwords do not match.');

        $this->assertSame('The two passwords do not match.', $validator->errors()['password_confirm']);
    }
}
