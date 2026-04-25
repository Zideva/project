<?php
$vitamin_file = __DIR__ . '/../data/vitamin.json';

function init_vitamin_file() {
    global $vitamin_file;
    if (!file_exists($vitamin_file)) {
        file_put_contents($vitamin_file, json_encode([], JSON_PRETTY_PRINT));
    }
}

function get_all_vitamin_entries() {
    global $vitamin_file;
    init_vitamin_file();
    $json = file_get_contents($vitamin_file);
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

function get_user_vitamin_entries($userId) {
    return array_values(array_filter(get_all_vitamin_entries(), function ($row) use ($userId) {
        return isset($row['user_id']) && $row['user_id'] === $userId;
    }));
}

function add_vitamin_entry($entry) {
    global $vitamin_file;
    $list = get_all_vitamin_entries();
    $list[] = $entry;
    file_put_contents($vitamin_file, json_encode($list, JSON_PRETTY_PRINT));
}

function get_vitamin_summary($rows = null) {
    if ($rows === null) {
        $rows = get_all_vitamin_entries();
    }
    $summary = [
        'male' => 0,
        'female' => 0,
        'A100' => 0,
        'A200' => 0,
        'B100' => 0,
        'B200' => 0,
        'total' => count($rows),
    ];

    foreach ($rows as $row) {
        if (($row['jk'] ?? '') === 'Laki-laki') {
            $summary['male']++;
        } else {
            $summary['female']++;
        }

        switch ($row['jenis_vitamin'] ?? '') {
            case 'Vitamin A 100.000 UI':
                $summary['A100']++;
                break;
            case 'Vitamin A 200.000 UI':
                $summary['A200']++;
                break;
            case 'Vitamin B 100.000 UI':
                $summary['B100']++;
                break;
            case 'Vitamin B 200.000 UI':
                $summary['B200']++;
                break;
        }
    }

    return $summary;
}
?>