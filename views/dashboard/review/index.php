<?php

use App\Manager\FilmDatabase;
use App\URL\CreateUrl;
use App\Security\Csrf;

$title  = 'Dashboard/Reviews';
$films  = (new FilmDatabase())->getAllFilms();

// Colour of each genre : a tag with a coloured outline and text (all readable on the dark background).
// A genre that is not in this list gets a colour computed from its name, so it always has one.
$genreColors = [
    'horror'      => '#ee6b5a',
    'thriller'    => '#b58cf0',
    'action'      => '#58d68d',
    'drama'       => '#6fa8f0',
    'comedy'      => '#f4a259',
    'biography'   => '#d8c79a',
    'sci-fi'      => '#5ad1e6',
    'crime'       => '#d96b9b',
    'adventure'   => '#4dd0c0',
    'animation'   => '#f4d35e',
    'fantasy'     => '#c39bd3',
    'romance'     => '#f5a3b8',
    'mystery'     => '#8c9eff',
    'western'     => '#c9a063',
    'documentary' => '#9fb3c8',
];
$genreColor = function(string $genre) use ($genreColors): string {
    $key = mb_strtolower(trim($genre));
    return $genreColors[$key] ?? 'hsl(' . (abs(crc32($key)) % 360) . ', 60%, 65%)';
};

// Message left by the edit or the deletion of a review
$flash = $_SESSION['flash_dash'] ?? null;
unset($_SESSION['flash_dash']);
?>

<div class="dash-pagehead">
    <h2 class="dash-title">Reviews</h2>
    <a class="dash-btn" href="<?= CreateUrl::url('dashboard/reviews/newReview'); ?>"><i class="fas fa-plus"></i> New review</a>
</div>

<?php if($flash): ?>
    <p class="dash-message valid"><?= htmlspecialchars($flash) ?></p>
<?php endif; ?>

<?php if(empty($films)): ?>
    <p class="dash-empty">No review yet.</p>
<?php else: ?>
    <table class="dash-table">
        <thead>
            <tr>
                <th><div><span>Title</span><span class="sort-table"><i class="fas fa-sort"></i></span></div></th>
                <th><div><span>Author</span><span class="sort-table"><i class="fas fa-sort"></i></span></div></th>
                <th><div><span>Genre</span><span class="sort-table"><i class="fas fa-sort"></i></span></div></th>
                <th><div><span>Year</span><span class="sort-table"><i class="fas fa-sort"></i></span></div></th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($films as $film): ?>
                <tr>
                    <td data-label="Title"><?= $film->getTitle() ?></td>
                    <td data-label="Author"><?= $film->getAuthor() ?></td>
                    <td data-label="Genre">
                        <?php if($film->getGenre() !== ''): ?>
                            <span class="genre-tag" style="--genre: <?= $genreColor($film->getGenre()) ?>"><?= $film->getGenre() ?></span>
                        <?php endif; ?>
                    </td>
                    <td data-label="Year"><?= $film->getDate() ?></td>
                    <td data-label="Action" class="dash-actions">
                        <a href="<?= CreateUrl::url('reviews', ['slug' => $film->getUrlTitle(), 'id' => $film->getId()]); ?>"><i class="fas fa-eye"></i>Preview</a>
                        <a href="<?= CreateUrl::urlDashboardAction('dashboard/reviews', $film->getId()); ?>"><i class="fas fa-pen"></i>Edit</a>
                        <form action="<?= CreateUrl::urlDashboardAction('dashboard/reviews', $film->getId(), 'delete'); ?>" method="post" onsubmit="return confirm('Are you sure you want to delete this review? Its comments will be deleted too.')">
                            <?= Csrf::field() ?>
                            <button type="submit" class="delete"><i class="fas fa-times-circle"></i>Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<script src="<?= PUBLIC_PATH ?>/js/sortTable.js"></script>