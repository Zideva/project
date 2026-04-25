<?php
// File untuk operasi CRUD agenda

$agenda_file = __DIR__ . '/../data/agenda.json';

// Inisialisasi file JSON jika belum ada
if (!file_exists($agenda_file)) {
    $default_agenda = [
        [
            'id' => 1,
            'tanggal' => '10 Maret',
            'hari' => 'Selasa',
            'nama_posyandu' => 'Posyandu Melati',
            'alamat' => 'Jl. Melati No.12',
            'jam' => '08:00 - 11:00',
            'deskripsi' => 'Pemeriksaan pertumbuhan, imunisasi, pemberian vitamin, dan penyuluhan kesehatan untuk ibu hamil dan balita.'
        ],
        [
            'id' => 2,
            'tanggal' => '12 Maret',
            'hari' => 'Kamis',
            'nama_posyandu' => 'Posyandu Mawar',
            'alamat' => 'Jl. Mawar No.35',
            'jam' => '08:00 - 09:35',
            'deskripsi' => 'Pemeriksaan pertumbuhan, edukasi sosial ke ibu hamil, vaksinasi, dan konseling gizi.'
        ],
        [
            'id' => 3,
            'tanggal' => '14 Maret',
            'hari' => 'Sabtu',
            'nama_posyandu' => 'Posyandu Kenangan',
            'alamat' => 'Jl. Kenangan No.25',
            'jam' => '09:30 - 12:45',
            'deskripsi' => 'Pemeriksaan pertumbuhan anak, pemberian vitamin, makan sehat bersama, dan edukasi orang tua.'
        ]
    ];
    file_put_contents($agenda_file, json_encode($default_agenda, JSON_PRETTY_PRINT));
}

// Ambil semua agenda
function get_all_agenda() {
    global $agenda_file;
    if (file_exists($agenda_file)) {
        $json = file_get_contents($agenda_file);
        $agenda = json_decode($json, true);
        return $agenda ? $agenda : [];
    }
    return [];
}

// Ambil agenda berdasarkan ID
function get_agenda_by_id($id) {
    $agenda_list = get_all_agenda();
    foreach ($agenda_list as $agenda) {
        if ($agenda['id'] == $id) {
            return $agenda;
        }
    }
    return null;
}

// Tambah agenda baru
function add_agenda($data) {
    global $agenda_file;
    $agenda_list = get_all_agenda();
    
    // Generate ID baru
    $max_id = 0;
    foreach ($agenda_list as $agenda) {
        if ($agenda['id'] > $max_id) {
            $max_id = $agenda['id'];
        }
    }
    
    $new_agenda = [
        'id' => $max_id + 1,
        'tanggal' => $data['tanggal'],
        'hari' => $data['hari'],
        'nama_posyandu' => $data['nama_posyandu'],
        'alamat' => $data['alamat'],
        'jam' => $data['jam'],
        'deskripsi' => $data['deskripsi']
    ];
    
    $agenda_list[] = $new_agenda;
    file_put_contents($agenda_file, json_encode($agenda_list, JSON_PRETTY_PRINT));
    
    return true;
}

// Update agenda
function update_agenda($id, $data) {
    global $agenda_file;
    $agenda_list = get_all_agenda();
    
    foreach ($agenda_list as &$agenda) {
        if ($agenda['id'] == $id) {
            $agenda['tanggal'] = $data['tanggal'];
            $agenda['hari'] = $data['hari'];
            $agenda['nama_posyandu'] = $data['nama_posyandu'];
            $agenda['alamat'] = $data['alamat'];
            $agenda['jam'] = $data['jam'];
            $agenda['deskripsi'] = $data['deskripsi'];
            break;
        }
    }
    
    file_put_contents($agenda_file, json_encode($agenda_list, JSON_PRETTY_PRINT));
    return true;
}

// Hapus agenda
function delete_agenda($id) {
    global $agenda_file;
    $agenda_list = get_all_agenda();
    
    $agenda_list = array_filter($agenda_list, function($agenda) use ($id) {
        return $agenda['id'] != $id;
    });
    
    // Reindex array
    $agenda_list = array_values($agenda_list);
    file_put_contents($agenda_file, json_encode($agenda_list, JSON_PRETTY_PRINT));
    
    return true;
}
?>
