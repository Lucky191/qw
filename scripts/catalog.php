<?php

// Объявляем нужные константы
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'site');
// Подключаемся к базе данных
function connectDB() {
    $errorMessage = 'Невозможно подключиться к серверу базы данных';
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if (!$conn)
        throw new Exception($errorMessage);
    else {
        $query = $conn->query('set names utf8');
        if (!$query)
            throw new Exception($errorMessage);
        else
            return $conn;
    }
}

// Получение данных из массива _GET
function getOptions() {
    // Категория, цены и дополнительные данные
    $categoryId = (isset($_GET['category'])) ? (int)$_GET['category'] : 0;
    $minPrice = (isset($_GET['min_price'])) ? (int)$_GET['min_price'] : 0;
    $maxPrice = (isset($_GET['max_price'])) ? (int)$_GET['max_price'] : 1000000;
    $needsData = (isset($_GET['needs_data'])) ? explode(',', $_GET['needs_data']) : array();

    return array(
        'category_id' => $categoryId,
        'min_price' => $minPrice,
        'max_price' => $maxPrice,
        'needs_data' => $needsData
    );
}
// Получение всех данных
function getData($options, $conn) {
    $result = array(
        'goods' => getGoods($options, $conn)
    );
}

    $needsData = $options['needs_data'];
    if (empty($needsData)) return $result;

    if (in_array('brands', $needsData)) {
        $result['brands'] = getBrands($options['category_id'], $conn);
    }
    if (in_array('prices', $needsData)) {
        $result['prices'] = getPrices($options['category_id'], $conn);
    }

    return $result;


try {
    // Подключаемся к базе данных
    $conn = connectDB();

    // Получаем данные от клиента
    $options = getOptions();

    // Получаем товары
    $data = getData($options, $conn);

    // Возвращаем клиенту успешный ответ
    echo json_encode(array(
        'code' => 'success',
        'data' => $data ));
}