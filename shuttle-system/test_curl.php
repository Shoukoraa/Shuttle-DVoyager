<?php
$token = '5|wmfp3hxF6xaQcS0i0ZYgaFh0zInS1LbsT3JpQVPb55eb6e78';
$url = 'http://localhost:8000/api/me/profile-photo';

// Create a dummy image
$imageContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');
file_put_contents('dummy.png', $imageContent);

$cfile = new CURLFile(realpath('dummy.png'), 'image/png', 'dummy.png');
$data = array('profile_photo' => $cfile);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: Bearer ' . $token
));

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if(curl_errno($ch)){
    echo 'Curl error: ' . curl_error($ch) . "\n";
}
curl_close($ch);

echo "HTTP Code: $httpcode\n";
echo "Response: $response\n";
