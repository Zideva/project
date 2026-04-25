<?php
$imunisasi_file = __DIR__ . '/../data/imunisasi.json';

function init_imunisasi_file() {
    global $imunisasi_file;
    if (!file_exists($imunisasi_file)) {
        file_put_contents($imunisasi_file, json_encode([], JSON_PRETTY_PRINT));
    }
}

function get_all_imunisasi_entries() {
    global $imunisasi_file;
    init_imunisasi_file();
    $json = file_get_contents($imunisasi_file);
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

function get_user_imunisasi_entries($userId) {
    return array_values(array_filter(get_all_imunisasi_entries(), function ($row) use ($userId) {
        return isset($row['user_id']) && $row['user_id'] === $userId;
    }));
}

function add_imunisasi_entry($entry) {
    global $imunisasi_file;
    $list = get_all_imunisasi_entries();
    $list[] = $entry;
    file_put_contents($imunisasi_file, json_encode($list, JSON_PRETTY_PRINT));
}

function get_imunisasi_summary($rows = null) {
    if ($rows === null) {
        $rows = get_all_imunisasi_entries();
    }
    $summary = [
        'male' => 0,
        'female' => 0,
        'total' => count($rows),
        'BCG' => 0,
        'Hepatitis B' => 0,
        'Polio' => 0,
        'DTP' => 0,
        'Hib' => 0,
        'PCV' => 0,
        'Rotavirus' => 0,
        'MR' => 0,
    ];

    foreach ($rows as $row) {
        if (($row['jk'] ?? '') === 'Laki-laki') {
            $summary['male']++;
        } else {
            $summary['female']++;
        }

        $jenis = $row['jenis_imunisasi'] ?? '';
        if (isset($summary[$jenis])) {
            $summary[$jenis]++;
        }
    }

    return $summary;
}
?>