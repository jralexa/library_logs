<?php
// Shared document end layout.
if (!isset($scripts)) {
    $scripts = [];
}
?>
    <?php foreach ($scripts as $src): ?>
        <script src="<?php echo h($src); ?>"></script>
    <?php endforeach; ?>
</body>
</html>
