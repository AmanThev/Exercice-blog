<?php

use App\Manager\UserDatabase;
use App\Manager\PostDatabase;
use App\Manager\FilmDatabase;
use App\Manager\CommentDatabase;
use App\Manager\Exception\NotFoundException;
use App\URL\CreateUrl;

// dashboard/users/3  ->  $_GET['url'] = "dashboard/users/3" : the admin is identified by his id
// (a name in the url breaks with spaces)
$parts = explode('/', trim($_GET['url'], '/'));
$id    = $parts[2] ?? '';
$admin = null;

if(ctype_digit($id)){
    try{
        $admin = (new UserDatabase())->getAdminById((int)$id);
    }catch(NotFoundException $e){
        $admin = null;
    }
}

if($admin === null){
    http_response_code(404);
    $title = 'Admin not found';
}else{
    $title = $admin->getName();

    $filmDb    = new FilmDatabase();
    $commentDb = new CommentDatabase();
    $posts     = (new PostDatabase())->getPostByAdminId($admin->getId());
    $films     = $filmDb->getFilmByAdminId($admin->getId());

    // "1 post" / "2 posts"
    $plural = fn(int $n, string $word) => $n . ' ' . $word . ($n === 1 ? '' : 's');
}
?>

<?php if($admin === null): ?>
    <div class="dash-pagehead">
        <h2 class="dash-title">Admin not found</h2>
        <a class="dash-btn-ghost" href="<?= CreateUrl::url('dashboard/users', ['users' => 'admins']) ?>"><i class="fas fa-chevron-left"></i> Back to users</a>
    </div>
    <p class="dash-empty">This profile doesn't exist.</p>

<?php else: ?>
    <div class="dash-pagehead">
        <h2 class="dash-title">Admin profile</h2>
        <a class="dash-btn-ghost" href="<?= CreateUrl::url('dashboard/users', ['users' => 'admins']) ?>"><i class="fas fa-chevron-left"></i> Back to users</a>
    </div>

    <div class="dash-profile">
        <img class="dash-avatar" src="<?= PUBLIC_PATH ?>/img/photoProfile/default.jpg" alt="">
        <div>
            <h3 class="dash-profile-name"><?= $admin->getName() ?></h3>
            <?php if($admin->getPosition() !== ''): ?>
                <p class="dash-profile-position"><?= $admin->getPosition() ?></p>
            <?php endif; ?>
            <?php if($admin->getDescription() !== ''): ?>
                <p class="dash-profile-description"><?= nl2br($admin->getDescription()) ?></p>
            <?php else: ?>
                <p class="dash-profile-description dash-muted">No description yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="dash-grid">
        <article class="dash-card">
            <h3 class="dash-card-title">Posts <span class="dash-count"><?= count($posts) ?></span></h3>
            <?php if(empty($posts)): ?>
                <p class="dash-muted">No post written yet.</p>
            <?php else: ?>
                <ul class="dash-list">
                    <?php foreach($posts as $post): ?>
                        <li>
                            <span><a href="<?= CreateUrl::url('blog', ['slug' => $post->getUrlTitle(), 'id' => $post->getId()]) ?>"><?= $post->getTitle() ?></a></span>
                            <strong>
                                <span class="dash-stat"><i class="fas fa-comment"></i><?= $commentDb->totalComment('comments_post', $post->getId()) ?></span>
                                <span class="dash-stat"><i class="fas fa-thumbs-up"></i><?= $post->getLike() ?></span>
                                <span class="dash-stat"><i class="fas fa-thumbs-down"></i><?= $post->getDislike() ?></span>
                            </strong>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </article>

        <article class="dash-card">
            <h3 class="dash-card-title">Reviews <span class="dash-count"><?= count($films) ?></span></h3>
            <?php if(empty($films)): ?>
                <p class="dash-muted">No review written yet.</p>
            <?php else: ?>
                <ul class="dash-list">
                    <?php foreach($films as $film): ?>
                        <li>
                            <span><a href="<?= CreateUrl::url('reviews', ['slug' => $film->getUrlTitle(), 'id' => $film->getId()]) ?>"><?= $film->getTitle() ?></a></span>
                            <strong>
                                <span class="dash-stat"><i class="fas fa-comment"></i><?= $commentDb->totalComment('comments_film', $film->getId()) ?></span>
                                <span class="dash-stat"><i class="fas fa-poll-h"></i><?= $filmDb->totalVote($film->getId()) ?></span>
                                <span class="dash-stat"><i class="fas fa-star"></i><?= $filmDb->totalRating($film->getId()) ?></span>
                            </strong>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </article>
    </div>
<?php endif; ?>