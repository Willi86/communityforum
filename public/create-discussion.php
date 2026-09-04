<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

$userId = (int) $_SESSION['user_id'];
$groupId = filter_input(
    INPUT_GET,
    'group_id',
    FILTER_VALIDATE_INT
);

if (!$groupId) {
    http_response_code(400);
    exit('Ogiltig grupp.');
}

/*
 * Make sure the group exists.
 */
$groupStatement = $pdo->prepare(
    'SELECT id, name
     FROM forum_groups
     WHERE id = :group_id
     LIMIT 1'
);

$groupStatement->execute([
    'group_id' => $groupId,
]);

$group = $groupStatement->fetch();

if (!$group) {
    http_response_code(404);
    exit('Gruppen kunde inte hittas.');
}

/*
 * Only members can create discussions.
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
    exit('Du måste vara medlem för att starta en diskussion.');
}

$errors = [];
$subject = '';
$content = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();

    $subject = trim((string) ($_POST['subject'] ?? ''));
    $content = trim((string) ($_POST['content'] ?? ''));

    if ($subject === '') {
        $errors[] = 'Rubrik krävs.';
    }

    if ($content === '') {
        $errors[] = 'Ett första inlägg krävs.';
    }

    if ($errors === []) {
        $pdo->beginTransaction();

        try {
            /*
             * Create discussion.
             */
            $discussionStatement = $pdo->prepare(
                'INSERT INTO discussions (
                    group_id,
                    user_id,
                    subject
                ) VALUES (
                    :group_id,
                    :user_id,
                    :subject
                )'
            );

            $discussionStatement->execute([
                'group_id' => $groupId,
                'user_id' => $userId,
                'subject' => $subject,
            ]);

            $discussionId = (int) $pdo->lastInsertId();

            /*
             * Create first post.
             */
            $postStatement = $pdo->prepare(
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

            $postStatement->execute([
                'discussion_id' => $discussionId,
                'user_id' => $userId,
                'content' => $content,
            ]);

            $pdo->commit();

            header(
                'Location: /discussion.php?id=' . $discussionId
            );
            exit;
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $errors[] = 'Diskussionen kunde inte skapas.';
        }
    }
}

$pageTitle = 'Starta diskussion';

require dirname(__DIR__) . '/templates/layout/header.php';
?>

<section class="section">
    <div class="container">

        <p class="eyebrow">Diskussion</p>

        <h1>Starta diskussion</h1>

        <p>
            Grupp:
            <strong><?= e($group['name']) ?></strong>
        </p>

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

            <div class="form-group">
                <label for="subject">Rubrik</label>

                <input
                    type="text"
                    id="subject"
                    name="subject"
                    value="<?= e($subject) ?>"
                    maxlength="200"
                    required
                >
            </div>

            <div class="form-group">
                <label for="content">Första inlägg</label>

                <textarea
                    id="content"
                    name="content"
                    rows="7"
                    required
                ><?= e($content) ?></textarea>
            </div>

            <button type="submit" class="button">
                Skapa diskussion
            </button>

        </form>

    </div>
</section>

<?php
require dirname(__DIR__) . '/templates/layout/footer.php';