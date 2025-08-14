<?php

use App\Manager\FilmDatabase;
use App\Manager\Connection;
use App\SQL\Paginate;
use App\URL\CreateUrl;
use App\DateReviews;
use App\HTML\Form;

$title              = 'Reviews';

$pagination         = new FilmDatabase();

$lastReviews        = new FilmDatabase();
$lastReviews        = $lastReviews->getLastFilms(5);

$reviews            = new FilmDatabase();
$reviews            = $reviews->getFilms();
$decadesSelected    = empty($_GET['decades']) ? null : (int)$_GET['decades'];

$searchForm = new Form($_POST);

if($decadesSelected){
    // htmlspecial sur $_GET['decade']
    //vérifier que c'est bien un int
    //si le nombre de page est trop grand, l'erreur ne s'affiche pas
    $reviews = DateReviews::getFilmByDecade($decadesSelected);
}

$yearSelected = empty($_GET['year']) ? null : (int)$_GET['year'];
if($yearSelected){
    $reviews = DateReviews::getFilmByYear($yearSelected);
}
?>

<h1 id="title-reviews">Reviews</h1>

<section class="section-last-reviews">
    <h1>Last reviews</h1>
    <div class="last-reviews">
        <?php foreach($lastReviews as $lastReview): ?>
        <figure>
            <div class="deck-last-reviews">
                <div class="img-last-reviews">
                    <img src="<?= PUBLIC_PATH ?>/img/posterFilm/<?= $lastReview->getPoster() ?>" alt="">
                </div>
                <div class="content-last-reviews">
                    <h2><?= $lastReview->getTitle() ?></h2>
                    <p><?= $lastReview->getSynopsis() ?></p>
                    <span><a href="<?= CreateUrl::url('reviews', ['slug' => $lastReview->getUrlTitle(), 'id' => $lastReview->getId()]); ?>">Read More</a></span>
                </div>
            </div>
        </figure>
        <?php endforeach; ?>
    </div>
</section>

<div class="bottom-reviews">
    <aside class="date-reviews">
        <h2>Choose a decade or a year :</h2>
        <a id="display-all-movies" class="<?= empty($decadesSelected) == TRUE ? 'disabled' : '' ?>" href="<?= CreateUrl::url('reviews') ?>">All movies</a>
        <?php foreach (DateReviews::listDecades() as $num => $decade): ?>
            <a class="list-item decades <?= $num == $decadesSelected ? 'active' : ''; ?>" href="?decades=<?= $num ?>"><?= $decade ?></a>
                <?php if($num == $decadesSelected): ?>
                    <?php foreach (DateReviews::getListYears($decadesSelected) as $listYear): ?>
                        <a class="list-item years <?= $listYear == $yearSelected ? 'active' : ''; ?><?= DateReviews::filmsExists($listYear) === 0 ? 'disabled' : ''; ?>" href="?decades=<?= $num ?>&year=<?= $listYear ?>">
                        <?= $listYear ?></a>
                    <?php endforeach; ?>
                <?php endif; ?>
        <?php endforeach; ?>
    </aside>
    <section class="all-reviews">
        <h1>All reviews</h1>
        <form class="search-box" method="post" action="<?= CreateUrl::url('search') ?>">
            <?= $searchForm->searchBox('search', 'film'); ?>
        </form>
        <div class="deck-all-reviews">
            <?php foreach($reviews as $review): ?>
                <div class="box-all-reviews">
                    <div class="content-all-reviews">
                        <h2 class="<?php 
                            if(strlen($review->getTitle()) <= 30){ 
                                echo 'short-title';
                            } ?>"><?= $review->getTitle() ?></h2>
                        <span>Directed by <?= $review->getDirector() ?>, <?= $review->getDate() ?></span>
                        <p class="text-film"><?= $review->getExcerptSynopsis(); ?></p>
                        <a href="<?= CreateUrl::url('reviews', ['slug' => $review->getUrlTitle(), 'id' => $review->getId()]); ?>">Read More</a>
                    </div>
                    <div class="img-all-reviews">
                        <img src="<?= PUBLIC_PATH ?>/img/posterFilm/<?= $review->getPoster() ?>" alt="<?= $review->getTitle() ?>">
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="pagination-all-post">
            <ul>
                <?php if(isset($decadesSelected) && $yearSelected == null): ?>
                    <?php DateReviews::filmPaginationNumberDecade($decadesSelected); ?>
                <?php elseif(isset($decadesSelected) && isset($yearSelected)): ?>
                    <?php DateReviews::filmPaginationNumberYear($decadesSelected, $yearSelected); ?>
                <?php else: ?>
                    <?= $pagination->filmPaginationNumber(); ?>
                <?php endif; ?>
            </ul>
        </div>
    </section>
</div>