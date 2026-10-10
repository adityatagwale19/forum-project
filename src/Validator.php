<?php

declare(strict_types=1);

namespace Forum;

/**
 * Checks one array of input, collects one message per field and keeps the
 * trimmed values so a form can be redrawn with what the user typed.
 */
class Validator
{
    /** @var array<string, string> */
    private array $errors = [];

    /** @var array<string, string> */
    private array $values = [];

    public function __construct(private readonly array $input)
    {
    }

    public function text(string $field, string $label, int $min, int $max): self
    {
        $value = $this->trimmed($field);
        $this->values[$field] = $value;

        if ($value === '') {
            return $this->fail($field, $label . ' is required.');
        }

        $length = mb_strlen($value);

        if ($length < $min) {
            return $this->fail($field, sprintf('%s needs at least %d characters.', $label, $min));
        }

        if ($length > $max) {
            return $this->fail($field, sprintf('%s cannot be longer than %d characters.', $label, $max));
        }

        return $this;
    }

    public function email(string $field, string $label = 'Email'): self
    {
        $value = $this->trimmed($field);
        $this->values[$field] = $value;

        if ($value === '') {
            return $this->fail($field, $label . ' is required.');
        }

        if (mb_strlen($value) > 190 || filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            return $this->fail($field, 'Enter a valid email address.');
        }

        return $this;
    }

    public function username(string $field, string $label = 'Username'): self
    {
        $value = $this->trimmed($field);
        $this->values[$field] = $value;

        if ($value === '') {
            return $this->fail($field, $label . ' is required.');
        }

        if (preg_match('/^[A-Za-z0-9_]{3,30}$/', $value) !== 1) {
            return $this->fail($field, 'Username can use 3 to 30 letters, numbers or underscores.');
        }

        return $this;
    }

    /**
     * Passwords are read without trimming, because a space is a valid character
     * and cutting it here would lock the user out on the next login.
     */
    public function password(string $field, int $min = 8): self
    {
        $value = $this->input[$field] ?? '';

        if (!is_string($value) || $value === '') {
            return $this->fail($field, 'Password is required.');
        }

        if (mb_strlen($value) < $min) {
            return $this->fail($field, sprintf('Password needs at least %d characters.', $min));
        }

        if (mb_strlen($value) > 200) {
            return $this->fail($field, 'Password cannot be longer than 200 characters.');
        }

        return $this;
    }

    public function sameAs(string $field, string $other, string $message): self
    {
        $a = $this->input[$field] ?? '';
        $b = $this->input[$other] ?? '';

        if (!is_string($a) || !is_string($b) || $a !== $b) {
            return $this->fail($field, $message);
        }

        return $this;
    }

    public function fail(string $field, string $message): self
    {
        // first message per field wins, so one mistake gives one line
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = $message;
        }

        return $this;
    }

    public function passes(): bool
    {
        return $this->errors === [];
    }

    /** @return array<string, string> */
    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        foreach ($this->errors as $message) {
            return $message;
        }

        return null;
    }

    public function value(string $field): string
    {
        return $this->values[$field] ?? '';
    }

    private function trimmed(string $field): string
    {
        $value = $this->input[$field] ?? '';

        return is_string($value) ? trim($value) : '';
    }
}
