<?php 

use App\Manager\ForumDatabase;
use App\URL\CreateUrl;

$title          = 'Dashboard/Forum'; 

$categories     = new ForumDatabase;
$categories     = $categories->getCategories();
$countMessages  = new ForumDatabase();
$countTopics    = new ForumDatabase();
$subCats        = new ForumDatabase();
$topics         = new ForumDatabase();
?>


<h3 class="title-page">Forum</h3>

<a class="create-button" href="#">Create New Categorie</a>
<a class="create-button" href="#">Create New Sub-Categorie</a>

<section class="forum">
    <?php foreach($categories as $category): ?>
        <ul class="list-cat">
            <li class="category">
                <div>
                    <span>
                        <a href="#<?= $category->getName() ?>" class="link-cat"><?= $category->getName() ?></a>
                    </span>
                    <span class="link-forum-cat">
                        <a href="<?= CreateUrl::url('forum', ['slug' => $category->getUrlName(), 'id' => $category->getId()]) ?>">View</a>
                        <a href="#">Edit</a>
                        <a href="#">Delete</a>
                    </span>
                </div>
            
                <?php foreach($subCats->getSubCategories($category->getId()) as $subCat): ?>
                    <ul class="list-subCat">
                        <li class="subCategory <?php echo $countTopics->countTopics($subCat->getId()) === 0 ? "disabled" : "" ?>">
                            <div>
                                <span>
                                    <a href="#<?= $subCat->getName() ?>" class="link-subCat <?php echo $countTopics->countTopics($subCat->getId()) === 0 ? "disabled" : "" ?>"><?= $subCat->getName() ?><i class="fas fa-caret-down"></i></a>
                                </span>

                                <span class="link-forum">
                                    <p>(<?php echo $countTopics->countTopics($subCat->getId()) <= 1 ? ' ' . $countTopics->countTopics($subCat->getId()) . " Topic" : ' ' . $countTopics->countTopics($subCat->getId()) . " Topics " ?>)</p>
                                    <a href="<?= CreateUrl::url('forum/' . $category->getUrlName(), ['slug' => $subCat->getUrlName(), 'id' => $subCat->getId()]) ?>"> View</a>
                                    <a href="#">Edit</a>
                                    <a href="#"> Delete</a>
                                </span>
                            </div>

                            <?php foreach($topics->getTopics($subCat->getId()) as $topic): ?>
                                <ul class="list-topic">
                                    <li class="topic">
                                        <div>
                                        <span>
                                            <p class="<?php echo $topic->getResolved() === 1 ? 'resolved' : 'no-resolved' ?> <?php echo $countMessages->countMessages($topic->getId()) === 0 ? "pending" : "" ?>"><i class="fas fa-check-circle"></i></p>
                                            <p><?= $topic->getTitle() ?></p>
                                        </span>
                                        <span class="link-forum">
                                            <p>(<?php echo $countMessages->countMessages($topic->getId()) <= 1 ? ' ' . $countMessages->countMessages($topic->getId()) . " Message " : ' ' . $countMessages->countMessages($topic->getId()) . " Messages " ?>)</p>
                                            <a href="<?= CreateUrl::url('forum/' . $category->getUrlName() . '/' . $subCat->getUrlName(), ['slug' => $topic->getUrlTitle(), 'id' => $topic->getId()]) ?>">View</a>
                                            <a href="#">Edit</a>
                                            <a href="#">Delete</a>
                                        </span>
                                        </div>
                                    </li>
                                </ul>
                            <?php endforeach; ?>

                        </li>
                    </ul>
                <?php endforeach; ?>

            </li>
        </ul>
    <?php endforeach; ?>
</section>

<script>
    $('.link-cat').click(function(){        
        $(this).closest('ul').toggleClass("active").siblings().removeClass("active");

        if ($('.list-subCat').hasClass('active')){
            $('.list-subCat').removeClass('active');
        }
    });

    $('.link-subCat').click(function(){
        $(this).closest('ul').toggleClass("active").siblings().removeClass("active");
    });
</script>