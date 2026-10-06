<?php

use App\URL\CreateUrl;
use App\Manager\ForumDatabase;

$title          = 'Forum';

$categories     = new ForumDatabase;
$categories     = $categories->getCategories();

$countMessages  = new ForumDatabase();

$countTopics    = new ForumDatabase();

$message        = new ForumDatabase();

$subCats        = new ForumDatabase;
?>

<section>

<div class="forum-welcome">
    <h1 class="forum-welcome-title">Welcome to the Forum</h1>
    <span class="forum-welcome-rule"></span>

    <p>This forum was developed to share our passion of the cinema. Hollywood will not have secrets anymore for you! Check out all the important topics &amp; great discussions so far. Hope you all contribute and enjoy the ambiance!</p>
    <p>This is your community, so don't forget to introduce yourself.</p>

    <h3>The rules of the forum</h3>
    <ul class="forum-rules">
        <li>Please display a positive, friendly attitude and be respectful of other's opinions.</li>
        <li>Attacking other members, bashing, slander and libel are not allowed and can result in a warning or ban. Comments that are disrespectful to others or otherwise violate appropriate standards for civil discussion may be deleted.</li>
        <li>Please try to post in the correct section of the forum.</li>
        <li>Please do not link to your own site in posts, or say "check out my site" / "contact me" (except in your signature).</li>
        <li>Three strikes rule: a gentle warning first, then a ban warning, then you're out. Blatant spam or overt guideline violations can result in an instant ban.</li>
        <li>Members are responsible for what they post; we cannot be held liable for the content of posted messages.</li>
    </ul>
</div>

<?php foreach($categories as $category): ?>
    <div class="forum-category">
        <div class="section-heading">
            <h2><a href="<?= CreateUrl::url('forum', ['slug' => $category->getUrlName(), 'id' => $category->getId()]) ?>"><?= $category->getName() ?></a></h2>
            <span class="rule"></span>
        </div>

        <table class="forum-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Topics</th>
                    <th>Messages</th>
                    <th>Last message</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($subCats->getLastSubCategories($category->getId()) as $subCat): ?>
                    <tr>
                        <td data-label="Title">
                            <a href="<?= CreateUrl::url('forum/' . $category->getUrlName(), ['slug' => $subCat->getUrlName(), 'id' => $subCat->getId()]) ?>" class="forum-subcat-link"><?= $subCat->getName() ?></a>
                        </td>
                        <td data-label="Topics"><?= $countTopics->countTopics($subCat->getId()) ?></td>
                        <td data-label="Messages"><?= $countMessages->countMessagesWithSubCat($subCat->getId()); ?></td>
                        <td data-label="Last message">
                            <?php if($countMessages->countMessagesWithSubCat($subCat->getId()) === 0): ?>
                                <span class="forum-no-message">No message</span>
                            <?php else: ?>
                                <span class="forum-last-author">by <?= $message->getLastMessageIndex($subCat->getId())->getName() ?></span>
                                <span class="forum-last-date"><?= $message->getLastMessageIndex($subCat->getId())->getDateTimeMessage()->format('d-m-Y, h:i') ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endforeach; ?>

</section>