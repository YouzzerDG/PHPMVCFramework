<?php require_once 'init.php'; ?>
<!doctype html>
<html lang="en">
<head>
    <title><?= $pageTitle ?? ''; ?></title>
    <meta name="description" content="<?= $metaDescription ?? ''; ?>"/>
    <meta charset="utf-8"/>
</head>
<body>
<?php require_once "app/views/templates/include/header.php" ?>

<?= $content ?? 'leeeeeg' ?>

<p style="color: crimson;">index php</p>

<?php require_once "app/views/templates/include/footer.php" ?>
</body>
</html>
