<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $pageInfo['pageDescription'] ?? '' ?>">
    <meta name="title" content="<?= $pageInfo['pageTitle'] ?? '' ?>">
    <title><?= $pageInfo['pageTitle'] ?? '' ?></title>
</head>
    <body>
        <?= $renderData ?? '' ?>
        <footer>
            footer!
        </footer>
    </body>
</html>