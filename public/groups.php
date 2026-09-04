<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

$pageTitle = 'Grupper';

$statement = $pdo->prepare(
    'SELECT
        forum_groups.id,
        forum_groups.name,
        forum_groups.topic,
        forum_groups.created_at,
        users.first_name,
        users.last_name
     FROM forum_groups
     INNER JOIN users
        ON users.id = forum_groups.created_by
     ORDER BY forum_groups.created_at DESC'
);

$statement->execute();

$groups = $statement->fetchAll();

require dirname(__DIR__) . '/templates/layout/header.php';
?>

<section class="section">
    <div class="container">
        <p class="eyebrow">Community</p>

        <h1>Grupper</h1>

        <p>
            Hitta grupper som intresserar dig eller skapa en egen grupp.
        </p>

        <div class="hero-actions">
            <a class="button" href="/create-group.php">
                Skapa grupp
            </a>
        </div>

        <?php if ($groups === []): ?>

            <p>Det finns inga grupper ännu.</p>

        <?php else: ?>

            <div class="feature-grid">

                <?php foreach ($groups as $group): ?>

                    <article class="feature-card">

                        <span class="feature-icon" aria-hidden="true">
                            #
                        </span>

                        <h3>
                            <?= e($group['name']) ?>
                        </h3>

                        <p>
                            <?= e($group['topic']) ?>
                        </p>

                        <p>
                            Skapad av
                            <?= e($group['first_name']) ?>
                            <?= e($group['last_name']) ?>
                        </p>

                        <div class="hero-actions">
                            <a
                                class="button"
                                href="/group.php?id=<?= (int) $group['id'] ?>"
                            >
                                Visa grupp
                            </a>
                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>
</section>

<?php
require dirname(__DIR__) . '/templates/layout/footer.php';