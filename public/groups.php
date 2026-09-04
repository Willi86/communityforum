<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

$userId = (int) $_SESSION['user_id'];
$pageTitle = 'Grupper';

/*
 * Groups the current user is already a member of.
 */
$myGroupsStatement = $pdo->prepare(
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
     INNER JOIN group_members
        ON group_members.group_id = forum_groups.id
     WHERE group_members.user_id = :user_id
     ORDER BY forum_groups.created_at DESC'
);

$myGroupsStatement->execute([
    'user_id' => $userId,
]);

$myGroups = $myGroupsStatement->fetchAll();

/*
 * Groups the current user is not a member of.
 */
$availableGroupsStatement = $pdo->prepare(
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
     WHERE NOT EXISTS (
        SELECT 1
        FROM group_members
        WHERE group_members.group_id = forum_groups.id
          AND group_members.user_id = :user_id
     )
     ORDER BY forum_groups.created_at DESC'
);

$availableGroupsStatement->execute([
    'user_id' => $userId,
]);

$availableGroups = $availableGroupsStatement->fetchAll();

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

        <h2>Mina grupper</h2>

        <?php if ($myGroups === []): ?>

            <p>Du är inte medlem i någon grupp ännu.</p>

        <?php else: ?>

            <div class="feature-grid">

                <?php foreach ($myGroups as $group): ?>

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

        <h2>Grupper du kan gå med i</h2>

        <?php if ($availableGroups === []): ?>

            <p>Det finns inga fler grupper att gå med i just nu.</p>

        <?php else: ?>

            <div class="feature-grid">

                <?php foreach ($availableGroups as $group): ?>

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