document.addEventListener('DOMContentLoaded', function () {
    var input   = document.getElementById('picture');
    var preview = document.getElementById('picture-preview');
    var clear   = document.getElementById('clear-picture');
    if (!input || !preview) return;

    var img = preview.querySelector('img');

    function reset() {
        if (img.src && img.src.indexOf('blob:') === 0) {
            URL.revokeObjectURL(img.src);
        }
        input.value = '';
        img.removeAttribute('src');
        preview.hidden = true;
        if (clear) clear.hidden = true;
    }

    input.addEventListener('change', function () {
        var file = input.files[0];
        if (!file) { reset(); return; }

        img.src = URL.createObjectURL(file);
        preview.hidden = false;
        if (clear) clear.hidden = false;
    });

    if (clear) clear.addEventListener('click', reset);
});