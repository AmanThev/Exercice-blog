<?php

use App\URL\CreateUrl;

$title = "Register"
?>

<h1 class="signup">Sign up</h1>

<section class="signup">
    <form>
        <div class="inputBox">
            <input type="text" name="name" id="name" required>
            <label for="name">Username</label>
        </div>
        <div class="inputBox">
            <input type="text" name="email" id="email" required>
            <label for="email">Email</label>
        </div>
        <div class="inputBox">
            <input type="password" name="password" id="password" required>
            <label class="password" for="password">Password</label>
        </div>
        <div class="inputBox">
            <input type="password" name="password2" id="password2" required>
            <label for="password2">Confirm password</label>
        </div>
          <input type="submit" name="signup" value="Sign up">
      </form>
      <p>Already have an account, <a href="<?= CreateUrl::url('user/login') ?>" class=""> login</a></p>
</section>