<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

$userId = (int) $_SESSION['user_id'];
$discussionId = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$discussionId) {
    http_response_code(404);
    exit('Diskussionen kunde inte hittas.');
}

/*
 * Get discussion and group.
 */
$discussionStatement = $pdo->prepare(
    'SELECT
        discussions.id,
        discussions.group_id,
        discussions.subject,
        discussions.created_at,
        forum_groups.name AS group_name
     FROM discussions
     INNER JOIN forum_groups
        ON forum_groups.id = discussions.group_id
     WHERE discussions.id = :discussion_id
     LIMIT 1'
);

$discussionStatement->execute([
    'discussion_id' => $discussionId,
]);

$discussion = $discussionStatement->fetch();

if (!$discussion) {
    http_response_code(404);
    exit('Diskussionen kunde inte hittas.');
}

$groupId = (int) $discussion['group_id'];

/*
 * Only group members may view and reply.
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

if (!$isMember) {
    http_response_code(403);
    exit('Du måste vara medlem för att se diskussionen.');
}

$errors = [];
$content = '';

/*
 * Add reply.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $content = trim((string) ($_POST['content'] ?? ''));

    if ($content === '') {
        $errors[] = 'Svaret får inte vara tomt.';
    }

    if ($errors === []) {
        $replyStatement = $pdo->prepare(
            'INSERT INTO posts (
                discussion_id,
                user_id,
                content
            ) VALUES (
                :discussion_id,
                :user_id,
                :content
            )'
        );

        $replyStatement->execute([
            'discussion_id' => $discussionId,
            'user_id' => $userId,
            'content' => $content,
        ]);

        header('Location: /discussion.php?id=' . $discussionId);
        exit;
    }
}

/*
 * Get all posts in the discussion.
 */
$postsStatement = $pdo->prepare(
    'SELECT
        posts.id,
        posts.content,
        posts.created_at,
        users.first_name,
        users.last_name
     FROM posts
     INNER JOIN users
        ON users.id = posts.user_id
     WHERE posts.discussion_id = :discussion_id
     ORDER BY posts.created_at ASC, posts.id ASC'
);

$postsStatement->execute([
    'discussion_id' => $discussionId,
]);

$posts = $postsStatement->fetchAll();

$pageTitle = $discussion['subject'];

require dirname(__DIR__) . '/templates/layout/header.php';
?>

<section class="section">
    <div class="container">

        <p class="eyebrow">
            <?= e($discussion['group_name']) ?>
        </p>

        <h1><?= e($discussion['subject']) ?></h1>

        <p>
            <a href="/group.php?id=<?= $groupId ?>">
                ← Tillbaka till gruppen
            </a>
        </p>

        <div class="form-card">
            <h2>Inlägg</h2>

            <?php foreach ($posts as $post): ?>

                <article>
                    <p>
                        <strong>
                            <?= e($post['first_name']) ?>
                            <?= e($post['last_name']) ?>
                        </strong>
                    </p>

                    <p>
                        <?= nl2br(e($post['content'])) ?>
                    </p>

                    <small>
                        <?= e($post['created_at']) ?>
                    </small>

                    <hr>
                </article>

            <?php endforeach; ?>
        </div>

        <?php if ($errors !== []): ?>

            <div class="form-errors" role="alert">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

        <?php endif; ?>

        <form method="post" class="form-card">

            <?= csrf_input() ?>

            <h2>Svara</h2>

            <div class="form-group">
                <label for="content">Ditt svar</label>

                <textarea
                    id="content"
                    name="content"
                    rows="5"
                    required
                ><?= e($content) ?></textarea>
            </div>

            <button type="submit" class="button">
                Skicka svar
            </button>

        </form>

    </div>
</section>

<?php
require dirname(__DIR__) . '/templates/layout/footer.php';