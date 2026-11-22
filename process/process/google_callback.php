<?php
session_start();
require_once '../config/database.php';
require_once '../config/oauth_config.php';

if (GOOGLE_CLIENT_ID === 'YOUR_GOOGLE_CLIENT_ID_HERE') {
    $_SESSION['error'] = 'Google OAuth is not configured. Please set up your Google credentials in config/oauth_config.php';
    header('Location: ../index.php?page=login');
    exit();
}

if (!isset($_GET['code'])) {
    $_SESSION['error'] = 'Authorization failed. Please try again.';
    header('Location: ../index.php?page=login');
    exit();
}

$code = $_GET['code'];
$token_url = 'https://oauth2.googleapis.com/token';

$post_data = [
    'code' => $code,
    'client_id' => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri' => GOOGLE_REDIRECT_URI,
    'grant_type' => 'authorization_code'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $token_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$token_data = json_decode($response, true);

if (!isset($token_data['access_token'])) {
    $_SESSION['error'] = 'Failed to obtain access token. Please try again.';
    header('Location: ../index.php?page=login');
    exit();
}

$user_url = 'https://www.googleapis.com/oauth2/v2/userinfo';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $user_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token_data['access_token']]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$user_response = curl_exec($ch);
curl_close($ch);

$user_data = json_decode($user_response, true);

if (!isset($user_data['email'])) {
    $_SESSION['error'] = 'Failed to retrieve user information. Please try again.';
    header('Location: ../index.php?page=login');
    exit();
}

try {

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $user_data['email']]);
    $user = $stmt->fetch();

    if ($user) {

        $status = $user['status'] ?? 'approved';
        if ($status === 'pending') {
            $_SESSION['error'] = 'Your account is pending admin approval. Please wait for approval before logging in.';
            header('Location: ../index.php?page=login');
            exit();
        }
        if ($status === 'rejected') {
            $_SESSION['error'] = 'Your account registration was rejected. Please contact the administrator.';
            header('Location: ../index.php?page=login');
            exit();
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        if (isset($user['patient_id'])) {
            $_SESSION['patient_id'] = $user['patient_id'];
        }

        $stmt = $pdo->prepare("UPDATE users SET is_online = 1, last_activity = NOW() WHERE id = :id");
        $stmt->execute([':id' => $user['id']]);

        $_SESSION['success'] = 'Welcome back, ' . htmlspecialchars($user['username']) . '!';
        header('Location: ../index.php?page=dashboard');
        exit();
    } else {

        $username = explode('@', $user_data['email'])[0];
        $password = bin2hex(random_bytes(16));
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $counter = 1;
        $original_username = $username;
        while (true) {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
            $stmt->execute([':username' => $username]);
            if (!$stmt->fetch()) {
                break;
            }
            $username = $original_username . $counter;
            $counter++;
        }

        $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role, status) VALUES (:username, :password, :email, :role, 'pending')");
        $stmt->execute([
            ':username' => $username,
            ':password' => $hashed_password,
            ':email' => $user_data['email'],
            ':role' => 'visitor'
        ]);

        $_SESSION['success'] = 'Account created successfully! Your account is pending admin approval. You will be notified once approved.';
        header('Location: ../index.php?page=login');
        exit();
    }
} catch(PDOException $e) {
    $_SESSION['error'] = 'Database error: ' . $e->getMessage();
    header('Location: ../index.php?page=login');
    exit();
}
?>
