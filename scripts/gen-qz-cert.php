<?php
$config = [
    'private_key_bits' => 2048,
    'private_key_type' => OPENSSL_KEYTYPE_RSA,
];
$key = openssl_pkey_new($config);
openssl_pkey_export($key, $privateKey);
file_put_contents(__DIR__ . '/../public/private-key.pem', $privateKey);

$dn  = ['commonName' => 'prismeyewear'];
$csr  = openssl_csr_new($dn, $key);
$cert = openssl_csr_sign($csr, null, $key, 3650);
openssl_x509_export($cert, $certOut);
file_put_contents(__DIR__ . '/../public/digital-certificate.txt', $certOut);

echo "Certificate generated OK.\n";
echo "private-key.pem      → public/private-key.pem\n";
echo "digital-certificate  → public/digital-certificate.txt\n";
