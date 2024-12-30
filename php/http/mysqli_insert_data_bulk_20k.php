<?php

use AP\PerformanceTest\Encryption\Algo;
use AP\PerformanceTest\Encryption\Cryptographer;
use AP\PerformanceTest\Encryption\UnsupportedAlgorithmException;
use AP\PerformanceTest\Helpers\StringsHelper;

require __DIR__ . '/../vendor/autoload.php';

microtime(true);

function renderBinaryStringAsBytes(string $binaryString): string
{
    $bytes = array_map('ord', str_split($binaryString));
    return implode(',', $bytes);
}


//$original_message = json_encode([
//    "user_id"   => 1111111,
//    "action"    => "update_profile",
//    "timestamp" => time(),
//]);
$original_message = "";

//$cryptographer = Crypto::getInstance()->cryptographer;

$passphrase_base64 = getenv('PASSPHRASE_BASE64');

if (!is_string($passphrase_base64) || !strlen($passphrase_base64)) {
    throw new RuntimeException("pre-condition: install ENV value PASSPHRASE_BASE64");
}

$passphrase = base64_decode($passphrase_base64);


if (isset($_GET['algo'], $_GET['message_len'], $_GET['tag_len'])) {
    $algo             = $_GET['algo'];
    $original_message = StringsHelper::randomString($_GET['message_len']);
    $res              = [
        "success"   => false,
        "match"     => null,
        "tag_bites" => [],
        "error"     => [],
    ];

    try {
        $cryptographer = new Cryptographer(
            algo: $algo,
            passphrase: $passphrase,
            tag_length: (int)$_GET['tag_len']
        );

        // serialize
        $payload            = $cryptographer->encrypt(body: $original_message);
        $serialized_payload = $payload->serialize();

        // deserialize
//        $restored_payload = EncryptedPayload::deserialize(
//            algo: $cryptographer->algo,
//            serialized_payload: $serialized_payload
//        );
        //$restored_message = $cryptographer->decryptPayload(payload: $restored_payload);
        $restored_message = $cryptographer->decryptPayload(payload: $payload);

        $res['success'] = $original_message == $restored_message;
        $res['match']   = $original_message == $restored_message;

        if (is_string($payload->tag)) {
            $res['tag_bites'] = renderBinaryStringAsBytes($payload->tag);
        }
    } catch (UnsupportedAlgorithmException) {
        $res['success'] = false;
    } catch (Throwable $e) {
        $res['error'] = $e->getMessage();
    }

    echo json_encode($res);
    die;
}

$len_test               = [];
$message_len_start      = 5;
$message_len_iterations = 1;


$tag_len_start      = 0;
$tag_len_iterations = 100;

$tag_algos = array(
    0 => 'aes-128-ccm',
    1 => 'aes-128-gcm',
    2 => 'aes-128-ocb',
    3 => 'aes-192-ccm',
    4 => 'aes-192-gcm',
    5 => 'aes-192-ocb',
    6 => 'aes-256-ccm',
    7 => 'aes-256-gcm',
    8 => 'aes-256-ocb',
    9 => 'aria-128-ccm',
    10 => 'aria-128-gcm',
    11 => 'aria-192-ccm',
    12 => 'aria-192-gcm',
    13 => 'aria-256-ccm',
    14 => 'aria-256-gcm',
    15 => 'chacha20-poly1305',
);

$good = [];
foreach ($tag_algos as $algo_str) {
    $good[$algo_str] = [];
    for ($i = 0; $i < 99; $i++){
        $good[$algo_str][$i] = $i;
    }
}

for ($message_len = $message_len_start; $message_len < $message_len_start + $message_len_iterations; $message_len++) {
    for ($tag_len = $tag_len_start; $tag_len < $tag_len_start + $tag_len_iterations; $tag_len++) {
        $original_message = StringsHelper::randomString($message_len);

        $all = openssl_get_cipher_methods();

        //foreach (Algo::cases() as $algo) {
        foreach ($tag_algos as $algo_str) {
            $algo = Algo::from($algo_str);

            $body = file_get_contents("http://nginx/mysqli_insert_data_bulk_20k?algo={$algo->value}&message_len=$message_len&tag_len=$tag_len");
            $json = json_decode($body, true);
            if (!isset($json['success']) || $json['success'] === false) {
                unset($good[$algo->value][$tag_len]);
                if (!isset($len_test[$algo->value])) {
                    $len_test[$algo->value] = [];
                }
                if (!isset($len_test[$algo->value][$tag_len])) {
                    $len_test[$algo->value][$tag_len] = true;
                }
                //$len_test[$algo->value][$tag_len][$message_len] = $body;
            }
            //$len_test[$algo . ":" . $i] = $json['match'] ?? $body;
        }
    }

//
//    $len_test[$i] = [];
//
//    if (count($error)) {
//        $len_test['errors'] = $error;
//    }
//
//    if (count($no_match)) {
//        $len_test['no_match'] = $no_match;
//    }
//
//    if (!count($len_test[$i])) {
//        $len_test[$i] = "OK";
//    }
}

//$keyl = [];
//foreach (openssl_get_cipher_methods() as $algo) {
//    $keyl[$algo] = openssl_cipher_key_length($algo);
//}
//echo "<pre>" . var_export($keyl, true) . "</pre>";



