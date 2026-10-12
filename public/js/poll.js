/*
 * Poll of the home page: the vote is sent without reloading the page.
 * Expects: <form id="poll-form"> inside <div id="poll-content">, and <p id="poll-message">.
 * The server answers with JSON: { status, message, html } where html is the new content of the poll (the results).
 */
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('poll-form');
    if (!form) {
        return; // visitor not logged in, or already voted: the results are shown, nothing to send
    }

    var content = document.getElementById('poll-content');
    var message = document.getElementById('poll-message');
    var button  = form.querySelector('button[type="submit"]');

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        message.textContent = '';
        button.disabled = true;

        fetch(form.action, {
            method:      'POST',
            body:        new FormData(form),
            headers:     { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin'
        })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (data.html) {
                    content.innerHTML = data.html; // the form disappears, the bars appear
                } else {
                    button.disabled = false;
                }
                message.textContent = data.message || '';
            })
            .catch(function () {
                button.disabled = false;
                message.textContent = 'Something went wrong, please try again.';
            });
    });
});
