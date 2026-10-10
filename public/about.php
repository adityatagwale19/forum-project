<?php

declare(strict_types=1);

$app = require __DIR__ . '/../config/bootstrap.php';

$pageTitle = 'About';

require __DIR__ . '/../templates/layout/header.php';

?>
<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="mb-3">About <?= e($appName) ?></h2>

            <p>
                This is a small discussion forum. Threads are grouped into categories, anyone can read
                and search them, and a registered member can post a thread, comment on one, and edit or
                delete their own posts.
            </p>

            <p>
                I built it with plain PHP and MySQL, without a framework. There is a small autoloader,
                one class per job in <code>src/</code>, and the pages in <code>public/</code> are the only
                files a browser can reach. Queries go through PDO prepared statements and every value
                printed to the page is escaped first.
            </p>

            <h4 class="mt-4">What you can do here</h4>
            <ul>
                <li>Browse categories and read any thread without an account</li>
                <li>Search thread titles and bodies</li>
                <li>Sign up, then post threads and comments</li>
                <li>Edit or delete the threads and comments you own</li>
            </ul>

            <p class="mt-4">
                Found a bug or want to say something? <a href="contact.php">Send me a message</a>.
            </p>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../templates/layout/footer.php'; ?>
