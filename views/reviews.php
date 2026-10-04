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
    $reviews = DateReviews::getFilmByDecade($decadesSelected);
}

$yearSelected = empty($_GET['year']) ? null : (int)$_GET['year'];
if($yearSelected){
    $reviews = DateReviews::getFilmByYear($yearSelected);
}
?>

<section>

<div class="section-heading"><h2>Last Reviews</h2><span class="rule"></span></div>
<div class="reviews-slider">
    <?php foreach($lastReviews as $lastReview): ?>
        <figure class="slider-slide">
            <div class="slider-img">
                <img src="<?= PUBLIC_PATH ?>/img/posterFilm/<?= $lastReview->getPoster() ?>" alt="">
            </div>
            <div class="slider-content">
                <h4><?= $lastReview->getTitle() ?></h4>
                <p><?= $lastReview->getSynopsis() ?></p>
                <a href="<?= CreateUrl::url('reviews', ['slug' => $lastReview->getUrlTitle(), 'id' => $lastReview->getId()]); ?>" class="card-link">Read more</a>
            </div>
        </figure>
    <?php endforeach; ?>
</div>

<div class="reviews-layout">
    <aside class="date-reviews">
        <div class="box">
            <h3>Choose a decade or a year</h3>
            <a id="display-all-movies" class="date-link <?= empty($decadesSelected) ? 'active' : '' ?>" href="<?= CreateUrl::url('reviews') ?>">All movies</a>
            <?php foreach (DateReviews::listDecades() as $num => $decade): ?>
                <a class="date-link decades <?= $num == $decadesSelected ? 'active' : ''; ?>" href="?decades=<?= $num ?>"><?= $decade ?></a>
                <?php if($num == $decadesSelected): ?>
                    <div class="date-sublist">
                        <?php foreach (DateReviews::getListYears($decadesSelected) as $listYear): ?>
                            <a class="date-link years <?= $listYear == $yearSelected ? 'active' : ''; ?> <?= DateReviews::filmsExists($listYear) === 0 ? 'disabled' : ''; ?>" href="?decades=<?= $num ?>&year=<?= $listYear ?>">
                            <?= $listYear ?></a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </aside>

    <div class="reviews-col" id="all-reviews">
        <div class="section-heading"><h2>All Reviews</h2><span class="rule"></span></div>

        <div class="blog-search">
            <form class="search-box" method="post" action="<?= CreateUrl::url('search') ?>">
                <?= $searchForm->searchBox('search', 'film'); ?>
            </form>
        </div>

        <div class="review-flip-grid">
            <?php foreach($reviews as $review): ?>
                <div class="flip-card">
                    <div class="flip-card-inner">
                        <div class="flip-card-front">
                            <img src="<?= PUBLIC_PATH ?>/img/posterFilm/<?= $review->getPoster() ?>" alt="<?= $review->getTitle() ?>">
                        </div>
                        <div class="flip-card-back">
                            <img src="<?= PUBLIC_PATH ?>/img/posterFilm/<?= $review->getPoster() ?>" class="flip-card-back-bg" alt="">
                            <div class="flip-card-back-content">
                                <h3><?= $review->getTitle() ?></h3>
                                <p class="flip-card-meta">Directed by <?= $review->getDirector() ?> · <?= $review->getDate() ?></p>
                                <p class="flip-card-text"><?= $review->getExcerptSynopsis(); ?></p>
                                <a href="<?= CreateUrl::url('reviews', ['slug' => $review->getUrlTitle(), 'id' => $review->getId()]); ?>" class="card-link">Read more</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <nav class="pagination-blog">
            <ul>
                <?php if(isset($decadesSelected) && $yearSelected == null): ?>
                    <?php DateReviews::filmPaginationNumberDecade($decadesSelected); ?>
                <?php elseif(isset($decadesSelected) && isset($yearSelected)): ?>
                    <?php DateReviews::filmPaginationNumberYear($decadesSelected, $yearSelected); ?>
                <?php else: ?>
                    <?= $pagination->filmPaginationNumber(); ?>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</div>

</section>

<?php if($decadesSelected || $yearSelected): ?>
<script>
    document.addEventListener('DOMContentLoaded', function(){
        document.getElementById('all-reviews').scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
</script>
<?php endif; ?>