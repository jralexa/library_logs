<?php
// Shared document start layout.
if (!isset($page_title)) {
    $page_title = 'Library Visitor Log System';
}
if (!isset($styles)) {
    $styles = [];
}
if (!isset($body_class)) {
    $body_class = '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($page_title); ?></title>
    <?php foreach ($styles as $href): ?>
        <link rel="stylesheet" href="<?php echo h($href); ?>">
    <?php endforeach; ?>
</head>
<body class="<?php echo h($body_class); ?>">
