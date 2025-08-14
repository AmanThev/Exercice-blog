<?php
use App\Form\Search;
use App\URL\CreateUrl;

$title  = "Search";

if(!empty($_POST)){
    $data = new Search($_POST);
    $totalResults = 0;

    if($data->validateSearch()->resultValidator()){
        $results = ($data->findResult());
        $totalResults = count($results);
        $type = $_POST['submit'];
    }
    $errors = $data->returnErrors();
}

?>

<h1 id="title-blog">Result search</h1>

<section class="section-error">
    <h2>We have find <?php echo $totalResults > 1 ? ' '.$totalResults.' results' : ' '.$totalResults.' result' ?> matching your seach : <?= $_POST['search']; ?> </h2>
    <?php if(!empty($errors)): ?>
        <?php foreach ($errors['search'] as $error): ?>
            <p id="p-error">Your <?= $error; ?></p>
        <?php endforeach; ?>
    <?php else: ?>
        <?php switch($type): ?><?php case 'post': ?>
            <?php foreach ($results as $result): ?>
                <a href="<?= CreateUrl::url('blog', ['slug' => $result->getUrlTitle(), 'id' => $result->getId()]); ?>">
                    <div class="result-search">
                        <h3><?= $result->getTitle() ?></h3>
                        <p><?= $result->getExcerptContent() ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php break; ?>
        <?php case 'film': ?>
            <?php foreach ($results as $result): ?>
                <a href="<?= CreateUrl::url('reviews', ['slug' => $result->getUrlTitle(), 'id' => $result->getId()]); ?>">
                    <div class="result-search">
                        <h3><?= $result->getTitle() ?></h3>
                        <p>Directed by <?= $result->getDirector() ?></p>
                        <p>Writer : <?= $result->getWriter() ?></p>
                        <p>Casting : <?= $result->getCast() ?></p>
                        <p>Production : <?= $result->getProduction() ?></p>
                        <p>Genre : <?= $result->getGenre() ?></p>
                        <p class=""><?= $result->getExcerptSynopsis(); ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php break; ?>
        <?php case 'all': ?>
            <?php if(!empty($results['posts'])): ?>
                <?php foreach($results['posts'] as $post): ?>
                    <a href="<?= CreateUrl::url('blog', ['slug' => $post->getUrlTitle(), 'id' => $post->getId()]); ?>">
                        <div class="result-search">
                            <h3><?= $post->getTitle() ?></h3>
                            <p><?= $post->getExcerptContent() ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if(!empty($results['films'])): ?>
                <?php foreach($results['films'] as $film): ?>
                    <a href="<?= CreateUrl::url('reviews', ['slug' => $film->getUrlTitle(), 'id' => $film->getId()]); ?>">
                        <div class="result-search">
                            <h3><?= $film->getTitle() ?></h3>
                            <p>Directed by <?= $film->getDirector() ?></p>
                            <p>Writer : <?= $film->getWriter() ?></p>
                            <p>Casting : <?= $film->getCast() ?></p>
                            <p>Production : <?= $film->getProduction() ?></p>
                            <p>Genre : <?= $film->getGenre() ?></p>
                            <p class=""><?= $film->getExcerptSynopsis(); ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php break; ?>
        <?php default: ?>
            <p>Nothing</p>
        <?php endswitch; ?>
    <?php endif; ?>
</section>