echo "<pre>" . var_export(($good), true) . "</pre>";
//echo "<pre>" . var_export($res, true) . "</pre>";
//echo "<pre>" . var_export($error, true) . "</pre>";
/*
    "aes-128-cbc",
    "aes-128-cbc-cts",
    "aes-128-cbc-hmac-sha1",
    "aes-128-cbc-hmac-sha256",
    "aes-128-ccm",
    "aes-128-cfb",
    "aes-128-cfb1",
    "aes-128-cfb8",
    "aes-128-ctr",
    "aes-128-ecb",
    "aes-128-gcm",
    "aes-128-ocb",
    "aes-128-ofb",
    "aes-128-siv",
    "aes-128-wrap",
    "aes-128-wrap-inv",
    "aes-128-wrap-pad",
    "aes-128-wrap-pad-inv",
    "aes-128-xts",
    "aes-192-cbc",
    "aes-192-cbc-cts",
    "aes-192-ccm",
    "aes-192-cfb",
    "aes-192-cfb1",
    "aes-192-cfb8",
    "aes-192-ctr",
    "aes-192-ecb",
    "aes-192-gcm",
    "aes-192-ocb",
    "aes-192-ofb",
    "aes-192-siv",
    "aes-192-wrap",
    "aes-192-wrap-inv",
    "aes-192-wrap-pad",
    "aes-192-wrap-pad-inv",
    "aes-256-cbc",
    "aes-256-cbc-cts",
    "aes-256-cbc-hmac-sha1",
    "aes-256-cbc-hmac-sha256",
    "aes-256-ccm",
    "aes-256-cfb",
    "aes-256-cfb1",
    "aes-256-cfb8",
    "aes-256-ctr",
    "aes-256-ecb",
    "aes-256-gcm",
    "aes-256-ocb",
    "aes-256-ofb",
    "aes-256-siv",
    "aes-256-wrap",
    "aes-256-wrap-inv",
    "aes-256-wrap-pad",
    "aes-256-wrap-pad-inv",
    "aes-256-xts",
    "aria-128-cbc",
    "aria-128-ccm",
    "aria-128-cfb",
    "aria-128-cfb1",
    "aria-128-cfb8",
    "aria-128-ctr",
    "aria-128-ecb",
    "aria-128-gcm",
    "aria-128-ofb",
    "aria-192-cbc",
    "aria-192-ccm",
    "aria-192-cfb",
    "aria-192-cfb1",
    "aria-192-cfb8",
    "aria-192-ctr",
    "aria-192-ecb",
    "aria-192-gcm",
    "aria-192-ofb",
    "aria-256-cbc",
    "aria-256-ccm",
    "aria-256-cfb",
    "aria-256-cfb1",
    "aria-256-cfb8",
    "aria-256-ctr",
    "aria-256-ecb",
    "aria-256-gcm",
    "aria-256-ofb",
    "camellia-128-cbc",
    "camellia-128-cbc-cts",
    "camellia-128-cfb",
    "camellia-128-cfb1",
    "camellia-128-cfb8",
    "camellia-128-ctr",
    "camellia-128-ecb",
    "camellia-128-ofb",
    "camellia-192-cbc",
    "camellia-192-cbc-cts",
    "camellia-192-cfb",
    "camellia-192-cfb1",
    "camellia-192-cfb8",
    "camellia-192-ctr",
    "camellia-192-ecb",
    "camellia-192-ofb",
    "camellia-256-cbc",
    "camellia-256-cbc-cts",
    "camellia-256-cfb",
    "camellia-256-cfb1",
    "camellia-256-cfb8",
    "camellia-256-ctr",
    "camellia-256-ecb",
    "camellia-256-ofb",
    "chacha20",
    "chacha20-poly1305",
    "des-ede-cbc",
    "des-ede-cfb",
    "des-ede-ecb",
    "des-ede-ofb",
    "des-ede3-cbc",
    "des-ede3-cfb",
    "des-ede3-cfb1",
    "des-ede3-cfb8",
    "des-ede3-ecb",
    "des-ede3-ofb",
    "des3-wrap",
    "null",
    "sm4-cbc",
    "sm4-cfb",
    "sm4-ctr",
    "sm4-ecb",
    "sm4-ofb"
*/
//echo json_encode(openssl_get_cipher_methods(), JSON_PRETTY_PRINT);
//echo $restored_message;
//var_export(openssl_get_cipher_methods());
//echo openssl_cipher_iv_length("aes-256-ccm");


//"INSERT INTO `_sandbox` (`guid`,`created`,`enum`,`int`,`string`,`bool`,`json`,`encrypted_json`) VALUES ('j�2���+��C�*�^','2024-12-27 17:06:56','2','849059','BYUx3jv4AF','1','{\"foo\":\"boo\"}','��k0�!\0S�')"

//
//
//$db = Db::getInstance()->connection;
//
//$iterations = 20000;
//
//$data = [];
//for ($i = 0; $i < $iterations; $i++) {
//    $data[] = [
//        "guid"           => UUIDv4::pack(UUIDv4::create()),
//        "created"        => date("Y-m-d H:i:s"),
//        "enum"           => 2,
//        "int"            => rand(100000, 999999),
//        "string"         => StringsHelper::randomString(10),
//        "bool"           => true,
//        "json"           => json_encode(["foo" => "boo"]),
//        "encrypted_json" => (new EncryptedJSON(["foo" => "boo"]))->toBase(),
//    ];
//}
//
//$start = microtime(true);
//$db->query(
//    "CREATE TABLE IF NOT EXISTS `_sandbox` (
//      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
//      `guid` binary(16) NOT NULL,
//      `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
//      `enum` tinyint(4) NOT NULL DEFAULT '1',
//      `int` int(11) NOT NULL,
//      `string` varchar(200) NOT NULL,
//      `bool` tinyint(1) NOT NULL DEFAULT '0',
//      `json` json NOT NULL,
//      `encrypted_json` longblob NOT NULL,
//      PRIMARY KEY (`id`)
//    ) ENGINE=InnoDB DEFAULT CHARSET=utf8"
//);
//$duration = (microtime(true) - $start);
//
//Json::staticRender(
//    response: new PerformanceTestMigration(
//        duration: $duration
//    )
//);