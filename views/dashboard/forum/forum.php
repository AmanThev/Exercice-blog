<?php

use App\Manager\ForumDatabase;
use App\URL\CreateUrl;
use App\Security\Csrf;

$title = 'Dashboard/Forum';

$forumDb         = new ForumDatabase();
$messagesByTopic = $forumDb->countMessagesByTopic();   // one query for the messages of every topic

// The whole forum is loaded once. The counts are calculated from what is already loaded
// (they used to be one query each : around 250 queries for this page).
$tree   = [];
$totals = ['categories' => 0, 'subCategories' => 0, 'topics' => 0, 'open' => 0, 'messages' => 0];

foreach($forumDb->getCategories() as $category){
    $subCategories = [];
    $categoryTopics = 0;
    $categoryMessages = 0;

    foreach($forumDb->getSubCategories($category->getId()) as $subCategory){
        $topics   = $forumDb->getTopics($subCategory->getId());
        $messages = 0;

        foreach($topics as $topic){
            $messages += (int)($messagesByTopic[$topic->getId()] ?? 0);
            if($topic->getResolved() === null){
                $totals['open']++;
            }
        }

        $subCategories[]   = ['model' => $subCategory, 'topics' => $topics, 'messages' => $messages];
        $categoryTopics   += count($topics);
        $categoryMessages += $messages;
    }

    $tree[] = ['model' => $category, 'subs' => $subCategories, 'topics' => $categoryTopics, 'messages' => $categoryMessages];
    $totals['categories']++;
    $totals['subCategories'] += count($subCategories);
    $totals['topics']        += $categoryTopics;
    $totals['messages']      += $categoryMessages;
}

// "1 topic" / "2 topics" / "2 categories"
$plural = fn(int $n, string $word) => $n . ' ' . ($n === 1 ? $word : (str_ends_with($word, 'y') ? substr($word, 0, -1) . 'ies' : $word . 's'));

// One small form per action (POST + CSRF token), sent to dashboard/forum/action
$actionUrl  = CreateUrl::url('dashboard/forum/action');
$actionForm = function(string $action, int $id, string $label, string $icon, string $confirm = '') use ($actionUrl): string {
    $onsubmit = $confirm !== '' ? ' onsubmit="return confirm(' . htmlspecialchars(json_encode($confirm), ENT_QUOTES) . ')"' : '';
    $class    = str_starts_with($action, 'delete') ? ' class="delete"' : '';

    return '<form action="' . $actionUrl . '" method="post"' . $onsubmit . '>'
         . Csrf::field()
         . '<input type="hidden" name="action" value="' . $action . '">'
         . '<input type="hidden" name="id" value="' . $id . '">'
         . '<button type="submit"' . $class . '><i class="fas ' . $icon . '"></i>' . $label . '</button>'
         . '</form>';
};

// Message left by an action (delete, close, reopen)
$flash = $_SESSION['flash_dash'] ?? null;
unset($_SESSION['flash_dash']);
?>

<div class="dash-pagehead">
    <h2 class="dash-title">Forum</h2>
</div>

<?php if($flash): ?>
    <p class="dash-message valid"><?= htmlspecialchars($flash) ?></p>
<?php endif; ?>

<p class="forum-summary">
    <?= $plural($totals['categories'], 'category') ?> &middot;
    <?= $plural($totals['subCategories'], 'sub-category') ?> &middot;
    <?= $plural($totals['topics'], 'topic') ?> (<?= $totals['open'] ?> open) &middot;
    <?= $plural($totals['messages'], 'message') ?>
</p>

<?php if(empty($tree)): ?>
    <p class="dash-empty">The forum is empty.</p>
