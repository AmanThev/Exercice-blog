<?php

use App\URL\ExplodeUrl;
use App\HTML\Profile;


$url            = new ExplodeUrl($_GET['url']);
$slug           = $url->getSlug();

$title          = $slug;

$profile     = new Profile('members');


?>


<?= $profile->topProfile($slug); ?>

<?= $profile->bottomProfile(); ?>
<!-- <section class="setting-admin">
    <div class="single-icon">
		<div class="icon">
            <i class="fas fa-lock" aria-hidden="true"></i>
        </div>
		<div class="content-icon">
			<h3>Password</h3>
			<p>Change your password</p><a href="#">>> Click here</a>
		</div>			
    </div>
    <div class="single-icon">
        <div class="icon">
            <i class="fas fa-file-alt" aria-hidden="true"></i>
        </div>
		<div class="content-icon">
			<h3>Description</h3>
			<p>Write or change your description</p><a href="#">>> Click here</a>
		</div>			
    </div>
    <div class="single-icon">
		<div class="icon">
            <i class="fas fa-user-times" aria-hidden="true"></i>
        </div>
		<div class="content-icon">
			<h3>Unsubscribe</h3>
			<p>If you want delete your account</p><a href="#">>> Click here</a>
		</div>			
	</div>
</section> -->