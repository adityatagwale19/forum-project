<?php

declare(strict_types=1);

use Forum\Paginator;

/** @var Paginator $pagination */
/** @var callable $pageLink takes a page number and returns the url for it */

if ($pagination->pages < 2) {
    return;
}

?>
<nav aria-label="Thread pages">
    <ul class="pagination justify-content-center">
        <li class="page-item<?= $pagination->hasPrevious() ? '' : ' disabled' ?>">
            <a class="page-link" href="<?= e($pageLink($pagination->previous())) ?>">Previous</a>
        </li>

        <?php foreach ($pagination->window() as $page) : ?>
            <li class="page-item<?= $page === $pagination->page ? ' active' : '' ?>">
                <a class="page-link" href="<?= e($pageLink($page)) ?>"><?= $page ?></a>
            </li>
        <?php endforeach; ?>

        <li class="page-item<?= $pagination->hasNext() ? '' : ' disabled' ?>">
            <a class="page-link" href="<?= e($pageLink($pagination->next())) ?>">Next</a>
        </li>
    </ul>
</nav>