<?php else: ?>
    <div class="forum-tree">
        <?php foreach($tree as $cat): ?>
            <?php $category = $cat['model']; ?>
            <details class="forum-cat">
                <summary>
                    <span class="forum-name"><?= $category->getName() ?></span>
                    <span class="forum-meta"><?= $plural(count($cat['subs']), 'sub-category') ?> &middot; <?= $plural($cat['topics'], 'topic') ?> &middot; <?= $plural($cat['messages'], 'message') ?></span>
                </summary>

                <div class="forum-body">
                    <div class="forum-actions">
                        <a href="<?= CreateUrl::url('forum', ['slug' => $category->getUrlName(), 'id' => $category->getId()]) ?>"><i class="fas fa-eye"></i>View on the site</a>
                        <?= $actionForm('delete_category', $category->getId(), 'Delete the category', 'fa-times-circle',
                            "Delete this category with its {$plural(count($cat['subs']), 'sub-category')}, {$plural($cat['topics'], 'topic')} and {$plural($cat['messages'], 'message')}? This cannot be undone.") ?>
                    </div>

                    <?php foreach($cat['subs'] as $sub): ?>
                        <?php $subCategory = $sub['model']; ?>
                        <details class="forum-sub">
                            <summary>
                                <span class="forum-name"><?= $subCategory->getName() ?></span>
                                <span class="forum-meta"><?= $plural(count($sub['topics']), 'topic') ?> &middot; <?= $plural($sub['messages'], 'message') ?></span>
                            </summary>

                            <div class="forum-body">
                                <div class="forum-actions">
                                    <a href="<?= CreateUrl::url('forum/' . $category->getUrlName(), ['slug' => $subCategory->getUrlName(), 'id' => $subCategory->getId()]) ?>"><i class="fas fa-eye"></i>View on the site</a>
                                    <?= $actionForm('delete_subcategory', $subCategory->getId(), 'Delete the sub-category', 'fa-times-circle',
                                        "Delete this sub-category with its {$plural(count($sub['topics']), 'topic')} and {$plural($sub['messages'], 'message')}? This cannot be undone.") ?>
                                </div>

                                <?php if(empty($sub['topics'])): ?>
                                    <p class="dash-muted">No topic in this sub-category.</p>
                                <?php else: ?>
                                    <ul class="forum-topics">
                                        <?php foreach($sub['topics'] as $topic): ?>
                                            <?php
                                                $count    = (int)($messagesByTopic[$topic->getId()] ?? 0);
                                                $resolved = $topic->getResolved() === 1;
                                                $status   = $resolved ? 'resolved' : ($count === 0 ? 'pending' : 'open');
                                                $labels   = ['resolved' => 'Resolved', 'open' => 'Open', 'pending' => 'No reply'];
                                            ?>
                                            <li class="forum-topic">
                                                <span class="status-badge <?= $status ?>"><?= $labels[$status] ?></span>
                                                <span class="forum-topic-title"><?= $topic->getTitle() ?></span>
                                                <span class="forum-meta"><?= $plural($count, 'message') ?></span>
                                                <span class="forum-actions">
                                                    <a href="<?= CreateUrl::url('forum/' . $category->getUrlName() . '/' . $subCategory->getUrlName(), ['slug' => $topic->getUrlTitle(), 'id' => $topic->getId()]) ?>"><i class="fas fa-eye"></i>View</a>
                                                    <?php if($resolved): ?>
                                                        <?= $actionForm('reopen_topic', $topic->getId(), 'Reopen', 'fa-lock-open') ?>
                                                    <?php else: ?>
                                                        <?= $actionForm('close_topic', $topic->getId(), 'Close', 'fa-lock') ?>
                                                    <?php endif; ?>
                                                    <?= $actionForm('delete_topic', $topic->getId(), 'Delete', 'fa-times-circle',
                                                        "Delete this topic with its {$plural($count, 'message')}? This cannot be undone.") ?>
                                                </span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </details>
                    <?php endforeach; ?>

                    <?php if(empty($cat['subs'])): ?>
                        <p class="dash-muted">No sub-category in this category.</p>
                    <?php endif; ?>
                </div>
            </details>
        <?php endforeach; ?>
    </div>
<?php endif; ?>