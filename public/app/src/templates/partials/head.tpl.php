<?php
$title = $title ?? 'CRM Обменка';
$css = $css ?? [];
$js = $js ?? [];
$module_scripts = $module_scripts ?? [];
?>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($title) ?></title>

<?php foreach ($css as $href) : ?>
    <link rel="stylesheet" href="<?= htmlspecialchars($href) ?>">
<?php endforeach; ?>

<?php foreach ($js as $src) : ?>
    <script src="<?= htmlspecialchars($src) ?>" defer></script>
<?php endforeach; ?>

<?php foreach ($module_scripts as $src) : ?>
    <script src="<?= htmlspecialchars($src) ?>" type="module"></script>
<?php endforeach; ?>
