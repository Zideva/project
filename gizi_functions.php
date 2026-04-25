<?php
$gizi_file = __DIR__ . '/../data/gizi.json';

function init_gizi_file() {
    global $gizi_file;
    if (!file_exists($gizi_file)) {
        file_put_contents($gizi_file, json_encode([], JSON_PRETTY_PRINT));
    }
}

function get_all_gizi_entries() {
    global $gizi_file;
    init_gizi_file();
    $json = file_get_contents($gizi_file);
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

function get_user_gizi_entries($userId) {
    return array_values(array_filter(get_all_gizi_entries(), function ($row) use ($userId) {
        return isset($row['user_id']) && $row['user_id'] === $userId;
    }));
}

function add_gizi_entry($entry) {
    global $gizi_file;
    $list = get_all_gizi_entries();
    $list[] = $entry;
    file_put_contents($gizi_file, json_encode($list, JSON_PRETTY_PRINT));
}

function get_gizi_summary($rows = null) {
    if ($rows === null) {
        $rows = get_all_gizi_entries();
    }
    $summary = [
        'male' => 0,
        'female' => 0,
        'total' => count($rows),
        'sesuai' => 0,
        'tidak_sesuai' => 0,
    ];

    foreach ($rows as $row) {
        if (($row['jk'] ?? '') === 'Laki-laki') {
            $summary['male']++;
        } else {
            $summary['female']++;
        }

        if (($row['statusWHO'] ?? '') === 'Sesuai Standar WHO') {
            $summary['sesuai']++;
        } else {
            $summary['tidak_sesuai']++;
        }
    }

    return $summary;
}
?>