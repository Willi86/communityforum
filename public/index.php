<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

$pageTitle = 'CommunityHub';
$baseUrl = '/communityforum/public';

require dirname(__DIR__) . '/templates/layout/header.php';

?>

<section class="hero">
    <div class="container hero-content">

        <p class="eyebrow">Community för alla dina intressen</p>

        <h1>Hitta människor som gillar samma saker som du.</h1>

        <p class="hero-text">
            Skapa ett konto, hitta grupper som intresserar dig
            och delta i diskussioner tillsammans med andra.
        </p>

        <div class="hero-actions">
            <a
                class="button"
                href="<?= $baseUrl ?>/register.php"
            >
                Skapa konto
            </a>

            <a
                class="button button-secondary"
                href="<?= $baseUrl ?>/login.php"
            >
                Logga in
            </a>
        </div>

    </div>
</section>

<section class="section">
    <div class="container">

        <p class="eyebrow">Så fungerar det</p>

        <div class="steps">

            <article class="step-card">
                <span class="step-number">1</span>

                <div>
                    <h2>Skapa ett konto</h2>

                    <p>
                        Registrera dig med namn och e-post.
                    </p>
                </div>
            </article>

            <article class="step-card">
                <span class="step-number">2</span>

                <div>
                    <h2>Hitta en grupp</h2>

                    <p>
                        Ansök till grupper som matchar dina intressen.
                    </p>
                </div>
            </article>

            <article class="step-card">
                <span class="step-number">3</span>

                <div>
                    <h2>Börja diskutera</h2>

                    <p>
                        Starta ämnen och svara på andra medlemmars inlägg.
                    </p>
                </div>
            </article>

        </div>

    </div>
</section>

<section class="section section-muted">
    <div class="container">

        <p class="eyebrow">Enkel gemenskap</p>

        <h2>
            Allt du behöver för att prata om det som intresserar dig.
        </h2>

        <div class="feature-grid">

            <article class="feature-card">
                <span class="feature-icon" aria-hidden="true">#</span>

                <h3>Intressegrupper</h3>

                <p>
                    Skapa egna grupper eller hitta communities som redan finns.
                </p>
            </article>

            <article class="feature-card">
                <span class="feature-icon" aria-hidden="true">💬</span>

                <h3>Diskussioner</h3>

                <p>
                    Starta nya ämnen och håll samtalet levande
                    med andra medlemmar.
                </p>
            </article>

            <article class="feature-card">
                <span class="feature-icon" aria-hidden="true">✓</span>

                <h3>Kontrollerat medlemskap</h3>

                <p>
                    Gruppmedlemmar kan behandla nya ansökningar
                    innan någon får tillgång.
                </p>
            </article>

        </div>

    </div>
</section>

<?php

require dirname(__DIR__) . '/templates/layout/footer.php';