# The Cryptographer
## Generate the passphrase once and save it securely in your configuration
> **IMPORTANT:** Ensure this passphrase is stored in a secure location (e.g., environment variables, secrets manager)

###  PHP:
```php
$algo       = "aes-256-cfb";
$key_lenght = openssl_cipher_key_length();
$passphrase = openssl_random_pseudo_bytes(32);

// if you're planing to save this to the environment, better to make base64
$passphrase_base64 = base64_encode($passphrase);
```

### Bash:
```bash
echo $(openssl rand -base64 32)
```

## Use cases:
### PHP Example:
```php
// Later use the saved passphrase to initialize the cryptographer
// ATTENTION: Don't use this passphrase in production
$passphrase    = base64_decode("HlngqpWdmYx4QmYuiO4zCYpOX65qhpfmBPqlAgQuxAk=");

$cryptographer = new Encryption(
    algo: "aes-256-cfb",
    passphrase: $passphrase,
);


$original_message = json_encode([
    "user_id" => 12345,
    "action" => "update_profile",
    "timestamp" => time(),
]);

// Encrypt and serialize the message
$serialized_payload = $cryptographer->encrypt(body: $original_message)->serialize();

// Deserialize and decrypt the message
$restored_message = $cryptographer->decryptPayload(
    payload: EncryptedPayload::deserialize(
        algo: $cryptographer->algo,
        serialized_payload: $serialized_payload
    )
);

echo $restored_message;
```