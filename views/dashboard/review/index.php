<?php

use App\Manager\FilmDatabase;
use App\SQL\Paginate;
use App\URL\CreateUrl;
use App\Colors;

$title  = 'Dashboard/Reviews';

$films  = new FilmDatabase();
$films  = $films->getAllFilms();

$colors = [
	"Horror"    => "#82ccdd",
	"Thriller"  => "#f0d1fa",
    "Action"    => "#55efc4",
    "Drama"     => "#c8d6e5",
    "Comedy"    => "#fab1a0",
    "Biography" => "#f7f1e3",
    "Sci-Fi"    => "#ffeaa7"];
?>

<h3 class="title-page">Reviews</h3>

<a class="create-button" href="<?= CreateUrl::url('dashboard/reviews/newReview'); ?>">Create New Review</a>

<table style="width:86%">
    <tr>
        <th style="width:40%">
            <div>
                <span>Title</span>
                <span class="sort-table"><i class="fas fa-caret-up"></i><i class="fas fa-caret-down"></i></span>
            </div>
        </th>
        <th style="width:15%">
            <div>
                <span>Author</span>
                <span class="sort-table"><i class="fas fa-caret-up"></i><i class="fas fa-caret-down"></i></span>
            </div>
        </th>
        <th style="width:15%">
            <div>
                <span>Genre</span>
                <span class="sort-table"><i class="fas fa-caret-up"></i><i class="fas fa-caret-down"></i></span>
            </div>
        </th>
        <th style="width:15%">
            <div>
                <span>Date</span>
                <span class="sort-table"><i class="fas fa-caret-up"></i><i class="fas fa-caret-down"></i></span>
            </div>
        </th>
        <th style="width:15%">Action</th>
    </tr>
    <?php foreach($films as $film): ?>
        <tr>
            <td><?= $film->getTitle() ?></td>
            <td><?= $film->getAuthor() ?></td>
            <td style="background:<?= Colors::getColor($film->getGenre(), $colors) ?>"><?= $film->getGenre() ?></td>
            <td><?= $film->getDate() ?></td>
            <td>
                <a href="<?= CreateUrl::url('reviews', ['slug' => $film->getUrlTitle(), 'id' => $film->getId()]); ?>"><i class="fas fa-eye"></i> Preview</a>
                <a href="<?= CreateUrl::urlDashboardAction('dashboard/reviews', $film->getId()); ?>"><i class="fas fa-pen"></i> Edit</a>
                <form action="<?= CreateUrl::urlDashboardAction('dashboard/films', $film->getId(), 'delete'); ?>" method="post" onsubmit="return confirm('Are you sure you want to delete this review?')">
                    <button><i class="fas fa-times-circle"></i> Delete</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>


