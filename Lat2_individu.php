<?php
$chatHistory = fopen("history.txt", 'a+') or die("can't open file");

$sensor = [
    'anjing',
    'bego',
    'goblog',
    'asu',
    'jancuk',
    'jancok',
    'jancuak'
];

function img_render($img) {
 	return "<img width='25' height='25' src='img/{$img}'/>";
}

$emoticon = [
    ':)' => img_render('glad.png'),
    ':D' => img_render('laugh.png'),
    ':p' => img_render('tongue.png'),
    ';-)' => img_render('wink.png'),
    ':x' => img_render('quiet.png'),
    ':(' => img_render('sad.png'),
    ':\'(' => img_render('crying.png'),
    '-_-' => img_render('down.png')
];

if (isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $pesan = $_POST['pesan'];

    # jika pesan hanya berisi satu kata
    # cocok kan dalam array 
    if (in_array($pesan, $sensor)):
        $pCount = strlen($pesan);
        $pesan = str_replace(
            substr($pesan, 1, ($pCount - 2)),
            str_repeat("*", ($pCount - 2)), $pesan
        );
    # jika pesan lebih dari satu kata
    else:
        # iterasi sebanyak indeks $sensor
        foreach ($sensor as $value):
            # jika pesan mengandung kata tak pantas dari indeks ke-n
            if (strpos($pesan, $value) !== false):
                # hitung jumlah karakter
                $pCount = strlen($value);
                # ganti kata tersebut dengan * sebanyak jumlah char-2
                $pesan = str_replace(
                    substr($value, 1, $pCount - 2),
                    str_repeat("*", $pCount - 2), $pesan
                );
            endif;
        endforeach;
    endif;
    # tulis log kedalam file history.txt
    fwrite($chatHistory, "\n$nama | $email | $pesan");
    fclose($chatHistory);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Chat-ing Sederhana</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="angular.min.js"></script>
    <script>
        var app = angular.module('chatApp', []);
        app.controller('validateCtrl', function ($scope) {
            // terapkan initial untuk angular model
            $scope.nama = 'ockifals';
            $scope.email = 'ocki.bagus.p@gmail.com';
            $scope.pesan = '';
        });
    </script>
</head>
<body>
<div class="wrapper">
    <form action="Lat2_individu.php" method="POST"
          ng-app="chatApp" ng-controller="validateCtrl"
          name="chatForm" novalidate>
        <table border=0 widht="125px">
            <tr>
                <td>Nama</td>
                <td>:</td>
                <td>
                    <input type="text" name="nama" ng-model="nama" required>
						<span ng-show="chatForm.nama.$dirty && chatForm.nama.$invalid">
							<span ng-show="chatForm.nama.$error.required">Required!</span>
						</span>
                </td>
            </tr>

            <tr>
                <td>Email</td>
                <td>:</td>
                <td>
                    <input type="email" name="email" ng-model="email" required>
					<span ng-show="chatForm.email.$dirty && chatForm.email.$invalid">
						<span ng-show="chatForm.email.$error.required">Required!</span>
					  	<span ng-show="chatForm.email.$error.email">Invalid email address.</span>
					</span>
                </td>
            </tr>

            <tr>
                <td>Pesan</td>
                <td>:</td>
                <td>
                    <input type="text" name="pesan" ng-model="pesan" required>
					<span ng-show="pesan == '' ||chatForm.pesan.$dirty && chatForm.pesan.$invalid">
						<span ng-show="chatForm.pesan.$error.required">Required!</span>
					</span>
                </td>
            </tr>

            <tr>
                <td>
                    <input type="submit" name="submit" ng-disabled="pesan == '' ||
                		chatForm.nama.$dirty && chatForm.nama.$invalid ||
  						chatForm.email.$dirty && chatForm.email.$invalid || 
  						chatForm.pesan.$dirty && chatForm.pesan.$invalid">
                </td>
            </tr>
        </table>
    </form>
    <div id="message">
        <?php
        $chatHistory = fopen("history.txt", 'r') or die("can't open file");
        while (!feof($chatHistory)):
            # pisahkan string kedalam array
            $pesan = explode('|', fgets($chatHistory));
            # jika node iterasi bukan merupakan akhir dari file
            if (($pesan[0] !== "") && (count($pesan) == 3)):
                $pesan[2] = str_replace(
                    array_keys($emoticon),
                    array_values($emoticon),
                    $pesan[2]
                );
                ?>
                <h4><?= "{$pesan[0]} => <a href='#'>{$pesan[1]}</a>" ?></h4>
                <div class="message-body">
                    <p><?= $pesan[2] ?></p>
                </div>
            <?php
            endif;
        endwhile;

        fclose($chatHistory);
        ?>
    </div>
</div>
</body>
</html>