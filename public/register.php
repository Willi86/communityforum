<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

session_start();

$pageTitle = 'Skapa konto';
$baseUrl = '';

$errors = [];
$firstName = '';
$lastName = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim((string) ($_POST['first_name'] ?? ''));
    $lastName = trim((string) ($_POST['last_name'] ?? ''));
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');
    $passwordConfirm = (string) ($_POST['password_confirm'] ?? '');

    if ($firstName === '') {
        $errors[] = 'Förnamn krävs.';
    }

    if ($lastName === '') {
        $errors[] = 'Efternamn krävs.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Ange en giltig e-postadress.';
    }

    if (strlen($password) < 8) {
        $errors[] = 'Lösenordet måste vara minst 8 tecken.';
    }

    if ($password !== $passwordConfirm) {
        $errors[] = 'Lösenorden matchar inte.';
    }

    if ($errors === []) {
        $statement = $pdo->prepare(
            'SELECT id FROM users WHERE email = :email LIMIT 1'
        );

        $statement->execute([
            'email' => $email,
        ]);

        if ($statement->fetch()) {
            $errors[] = 'Det finns redan ett konto med den e-postadressen.';
        }
    }

    if ($errors === []) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $statement = $pdo->prepare(
            'INSERT INTO users (
                first_name,
                last_name,
                email,
                password_hash
            ) VALUES (
                :first_name,
                :last_name,
                :email,
                :password_hash
            )'
        );

        $statement->execute([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password_hash' => $passwordHash,
        ]);

        session_regenerate_id(true);

        $_SESSION['user_id'] = (int) $pdo->lastInsertId();

        header('Location: /');
        exit;
    }
}

require dirname(__DIR__) . '/templates/layout/header.php';
?>

<section class="section">
    <div class="container">
        <p class="eyebrow">Bli medlem</p>
        <h1>Skapa konto</h1>

        <p>
            Registrera dig för att kunna gå med i grupper
            och delta i diskussioner.
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
                <label for="first_name">Förnamn</label>

                <input
                    type="text"
                    id="first_name"
                    name="first_name"
                    value="<?= e($firstName) ?>"
                    autocomplete="given-name"
                    required
                >
            </div>

            <div class="form-group">
                <label for="last_name">Efternamn</label>

                <input
                    type="text"
                    id="last_name"
                    name="last_name"
                    value="<?= e($lastName) ?>"
                    autocomplete="family-name"
                    required
                >
            </div>

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
                    autocomplete="new-password"
                    minlength="8"
                    required
                >
            </div>

            <div class="form-group">
                <label for="password_confirm">Bekräfta lösenord</label>

                <input
                    type="password"
                    id="password_confirm"
                    name="password_confirm"
                    autocomplete="new-password"
                    minlength="8"
                    required
                >
            </div>

            <button type="submit" class="button">
                Skapa konto
            </button>
        </form>
    </div>
</section>

<?php
require dirname(__DIR__) . '/templates/layout/footer.php';