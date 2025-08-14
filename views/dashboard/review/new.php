<?php
use App\URL\CreateUrl;

$title = "New Review";
?>

<h3 class="title-page">New Review</h3>

<form class="dashboard-form" action="<?= CreateUrl::url('ajax/addFilmAjax'); ?>" method="post" enctype="multipart/form-data">
    <div class="input">
        <label for="author">Your Name</label>
        <input type="text" name="author" id="author" value="" aria-describedby="authorInfo" placeholder="Write your name">
        <small id="authorInfo">Your name must not exceed 20 characters</small>
    </div>

    <h4>Film's Information</h4>

    <div class="input">
        <label for="title">Title</label>
        <input type="text" name="title" id="title" value="" aria-describedby="titleInfo" placeholder="Write your title">
    </div>

    <div class="picture poster">
        <div class="poster-input">
            <label for="poster">Upload</label>
            <input type="file" name="poster" id="poster">
            <button type="button"  class="delete-file" ><i class="fas fa-times-circle"></i></button>
            <small>Add the poster of the film</small>
        </div>
        <div class="poster-preview">
            <img src="" alt="Poster Preview" class="poster-preview-image">
            <span class="poster-preview-text">Poster Preview</span>
        </div>
    </div>

    <div class="input">
        <label for="date" class="choose-year">Year</label>
        <select id="date" name="date">
            <script>
                var movieDate = new Date();
                var year = movieDate.getFullYear();
                for(var i = year; i >= 1900; i--){
                    document.write('<option value="'+i+'">'+i+'</option>');
                }
            </script>
        </select>
    </div>

    <div class="input">
        <label for="director">Director</label>
        <input type="text" name="director" id="director" value="" aria-describedby="directorInfo">
    </div>

    <div class="input">
        <label for="writer">Writer</label>
        <input type="text" name="writer" id="writer" value="" aria-describedby="writerInfo">
    </div>

    <div class="input">
        <label for="cast">Starring</label>
        <input type="text" name="cast" id="cast" value="" aria-describedby="productionInfo">
    </div>

    <div class="input">
        <label for="production">Production</label>
        <input type="text" name="production" id="production" value="" aria-describedby="productionInfo">
    </div>
    
    <div class="input">
        <label for="genre">Genre</label>
        <input type="text" name="genre" id="genre" value="" aria-describedby="genreInfo">
    </div>

    <div class="textarea">
        <label for="synopsis">Synopsis</label>
        <textarea type="text" name="synopsis" id="synopsis" rows="20"></textarea>
    </div>
    
    <h4>Your opinion</h4>

    <div class="textarea">
        <label for="review">Your review</label>
        <textarea type="text" name="review" id="review" rows="20"></textarea>
    </div>

    <div class="range-score">
        <label for="score">Your score</label>
        <div class="score-content">
            <div id="list-score-option">
                <?php
                    for ($i = 0; $i <= 5; $i++){
                        echo "<span class='score-option'>$i</span>";
                    }
                ?>
            </div>
            <input type="range" id="score" name="score" min="0" max="5" value="0">
            <div class="container-value">
                <output id="score-value"></output>
            </div>
        </div>
    </div> 

    <div>
        <p id="message"></p>
    </div>
    
    <div class="button">
        <button id="validateForm" type="submit" name="submit"><span>Submit</span></button>
    </div>
</form>

