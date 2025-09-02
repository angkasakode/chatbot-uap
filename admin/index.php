<?php
// --- LOGIKA BACKEND PHP ---
// Tentukan path ke file JSON
$jsonFilePath = '../data/university_data.json';
$universityData = [];
$notification = '';

// 1. Membaca data JSON yang ada
if (file_exists($jsonFilePath)) {
    $jsonContent = file_get_contents($jsonFilePath);
    $universityData = json_decode($jsonContent, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        die('Error: Gagal mem-parsing file JSON. Periksa formatnya.');
    }
} else {
    die('Error: File university_data.json tidak ditemukan.');
}

// Inisialisasi key baru jika belum ada untuk menghindari error
$keys_to_ensure = ['info_umum', 'visi_misi_tujuan', 'pimpinan', 'fakultas', 'unit_kerja', 'beasiswa', 'fasilitas', 'faq'];
foreach ($keys_to_ensure as $key) {
    if (!isset($universityData[$key])) {
        $universityData[$key] = [];
    }
}

// Fungsi untuk membersihkan array secara rekursif dari item kosong
function array_filter_recursive($array)
{
    if (is_array($array)) {
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                $value = array_filter_recursive($value);
            }
        }
        return array_values(array_filter($array));
    }
    return $array;
}


// 2. Memproses data dari form jika ada request POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $section = $_POST['section'] ?? '';

    if ($section && isset($universityData[$section])) {
        $submittedData = $_POST[$section] ?? [];

        // Penanganan untuk data yang merupakan array of objects (list)
        if (in_array($section, ['pimpinan', 'fakultas', 'unit_kerja', 'fasilitas', 'faq'])) {
            $cleanedData = [];
            if (!empty($submittedData)) {
                // Logika baru: Filter dan buang seluruh item jika item tersebut kosong.
                // Ini mencegah penyimpanan array kosong '[]' ke dalam JSON.
                $cleanedData = array_filter($submittedData, function ($item) {
                    // Pastikan item adalah array sebelum diperiksa
                    if (!is_array($item)) {
                        return false; // Buang jika bukan array
                    }
                    // Cek apakah ada setidaknya satu nilai yang tidak kosong di dalam item.
                    // Ini akan menjaga item yang hanya diisi pertanyaan atau jawabannya saja.
                    $filled_values = array_filter($item, function ($value) {
                        return is_array($value) || trim($value) !== '';
                    });
                    return !empty($filled_values);
                });
                // Re-index array agar urutannya benar (0, 1, 2, ...)
                $cleanedData = array_values($cleanedData);
            }
            $universityData[$section] = $cleanedData;
        } else { // Penanganan untuk data key-value sederhana (info_umum, visi_misi_tujuan)
            foreach ($submittedData as $key => $value) {
                if (is_array($value)) {
                    // Hapus item list yang kosong (cth: misi atau tujuan)
                    $submittedData[$key] = array_values(array_filter($value, function ($item) {
                        return !empty(trim($item));
                    }));
                }
            }
            // Merge untuk mempertahankan key yang tidak ada di form
            $universityData[$section] = array_merge($universityData[$section], $submittedData);
        }

        // 3. Menyimpan kembali data yang sudah diupdate ke file JSON
        if (file_put_contents($jsonFilePath, json_encode($universityData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE))) {
            $notification = 'Data ' . htmlspecialchars(str_replace('_', ' ', $section)) . ' berhasil diperbarui!';
            // Refresh data setelah menyimpan untuk menampilkan data terbaru
            $universityData = json_decode(file_get_contents($jsonFilePath), true);
        } else {
            $notification = 'Error: Gagal menyimpan data. Periksa hak akses file.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Knowledge Base UAP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/heroicons/2.0.18/24/outline/heroicons.min.css" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #a78bfa;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #8b5cf6;
        }

        .focus-ring {
            --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
            --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(3px + var(--tw-ring-offset-width)) var(--tw-ring-color);
            box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
            --tw-ring-color: rgba(167, 139, 250, .5);
            --tw-ring-offset-width: 2px;
        }

        .sidebar-link.active {
            background-color: #8b5cf6;
            color: white;
        }

        .dynamic-card {
            transition: all 0.3s ease-in-out;
        }
    </style>
</head>

<body class="bg-slate-100 antialiased">
    <div x-data="{ sidebarOpen: false }" class="flex h-screen bg-slate-100">
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 flex-shrink-0 w-64 bg-slate-900 text-white shadow-lg transform transition-transform duration-300 ease-in-out lg:relative lg:translate-x-0">
            <div class="flex flex-col w-full h-full">
                <div class="h-20 flex items-center justify-center border-b border-slate-700">
                    <a href="#" class="flex items-center space-x-3">
                        <svg class="h-8 w-8 text-violet-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5m0 15V21m3.75-18v1.5m0 15V21m-9-1.5h10.5a2.25 2.25 0 0 0 2.25-2.25V6.75a2.25 2.25 0 0 0-2.25-2.25H6.75A2.25 2.25 0 0 0 4.5 6.75v10.5a2.25 2.25 0 0 0 2.25 2.25Zm.75-12h9v9h-9v-9Z" />
                        </svg>
                        <span class="text-2xl font-bold">Admin UAP</span>
                    </a>
                </div>
                <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                    <?php
                    $nav_items = [
                        'info_umum' => ['name' => 'Informasi Umum', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M13.5 16.875h3.375m0 0h3.375m-3.375 0V13.5m0 3.375v3.375M6 10.5h2.25a2.25 2.25 0 0 0 2.25-2.25V6a2.25 2.25 0 0 0-2.25-2.25H6A2.25 2.25 0 0 0 3.75 6v2.25A2.25 2.25 0 0 0 6 10.5Zm0 9.75h2.25A2.25 2.25 0 0 0 10.5 18v-2.25a2.25 2.25 0 0 0-2.25-2.25H6a2.25 2.25 0 0 0-2.25 2.25V18A2.25 2.25 0 0 0 6 20.25Zm9.75-9.75H18a2.25 2.25 0 0 0 2.25-2.25V6a2.25 2.25 0 0 0-2.25-2.25h-2.25A2.25 2.25 0 0 0 13.5 6v2.25a2.25 2.25 0 0 0 2.25 2.25Z" />'],
                        'visi_misi_tujuan' => ['name' => 'Visi & Misi', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />'],
                        'pimpinan' => ['name' => 'Pimpinan', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-2.253-9.527-9.527 0 0 0-4.12-15.025A9.38 9.38 0 0 0 12 2.25a9.38 9.38 0 0 0-2.625.372A9.337 9.337 0 0 0 5.25 4.872a9.527 9.527 0 0 0-4.12 15.025 9.38 9.38 0 0 0 2.625.372M15 19.128v-3.064A9.35 9.35 0 0 1 12 15a9.35 9.35 0 0 1-3-6.064v-3.064m6 12.128-3-4.5-3 4.5m6-12.128-3 4.5-3-4.5" />'],
                        'fakultas' => ['name' => 'Fakultas & Prodi', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z" />'],
                        'unit_kerja' => ['name' => 'Unit Kerja', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18h16.5M5.25 21V3.75m13.5 17.25V3.75M9 21v-6.57m6 6.57v-6.57m-6.024-6.57 4.5-1.5-4.5-1.5m6.024 1.5-4.5 1.5-4.5-1.5" />'],
                        'beasiswa' => ['name' => 'Beasiswa', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 0 0-.491 6.347A48.627 48.627 0 0 1 12 20.904a48.627 48.627 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.57 50.57 0 0 0-2.658-.813A59.905 59.905 0 0 1 12 3.493a59.902 59.902 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />'],
                        'fasilitas' => ['name' => 'Fasilitas', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h7.5" />'],
                        'faq' => ['name' => 'Tanya Jawab (FAQ)', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />']
                    ];
                    ?>
                    <?php foreach ($nav_items as $key => $item) : ?>
                        <a href="#<?php echo $key; ?>" data-section-id="<?php echo $key; ?>" class="sidebar-link flex items-center px-4 py-2.5 text-slate-300 hover:bg-violet-500 hover:text-white rounded-lg transition-colors duration-200">
                            <svg class="h-6 w-6 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <?php echo $item['icon']; ?>
                            </svg>
                            <span><?php echo $item['name']; ?></span>
                        </a>
                    <?php endforeach; ?>
                </nav>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="h-20 flex items-center justify-between px-6 bg-white border-b border-slate-200 sticky top-0 z-10">
                <div class="flex items-center">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-slate-500 focus:outline-none lg:hidden">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 6H20M4 12H20M4 18H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </button>
                    <h1 class="text-2xl font-bold text-slate-800 ml-4 lg:ml-0">Knowledge Base Editor</h1>
                </div>
                <div class="flex items-center"><img class="h-10 w-10 rounded-full object-cover" src="https://ui-avatars.com/api/?name=Admin&background=8b5cf6&color=fff&bold=true" alt="Admin Avatar"></div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-100 p-8">

                <?php if ($notification) : ?>
                    <div id="notification" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md shadow-md flex justify-between items-center transition-opacity duration-300">
                        <div class="flex items-center"><svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="font-semibold"><?php echo $notification; ?></p>
                        </div>
                        <button onclick="document.getElementById('notification').style.display='none'" class="text-green-500 hover:text-green-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg></button>
                    </div>
                <?php endif; ?>

                <section id="info_umum" class="scroll-mt-24 mb-8 p-6 bg-white rounded-xl shadow-lg">
                    <h2 class="text-2xl font-bold text-slate-800 mb-6 border-b pb-4">Informasi Umum</h2>
                    <form action="#info_umum" method="POST">
                        <input type="hidden" name="section" value="info_umum">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div><label for="nama_univ" class="block text-sm font-medium text-slate-600 mb-1">Nama Universitas</label><input type="text" id="nama_univ" name="info_umum[nama]" value="<?= htmlspecialchars($universityData['info_umum']['nama'] ?? '') ?>" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-400"></div>
                            <div><label for="singkatan" class="block text-sm font-medium text-slate-600 mb-1">Singkatan</label><input type="text" id="singkatan" name="info_umum[singkatan]" value="<?= htmlspecialchars($universityData['info_umum']['singkatan'] ?? '') ?>" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-400"></div>
                            <div><label for="rektor" class="block text-sm font-medium text-slate-600 mb-1">Rektor</label><input type="text" id="rektor" name="info_umum[rektor]" value="<?= htmlspecialchars($universityData['info_umum']['rektor'] ?? '') ?>" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-400"></div>
                            <div><label for="telepon" class="block text-sm font-medium text-slate-600 mb-1">Telepon</label><input type="text" id="telepon" name="info_umum[telepon]" value="<?= htmlspecialchars($universityData['info_umum']['telepon'] ?? '') ?>" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-400"></div>
                            <div class="md:col-span-2"><label for="alamat" class="block text-sm font-medium text-slate-600 mb-1">Alamat</label><input type="text" id="alamat" name="info_umum[alamat]" value="<?= htmlspecialchars($universityData['info_umum']['alamat'] ?? '') ?>" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-400"></div>
                            <div class="md:col-span-2"><label for="deskripsi" class="block text-sm font-medium text-slate-600 mb-1">Deskripsi (Knowledge untuk Chatbot)</label><textarea id="deskripsi" name="info_umum[deskripsi]" rows="6" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-400"><?= htmlspecialchars($universityData['info_umum']['deskripsi'] ?? '') ?></textarea></div>
                        </div>
                        <div class="mt-8 text-right"><button type="submit" class="px-6 py-2.5 bg-violet-600 text-white font-semibold rounded-lg shadow-md hover:bg-violet-700 focus:outline-none focus-ring transition-all duration-200 transform hover:scale-105">Simpan Perubahan</button></div>
                    </form>
                </section>

                <section id="visi_misi_tujuan" class="scroll-mt-24 mb-8 p-6 bg-white rounded-xl shadow-lg">
                    <h2 class="text-2xl font-bold text-slate-800 mb-6 border-b pb-4">Visi, Misi, & Tujuan</h2>
                    <form action="#visi_misi_tujuan" method="POST">
                        <input type="hidden" name="section" value="visi_misi_tujuan">
                        <div class="space-y-6">
                            <div><label for="visi" class="block text-sm font-medium text-slate-600 mb-1">Visi</label><textarea id="visi" name="visi_misi_tujuan[visi]" rows="4" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-400"><?= htmlspecialchars($universityData['visi_misi_tujuan']['visi'] ?? '') ?></textarea></div>
                            <div id="misi-container"><label class="block text-sm font-medium text-slate-600 mb-2">Misi</label><?php foreach ($universityData['visi_misi_tujuan']['misi'] as $index => $misi) : ?><div class="flex items-center space-x-2 mb-2 simple-item"><input type="text" name="visi_misi_tujuan[misi][]" value="<?= htmlspecialchars($misi) ?>" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-400"><button type="button" onclick="removeItem(this)" class="p-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg></button></div><?php endforeach; ?></div><button type="button" onclick="addSimpleItem('misi', 'visi_misi_tujuan')" class="text-sm font-semibold text-violet-600 hover:text-violet-800">+ Tambah Misi</button>
                            <div id="tujuan-container" class="pt-4 border-t"><label class="block text-sm font-medium text-slate-600 mb-2">Tujuan</label><?php foreach ($universityData['visi_misi_tujuan']['tujuan'] as $index => $tujuan) : ?><div class="flex items-center space-x-2 mb-2 simple-item"><input type="text" name="visi_misi_tujuan[tujuan][]" value="<?= htmlspecialchars($tujuan) ?>" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-400"><button type="button" onclick="removeItem(this)" class="p-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg></button></div><?php endforeach; ?></div><button type="button" onclick="addSimpleItem('tujuan', 'visi_misi_tujuan')" class="text-sm font-semibold text-violet-600 hover:text-violet-800">+ Tambah Tujuan</button>
                        </div>
                        <div class="mt-8 text-right"><button type="submit" class="px-6 py-2.5 bg-violet-600 text-white font-semibold rounded-lg shadow-md hover:bg-violet-700 focus:outline-none focus-ring transition-all duration-200 transform hover:scale-105">Simpan Perubahan</button></div>
                    </form>
                </section>

                <section id="pimpinan" class="scroll-mt-24 mb-8 p-6 bg-white rounded-xl shadow-lg">
                    <h2 class="text-2xl font-bold text-slate-800 mb-6 border-b pb-4">Pimpinan</h2>
                    <form action="#pimpinan" method="POST">
                        <input type="hidden" name="section" value="pimpinan">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-slate-700 mb-3">Dekan Fakultas</h3>
                            <div id="dekan-container" class="space-y-4">
                                <?php foreach ($universityData['pimpinan']['dekan'] ?? [] as $index => $dekan) : ?>
                                    <div class="dynamic-card border p-4 rounded-lg bg-slate-50 relative">
                                        <button type="button" onclick="removeItem(this)" class="absolute top-2 right-2 p-1.5 bg-red-500 text-white rounded-full hover:bg-red-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg></button>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div><label class="block text-sm font-medium text-slate-600 mb-1">Nama</label><input type="text" name="pimpinan[dekan][<?= $index ?>][nama]" value="<?= htmlspecialchars($dekan['nama']) ?>" class="w-full px-3 py-2 border rounded-lg"></div>
                                            <div><label class="block text-sm font-medium text-slate-600 mb-1">Jabatan</label><input type="text" name="pimpinan[dekan][<?= $index ?>][jabatan]" value="<?= htmlspecialchars($dekan['jabatan'] ?? 'Dekan') ?>" class="w-full px-3 py-2 border rounded-lg"></div>
                                            <div><label class="block text-sm font-medium text-slate-600 mb-1">Fakultas</label><input type="text" name="pimpinan[dekan][<?= $index ?>][fakultas]" value="<?= htmlspecialchars($dekan['fakultas']) ?>" class="w-full px-3 py-2 border rounded-lg"></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" onclick="addComplexItem('dekan-container', 'dekan-template')" class="mt-4 px-4 py-2 text-sm bg-slate-200 text-slate-800 font-semibold rounded-lg hover:bg-slate-300 transition-all">+ Tambah Dekan</button>
                        </div>
                        <div class="mt-8 text-right"><button type="submit" class="px-6 py-2.5 bg-violet-600 text-white font-semibold rounded-lg shadow-md hover:bg-violet-700 focus:outline-none focus-ring transition-all duration-200 transform hover:scale-105">Simpan Perubahan Pimpinan</button></div>
                    </form>
                </section>

                <section id="fakultas" class="scroll-mt-24 mb-8 p-6 bg-white rounded-xl shadow-lg">
                    <h2 class="text-2xl font-bold text-slate-800 mb-6 border-b pb-4">Fakultas & Program Studi</h2>
                    <form action="#fakultas" method="POST">
                        <input type="hidden" name="section" value="fakultas">
                        <div id="fakultas-container" class="space-y-6">
                            <?php foreach ($universityData['fakultas'] ?? [] as $fak_index => $fakultas) : ?>
                                <div class="dynamic-card border-2 p-5 rounded-xl bg-white relative">
                                    <button type="button" onclick="removeItem(this)" class="absolute top-3 right-3 p-1.5 bg-red-500 text-white rounded-full hover:bg-red-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg></button>
                                    <h3 class="text-lg font-bold text-slate-700 mb-1">Data Fakultas</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div><label class="block text-sm font-medium text-slate-600 mb-1">Nama Fakultas</label><input type="text" name="fakultas[<?= $fak_index ?>][nama]" value="<?= htmlspecialchars($fakultas['nama']) ?>" class="w-full px-3 py-2 border rounded-lg"></div>
                                    </div>

                                    <div class="border-t pt-4 mt-4">
                                        <h4 class="font-semibold text-slate-600 mb-2">Program Studi</h4>
                                        <div class="prodi-container space-y-3">
                                            <?php foreach ($fakultas['program_studi'] ?? [] as $ps_index => $prodi) : ?>
                                                <div class="dynamic-card-nested flex items-center gap-3 bg-slate-50 p-3 rounded-lg">
                                                    <input type="text" name="fakultas[<?= $fak_index ?>][program_studi][<?= $ps_index ?>][prodi]" value="<?= htmlspecialchars($prodi['prodi']) ?>" placeholder="Nama Prodi" class="w-full px-3 py-2 border rounded-md">
                                                    <input type="text" name="fakultas[<?= $fak_index ?>][program_studi][<?= $ps_index ?>][jenjang]" value="<?= htmlspecialchars($prodi['jenjang']) ?>" placeholder="Jenjang (S1, D3)" class="w-1/4 px-3 py-2 border rounded-md">
                                                    <input type="text" name="fakultas[<?= $fak_index ?>][program_studi][<?= $ps_index ?>][akreditasi]" value="<?= htmlspecialchars($prodi['akreditasi']) ?>" placeholder="Akreditasi" class="w-1/3 px-3 py-2 border rounded-md">
                                                    <button type="button" onclick="removeItem(this, '.dynamic-card-nested')" class="p-2 text-red-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg></button>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <button type="button" onclick="addNestedItem(this, 'prodi-template', 'fakultas[<?= $fak_index ?>][program_studi]')" class="mt-3 px-3 py-1.5 text-xs bg-violet-100 text-violet-800 font-semibold rounded-md hover:bg-violet-200">+ Tambah Prodi</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-6 flex justify-between items-center">
                            <button type="button" onclick="addComplexItem('fakultas-container', 'fakultas-template')" class="px-5 py-2 bg-slate-200 text-slate-800 font-semibold rounded-lg hover:bg-slate-300 transition-all">+ Tambah Fakultas</button>
                            <button type="submit" class="px-6 py-2.5 bg-violet-600 text-white font-semibold rounded-lg shadow-md hover:bg-violet-700 focus:outline-none focus-ring">Simpan Perubahan Fakultas</button>
                        </div>
                    </form>
                </section>

                <section id="unit_kerja" class="scroll-mt-24 mb-8 p-6 bg-white rounded-xl shadow-lg">
                    <h2 class="text-2xl font-bold text-slate-800 mb-6 border-b pb-4">Unit Kerja</h2>
                    <form action="#unit_kerja" method="POST">
                        <input type="hidden" name="section" value="unit_kerja">
                        <div id="unit-kerja-container" class="space-y-4">
                            <?php foreach ($universityData['unit_kerja'] ?? [] as $index => $item) : ?>
                                <div class="dynamic-card border p-4 rounded-lg bg-slate-50 relative">
                                    <button type="button" onclick="removeItem(this)" class="absolute top-2 right-2 p-1.5 bg-red-500 text-white rounded-full hover:bg-red-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg></button>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div><label class="block text-sm font-medium text-slate-600 mb-1">Nama Unit</label><input type="text" name="unit_kerja[<?= $index ?>][nama_unit]" value="<?= htmlspecialchars($item['nama_unit']) ?>" class="w-full px-3 py-2 border rounded-lg"></div>
                                        <div><label class="block text-sm font-medium text-slate-600 mb-1">Kepala</label><input type="text" name="unit_kerja[<?= $index ?>][kepala]" value="<?= htmlspecialchars($item['kepala']) ?>" class="w-full px-3 py-2 border rounded-lg"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-6 flex justify-between items-center">
                            <button type="button" onclick="addComplexItem('unit-kerja-container', 'unit-kerja-template')" class="px-5 py-2 bg-slate-200 text-slate-800 font-semibold rounded-lg hover:bg-slate-300 transition-all">+ Tambah Unit Kerja</button>
                            <button type="submit" class="px-6 py-2.5 bg-violet-600 text-white font-semibold rounded-lg shadow-md hover:bg-violet-700 focus:outline-none focus-ring">Simpan Perubahan Unit Kerja</button>
                        </div>
                    </form>
                </section>

                <section id="beasiswa" class="scroll-mt-24 mb-8 p-6 bg-white rounded-xl shadow-lg">
                    <h2 class="text-2xl font-bold text-slate-800 mb-6 border-b pb-4">Beasiswa</h2>
                    <form action="#beasiswa" method="POST">
                        <input type="hidden" name="section" value="beasiswa">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-700 mb-3">Persyaratan Umum</h3>
                                <div id="syarat-umum-container" class="space-y-2">
                                    <?php foreach ($universityData['beasiswa']['persyaratan_umum'] ?? [] as $syarat) : ?>
                                        <div class="flex items-center space-x-2 simple-item">
                                            <input type="text" name="beasiswa[persyaratan_umum][]" value="<?= htmlspecialchars($syarat) ?>" class="w-full px-4 py-2 border rounded-lg">
                                            <button type="button" onclick="removeItem(this)" class="p-2 bg-red-500 text-white rounded-lg hover:bg-red-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg></button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <button type="button" onclick="addSimpleItem('syarat-umum', 'beasiswa[persyaratan_umum]')" class="mt-3 text-sm font-semibold text-violet-600 hover:text-violet-800">+ Tambah Syarat Umum</button>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-slate-700 mb-3">Persyaratan Khusus</h3>
                                <div id="syarat-khusus-container" class="space-y-3">
                                    <?php foreach ($universityData['beasiswa']['persyaratan_khusus'] ?? [] as $index => $syarat) : ?>
                                        <div class="dynamic-card-nested border p-3 rounded-lg bg-slate-50 relative">
                                            <button type="button" onclick="removeItem(this)" class="absolute top-1 right-1 p-1 bg-red-500 text-white rounded-full hover:bg-red-600"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg></button>
                                            <div class="grid grid-cols-2 gap-2">
                                                <input type="text" name="beasiswa[persyaratan_khusus][<?= $index ?>][jenis]" value="<?= htmlspecialchars($syarat['jenis']) ?>" placeholder="Jenis Beasiswa" class="px-3 py-2 border rounded-md">
                                                <input type="text" name="beasiswa[persyaratan_khusus][<?= $index ?>][detail]" value="<?= htmlspecialchars($syarat['detail']) ?>" placeholder="Detail Persyaratan" class="px-3 py-2 border rounded-md">
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <button type="button" onclick="addComplexItem('syarat-khusus-container', 'syarat-khusus-template')" class="mt-3 text-sm font-semibold text-violet-600 hover:text-violet-800">+ Tambah Syarat Khusus</button>
                            </div>
                        </div>
                        <div class="mt-8 text-right"><button type="submit" class="px-6 py-2.5 bg-violet-600 text-white font-semibold rounded-lg shadow-md hover:bg-violet-700 focus:outline-none focus-ring">Simpan Perubahan Beasiswa</button></div>
                    </form>
                </section>

                <section id="fasilitas" class="scroll-mt-24 mb-8 p-6 bg-white rounded-xl shadow-lg">
                    <h2 class="text-2xl font-bold text-slate-800 mb-6 border-b pb-4">Fasilitas & Layanan</h2>
                    <form action="#fasilitas" method="POST">
                        <input type="hidden" name="section" value="fasilitas">
                        <div id="fasilitas-container" class="space-y-4">
                            <?php foreach ($universityData['fasilitas'] as $index => $item) : ?>
                                <div class="dynamic-card border p-4 rounded-lg bg-slate-50 relative">
                                    <button type="button" onclick="removeItem(this)" class="absolute top-2 right-2 p-1.5 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div class="md:col-span-1"><label class="block text-sm font-medium text-slate-600 mb-1">Nama Fasilitas</label><input type="text" name="fasilitas[<?= $index ?>][nama]" value="<?= htmlspecialchars($item['nama']) ?>" class="w-full px-4 py-2 border border-slate-300 rounded-lg"></div>
                                        <div class="md:col-span-2"><label class="block text-sm font-medium text-slate-600 mb-1">Deskripsi</label><textarea name="fasilitas[<?= $index ?>][deskripsi]" rows="2" class="w-full px-4 py-2 border border-slate-300 rounded-lg"><?= htmlspecialchars($item['deskripsi']) ?></textarea></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-6 flex justify-between items-center">
                            <button type="button" onclick="addComplexItem('fasilitas-container', 'fasilitas-template')" class="px-5 py-2 bg-slate-200 text-slate-800 font-semibold rounded-lg hover:bg-slate-300 transition-all duration-200">+ Tambah Fasilitas</button>
                            <button type="submit" class="px-6 py-2.5 bg-violet-600 text-white font-semibold rounded-lg shadow-md hover:bg-violet-700 focus:outline-none focus-ring transition-all duration-200 transform hover:scale-105">Simpan Perubahan Fasilitas</button>
                        </div>
                    </form>
                </section>

                <section id="faq" class="scroll-mt-24 p-6 bg-white rounded-xl shadow-lg">
                    <h2 class="text-2xl font-bold text-slate-800 mb-6 border-b pb-4">Tanya Jawab (FAQ)</h2>
                    <form action="#faq" method="POST">
                        <input type="hidden" name="section" value="faq">
                        <div id="faq-container" class="space-y-4">
                            <?php foreach ($universityData['faq'] as $index => $item) : ?>
                                <div class="dynamic-card border p-4 rounded-lg bg-slate-50 relative">
                                    <button type="button" onclick="removeItem(this)" class="absolute top-2 right-2 p-1.5 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                    <div class="space-y-2">
                                        <div><label class="block text-sm font-medium text-slate-600 mb-1">Pertanyaan</label><input type="text" name="faq[<?= $index ?>][pertanyaan]" value="<?= htmlspecialchars($item['pertanyaan']) ?>" class="w-full px-4 py-2 border border-slate-300 rounded-lg"></div>
                                        <div><label class="block text-sm font-medium text-slate-600 mb-1">Jawaban</label><textarea name="faq[<?= $index ?>][jawaban]" rows="3" class="w-full px-4 py-2 border border-slate-300 rounded-lg"><?= htmlspecialchars($item['jawaban']) ?></textarea></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-6 flex justify-between items-center">
                            <button type="button" onclick="addComplexItem('faq-container', 'faq-template')" class="px-5 py-2 bg-slate-200 text-slate-800 font-semibold rounded-lg hover:bg-slate-300 transition-all duration-200">+ Tambah Pertanyaan</button>
                            <button type="submit" class="px-6 py-2.5 bg-violet-600 text-white font-semibold rounded-lg shadow-md hover:bg-violet-700 focus:outline-none focus-ring transition-all duration-200 transform hover:scale-105">Simpan Perubahan FAQ</button>
                        </div>
                    </form>
                </section>

            </main>
        </div>
    </div>

    <template id="dekan-template">
        <div class="dynamic-card border p-4 rounded-lg bg-slate-50 relative">
            <button type="button" onclick="removeItem(this)" class="absolute top-2 right-2 p-1.5 bg-red-500 text-white rounded-full hover:bg-red-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg></button>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div><label class="block text-sm font-medium text-slate-600 mb-1">Nama</label><input type="text" name="pimpinan[dekan][__INDEX__][nama]" placeholder="Nama Dekan" class="w-full px-3 py-2 border rounded-lg"></div>
                <div><label class="block text-sm font-medium text-slate-600 mb-1">Jabatan</label><input type="text" name="pimpinan[dekan][__INDEX__][jabatan]" value="Dekan" class="w-full px-3 py-2 border rounded-lg"></div>
                <div><label class="block text-sm font-medium text-slate-600 mb-1">Fakultas</label><input type="text" name="pimpinan[dekan][__INDEX__][fakultas]" placeholder="Nama Fakultas" class="w-full px-3 py-2 border rounded-lg"></div>
            </div>
        </div>
    </template>

    <template id="fakultas-template">
        <div class="dynamic-card border-2 p-5 rounded-xl bg-white relative">
            <button type="button" onclick="removeItem(this)" class="absolute top-3 right-3 p-1.5 bg-red-500 text-white rounded-full hover:bg-red-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg></button>
            <h3 class="text-lg font-bold text-slate-700 mb-1">Data Fakultas Baru</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div><label class="block text-sm font-medium text-slate-600 mb-1">Nama Fakultas</label><input type="text" name="fakultas[__INDEX__][nama]" placeholder="Cth: Fakultas Kesehatan" class="w-full px-3 py-2 border rounded-lg"></div>
            </div>
            <div class="border-t pt-4 mt-4">
                <h4 class="font-semibold text-slate-600 mb-2">Program Studi</h4>
                <div class="prodi-container space-y-3"></div>
                <button type="button" onclick="addNestedItem(this, 'prodi-template', 'fakultas[__INDEX__][program_studi]')" class="mt-3 px-3 py-1.5 text-xs bg-violet-100 text-violet-800 font-semibold rounded-md hover:bg-violet-200">+ Tambah Prodi</button>
            </div>
        </div>
    </template>

    <template id="prodi-template">
        <div class="dynamic-card-nested flex items-center gap-3 bg-slate-50 p-3 rounded-lg">
            <input type="text" name="__NAME_PREFIX__[__NESTED_INDEX__][prodi]" placeholder="Nama Prodi" class="w-full px-3 py-2 border rounded-md">
            <input type="text" name="__NAME_PREFIX__[__NESTED_INDEX__][jenjang]" placeholder="Jenjang (S1, D3)" class="w-1/4 px-3 py-2 border rounded-md">
            <input type="text" name="__NAME_PREFIX__[__NESTED_INDEX__][akreditasi]" placeholder="Akreditasi" class="w-1/3 px-3 py-2 border rounded-md">
            <button type="button" onclick="removeItem(this, '.dynamic-card-nested')" class="p-2 text-red-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg></button>
        </div>
    </template>

    <template id="unit-kerja-template">
        <div class="dynamic-card border p-4 rounded-lg bg-slate-50 relative">
            <button type="button" onclick="removeItem(this)" class="absolute top-2 right-2 p-1.5 bg-red-500 text-white rounded-full hover:bg-red-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg></button>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium text-slate-600 mb-1">Nama Unit</label><input type="text" name="unit_kerja[__INDEX__][nama_unit]" placeholder="Cth: Perpustakaan" class="w-full px-3 py-2 border rounded-lg"></div>
                <div><label class="block text-sm font-medium text-slate-600 mb-1">Kepala</label><input type="text" name="unit_kerja[__INDEX__][kepala]" placeholder="Nama Kepala Unit" class="w-full px-3 py-2 border rounded-lg"></div>
            </div>
        </div>
    </template>

    <template id="syarat-khusus-template">
        <div class="dynamic-card-nested border p-3 rounded-lg bg-slate-50 relative">
            <button type="button" onclick="removeItem(this)" class="absolute top-1 right-1 p-1 bg-red-500 text-white rounded-full hover:bg-red-600"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>
                </svg></button>
            <div class="grid grid-cols-2 gap-2">
                <input type="text" name="beasiswa[persyaratan_khusus][__INDEX__][jenis]" placeholder="Jenis Beasiswa" class="px-3 py-2 border rounded-md">
                <input type="text" name="beasiswa[persyaratan_khusus][__INDEX__][detail]" placeholder="Detail Persyaratan" class="px-3 py-2 border rounded-md">
            </div>
        </div>
    </template>

    <template id="fasilitas-template">
        <div class="dynamic-card border p-4 rounded-lg bg-slate-50 relative">
            <button type="button" onclick="removeItem(this)" class="absolute top-2 right-2 p-1.5 bg-red-500 text-white rounded-full hover:bg-red-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg></button>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-1"><label class="block text-sm font-medium text-slate-600 mb-1">Nama Fasilitas</label><input type="text" name="fasilitas[__INDEX__][nama]" placeholder="Cth: Perpustakaan" class="w-full px-4 py-2 border rounded-lg"></div>
                <div class="md:col-span-2"><label class="block text-sm font-medium text-slate-600 mb-1">Deskripsi</label><textarea name="fasilitas[__INDEX__][deskripsi]" rows="2" placeholder="Jelaskan fasilitas ini..." class="w-full px-4 py-2 border rounded-lg"></textarea></div>
            </div>
        </div>
    </template>

    <template id="faq-template">
        <div class="dynamic-card border p-4 rounded-lg bg-slate-50 relative">
            <button type="button" onclick="removeItem(this)" class="absolute top-2 right-2 p-1.5 bg-red-500 text-white rounded-full hover:bg-red-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg></button>
            <div class="space-y-2">
                <div><label class="block text-sm font-medium text-slate-600 mb-1">Pertanyaan</label><input type="text" name="faq[__INDEX__][pertanyaan]" placeholder="Tulis pertanyaan..." class="w-full px-4 py-2 border rounded-lg"></div>
                <div><label class="block text-sm font-medium text-slate-600 mb-1">Jawaban</label><textarea name="faq[__INDEX__][jawaban]" rows="3" placeholder="Berikan jawaban..." class="w-full px-4 py-2 border rounded-lg"></textarea></div>
            </div>
        </div>
    </template>

    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // --- LOGIKA FRONTEND JAVASCRIPT ---
            window.removeItem = function(button, parentSelector = '.dynamic-card') {
                const item = button.closest(parentSelector) || button.closest('.simple-item');
                if (item) {
                    item.style.opacity = '0';
                    item.style.transform = 'scale(0.9)';
                    item.style.transition = 'all 0.3s ease';
                    setTimeout(() => item.remove(), 300);
                }
            }

            window.addSimpleItem = function(type, namePrefix) {
                const container = document.getElementById(`${type}-container`);
                const newItem = document.createElement('div');
                newItem.className = `flex items-center space-x-2 mb-2 simple-item`;
                newItem.innerHTML = `
                <input type="text" name="${namePrefix}[]" placeholder="Tulis ${type.replace('-', ' ')} baru..." class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-400">
                <button type="button" onclick="removeItem(this)" class="p-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            `;
                container.appendChild(newItem);
            }

            window.addComplexItem = function(containerId, templateId) {
                const container = document.getElementById(containerId);
                const template = document.getElementById(templateId).innerHTML;
                const index = container.querySelectorAll('.dynamic-card, .dynamic-card-nested').length;

                const newItemHtml = template.replace(/__INDEX__/g, index);

                const newItem = document.createElement('div');
                // Salin kelas dari template jika ada
                const tempNode = document.createElement('div');
                tempNode.innerHTML = newItemHtml.trim();
                newItem.className = tempNode.firstChild.className;
                newItem.innerHTML = tempNode.firstChild.innerHTML;

                container.appendChild(newItem);
            }

            window.addNestedItem = function(button, templateId, namePrefix) {
                const container = button.previousElementSibling; // The .prodi-container
                const template = document.getElementById(templateId).innerHTML;
                const nestedIndex = container.children.length;

                let newItemHtml = template.replace(/__NAME_PREFIX__/g, namePrefix);
                newItemHtml = newItemHtml.replace(/__NESTED_INDEX__/g, nestedIndex);

                const newItem = document.createElement('div');
                const tempNode = document.createElement('div');
                tempNode.innerHTML = newItemHtml.trim();
                newItem.className = tempNode.firstChild.className;
                newItem.innerHTML = tempNode.firstChild.innerHTML;

                container.appendChild(newItem);
            }

            // Auto-hide notification
            const notification = document.getElementById('notification');
            if (notification) {
                setTimeout(() => {
                    notification.style.opacity = '0';
                    setTimeout(() => notification.style.display = 'none', 500);
                }, 5000);
            }

            // Active sidebar link on scroll
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('.sidebar-link');

            const observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        navLinks.forEach(link => {
                            link.classList.toggle('active', link.getAttribute('href') === `#${entry.target.id}`);
                        });
                    }
                });
            }, {
                rootMargin: '-50% 0px -50% 0px',
                threshold: 0
            });

            sections.forEach(section => observer.observe(section));

        });
    </script>
</body>

</html>
