<?php
$report_file = __DIR__ . '/../data/pemeriksaans.json';

function init_pemeriksaan_file() {
    global $report_file;
    if (!file_exists($report_file)) {
        file_put_contents($report_file, json_encode([], JSON_PRETTY_PRINT));
    }
}

function get_all_pemeriksaan() {
    global $report_file;
    init_pemeriksaan_file();
    $json = file_get_contents($report_file);
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

function get_user_pemeriksaan($userId) {
    return array_values(array_filter(get_all_pemeriksaan(), function ($row) use ($userId) {
        return isset($row['user_id']) && $row['user_id'] === $userId;
    }));
}

function add_pemeriksaan($item) {
    global $report_file;
    $list = get_all_pemeriksaan();
    $list[] = $item;
    file_put_contents($report_file, json_encode($list, JSON_PRETTY_PRINT));
    return true;
}
?>