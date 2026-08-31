<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';


if (isset($_SESSION['user_id'])) {
    header('Location: /');
    exit;
}

$pageTitle = 'Logga in';

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = 'Fyll i e-post och lösenord.';
    } else {
        $statement = $pdo->prepare(
            'SELECT id, password_hash
             FROM users
             WHERE email = :email
             LIMIT 1'
        );

        $statement->execute([
            'email' => $email,
        ]);

        $user = $statement->fetch();

        if (
            !$user ||
            !password_verify($password, $user['password_hash'])
        ) {
            $error = 'Fel e-post eller lösenord.';
        } else {
            session_regenerate_id(true);

            $_SESSION['user_id'] = (int) $user['id'];

            header('Location: /');
            exit;
        }
    }
}

require dirname(__DIR__) . '/templates/layout/header.php';
?>

<section class="section">
    <div class="container">
        <p class="eyebrow">Välkommen tillbaka</p>
        <h1>Logga in</h1>

        <p>
            Logga in för att komma åt dina grupper och diskussioner.
        </p>

        <?php if ($error !== ''): ?>
            <div class="form-errors" role="alert">
                <p><?= e($error) ?></p>
            </div>
        <?php endif; ?>

        <form method="post" class="form-card">
            <div class="form-group">
                <label for="email">E-post</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= e($email) ?>"
                    autocomplete="email"
                    required
                >
            </div>

            <div class="form-group">
                <label for="password">Lösenord</label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    autocomplete="current-password"
                    required
                >
            </div>

            <button type="submit" class="button">
                Logga in
            </button>
        </form>
    </div>
</section>

<?php
require dirname(__DIR__) . '/templates/layout/footer.php';