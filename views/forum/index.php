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

<h1 class="title-forum">Forum</h1>

<section class="forum welcome">
    <h2>Welcome to the Forum! </h2>
    <div class="separate-forum"></div>

<p>This forum was developed to share our passion of the cinema!!! Hollywood will not have secrets anymore for you! 
Check out all the important topics & great discussions so far. Hope you all contribute and enjoy the ambiance!</p>
<p>This is YOUR COMMUNITY so don't forget to introduce yourself ;-)</p>
<strong>The rules of the forum :</strong>
<ul>
    <li>Please display a positive, friendly attitude and be respectful of other's opinions.</li>
    <li>Attacking other members, bashing, slander and libel are not allowed and can result in a warning or ban. Comments that are disrespectful to others or otherwise violate what we believe are appropriate standards for civil discussion may be deleted.</li>
    <li>Please try to post in the correct section of the forum</li>
    <li>Please do not link to your own site in posts or say check out my site or contact me (except in your sig).</li>
    <li>3 STRIKES RULE we believe in 3 strikes, you're out. 1st violation, gentle warning and reminder of the rules. 2nd violation, ban warning. 3rd - you are out. Exceptions to the rule are blatant spam or overt guideline violations which can result in an instant ban.</li>
    <li>Members are responsible for what they post and we cannot be held liable for the content of posted messages.</li>
</ul>
<p>Enjoy!!!</p>
</section>

<?php foreach($categories as $category): ?>
<section class="forum home">
    <h2><a href="<?= CreateUrl::url('forum', ['slug' => $category->getUrlName(), 'id' => $category->getId()]) ?>"><?= $category->getName() ?></a></h2>
    <table style="width:100%">
        <tr>
            <th style="width:46%;">Title</th>
            <th style="width:14%">Topic</th>
            <th style="width:14%">Message(s)</th>
            <th style="width:26%">Last Message</th>
        </tr>
        <?php foreach($subCats->getLastSubCategories($category->getId()) as $subCat): ?>
        <tr>
            <td><a href="<?= CreateUrl::url('forum/' . $category->getUrlName(), ['slug' => $subCat->getUrlName(), 'id' => $subCat->getId()]) ?>"><?= $subCat->getName() ?></a></td>
            <td><?= $countTopics->countTopics($subCat->getId()) ?></td>
            <td><?= $countMessages->countMessagesWithSubCat($subCat->getId()); ?></td>
            <td><?php echo $countMessages->countMessagesWithSubCat($subCat->getId()) === 0 ? "No Message" : 'by ' . $message->getLastMessageIndex($subCat->getId())->getName() . '<br>' .  $message->getLastMessageIndex($subCat->getId())->getDateTimeMessage()->format('d-m-Y, h:i'); ?></td>
        </tr>
        <?php endforeach; ?>
    </table> 
</section>
<?php endforeach; ?>


         