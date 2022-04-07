<?php

use App\URL\CreateUrl;

$title = "New Post";
?>

<h3 class="title-page">New Post</h3>

<form class="new" action="<?= CreateUrl::url('ajax/addPostAjax'); ?>" method="post" enctype="multipart/form-data">
    <div class="input inputAuthor">
        <label for="author">Your Name</label>
        <input type="text" name="author" id="author" value="" aria-describedby="authorInfo" placeholder="Write your name">
        <small id="authorInfo">Your name must not exceed 20 characters</small>
    </div>

    <div class="input">
        <label for="title">Post's Title</label>
        <input type="text" name="title" id="title" value="" aria-describedby="titleInfo" placeholder="Write your title">
        <p id="title-error"></p>
    </div>

    <div class="textarea">
        <label for="content">Your Post</label>
        <textarea type="text" name="content" id="content" rows="20"></textarea>
        <p id="content-error"></p>
    </div>

    <div class="picture">
        <label for="picture">Upload</label>
        <input type="hidden" name="MAX_FILE_SIZE" value="250000">
        <input type="file" name="picture" id="picture">
        <button type="button" onclick="document.getElementById('picture').value=''" class="delete-file deleteImagePost"><i class="fas fa-times-circle"></i></button>
        <small>Choose a picture to illustrate your post</small>
        <p id="picture-error"></p>
    </div>

    <div>
        <label class="switch">
            <input class="switch-input" type="checkbox" id="checkbox">
            <span class="switch-label" data-public="public" data-private="private"></span> 
            <span class="switch-handle"></span> 
        </label>
    </div> 

    <div>
        <p id="message"></p>
    </div>

    <div class="button">
        <button id="validateForm" type="submit" name="submit"><span>Submit</span></button>
    </div>
</form>

<script>
    $(function(){
    $("form").submit(function(e){
        e.preventDefault();
        var url     = $("form").attr("action");
        var button  = $("#validateForm span");
        var error; 

        $("#validateForm").addClass("submit");
        button.fadeOut("slow", function(){
            button.empty().html('<i class="fas fa-spinner"></i>').fadeIn("slow");
        });

        $(".inputAuthor p").remove();
        $(".error").empty().removeClass("error");

		if (!$("input:text").val()) {
			error = "Please write your name and a title!";
            setTimeout(function() {
			    $("#message").html(error);
                $("#message").addClass("error");
            }, 2000);
            setTimeout(function() {
                button.fadeOut(function(){
                    button.empty().append("Submit").fadeIn();
                    $("#button").removeClass("submit");
                });
            }, 2800);
            return false;
        }

        var formData = new FormData(); 
        var author = $("#author").val();
            formData.append('author', author);
        var title = $("#title").val();
            formData.append('title', title);
        var content = $("#content").val();
            formData.append('content', content);
        if ($('#picture').val() != ''){
            var picture = $('#picture').prop('files')[0];
                formData.append('picture', picture);
        }
        var checkbox = $("#checkbox").is(':checked');
            formData.append('public', checkbox);

        $.ajax({
            type: "POST",
            url: url,
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            success:function(data){
                data = JSON.parse(data)
                if(data.status === 'ok'){
                    setTimeout(function() {
                        $("#message").addClass("valid");
                        $('#message').text(data.good);
                        $("form")[0].reset();
                    }, 2000);
                    setTimeout(function() {
                        button.fadeOut(function(){
                            $("#validateForm").removeClass("submit").prop('disabled', true);
                            button.empty().html('<i class="fas fa-check"></i>').fadeIn("slow");
                        });
                    }, 2800);  
                }else{
                    setTimeout(function() {
                            if(data.error.author){
                                $.each(data.error.author, function (key, value){
                                    $(".inputAuthor").append("<p class='error'>" + value + "</p>");
                                })
                            }
                            if(data.error.title){
                                var titleError = "";
                                $.each(data.error.title, function (key, value){
                                    titleError += value;
                                    $("#title-error").text(titleError);
                                    $("#title-error").addClass("error");
                                })
                            }
                            if(data.error.content){
                                var contentError = "";
                                $.each(data.error.content, function (key, value){
                                    contentError += value;
                                    $("#content-error").text(contentError);
                                    $("#content-error").addClass("error");
                                })
                            }
                            if(data.error.picture){
                                var pictureError = "";
                                $.each(data.error.picture, function (key, value){
                                    pictureError += value;
                                    $("#picture-error").text(pictureError);
                                    $("#picture-error").addClass("error");
                                })
                            }
                            $("#message").text("Please, correct your error(s)");
                            $("#message").addClass("error");
                        // });
                    }, 2000);
                    setTimeout(function() {
                        button.fadeOut(function(){
                            button.empty().append("Submit").fadeIn();
                            $("#validateForm").removeClass("submit");
                        });
                }, 2800);
                } 
            }
         })
        return false;
    });
 });
</script>