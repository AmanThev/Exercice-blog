<h3 class="title-page">New Post</h3>

<form class="new" action="" method="post" enctype="multipart/form-data">
    <!-- 
    title
    author / idadmin
    content
    date
    ?image
    public
     -->
    <div class="input">
        <label for="author">Your name</label>
        <input type="text" name="author" id="author" value="" aria-describedby="authorInfo" placeholder="Write your name">
        <small id="authorInfo">Your name must not exceed 20 characters</small>
    </div>

    <div class="input">
        <label for="titlePost">Post's Title</label>
        <input type="text" name="titlePost" id="titlePost" value="" aria-describedby="titleInfo" placeholder="Write your title">
    </div>

    <div class="textarea">
        <label for="content">Your post :</label>
        <textarea type="text"name="content" id="content" rows="10"></textarea>
    </div>

    <div class="picture">
        <label for="picture">Upload</label>
        <input type="hidden" name="MAX_FILE_SIZE" value="250000">
        <input type="file" id="picture" name="picture">
        <small>Choose a picture to illustrate your post</small>
    </div>

    <!-- <div>
        <input type="checkbox" id="public" name="public">
        <label for="public">Public</label>
    </div> -->

    <div>
        <label class="switch">
            <input class="switch-input" type="checkbox">
            <span class="switch-label" data-public="public" data-private="private"></span> 
            <span class="switch-handle"></span> 
        </label>
    </div>
        
    <button type="submit" name="submit">Submit</button>

</form>