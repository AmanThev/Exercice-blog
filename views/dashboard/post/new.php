<?php

use App\URL\CreateUrl;
use App\Security\Csrf;

$title = "New Post";
?>

<div class="dash-pagehead">
    <h2 class="dash-title">New post</h2>
    <a class="dash-btn-ghost" href="<?= CreateUrl::url('dashboard/posts') ?>"><i class="fas fa-chevron-left"></i> Back to posts</a>
</div>

<form id="post-form" class="dash-form" action="<?= CreateUrl::url('ajax/addPostAjax'); ?>" method="post" enctype="multipart/form-data" novalidate>
    <?= Csrf::field() ?>

    <div class="field">
        <label for="author">Author</label>
        <input type="text" name="author" id="author" placeholder="Name of an administrator" autocomplete="off">
        <small class="hint">Must be the name of an administrator (2 to 20 characters).</small>
        <p class="field-error" data-error="author"></p>
    </div>

    <div class="field">
        <label for="title">Title</label>
        <input type="text" name="title" id="title" placeholder="Write your title" autocomplete="off">
        <p class="field-error" data-error="title"></p>
    </div>

    <div class="field">
        <label for="content">Content</label>
        <textarea name="content" id="content" rows="14"></textarea>
        <small class="hint">At least 20 characters.</small>
        <p class="field-error" data-error="content"></p>
    </div>

    <div class="field">
        <label for="picture">Picture</label>
        <div class="file-row">
            <input type="file" name="picture" id="picture" accept="image/png,image/jpeg,image/gif">
            <button type="button" class="file-clear" id="clear-picture" aria-label="Remove the picture" hidden><i class="fas fa-times"></i></button>
        </div>
        <div class="picture-preview" id="picture-preview" hidden><img src="" alt="Preview"></div>
        <small class="hint">Optional. PNG, JPG or GIF, 2 MB maximum.</small>
        <p class="field-error" data-error="picture"></p>
    </div>

    <div class="field">
        <span class="field-label">Visibility</span>
        <label class="switch">
            <input class="switch-input" type="checkbox" id="checkbox">
            <span class="switch-label" data-public="public" data-private="private"></span>
            <span class="switch-handle"></span>
        </label>
    </div>

    <p id="message" class="dash-message" role="status"></p>
    <p id="form-done" class="dash-message valid" hidden>Post published. <a href="<?= CreateUrl::url('dashboard/posts') ?>">Back to the posts list</a></p>

    <div class="form-actions">
        <button id="validateForm" class="dash-submit" type="submit"><span>Publish</span></button>
    </div>
</form>

<script src="<?= PUBLIC_PATH ?>/js/imagePreview.js"></script>
<script>
$(function(){
    var $form  = $('#post-form');
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

        if(!$.trim($('#author').val()) || !$.trim($('#title').val())){
            setMessage('Please write the author name and a title.', 'error');
            return;
        }

        var formData = new FormData();
        formData.append('csrf_token', $form.find('input[name="csrf_token"]').val());
        formData.append('author',  $('#author').val());
        formData.append('title',   $('#title').val());
        formData.append('content', $('#content').val());
        formData.append('public',  $('#checkbox').is(':checked'));

        var file = $('#picture').prop('files')[0];
        if(file){
            formData.append('picture', file);
        }

        $btn.prop('disabled', true);
        $label.text('Sending...');

        $.ajax({
            type: 'POST',
            url: $form.attr('action'),
            data: formData,
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