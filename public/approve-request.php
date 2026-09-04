<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Metoden är inte tillåten.');
}

require_csrf();

$userId = (int) $_SESSION['user_id'];

$requestId = filter_input(
    INPUT_POST,
    'request_id',
    FILTER_VALIDATE_INT
);

if (!$requestId) {
    http_response_code(400);
    exit('Ogiltig ansökan.');
}

/*
 * Get the join request.
 */
$requestStatement = $pdo->prepare(
    'SELECT
        group_join_requests.id,
        group_join_requests.group_id,
        group_join_requests.user_id,
        group_join_requests.status
     FROM group_join_requests
     WHERE group_join_requests.id = :request_id
     LIMIT 1'
);

$requestStatement->execute([
    'request_id' => $requestId,
]);

$request = $requestStatement->fetch();

if (!$request || $request['status'] !== 'pending') {
    http_response_code(404);
    exit('Ansökan kunde inte hittas.');
}

$groupId = (int) $request['group_id'];
$applicantId = (int) $request['user_id'];

/*
 * Make sure the current user is already a member.
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
    exit('Du har inte behörighet att godkänna ansökan.');
}

$pdo->beginTransaction();

try {
    /*
     * Add applicant as member.
     */
    $insertMemberStatement = $pdo->prepare(
        'INSERT INTO group_members (
            group_id,
            user_id
        ) VALUES (
            :group_id,
            :user_id
        )'
    );

    $insertMemberStatement->execute([
        'group_id' => $groupId,
        'user_id' => $applicantId,
    ]);

    /*
     * Mark request as approved.
     */
    $approveStatement = $pdo->prepare(
        'UPDATE group_join_requests
         SET
            status = :status,
            reviewed_at = CURRENT_TIMESTAMP,
            reviewed_by = :reviewed_by
         WHERE id = :request_id'
    );

    $approveStatement->execute([
        'status' => 'approved',
        'reviewed_by' => $userId,
        'request_id' => $requestId,
    ]);

    $pdo->commit();
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);
    exit('Ansökan kunde inte godkännas.');
}

header('Location: /group.php?id=' . $groupId);
exit;