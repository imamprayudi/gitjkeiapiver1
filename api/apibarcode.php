<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

// require '../config/auth.php';
// $pdo = require '../config/pdo.php';

$env = parse_ini_file(__DIR__ . '/.env');

$dsn = "mysql:host={$env['DB_HOST']};dbname={$env['DB_NAME']};charset=utf8mb4";

return new PDO(
    $dsn,
    $env['DB_USER'],
    $env['DB_PASSWORD'],
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
);


if (
    empty($_SESSION['user']) ||
    ($_SESSION['appkey'] ?? '') !== $env['APP_KEY']
) {
    session_destroy();
    header("Location: /login.php");
    exit();
}session_start();

if (
    empty($_SESSION['user']) ||
    ($_SESSION['appkey'] ?? '') !== $env['APP_KEY']
) {
    session_destroy();
    header("Location: /login.php");
    exit();
}

$action = $_GET['action'] ?? '';

try {

    switch ($action) {

        /*
        =====================================
        SUPPLIER LIST
        =====================================
        */
        case 'supplier':

            $stmt = $pdo->prepare("
                SELECT s.suppname, s.suppcode
                FROM usersupp u
                JOIN supplier s ON u.suppcode = s.suppcode
                WHERE u.userid = ?
                AND s.status = 'active'
                ORDER BY s.suppname
            ");

            $stmt->execute([$_SESSION['user']]);

            echo json_encode(['rows'=>$stmt->fetchAll()]);
        break;


        /*
        =====================================
        PART LIST
        =====================================
        */
        case 'part':

            $stmt = $pdo->prepare("
                SELECT DISTINCT partno
                FROM po
                WHERE suppcode = ?
                ORDER BY partno
            ");

            $stmt->execute([$_GET['suppcode']]);

            echo json_encode(['rows'=>$stmt->fetchAll()]);
        break;


        /*
        =====================================
        PO LIST
        =====================================
        */
        case 'po':

            $stmt = $pdo->prepare("
                SELECT pono
                FROM po
                WHERE suppcode = ?
                AND partno = ?
                ORDER BY pono
            ");

            $stmt->execute([
                $_GET['suppcode'],
                $_GET['partno']
            ]);

            echo json_encode(['rows'=>$stmt->fetchAll()]);
        break;


        /*
        =====================================
        QTY
        =====================================
        */
        case 'qty':

            $stmt = $pdo->prepare("
                SELECT qty
                FROM po
                WHERE suppcode = ?
                AND partno = ?
                AND pono = ?
                LIMIT 1
            ");

            $stmt->execute([
                $_GET['suppcode'],
                $_GET['partno'],
                $_GET['pono']
            ]);

            echo json_encode(['rows'=>$stmt->fetchAll()]);
        break;


        default:
            http_response_code(400);
            echo json_encode(['error'=>'Invalid action']);
    }

} catch (Throwable $e) {

    http_response_code(500);
    echo json_encode(['error'=>$e->getMessage()]);
}