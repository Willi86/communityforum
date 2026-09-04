<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

$userId = (int) $_SESSION['user_id'];
$groupId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$groupId) {
    http_response_code(404);
    exit('Gruppen kunde inte hittas.');
}

/*
 * Get the group.
 */
$statement = $pdo->prepare(
    'SELECT
        forum_groups.id,
        forum_groups.name,
        forum_groups.topic,
        forum_groups.created_by,
        users.first_name,
        users.last_name
     FROM forum_groups
     INNER JOIN users
        ON users.id = forum_groups.created_by
     WHERE forum_groups.id = :group_id
     LIMIT 1'
);

$statement->execute([
    'group_id' => $groupId,
]);

$group = $statement->fetch();

if (!$group) {
    http_response_code(404);
    exit('Gruppen kunde inte hittas.');
}

/*
 * Check if the current user is a member.
 */
$memberStatement = $pdo->prepare(
    'SELECT group_id
     FROM group_members
     WHERE group_id = :group_id
       AND user_id = :user_id
     LIMIT 1'
);

$memberStatement->execute([
    'group_id' => $groupId,
    'user_id' => $userId,
]);

$isMember = (bool) $memberStatement->fetch();

/*
 * Check if the user already has a join request.
 */
$requestStatus = null;

if (!$isMember) {
    $requestStatement = $pdo->prepare(
        'SELECT status
         FROM group_join_requests
         WHERE group_id = :group_id
           AND user_id = :user_id
         LIMIT 1'
    );

    $requestStatement->execute([
        'group_id' => $groupId,
        'user_id' => $userId,
    ]);

    $request = $requestStatement->fetch();

    if ($request) {
        $requestStatus = $request['status'];
    }
}

/*
 * Send a join request.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$isMember) {
    if ($requestStatus === null) {
        $joinStatement = $pdo->prepare(
            'INSERT INTO group_join_requests (
                group_id,
                user_id
            ) VALUES (
                :group_id,
                :user_id
            )'
        );

        $joinStatement->execute([
            'group_id' => $groupId,
            'user_id' => $userId,
        ]);
    }

    header('Location: /group.php?id=' . $groupId);
    exit;
}

/*
 * Get pending join requests.
 */
$pendingRequests = [];

/*
 * Get discussions.
 */
$discussions = [];

if ($isMember) {
    $pendingStatement = $pdo->prepare(
        'SELECT
            group_join_requests.id,
            users.first_name,
            users.last_name,
            users.email
         FROM group_join_requests
         INNER JOIN users
            ON users.id = group_join_requests.user_id
         WHERE group_join_requests.group_id = :group_id
           AND group_join_requests.status = :status
         ORDER BY group_join_requests.created_at ASC'
    );

    $pendingStatement->execute([
        'group_id' => $groupId,
        'status' => 'pending',
    ]);

    $pendingRequests = $pendingStatement->fetchAll();

    $discussionStatement = $pdo->prepare(
        'SELECT
            discussions.id,
            discussions.subject,
            discussions.created_at,
            users.first_name,
            users.last_name
         FROM discussions
         INNER JOIN users
            ON users.id = discussions.user_id
         WHERE discussions.group_id = :group_id
         ORDER BY discussions.created_at DESC'
    );

    $discussionStatement->execute([
        'group_id' => $groupId,
    ]);

    $discussions = $discussionStatement->fetchAll();
}

$pageTitle = $group['name'];

require dirname(__DIR__) . '/templates/layout/header.php';
?>

<section class="section">
    <div class="container">

        <p class="eyebrow">Grupp</p>

        <h1><?= e($group['name']) ?></h1>

        <p><?= e($group['topic']) ?></p>

        <p>
            Skapad av
            <?= e($group['first_name']) ?>
            <?= e($group['last_name']) ?>
        </p>

        <?php if ($isMember): ?>

            <div class="form-card">
                <h2>Du är medlem</h2>

                <p>
                    Som medlem kan du delta i gruppens diskussioner.
                </p>

                <div class="hero-actions">
                    <a
                        class="button"
                        href="/create-discussion.php?group_id=<?= (int) $group['id'] ?>"
                    >
                        Starta diskussion
                    </a>
                </div>
            </div>

            <div class="form-card">
                <h2>Diskussioner</h2>

                <?php if ($discussions === []): ?>

                    <p>Det finns inga diskussioner ännu.</p>

                <?php else: ?>

                    <?php foreach ($discussions as $discussion): ?>

                        <article>
                            <h3>
                                <a href="/discussion.php?id=<?= (int) $discussion['id'] ?>">
                                    <?= e($discussion['subject']) ?>
                                </a>
                            </h3>

                            <p>
                                Startad av
                                <?= e($discussion['first_name']) ?>
                                <?= e($discussion['last_name']) ?>
                            </p>

                            <small>
                                <?= e($discussion['created_at']) ?>
                            </small>
                        </article>

                    <?php endforeach; ?>

                <?php endif; ?>
            </div>

            <div class="form-card">
                <h2>Medlemsansökningar</h2>

                <?php if ($pendingRequests === []): ?>

                    <p>Det finns inga väntande ansökningar.</p>

                <?php else: ?>

                    <?php foreach ($pendingRequests as $pendingRequest): ?>

                        <div>
                            <p>
                                <strong>
                                    <?= e($pendingRequest['first_name']) ?>
                                    <?= e($pendingRequest['last_name']) ?>
                                </strong>
                            </p>

                            <p>
                                <?= e($pendingRequest['email']) ?>
                            </p>

                            <form
                                method="post"
                                action="/approve-request.php"
                            >
                                <input
                                    type="hidden"
                                    name="request_id"
                                    value="<?= (int) $pendingRequest['id'] ?>"
                                >

                                <button
                                    type="submit"
                                    class="button"
                                >
                                    Godkänn
                                </button>
                            </form>
                        </div>

                    <?php endforeach; ?>

                <?php endif; ?>
            </div>

        <?php elseif ($requestStatus === 'pending'): ?>

            <div class="form-card">
                <h2>Ansökan skickad</h2>

                <p>
                    Din medlemsansökan väntar på att godkännas.
                </p>
            </div>

        <?php else: ?>

            <form method="post" class="form-card">

                <h2>Gå med i gruppen</h2>

                <p>
                    Ansök om medlemskap för att kunna delta
                    i diskussionerna.
                </p>

                <button type="submit" class="button">
                    Ansök om medlemskap
                </button>

            </form>

        <?php endif; ?>

    </div>
</section>

<?php
require dirname(__DIR__) . '/templates/layout/footer.php';