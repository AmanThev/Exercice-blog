<?php

use App\Form\Search;
use App\URL\CreateUrl;

$title        = "Search";
$allowedTypes = ['post', 'film', 'all'];

$searched     = isset($_POST['search']) && is_string($_POST['search']);
$term         = '';
$errors       = [];
$posts        = [];
$films        = [];
$totalResults = 0;

if($searched){
    $term = trim($_POST['search']);
    $type = (isset($_POST['submit']) && in_array($_POST['submit'], $allowedTypes, true)) ? $_POST['submit'] : 'all';

    $_POST['search'] = $term;
    $_POST['submit'] = $type;

    $data = new Search($_POST);
    if($data->validateSearch()->resultValidator()){
        $results = $data->findResult();
        if($type === 'all'){
            $posts = $results['posts'];
            $films = $results['films'];
        }elseif($type === 'post'){
            $posts = $results;
        }else{
            $films = $results;
        }
        $totalResults = count($posts) + count($films);
    }else{
        $errors = $data->returnErrors();
    }
}
?>

<section>

<div class="section-heading"><h1>Search results</h1><span class="rule"></span></div>

<?php if(!$searched): ?>
    <p class="forum-empty">Type a few words in a search box to look for a post or a review. You can start from the <a href="<?= CreateUrl::url('blog') ?>">blog</a> or the <a href="<?= CreateUrl::url('reviews') ?>">reviews</a>.</p>

<?php elseif(!empty($errors)): ?>
    <?php foreach(($errors['search'] ?? []) as $error): ?>
        <p class="error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></p>
    <?php endforeach; ?>

<?php else: ?>
    <p class="search-summary">
        <strong><?= $totalResults ?></strong> <?= $totalResults > 1 ? 'results' : 'result' ?> matching &ldquo;<em><?= htmlspecialchars($term) ?></em>&rdquo;
    </p>

    <?php if($totalResults === 0): ?>
        <p class="forum-empty">Nothing matches your search. Try fewer or different words.</p>
    <?php endif; ?>

    <?php if(!empty($posts)): ?>
        <h2 class="search-group-title">Blog posts <span>(<?= count($posts) ?>)</span></h2>
        <div class="search-results">
            <?php foreach($posts as $post): ?>
                <a class="search-result" href="<?= CreateUrl::url('blog', ['slug' => $post->getUrlTitle(), 'id' => $post->getId()]); ?>">
                    <img class="search-result-img" src="<?= PUBLIC_PATH ?>/img/postPicture/<?= $post->getPicture() ?>" alt="">
                    <div class="search-result-body">
                        <h3><?= $post->getTitle() ?></h3>
                        <p class="search-result-text"><?= $post->getExcerptContent() ?></p>
                        <span class="search-result-more">Read more</span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if(!empty($films)): ?>
        <h2 class="search-group-title">Reviews <span>(<?= count($films) ?>)</span></h2>
        <div class="search-results">
            <?php foreach($films as $film): ?>
                <a class="search-result search-result-film" href="<?= CreateUrl::url('reviews', ['slug' => $film->getUrlTitle(), 'id' => $film->getId()]); ?>">
                    <img class="search-result-img" src="<?= PUBLIC_PATH ?>/img/posterFilm/<?= $film->getPoster() ?>" alt="">
                    <div class="search-result-body">
                        <h3><?= $film->getTitle() ?></h3>
                        <dl class="search-result-meta">
                            <div><dt>Director</dt><dd><?= $film->getDirector() ?></dd></div>
                            <div><dt>Writer</dt><dd><?= $film->getWriter() ?></dd></div>
                            <div><dt>Cast</dt><dd><?= $film->getCast() ?></dd></div>
                            <div><dt>Production</dt><dd><?= $film->getProduction() ?></dd></div>
                            <div><dt>Genre</dt><dd><?= $film->getGenre() ?></dd></div>
                        </dl>
                        <p class="search-result-text"><?= $film->getExcerptSynopsis(); ?></p>
                        <span class="search-result-more">Read more</span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

</section>