<?php
/**
 * The results of the poll, as bars. Used by the home page and by actions/poll.php
 * (which sends this HTML back to the page after a vote).
 *
 * Expects : $results (answer => number of votes) and $userAnswer (answer of the member, or null)
 */
use App\Manager\PollDatabase;

$total = array_sum($results);
?>
<p class="poll-q"><?= htmlspecialchars(PollDatabase::QUESTION) ?></p>
<ul class="poll-results">
    <?php foreach(PollDatabase::ANSWERS as $key => $label): ?>
        <?php $percent = $total > 0 ? (int)round($results[$key] / $total * 100) : 0; ?>
        <li<?= $userAnswer === $key ? ' class="is-yours"' : '' ?>>
            <div class="poll-line">
                <span><?= htmlspecialchars($label) ?><?= $userAnswer === $key ? ' · your vote' : '' ?></span>
                <span><?= $percent ?>%</span>
            </div>
            <div class="poll-bar"><span style="width: <?= $percent ?>%"></span></div>
        </li>
    <?php endforeach; ?>
</ul>
<p class="poll-total"><?= $total ?> vote<?= $total === 1 ? '' : 's' ?></p>
