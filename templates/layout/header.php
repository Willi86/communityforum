<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? 'CommunityHub';
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="CommunityHub - hitta grupper och diskutera dina intressen.">
    <title><?= e($pageTitle) ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="/">
            <span class="brand-mark" aria-hidden="true">C</span>
            <span>CommunityHub</span>
        </a>

        <nav class="main-nav" aria-label="Huvudmeny">
            <a href="/">Startsida</a>
            <a class="button button-small button-secondary" href="/login.php">Logga in</a>
            <a class="button button-small" href="/register.php">Skapa konto</a>
        </nav>
    </div>
</header>

<main>
