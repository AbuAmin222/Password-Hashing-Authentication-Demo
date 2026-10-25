# Password Hashing with Salt

Chapter 03 (User Authentication) programming task for Software Security (SDEV 4303), Islamic University of Gaza.

A small PHP program that registers users and logs them in. 
Each password is combined with a random salt and hashed with SHA-256. 
Only the login name, the salt, and the hash are stored in a text file, so the real password is never saved.

## Requirements

- PHP

## Run

```
Password Auth.php
```

Menu: 1) Register, 2) Login, 3) Exit.

## Storage format

Users are saved in `users.txt`, one line per user:

```
login_name:salt:hash
```

- `salt`: 16 random bytes, written in hex
- `hash`: SHA-256 of (password + salt), written in hex

## How it works

1. Register: generate a random salt, compute SHA-256(password + salt), append the line to `users.txt`.
2. Login: read the user's salt from the file, hash the typed password with it, and compare with the stored hash.
3. Two users with the same password get different hashes because their salts are different.
