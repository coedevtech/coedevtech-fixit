<?php

namespace Fixit\Support;

use RuntimeException;

class SecureEncryptor
{
    // The 256-bit (32-byte) encryption key
    protected string $key;

    /**
     * Initialize the encryptor with a key from the environment.
     * The key must be base64-encoded and exactly 32 bytes when decoded.
     */
    public function __construct()
    {
        $base64Key = env('FIXIT_ENCRYPTION_KEY');

        if (!$base64Key) {
            throw new RuntimeException('FIXIT_ENCRYPTION_KEY is not set.');
        }

        $this->key = base64_decode($base64Key);

        if (strlen($this->key) !== 32) {
            throw new RuntimeException('Encryption key must be 256 bits (32 bytes).');
        }
    }

    /**
     * Encrypt a value (string or any serializable data).
     * Strings are marked with a prefix to distinguish them from JSON-encoded data.
     */
    public function encrypt(mixed $input): string
    {
        // Mark strings explicitly to distinguish them from JSON
        if (is_string($input)) {
            $encoded = '__string__' . $input;
        } else {
            $encoded = json_encode($input);
        }

        // Generate a random 16-byte IV for AES-256-CBC
        $iv = random_bytes(16);

        // Perform the encryption
        $ciphertext = openssl_encrypt($encoded, 'aes-256-cbc', $this->key, 0, $iv);

        // Concatenate IV and ciphertext, then base64 encode
        return base64_encode($iv . $ciphertext);
    }

    /**
     * Decrypt an encrypted string.
     * Automatically handles strings and JSON-encoded values.
     */
    public function decrypt(string $encoded, bool $asArray = true): mixed
    {
        // Decode base64 input into raw IV + ciphertext
        $raw = base64_decode($encoded);
        $iv = substr($raw, 0, 16);
        $ciphertext = substr($raw, 16);

        // Decrypt the ciphertext
        $plaintext = openssl_decrypt($ciphertext, 'aes-256-cbc', $this->key, 0, $iv);

        if ($plaintext === false) {
            throw new RuntimeException('Unable to decrypt data.');
        }

        // Return raw string if it was originally a string
        if (str_starts_with($plaintext, '__string__')) {
            return substr($plaintext, 10); // remove string marker
        }

        // Otherwise decode as JSON (array or object based on $asArray)
        return json_decode($plaintext, $asArray);
    }
}
