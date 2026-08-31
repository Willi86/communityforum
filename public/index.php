<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

$pageTitle = 'CommunityHub | Hitta ditt community';

require dirname(__DIR__) . '/templates/layout/header.php';
?>

<section class="hero">
    <div class="container hero-grid">
        <div class="hero-content">
            <span class="eyebrow">Community för alla dina intressen</span>

            <h1>Hitta människor som gillar samma saker som du.</h1>

            <p class="hero-copy">
                Skapa ett konto, hitta grupper som intresserar dig och delta i diskussioner tillsammans med andra.
            </p>

            <div class="hero-actions">
                <a class="button" href="/register.php">Skapa konto</a>
                <a class="button button-secondary" href="/login.php">Logga in</a>
            </div>
        </div>

        <aside class="hero-card" aria-label="Så fungerar CommunityHub">
            <span class="card-label">Så fungerar det</span>

            <ol class="steps">
                <li>
                    <span>1</span>
                    <div>
                        <strong>Skapa ett konto</strong>
                        <p>Registrera dig med namn och e-post.</p>
                    </div>
                </li>
                <li>
                    <span>2</span>
                    <div>
                        <strong>Hitta en grupp</strong>
                        <p>Ansök till grupper som matchar dina intressen.</p>
                    </div>
                </li>
                <li>
                    <span>3</span>
                    <div>
                        <strong>Börja diskutera</strong>
                        <p>Starta ämnen och svara på andra medlemmars inlägg.</p>
                    </div>
                </li>
            </ol>
        </aside>
    </div>
</section>

<section class="feature-section">
    <div class="container">
        <div class="section-heading">
            <span class="eyebrow">Enkel gemenskap</span>
            <h2>Allt du behöver för att prata om det som intresserar dig.</h2>
        </div>

        <div class="feature-grid">
            <article class="feature-card">
                <div class="feature-icon" aria-hidden="true">#</div>
                <h3>Intressegrupper</h3>
                <p>Skapa egna grupper eller hitta communities som redan finns.</p>
            </article>

            <article class="feature-card">
                <div class="feature-icon" aria-hidden="true">💬</div>
                <h3>Diskussioner</h3>
                <p>Starta nya ämnen och håll samtalet levande med andra medlemmar.</p>
            </article>

            <article class="feature-card">
                <div class="feature-icon" aria-hidden="true">✓</div>
                <h3>Kontrollerat medlemskap</h3>
                <p>Gruppmedlemmar kan behandla nya ansökningar innan någon får tillgång.</p>
            </article>
        </div>
    </div>
</section>

<?php require dirname(__DIR__) . '/templates/layout/footer.php'; ?>
