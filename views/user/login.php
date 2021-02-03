<?php

use App\URL\CreateUrl;

$title = "Login"
?>

<h1 class="login">Login</h1>

<section class="login">
    <form>
        <div class="inputBox">
            <input type="text" name="name" id="name" required>
            <label for="name">Username</label>
        </div>
        <div class="inputBox">
            <input type="password" name="password" id="password" required>
            <label class="password" for="password">Password</label>
        </div>
          <input type="submit" name="login" value="Login">
      </form>
      <p>New around here? <a href="<?= CreateUrl::url('authentication/register') ?>" class="">Sign up</a></p>
</section>