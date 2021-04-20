<?php

$title = "New Post";

?>

<h3 class="title-page">New Post</h3>

<form class="new" action="" method="post" enctype="multipart/form-data">
    <div class="input">
        <label for="author">Your Name</label>
        <input type="text" name="author" id="author" value="" aria-describedby="authorInfo" placeholder="Write your name">
        <small id="authorInfo">Your name must not exceed 20 characters</small>
    </div>

    <div class="input">
        <label for="titlePost">Post's Title</label>
        <input type="text" name="titlePost" id="titlePost" value="" aria-describedby="titleInfo" placeholder="Write your title">
    </div>

    <div class="textarea">
        <label for="content">Your Post</label>
        <textarea type="text"name="content" id="content" rows="20"></textarea>
    </div>

    <div class="picture">
        <label for="picture">Upload</label>
        <input type="hidden" name="MAX_FILE_SIZE" value="250000">
        <input type="file" id="picture" name="picture">
        <small>Choose a picture to illustrate your post</small>
    </div>

    <div>
        <label class="switch">
            <input class="switch-input" type="checkbox" id="checkbox">
            <span class="switch-label" data-public="public" data-private="private"></span> 
            <span class="switch-handle"></span> 
        </label>
    </div> 

    <div>
        <p id="error"></p>
    </div>

    <div class="button">
        <button id="button" type="submit" name="submit"><span>Submit</span></button>
    </div>
</form>

<script>
var url="ajaxUpload";


$(function(){
    $("form").submit(function(e){
        e.preventDefault();
        var error; 
        var button = $("#button span");

        $("#button").addClass("submit");
        button.fadeOut("slow", function(){
            button.empty().html('<i class="fas fa-spinner"></i>').fadeIn("slow");
        });
		if (!$("input:text").val()) {
			error = "Please write your name and a title!";
            setTimeout(function() {
			    document.getElementById("error").innerHTML = error;
                $("#error").addClass("active");
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
        var titlePost = $("#titlePost").val();
            formData.append('titlePost', titlePost);
        var content = $("#content").val();
            formData.append('content', content);
        var picture = $('#picture').prop('files')[0];
            formData.append('picture', picture);
        var checkbox = $("#checkbox").is(':checked');
            formData.append('public', checkbox);

        $.ajax({
            url: url,
            type: "POST",
            data:  formData,
            contentType: false,
            cache: false,
            processData:false,
            success     : function(data){
                setTimeout(function() {
                    button.fadeOut(function(){
                        $("#button").removeClass("submit");
                        button.empty().html('<i class="fas fa-check"></i>').fadeIn("slow");
                    });
                }, 2800);         
            }
         });
        return false;
    });
 });
</script>