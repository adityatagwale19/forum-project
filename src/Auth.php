<?php

declare(strict_types=1);

namespace Forum;

/**
 * Session login. The session holds the user id only, so a user who renames
 * themselves or gets deleted cannot keep stale data in the browser.
 */
class Auth
{
    private ?array $cached = null;

    public function __construct(private readonly UserRepository $users)
    {
    }

    public function check(): bool
    {
        return $this->id() !== null;
    }

    public function id(): ?int
    {
        $id = $_SESSION['user_id'] ?? null;

        return is_int($id) && $id > 0 ? $id : null;
    }

    public function user(): ?array
    {
        if ($this->cached !== null) {
            return $this->cached;
        }

        $id = $this->id();

        if ($id === null) {
            return null;
        }

        $user = $this->users->find($id);

        if ($user === null) {
            $this->logout();

            return null;
        }

        return $this->cached = $user;
    }

    public function attempt(string $email, string $password): bool
    {
        $user = $this->users->findForLogin($email);

        if ($user === null) {
            // hash anyway, so an unknown email takes about as long as a wrong
            // password and the response time does not say which it was
            password_hash($password, PASSWORD_DEFAULT);

            return false;
        }

        if (!password_verify($password, $user['password_hash'])) {
            return false;
        }

        $this->start((int) $user['id']);

        return true;
    }

    public function start(int $userId): void
    {
        // new session id on login, so a session id captured before login is useless
        session_regenerate_id(true);

        $_SESSION['user_id'] = $userId;
        Csrf::rotate();

        $this->cached = null;
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }

        session_destroy();

        $this->cached = null;
    }

    public function requireLogin(): void
    {
        if (!$this->check()) {
            Flash::error('Please log in to do that.');
            redirect('login.php');
        }
    }

    public function owns(int $userId): bool
    {
        return $this->id() === $userId;
    }
}
