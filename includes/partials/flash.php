<?php
// Flash message block (clears message after render).
$flash = consume_flash();
?>
<?php if (!empty($flash)): ?>
    <div class="message <?php echo h($flash['type']); ?>" id="flashMessage">
        <?php echo h($flash['message']); ?>
    </div>
<?php endif; ?>
