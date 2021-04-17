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
        <p style="color: red;" id="error"></p>
    </div>

    <div class="button">
        <button id="button" type="submit" name="submit">Submit</button>
    </div>

</form>
<div id="messages"></div>

<script>
var url="ajaxUpload";

$(function(){
    $("form").submit(function(e){
        e.preventDefault();
        var error; 

		if (!$('input:text').val()) {
			error = "Please complete all fields!";
			document.getElementById("error").innerHTML = error;
            return false;
        }

        var formData = new FormData(); 
        var author = $("#author").val();
            formData.append('author', author);
        var titlePost = $("#titlePost").val();
            formData.append('titlePost', titlePost);
        var content = $("#content").val();
            formData.append('content', content);
        // var picture = $('#picture').prop('files')[0];
        //     formData.append('picture', picture);
        // var checkbox = $("#checkbox").is(':checked');
        //     formData.append('public', checkbox);

        $.ajax({
            url: url,
            type: "POST",
            data:  formData,
            contentType: false,
            cache: false,
            processData:false,
            success     : function(data){
                alert("ok");              
            }
         });
        return false;
    });
 });

 

    // $(function () {
    //     $("#button").click(function () {
    //         $("#button").addClass("onclic", 250, validate);
    //     });
        
    //     function validate() {
    //         setTimeout(function () {
    //             $("#button").removeClass("onclic");
    //             $("#button").addClass("validate", 450, callback);
    //         }, 2250);
    //     }
    //     function callback() {
    //         setTimeout(function () {
    //             $("#button").removeClass("validate");
    //         }, 1250);
    //     }
    // });
</script>