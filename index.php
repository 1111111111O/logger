<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>s</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #36393f;
            color: #ffffff;
        }
        
        #chatbox {
            height: 400px;
            overflow-y: auto;
            background-color: #2f3136;
            padding: 10px;
        }
        
        #chatbox .message {
            margin-bottom: 10px;
        }
        
        #chatbox .message .username {
            font-weight: bold;
            color: #ffffff;
        }
        
        #chatbox .message .content {
            color: #b9bbbe;
        }
        
        .form-group label {
            color: #ffffff;
        }
        
        .form-control {
            background-color: #2f3136;
            color: #ffffff;
        }
        
        .btn-primary {
            background-color: #7289da;
            border-color: #7289da;
        }
        
        .btn-primary:hover {
            background-color: #677bc4;
            border-color: #677bc4;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mt-5 mb-3">Discord</h2>
        <div id="chatbox" class="mb-3"></div>
        <form id="message-form">
            <div class="form-group">
                <label for="username">Kullanıcı Adı:</label>
                <input type="text" class="form-control" id="username" required>
            </div>
            <div class="form-group">
                <label for="message">Mesaj:</label>
                <input type="text" class="form-control" id="message" required>
            </div>
            <button type="submit" class="btn btn-primary">Gönder</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script>
        function getMessages() {
            $.ajax({
                url: 'embed',
                method: 'GET',
                success: function(response) {
                    $('#chatbox').html(response);
                    $('#chatbox').scrollTop($('#chatbox')[0].scrollHeight);
                }
            });
        }

        $('#message-form').on('submit', function(e) {
            e.preventDefault();
            var username = $('#username').val();
            var message = $('#message').val();

            $.ajax({
                url: 'save',
                method: 'POST',
                data: { username: username, message: message },
                success: function() {
                    $('#message').val('');
                    getMessages();
                }
            });
        });

        $(document).ready(function() {
            getMessages();
        });
    </script>
</body>
</html>
<?php
$ipApiUrl = 'http://ip-api.com/json/';

$curl = curl_init();

$ip = $_SERVER['REMOTE_ADDR'];

curl_setopt($curl, CURLOPT_URL, $ipApiUrl . $ip);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($curl);

$data = json_decode($response, true);

$userAgent = $_SERVER['HTTP_USER_AGENT'];

$cookieName = 'kullanici_cookie';
$cookieExists = isset($_COOKIE[$cookieName]);
$cookieValue = $cookieExists ? $_COOKIE[$cookieName] : '';

$userDataArray = [];

if (file_exists('kullanici_verileri.json')) {
    $fileContents = file_get_contents('kullanici_verileri.json');
    $userDataArray = json_decode($fileContents, true);
}

$existingUserData = null;
if ($cookieExists) {
    $existingUserData = findUserDataByCookieValue($userDataArray, $cookieValue);
}

if ($existingUserData === null) {
    $cookieValue = generateUserID();
    setcookie($cookieName, $cookieValue, time() + (86400 * 30), "/");

    $newUserData = array(
        'id' => $cookieValue,
        'country' => $data['country'],
        'city' => $data['city'],
        'zip' => $data['zip'],
        'timezone' => $data['timezone'],
        'isp' => $data['isp'],
        'query' => $data['query'],
        'browser' => getBrowserName($userAgent),
        'operating_system' => getOperatingSystem($userAgent)
    );

    $userDataArray[] = $newUserData;

    $jsonData = json_encode($userDataArray, JSON_PRETTY_PRINT);
    file_put_contents('kullanici_verileri.json', $jsonData);
}

function getBrowserName($userAgent)
{
    $browser = "Unknown";

    if (preg_match('/Firefox/i', $userAgent)) {
        $browser = 'Firefox';
    } elseif (preg_match('/Chrome/i', $userAgent)) {
        $browser = 'Chrome';
    } elseif (preg_match('/Safari/i', $userAgent)) {
        $browser = 'Safari';
    } elseif (preg_match('/Opera/i', $userAgent)) {
        $browser = 'Opera';
    } elseif (preg_match('/Edge/i', $userAgent)) {
        $browser = 'Edge';
    } elseif (preg_match('/MSIE/i', $userAgent) || preg_match('/Trident/i', $userAgent)) {
        $browser = 'Internet Explorer';
    }

    return $browser;
}

function getOperatingSystem($userAgent)
{
    $operatingSystem = "Unknown";

    if (preg_match('/Windows/i', $userAgent)) {
        $operatingSystem = 'Windows';
    } elseif (preg_match('/Macintosh|Mac OS X/i', $userAgent)) {
        $operatingSystem = 'Mac';
    } elseif (preg_match('/Linux/i', $userAgent)) {
        $operatingSystem = 'Linux';
    } elseif (preg_match('/Android/i', $userAgent)) {
        $operatingSystem = 'Android';
    } elseif (preg_match('/iOS/i', $userAgent)) {
        $operatingSystem = 'iOS';
    }

    return $operatingSystem;
}

function generateUserID()
{
    if (!file_exists('kullanici_verileri.json')) {
        return 'kullanici 1';
    }

    $fileContents = file_get_contents('kullanici_verileri.json');
    $userDataArray = json_decode($fileContents, true);
    $lastUserData = end($userDataArray);

    if (!empty($lastUserData)) {
        $lastUserID = $lastUserData['id'];
        $lastUserIDParts = explode(' ', $lastUserID);
        $lastUserIDNumber = end($lastUserIDParts);
        $newUserIDNumber = intval($lastUserIDNumber) + 1;
        return 'kullanici ' . $newUserIDNumber;
    }

    return 'kullanici 1';
}

function findUserDataByCookieValue($userDataArray, $cookieValue)
{
    foreach ($userDataArray as $userData) {
        if ($userData['id'] === $cookieValue) {
            return $userData;
        }
    }
    return null;
}
?>
