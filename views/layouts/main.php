<?php
/** @var string $pageTitle */
/** @var string $view */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($pageTitle ?? 'Estimateur Immobilier', ENT_QUOTES, 'UTF-8'); ?></title>
</head>
<body>
<?php require $view; ?>
</body>
</html>
