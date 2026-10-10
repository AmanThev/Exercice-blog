<?php

use App\URL\CreateUrl;
use App\Security\Csrf;

$title = "New Review";

// Variables read by the shared fields (_fields.php)
$isEdit        = false;
$currentPoster = '';
$values        = array_fill_keys(['author', 'title', 'date', 'director', 'writer', 'cast', 'production', 'genre', 'synopsis', 'review'], '') + ['score' => 0];
$fieldError    = fn(string $key) => '';
?>

<div class="dash-pagehead">
    <h2 class="dash-title">New review</h2>
    <a class="dash-btn-ghost" href="<?= CreateUrl::url('dashboard/reviews') ?>"><i class="fas fa-chevron-left"></i> Back to reviews</a>
</div>

<form id="film-form" class="dash-form" action="<?= CreateUrl::url('ajax/addFilmAjax'); ?>" method="post" enctype="multipart/form-data" novalidate>
    <?= Csrf::field() ?>

    <?php require VIEWS . 'dashboard/review/_fields.php'; ?>

    <p id="message" class="dash-message" role="status"></p>
    <p id="form-done" class="dash-message valid" hidden>Review published. <a href="<?= CreateUrl::url('dashboard/reviews') ?>">Back to the reviews list</a></p>

    <div class="form-actions">
        <button id="validateForm" class="dash-submit" type="submit"><span>Publish</span></button>
    </div>
</form>

<script src="<?= PUBLIC_PATH ?>/js/imagePreview.js"></script>
<script>
$(function(){
    var $form  = $('#film-form');
    var $btn   = $('#validateForm');
    var $label = $btn.find('span');
    var $msg   = $('#message');
    var $done  = $('#form-done');

    function setMessage(text, type){
        $msg.text(text).removeClass('error valid').addClass(type);
    }

    function clearFeedback(){
        $('.field-error').text('');
        $msg.text('').removeClass('error valid');
        $done.prop('hidden', true);
    }

    $form.on('submit', function(e){
        e.preventDefault();
        clearFeedback();

        $btn.prop('disabled', true);
        $label.text('Sending...');

        $.ajax({
            type: 'POST',
            url: $form.attr('action'),
            data: new FormData(this),
            contentType: false,
            processData: false,
            cache: false,
            dataType: 'json'
        })
        .done(function(data){
            if(data.status === 'ok'){
                setMessage(data.good, 'valid');
                $form[0].reset();
                $('#clear-picture').trigger('click');
                refreshReviewForm();
                $done.prop('hidden', false);
            }else{
                var errors = data.error || {};
                $.each(errors, function(field, messages){
                    $('[data-error="' + field + '"]').text(messages.join(' '));
                });
                setMessage(errors.form ? errors.form.join(' ') : 'Please correct the highlighted errors.', 'error');
            }
        })
        .fail(function(){
            setMessage('Something went wrong on the server. Please try again.', 'error');
        })
        .always(function(){
            $btn.prop('disabled', false);
            $label.text('Publish');
        });
    });
});
</script>