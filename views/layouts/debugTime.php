<style type="text/css">
    div.footer{
        width: 86%;
        margin: 2rem 0 0 12rem;
    }
</style>


<div class="bg-light py-4 footer">
    <div class="container">
        <?php if (defined('DEBUG_TIME')): ?>
            Page générée en <?= round(1000 * (microtime(true) - DEBUG_TIME)) ?> ms
        <?php endif ?>
    </div>
</div>