<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}
require_once __DIR__ . '/../src/bootstrap.php';

echo "\n=== Admin Account Setup ===\n";
echo "Admin username (press Enter for admin): ";
$username = trim((string) fgets(STDIN));
$username = $username !== '' ? $username : 'admin';
if (preg_match('/^[a-zA-Z0-9_]{3,40}$/', $username) !== 1) {
    exit("Username must be 3 to 40 characters (A-Z, a-z, 0-9, or underscore).\n");
}

function hidden_password_prompt(string $prompt): string
{
    $canHide = DIRECTORY_SEPARATOR !== '\\' && function_exists('shell_exec') && is_string(shell_exec('stty -g 2>/dev/null')) && trim((string) shell_exec('stty -g 2>/dev/null')) !== '';
    if ($canHide) {
        echo $prompt;
        $settings = trim((string) shell_exec('stty -g 2>/dev/null'));
        shell_exec('stty -echo 2>/dev/null');
        try {
            return rtrim((string) fgets(STDIN), "\r\n");
        } finally {
            shell_exec('stty ' . escapeshellarg($settings) . ' 2>/dev/null');
            echo "\n";
        }
    }
    static $warningShown = false;
    if (!$warningShown) {
        echo "Note: Password input is visible in this terminal.\n";
        $warningShown = true;
    }
    echo $prompt;
    return rtrim((string) fgets(STDIN), "\r\n");
}

$password = hidden_password_prompt('Password (at least 12 characters): ');
$confirm = hidden_password_prompt('Confirm password: ');
if (strlen($password) < 12 || strlen($password) > 200 || $password !== $confirm) {
    exit("Password must be 12 to 200 characters and match the confirmation.\n");
}
try {
    $stmt = db()->prepare('INSERT INTO admins(username, password_hash) VALUES(:username, :password_hash) ON CONFLICT(username) DO UPDATE SET password_hash = excluded.password_hash');
    $stmt->execute(['username' => $username, 'password_hash' => password_hash($password, PASSWORD_DEFAULT)]);
    echo "\nAdmin account '{$username}' was created or updated successfully.\n";
} catch (Throwable $exception) {
    error_log($exception->getMessage());
    exit("Could not create or update the admin account. Check PDO_SQLITE and write permissions for the data directory.\n");
}
