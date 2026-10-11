# Cinema Blog

A movie blog with reviews, a forum and an administration dashboard, built with **PHP (OOP, MVC) and no framework**.
The design is an Art Déco cinema marquee: dark burgundy, brass and cream.

## About the project

This project is a blog developed with PHP using Object-Oriented Programming (OOP) and the MVC architecture.

The main goal of this project was to learn how to build a PHP application without using a framework, and to start working with JavaScript and AJAX.

It is a learning project: it is not hosted online.

## Features

**Public site**
* Home page with the latest post, the latest reviews and a search box
* Blog with pagination and a page for each post: comments, like / dislike
* Movie reviews: slider, filter by decade or year, a page for each film with a star rating, comments and `[Spoiler]` tags
* Forum: categories, sub-categories, topics and messages. A topic can be closed
* Search in the posts and in the reviews

**Members**
* Registration, login, logout, password reset by e-mail
* Public profile with the likes and dislikes given
* Account page: edit the description, change the password, **delete the account** (what the member wrote is kept under the name "Deleted member")

**Administration (dashboard, administrators only)**
* Counters and a summary of the site
* Posts and reviews: create, edit, delete, with an image upload and a preview
* Moderation of the comments
* Forum management: categories, sub-categories, topics (close, reopen, delete)
* List of the members and of the administrators, with their activity

## Tech stack

* PHP 8.0 or later, PDO, Composer (autoloading, [Whoops](https://github.com/filp/whoops), [var-dumper](https://symfony.com/doc/current/components/var_dumper.html))
* MySQL / MariaDB
* Apache with `mod_rewrite`
* HTML, CSS (Grid, Flexbox, custom properties, `@keyframes`), JavaScript, jQuery for the AJAX forms of the dashboard

## Project structure

```
cinema/
├── public/        the entry point (index.php), CSS, JavaScript and pictures
├── src/
│   ├── Form/      forms: validation and saving (posts, films, users, forum...)
│   ├── Manager/   access to the database, one class for each kind of data
│   ├── Model/     entities: Post, Film, Member, Admin, Comment, forum...
│   ├── Router/    the router
│   ├── Security/  CSRF protection and the administrator guard
│   ├── URL/       creation of the addresses
│   └── HTML/, SQL/, Helpers/   small tools (forms, files, texts...)
├── views/         the pages (public site, forum, user, dashboard) and the layouts
├── ajax/          the endpoints of the AJAX forms (administrators only)
├── actions/       server-side actions without a page (votes)
└── scripts/       command-line scripts
```

To start reading the code: `public/index.php` lists every route, `src/Security/` and `src/Form/DeleteAccount.php` show the security work, and `src/Manager/` contains the SQL queries and the transactions.

## Security

* **PDO prepared statements** everywhere. The names of tables that cannot be bound are checked against a whitelist.
* **CSRF token** on every form that changes something.
* **Passwords hashed with Argon2id.** The password reset tokens are stored hashed and expire after 15 minutes.
* **Separate administrator login.** Every `/dashboard` and `/ajax` route is checked in the router. The login takes the same time and shows the same message for a wrong name or a wrong password, and the session identifier is renewed when logging in and out.
* **Sessions**: `HttpOnly` and `SameSite` cookie, strict mode (an identifier chosen by somebody else is refused).
* **The author comes from the session**, never from the form: nobody can post under another name.
* **Uploaded pictures** are checked (real image, type, size) and saved under a random name.
* **Private posts** are only visible to an administrator.
* **Detailed error pages** (Whoops) are only shown on the local machine.
* **Account deletion** is done in one transaction, so the database is never left half done.

## What I learned

* **PHP**
  * Creating a simple router
  * Object-Oriented Programming (OOP), namespaces, abstract classes and traits
  * Autoloading with Composer
  * File uploads
  * Sessions, cookies and authentication
  * Protection against CSRF, XSS and SQL injection
* **CSS**
  * `@keyframes`
  * CSS Grid and Flexbox
  * Creating a slider and flip cards without JavaScript
  * A responsive layout, from a phone to a large screen
* **JavaScript**
  * Spoiler: hide an element and reveal it when hovering over it
  * Scroll to top: display a button when the user reaches the bottom of the page, and scroll back to the top when clicking it
  * Rating with stars: display the selected rating, and a different comment depending on the rating
  * Image preview when selecting a file (`URL.createObjectURL`)
  * Sort a table: sort columns alphabetically or numerically, in both directions
* **jQuery and AJAX**
  * Add a post or a film without reloading the page: success and error messages, a loading button, a success icon after the submission
* **Libraries**
  * var-dumper (helps to display the values while debugging)
  * Whoops (displays the errors)
* **MySQL**
  * phpMyAdmin
  * Inner join, right join and left join
  * PDO and transactions
* **Git / GitHub**
  * Branches
  * Commits
  * Push / pull
  * GitHub repository management
* Favicon

## What I would improve

* Automated tests (PHPUnit)
* Limit the number of login attempts
* Host the fonts and the icons on the site instead of loading them from other servers
* Make the dashboard more comfortable on a phone

## Author

Made by [AmanThev](https://github.com/AmanThev).