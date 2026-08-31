<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

$pageTitle = 'Grupper';

require dirname(__DIR__) . '/templates/layout/header.php';
?>

<section class="section">
    <div class="container">
        <p class="eyebrow">Community</p>

        <h1>Grupper</h1>

        <p>
            Här kommer du snart kunna se grupper,
            skapa en grupp och ansöka om medlemskap.
        </p>
    </div>
</section>

<?php
require dirname(__DIR__) . '/templates/layout/footer.php';