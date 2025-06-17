<?php
// Database connection configuration
define('DB_HOST', 'localhost');
// define('DB_NAME', 'dgn_china');
define('DB_NAME', 'chinesse_restaurant');
define('DB_USER', 'root');
define('DB_PASS', '');
define('ROOT', 'http://localhost/chinnese-restaurant/');
// Global DB state for query diagnostics
$GLOBALS['DB_STATE'] = [
    'affected_rows'   => 0,
    'insert_id'       => 0,
    'error'           => '',
    'has_error'       => false,
    'query_id'        => '',
    'table_schema'    => DB_NAME,
    'missing_tables'  => [],
];

// Establish a PDO connection
function db_connect() {
    static $pdo = null;

    if ($pdo === null) {
        try {
            $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
        }
    }

    return $pdo;
}

// Query helper with state management
function db_query(string $query, array $data = [], string $data_type = 'object') {
    $GLOBALS['DB_STATE']['error'] = '';
    $GLOBALS['DB_STATE']['has_error'] = false;
    $GLOBALS['DB_STATE']['affected_rows'] = 0;
    $GLOBALS['DB_STATE']['insert_id'] = 0;

    $con = db_connect();

    try {
        $stm = $con->prepare($query);

        $is_named = !empty($data) && is_string(array_key_first($data));

        if ($is_named) {
    foreach ($data as $key => $value) {
        $type = PDO::PARAM_STR;
        if (is_int($value)) $type = PDO::PARAM_INT;
        elseif (is_bool($value)) $type = PDO::PARAM_BOOL;
        elseif (is_null($value)) $type = PDO::PARAM_NULL;

        $key = (strpos($key, ':') === 0) ? $key : ':' . $key;
        $stm->bindValue($key, $value, $type);
    }
}
 else {
            foreach (array_values($data) as $index => $value) {
                $type = PDO::PARAM_STR;
                if (is_int($value)) $type = PDO::PARAM_INT;
                elseif (is_bool($value)) $type = PDO::PARAM_BOOL;
                elseif (is_null($value)) $type = PDO::PARAM_NULL;

                $stm->bindValue($index + 1, $value, $type);
            }
        }

        $stm->execute();

        $GLOBALS['DB_STATE']['affected_rows'] = $stm->rowCount();
        $GLOBALS['DB_STATE']['insert_id'] = $con->lastInsertId();

        if (stripos(trim($query), 'SELECT') === 0) {
            return $data_type === 'object'
                ? $stm->fetchAll(PDO::FETCH_OBJ)
                : $stm->fetchAll(PDO::FETCH_ASSOC);
        }

        return true;
    } catch (PDOException $e) {
        $GLOBALS['DB_STATE']['error'] = $e->getMessage();
        $GLOBALS['DB_STATE']['has_error'] = true;
        return false;
    }
}

// Utility function to handle file upload
function uploadImage($file) {
    $uploadDir = dirname(__DIR__) . '/uploads/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $fileName = uniqid() . '-' . basename($file['name']);
    $filePath = $uploadDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        return '/uploads/' . $fileName;
    }

    return null;
}

// Mail Settings Configuration
if (!defined('MAIL_HOST'))         define('MAIL_HOST', 'smtp.gmail.com');
if (!defined('MAIL_PORT'))         define('MAIL_PORT', 587);
if (!defined('MAIL_USERNAME'))     define('MAIL_USERNAME', 'charlesebuka1@gmail.com');
if (!defined('MAIL_PASSWORD'))     define('MAIL_PASSWORD', 'vKKomvnytrbzfmlr');
if (!defined('MAIL_ENCRYPTION'))   define('MAIL_ENCRYPTION', 'tls');
if (!defined('MAIL_FROM_EMAIL'))   define('MAIL_FROM_EMAIL', 'charlesebuka1@gmail.com');
if (!defined('MAIL_FROM_NAME'))    define('MAIL_FROM_NAME', 'Cedigitalweb');
?>
