<?php

define("USERS_FILE", "users.txt");

function make_hash($password, $salt)
{
    return hash("sha256", $password . $salt);
}

function load_users()
{
    $users = [];
    if (!file_exists(USERS_FILE)) {
        return $users;
    }

    $file = fopen(USERS_FILE, "r");
    if ($file) {
        while (($line = fgets($file)) !== false) {
            $line = trim($line);
            if (!empty($line)) {
                $parts = explode(":", $line);
                if (count($parts) === 3) {
                    list($name, $salt, $hashed) = $parts;
                    $users[$name] = [$salt, $hashed];
                }
            }
        }
        fclose($file);
    }
    return $users;
}

function register($name, $password)
{
    if (empty($name) || strpos($name, ":") !== false) {
        return "Registration failed: invalid login name.";
    }
    if ($password === "" || $password === null) {
        return "Registration failed: password cannot be empty.";
    }
    if (array_key_exists($name, load_users())) {
        return "Registration failed: this login name already exists.";
    }
    $salt = bin2hex(random_bytes(16));
    $hashed = make_hash($password, $salt);

    file_put_contents(USERS_FILE, "{$name}:{$salt}:{$hashed}\n", FILE_APPEND | LOCK_EX);
    return "Registration successful.";
}

function login($name, $password)
{
    $users = load_users();
    if (!array_key_exists($name, $users)) {
        return "Login failed: wrong login name or password.";
    }
    list($salt, $stored) = $users[$name];
    if (hash_equals(make_hash($password, $salt), $stored)) {
        return "Login successful.";
    }
    return "Login failed: wrong login name or password.";
}

function getpass($prompt = "Password: ")
{
    echo $prompt;
    // قراءة كلمة المرور من سطر الأوامر
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $password = rtrim(fgets(STDIN), "\r\n");
    } else {
        system('stty -echo');
        $password = rtrim(fgets(STDIN), "\r\n");
        system('stty echo');
        echo "\n";
    }
    return $password;
}

function main()
{
    while (true) {
        echo "\n1) Register   2) Login   3) Exit\n";
        echo "Choose: ";
        $choice = trim(fgets(STDIN));

        if ($choice === "1") {
            echo "Login name: ";
            $name = trim(fgets(STDIN));
            $password = getpass("Password: ");
            echo register($name, $password) . "\n";
        } elseif ($choice === "2") {
            echo "Login name: ";
            $name = trim(fgets(STDIN));
            $password = getpass("Password: ");
            echo login($name, $password) . "\n";
        } elseif ($choice === "3") {
            break;
        } else {
            echo "Invalid choice.\n";
        }
    }
}

if (php_sapi_name() === "cli") {
    main();
}
