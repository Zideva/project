<?php
// File untuk manajemen user dan pendaftaran
$user_file = __DIR__ . '/../data/users.json';

function init_user_file() {
    global $user_file;
    if (!file_exists($user_file)) {
        $default_users = [
            [
                'id' => 1,
                'username' => 'admin',
                'password' => password_hash('password', PASSWORD_DEFAULT),
                'role' => 'admin'
            ]
        ];
        file_put_contents($user_file, json_encode($default_users, JSON_PRETTY_PRINT));
    }
}

function get_all_users() {
    global $user_file;
    init_user_file();
    $json = file_get_contents($user_file);
    $users = json_decode($json, true);
    return $users ? $users : [];
}

function get_user_by_username($username) {
    $users = get_all_users();
    foreach ($users as $user) {
        if ($user['username'] === $username) {
            return $user;
        }
    }
    return null;
}

function register_user($username, $password) {
    global $user_file;
    $username = trim($username);
    if ($username === '') {
        return [false, 'Username tidak boleh kosong.'];
    }

    if (get_user_by_username($username)) {
        return [false, 'Username sudah digunakan.'];
    }

    $users = get_all_users();
    $new_id = 1;
    foreach ($users as $user) {
        if ($user['id'] >= $new_id) {
            $new_id = $user['id'] + 1;
        }
    }

    $users[] = [
        'id' => $new_id,
        'username' => $username,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'role' => 'user'
    ];

    file_put_contents($user_file, json_encode($users, JSON_PRETTY_PRINT));
    return [true, ''];
}
?>