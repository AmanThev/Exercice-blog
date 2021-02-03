<?php

use App\Manager\FilmDatabase;
use App\SQL\Paginate;
use App\URL\CreateUrl;
use App\Colors;

$title  = 'Dashboard/Reviews';

$films  = new FilmDatabase();
$films  = $films->getAllFilms();

$colors = [
	"Horror" => "#fe0800 ",
	"Thriller" => "#dc81fb",
    "Action" => "#86fa93",
    "Drama" => "#bfbfbf",
    "Comedy" => "#ffbc0a",
    "Biography" => "#cb9a80",
    "Sci-Fi" => "#41ffc8"];
?>

<h3 class="title-page">Reviews</h3>

<button class="create-button">Create New Review</button>

<table style="width:86%">
    <tr>
        <th style="width:40%">Title</th>
        <th style="width:15%">Author</th>
        <th style="width:15%">Genre</th>
        <th style="width:15%">Date</th>
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
                <a href=""><i class="fas fa-pen"></i> Edit</a>
                <a href=""><i class="fas fa-times"></i> Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>