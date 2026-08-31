<?php
declare(strict_types=1);

$pageTitle = $pageTitle ?? 'CommunityHub';
$baseUrl = '';

$currentUser = null;

if (isset($_SESSION['user_id'])) {
    $statement = $pdo->prepare(
        'SELECT id, first_name, last_name, email
         FROM users
         WHERE id = :id
         LIMIT 1'
    );

    $statement->execute([
        'id' => (int) $_SESSION['user_id'],
    ]);

    $currentUser = $statement->fetch() ?: null;
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta
        name="description"
        content="CommunityHub - hitta grupper och diskutera dina intressen."
    >

    <title><?= e($pageTitle) ?></title>

    <link
        rel="stylesheet"
        href="<?= $baseUrl ?>/assets/css/app.css"
    >
</head>

<body>
<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="<?= $baseUrl ?>/">
            <span class="brand-mark" aria-hidden="true">C</span>
            <span>CommunityHub</span>
        </a>

        <nav class="main-nav" aria-label="Huvudmeny">
            <a href="<?= $baseUrl ?>/">
                Startsida
            </a>

            <?php if ($currentUser): ?>
                <span>
                    Hej, <?= e($currentUser['first_name']) ?>
                </span>

                <a
                    class="button button-small button-secondary"
                    href="<?= $baseUrl ?>/logout.php"
                >
                    Logga ut
                </a>
            <?php else: ?>
                <a
                    class="button button-small button-secondary"
                    href="<?= $baseUrl ?>/login.php"
                >
                    Logga in
                </a>

                <a
                    class="button button-small"
                    href="<?= $baseUrl ?>/register.php"
                >
                    Skapa konto
                </a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main>