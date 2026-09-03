<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

$pageTitle = 'Skapa grupp';

$errors = [];
$name = '';
$topic = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $topic = trim((string) ($_POST['topic'] ?? ''));

    if ($name === '') {
        $errors[] = 'Gruppnamn krävs.';
    }

    if ($topic === '') {
        $errors[] = 'Ämne krävs.';
    }

    if ($errors === []) {
        $pdo->beginTransaction();

        try {
            $statement = $pdo->prepare(
                'INSERT INTO forum_groups (
                    name,
                    topic,
                    created_by
                ) VALUES (
                    :name,
                    :topic,
                    :created_by
                )'
            );

            $statement->execute([
                'name' => $name,
                'topic' => $topic,
                'created_by' => (int) $_SESSION['user_id'],
            ]);

            $groupId = (int) $pdo->lastInsertId();

            $memberStatement = $pdo->prepare(
                'INSERT INTO group_members (
                    group_id,
                    user_id
                ) VALUES (
                    :group_id,
                    :user_id
                )'
            );

            $memberStatement->execute([
                'group_id' => $groupId,
                'user_id' => (int) $_SESSION['user_id'],
            ]);

            $pdo->commit();

            header('Location: /groups.php');
            exit;
        } catch (Throwable $exception) {
            $pdo->rollBack();

            $errors[] = 'Gruppen kunde inte skapas.';
        }
    }
}

require dirname(__DIR__) . '/templates/layout/header.php';
?>

<section class="section">
    <div class="container">
        <p class="eyebrow">Ny grupp</p>

        <h1>Skapa grupp</h1>

        <p>
            Skapa en grupp för ett ämne som du vill diskutera med andra.
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
            <div class="form-group">
                <label for="name">Gruppnamn</label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="<?= e($name) ?>"
                    maxlength="150"
                    required
                >
            </div>

            <div class="form-group">
                <label for="topic">Ämne</label>

                <input
                    type="text"
                    id="topic"
                    name="topic"
                    value="<?= e($topic) ?>"
                    maxlength="255"
                    required
                >
            </div>

            <button type="submit" class="button">
                Skapa grupp
            </button>
        </form>
    </div>
</section>

<?php
require dirname(__DIR__) . '/templates/layout/footer.php';