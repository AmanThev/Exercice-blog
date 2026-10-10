<?php
/**
 * Fields shared by "New review" and "Edit review".
 *
 * Expects :
 *   $isEdit        bool      false on the creation page, true on the edit page
 *   $values        array     the value of each field, already safe to print
 *   $fieldError    callable  $fieldError('title') : the error to show under a field ('' if none)
 *   $currentPoster string    (edit page) the file name of the current poster
 */
$yearMax = (int)date('Y');
?>
<div class="field">
    <label for="author">Author</label>
    <?php if($isEdit): ?>
        <input type="text" id="author" value="<?= $values['author'] ?>" readonly>
        <small class="hint">The author cannot be changed.</small>
    <?php else: ?>
        <input type="text" name="author" id="author" value="<?= $values['author'] ?>" placeholder="Name of an administrator" autocomplete="off">
        <small class="hint">Must be the name of an administrator (2 to 20 characters).</small>
        <p class="field-error" data-error="author"><?= $fieldError('author') ?></p>
    <?php endif; ?>
</div>

<h3 class="form-section">Film's information</h3>

<div class="field">
    <label for="title">Title</label>
    <input type="text" name="title" id="title" value="<?= $values['title'] ?>" autocomplete="off">
    <p class="field-error" data-error="title"><?= $fieldError('title') ?></p>
</div>

<div class="field">
    <?php if($isEdit && $currentPoster !== ''): ?>
        <span class="field-label">Current poster</span>
        <div class="current-picture"><img src="<?= PUBLIC_PATH ?>/img/posterFilm/<?= $currentPoster ?>" alt="Current poster"></div>
    <?php endif; ?>
    <label for="picture"><?= $isEdit ? 'Replace the poster' : 'Poster' ?></label>
    <div class="file-row">
        <input type="file" name="poster" id="picture" accept="image/png,image/jpeg,image/gif">
        <button type="button" class="file-clear" id="clear-picture" aria-label="Remove the poster" hidden><i class="fas fa-times"></i></button>
    </div>
    <div class="picture-preview" id="picture-preview" hidden><img src="" alt="Poster preview"></div>
    <small class="hint">Optional. PNG, JPG or GIF, 2 MB maximum.<?= $isEdit ? ' Leave empty to keep the current poster.' : '' ?></small>
    <p class="field-error" data-error="poster"><?= $fieldError('poster') ?></p>
</div>

<div class="field-row">
    <div class="field">
        <label for="date">Year</label>
        <select name="date" id="date">
            <?php for($year = $yearMax; $year >= 1900; $year--): ?>
                <option value="<?= $year ?>" <?= (int)$values['date'] === $year ? 'selected' : '' ?>><?= $year ?></option>
            <?php endfor; ?>
        </select>
        <p class="field-error" data-error="date"><?= $fieldError('date') ?></p>
    </div>
    <div class="field">
        <label for="genre">Genre</label>
        <input type="text" name="genre" id="genre" value="<?= $values['genre'] ?>" autocomplete="off">
        <p class="field-error" data-error="genre"><?= $fieldError('genre') ?></p>
    </div>
</div>

<div class="field-row">
    <div class="field">
        <label for="director">Director</label>
        <input type="text" name="director" id="director" value="<?= $values['director'] ?>" autocomplete="off">
        <p class="field-error" data-error="director"><?= $fieldError('director') ?></p>
    </div>
    <div class="field">
        <label for="writer">Writer</label>
        <input type="text" name="writer" id="writer" value="<?= $values['writer'] ?>" autocomplete="off">
        <p class="field-error" data-error="writer"><?= $fieldError('writer') ?></p>
    </div>
</div>

<div class="field-row">
    <div class="field">
        <label for="cast">Starring</label>
        <input type="text" name="cast" id="cast" value="<?= $values['cast'] ?>" autocomplete="off">
        <small class="hint">The main actors, separated by commas.</small>
        <p class="field-error" data-error="cast"><?= $fieldError('cast') ?></p>
    </div>
    <div class="field">
        <label for="production">Production</label>
        <input type="text" name="production" id="production" value="<?= $values['production'] ?>" autocomplete="off">
        <p class="field-error" data-error="production"><?= $fieldError('production') ?></p>
    </div>
</div>

<div class="field">
    <label for="synopsis">Synopsis</label>
    <textarea name="synopsis" id="synopsis" rows="6"><?= $values['synopsis'] ?></textarea>
    <small class="hint"><span id="synopsis-count">0</span> / 250 characters</small>
    <p class="field-error" data-error="synopsis"><?= $fieldError('synopsis') ?></p>
</div>

<h3 class="form-section">Your opinion</h3>

<div class="field">
    <label for="review">Your review</label>
    <textarea name="review" id="review" rows="12"><?= $values['review'] ?></textarea>
    <small class="hint">To hide a spoiler, write it between [Spoiler] and [/Spoiler].</small>
    <p class="field-error" data-error="review"><?= $fieldError('review') ?></p>
</div>

<div class="field">
    <label for="score">Your score</label>
    <div class="score-row">
        <input type="range" name="score" id="score" min="0" max="5" step="1" value="<?= (int)$values['score'] ?>">
        <output id="score-value" for="score"></output>
    </div>
    <p class="field-error" data-error="score"><?= $fieldError('score') ?></p>
</div>

<script>
    // Live score and synopsis counter (also called again after the form is reset)
    function refreshReviewForm(){
        document.getElementById('score-value').textContent = document.getElementById('score').value + ' / 5';
        document.getElementById('synopsis-count').textContent = document.getElementById('synopsis').value.length;
    }
    document.getElementById('score').addEventListener('input', refreshReviewForm);
    document.getElementById('synopsis').addEventListener('input', refreshReviewForm);
    refreshReviewForm();
</script>