<script>
    var inputPoster      = document.getElementById("poster");
    var previewImage     = document.querySelector(".poster-preview-image");
    var previewText      = document.querySelector(".poster-preview-text");
    var buttonDeleteFile = document.querySelector(".delete-file");

    window.addEventListener("load", function(){
        inputPoster.value='';
        showSliderValue();
    })

    inputPoster.addEventListener("change", function(){
        var file = this.files[0];

        if(file){
            var fileReader = new FileReader();

            previewText.style.display       = "none";
            previewImage.style.display      = "block";
            buttonDeleteFile.style.display  = "inline";

            fileReader.addEventListener("load", function(){
                previewImage.setAttribute("src", this.result);
            });
            fileReader.readAsDataURL(file);
        }else{
            previewText.style.display   = null;
            previewImage.style.display  = null;
            button.style.display        = null;
        }
    })

    buttonDeleteFile.addEventListener("click", function(){
        inputPoster.value               =''; 
        previewImage.style.display      = null; 
        previewText.style.display       = null;
        buttonDeleteFile.style.display  = "none";
    })
    
    var slider  = document.getElementById("score");
    var score   = document.getElementById("score-value");
    slider.addEventListener("input", showSliderValue, false);

    function showSliderValue(){
        score.innerHTML   = slider.value;
        var scorePosition = (slider.value / slider.max);
        score.style.left  = (scorePosition * 382) + "px";
    }

    var scoreOption = document.querySelectorAll('.score-option');
    for (var i=0; i<scoreOption.length; i++) {
        var valueOption           = scoreOption[i].innerHTML;
        var lastScoreOption       = document.getElementById('list-score-option').lastElementChild.innerHTML;
        var scoreOptionPosition   = (valueOption / lastScoreOption);
        scoreOption[i].style.left = (scoreOptionPosition * 335) + "px";
    }

    var form        = document.querySelector('form');
    var url         = form.action;
    var message     = document.getElementById('message');
    var button      = document.getElementById('validateForm');
    var error;

    form.addEventListener('submit', function (e){
        e.preventDefault();

        var data        = new FormData(form);
        var httpRequest = new XMLHttpRequest();

        document.querySelectorAll(".error").forEach(element => element.remove());
        
        button.className = 'submit';
        fadeOut(button.firstChild, replaceBySpinner);
        
        var inputs = this.querySelectorAll("input");
        for(var i = 0; i < inputs.length; i++){
            if (!inputs[i].value){
                error = "Please please fill all fields";
                setTimeout(function() {
                    message.innerHTML = error;
                    message.className = 'error';
                }, 2000);
                setTimeout(function() {
                    fadeOut(button.firstChild, replaceBySubmit);
                }, 2800);
                break;
            }
        }

        function fadeOut(element, callback){
            if(element.style.opacity == ""){
                element.style.opacity = 1;
            }
            var intervalFadeOut = setInterval(function(){
                if (element.style.opacity > 0){
                    element.style.opacity -= 0.01;
                }else{
                    clearInterval(intervalFadeOut);
                    callback(element);
                }
            }, 10)
        }

        function replaceBySpinner(element){
            var newSpanButton = document.createElement('i');
                newSpanButton.classList.add("fas");
                newSpanButton.classList.add("fa-spinner");
                newSpanButton.style.opacity = 0;
                button.replaceChild(newSpanButton, element);
                fadeIn(newSpanButton);
        }

       function replaceBySubmit(element){
            var newSpanButton = document.createElement('span');
                submit = document.createTextNode('submit');
                newSpanButton.appendChild(submit);
                newSpanButton.style.opacity = 0;
                button.replaceChild(newSpanButton, element);
                button.classList.remove('submit');
                fadeIn(newSpanButton);
        }

        function replaceByValid(element){
            var newSpanButton = document.createElement('i');
                newSpanButton.classList.add("fas");
                newSpanButton.classList.add("fa-check");
                newSpanButton.style.opacity = 0;
                button.replaceChild(newSpanButton, element);
                button.classList.remove('submit');
                fadeIn(newSpanButton);
        }

        function fadeIn(element){
            opacity = Number(window.getComputedStyle(element).getPropertyValue("opacity"));

            var intervalFadeIn = setInterval(function(){
                if (opacity <  1){
                    opacity = opacity + 0.1;
                    element.style.opacity = opacity;
                }else{
                    clearInterval(intervalFadeIn);
                }
            }, 100)
        }

        function displayPosted(dataPosted){
            setTimeout(function() {
                message.innerHTML = dataPosted.good;
                message.className = 'valid';
            }, 2000);
            setTimeout(function() {
                fadeOut(button.firstChild, replaceByValid);
            }, 2800);
        }

        function displayError(dataError){  
            setTimeout(function() {
                for(let field in dataError.error){
                    createElementError(field, dataError.error[field]);
                }
            }, 2000);
            setTimeout(function() {
                fadeOut(button.firstChild, replaceBySubmit);
            }, 2800);    
        }

        function createElementError(nameField, errorField){
            errorField.forEach(function(value){
                let errorElement = document.createElement('p');
                errorElement.textContent = value;
                errorElement.className = "error";
                let idField = document.getElementById(nameField).parentElement;
                idField.appendChild(errorElement);
            })
        }
                
        httpRequest.onreadystatechange = function(){
            if(httpRequest.readyState === 4 && httpRequest.status === 200){
                let dataResult = JSON.parse(httpRequest.responseText);
                if(dataResult.status === 'error'){
                    displayError(dataResult);
                }else{
                    displayPosted(dataResult);
                }
            }
        }
        httpRequest.open('POST', url, true);
        httpRequest.send(data);
    })
</script>