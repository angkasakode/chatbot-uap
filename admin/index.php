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
if (!isset($universityData['fasilitas'])) {
    $universityData['fasilitas'] = [];
}
if (!isset($universityData['faq'])) {
    $universityData['faq'] = [];
}

// 2. Memproses data dari form jika ada request POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $section = $_POST['section'] ?? '';

    if ($section && isset($universityData[$section])) {
        // Ambil data yang dikirim dari form
        $submittedData = $_POST[$section] ?? [];

        // Penanganan khusus untuk data terstruktur (list/array of objects) seperti fasilitas dan faq
        if ($section === 'fasilitas' || $section === 'faq') {
            // Kita replace seluruh datanya, bukan di-merge, untuk menghandle item yang dihapus
            // Filter untuk menghapus item yang 'nama' atau 'pertanyaan'-nya kosong
            $cleanedData = [];
            if (!empty($submittedData)) {
                foreach ($submittedData as $item) {
                    if ( (isset($item['nama']) && !empty(trim($item['nama']))) || (isset($item['pertanyaan']) && !empty(trim($item['pertanyaan']))) ) {
                        $cleanedData[] = $item;
                    }
                }
            }
            $universityData[$section] = $cleanedData;
        } else { // Penanganan untuk data yang sudah ada (info_umum, visi_misi)
             foreach ($submittedData as $key => $value) {
                if (is_array($value)) {
                    $submittedData[$key] = array_values(array_filter($value));
                }
            }
            $universityData[$section] = array_merge($universityData[$section], $submittedData);
        }
        
        // 3. Menyimpan kembali data yang sudah diupdate ke file JSON
        if (file_put_contents($jsonFilePath, json_encode($universityData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))) {
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
    <title>Admin Dashboard - Knowledge Base Chatbot</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #a78bfa; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #8b5cf6; }
        .focus-ring {
            --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
            --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(3px + var(--tw-ring-offset-width)) var(--tw-ring-color);
            box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
            --tw-ring-color: rgba(167, 139, 250, .5);
            --tw-ring-offset-width: 2px;
        }
    </style>
</head>
<body class="bg-slate-100 antialiased">
    <div x-data="{ sidebarOpen: false }" class="flex h-screen bg-slate-100">
        <aside
            x-show="sidebarOpen"
            @click.away="sidebarOpen = false"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-x-full"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 -translate-x-full"
            class="fixed inset-y-0 left-0 z-50 flex-shrink-0 w-64 bg-slate-900 text-white shadow-lg lg:relative lg:translate-x-0 lg:flex lg:w-64"
        >
            <div class="flex flex-col w-full">
                <div class="h-20 flex items-center justify-center border-b border-slate-700">
                    <a href="#" class="flex items-center space-x-3">
                         <svg class="h-8 w-8 text-violet-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5m0 15V21m3.75-18v1.5m0 15V21m-9-1.5h10.5a2.25 2.25 0 0 0 2.25-2.25V6.75a2.25 2.25 0 0 0-2.25-2.25H6.75A2.25 2.25 0 0 0 4.5 6.75v10.5a2.25 2.25 0 0 0 2.25 2.25Zm.75-12h9v9h-9v-9Z" />
                        </svg>
                        <span class="text-2xl font-bold">Admin UAP</span>
                    </a>
                </div>
                <nav class="flex-1 px-4 py-6 space-y-2">
                    <?php 
                        $nav_items = [
                            'info_umum' => ['name' => 'Informasi Umum', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M13.5 16.875h3.375m0 0h3.375m-3.375 0V13.5m0 3.375v3.375M6 10.5h2.25a2.25 2.25 0 0 0 2.25-2.25V6a2.25 2.25 0 0 0-2.25-2.25H6A2.25 2.25 0 0 0 3.75 6v2.25A2.25 2.25 0 0 0 6 10.5Zm0 9.75h2.25A2.25 2.25 0 0 0 10.5 18v-2.25a2.25 2.25 0 0 0-2.25-2.25H6a2.25 2.25 0 0 0-2.25 2.25V18A2.25 2.25 0 0 0 6 20.25Zm9.75-9.75H18a2.25 2.25 0 0 0 2.25-2.25V6a2.25 2.25 0 0 0-2.25-2.25h-2.25A2.25 2.25 0 0 0 13.5 6v2.25a2.25 2.25 0 0 0 2.25 2.25Z" />'],
                            'visi_misi_tujuan' => ['name' => 'Visi & Misi', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />'],
                            'fasilitas' => ['name' => 'Fasilitas', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h7.5" />'],
                            'faq' => ['name' => 'Tanya Jawab (FAQ)', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />']
                        ];
                    ?>
                    <?php foreach ($nav_items as $key => $item): ?>
                    <a href="#<?php echo $key; ?>" class="flex items-center px-4 py-2.5 text-slate-300 hover:bg-violet-500 hover:text-white rounded-lg transition-colors duration-200">
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
            <header class="h-20 flex items-center justify-between px-6 bg-white border-b border-slate-200">
                <div class="flex items-center">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-slate-500 focus:outline-none lg:hidden">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 6H20M4 12H20M4 18H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                    </button>
                    <h1 class="text-2xl font-bold text-slate-800 ml-4 lg:ml-0">Knowledge Base Editor</h1>
                </div>
                <div class="flex items-center"><img class="h-10 w-10 rounded-full object-cover" src="https://ui-avatars.com/api/?name=Admin&background=8b5cf6&color=fff&bold=true" alt="Admin Avatar"></div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-100 p-8">
                
                <?php if ($notification): ?>
                <div id="notification" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md shadow-md flex justify-between items-center transition-opacity duration-300">
                    <div class="flex items-center"><svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><p class="font-semibold"><?php echo $notification; ?></p></div>
                    <button onclick="document.getElementById('notification').style.display='none'" class="text-green-500 hover:text-green-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                <?php endif; ?>

                <div id="info_umum" class="mb-8 p-6 bg-white rounded-xl shadow-lg">
                    <h2 class="text-2xl font-bold text-slate-800 mb-6 border-b pb-4">Informasi Umum</h2>
                    <form action="" method="POST">
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
                </div>

                <div id="visi_misi_tujuan" class="mb-8 p-6 bg-white rounded-xl shadow-lg">
                    <h2 class="text-2xl font-bold text-slate-800 mb-6 border-b pb-4">Visi, Misi, & Tujuan</h2>
                    <form action="" method="POST">
                        <input type="hidden" name="section" value="visi_misi_tujuan">
                        <div class="space-y-6">
                            <div><label for="visi" class="block text-sm font-medium text-slate-600 mb-1">Visi</label><textarea id="visi" name="visi_misi_tujuan[visi]" rows="4" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-400"><?= htmlspecialchars($universityData['visi_misi_tujuan']['visi'] ?? '') ?></textarea></div>
                            <div id="misi-container"><label class="block text-sm font-medium text-slate-600 mb-2">Misi</label><?php foreach ($universityData['visi_misi_tujuan']['misi'] as $index => $misi): ?><div class="flex items-center space-x-2 mb-2 misi-item"><input type="text" name="visi_misi_tujuan[misi][]" value="<?= htmlspecialchars($misi) ?>" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-400"><button type="button" onclick="removeItem(this, '.misi-item')" class="p-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button></div><?php endforeach; ?></div><button type="button" onclick="addSimpleItem('misi')" class="text-sm font-semibold text-violet-600 hover:text-violet-800">+ Tambah Misi</button>
                            <div id="tujuan-container" class="pt-4 border-t"><label class="block text-sm font-medium text-slate-600 mb-2">Tujuan</label><?php foreach ($universityData['visi_misi_tujuan']['tujuan'] as $index => $tujuan): ?><div class="flex items-center space-x-2 mb-2 tujuan-item"><input type="text" name="visi_misi_tujuan[tujuan][]" value="<?= htmlspecialchars($tujuan) ?>" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-400"><button type="button" onclick="removeItem(this, '.tujuan-item')" class="p-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button></div><?php endforeach; ?></div><button type="button" onclick="addSimpleItem('tujuan')" class="text-sm font-semibold text-violet-600 hover:text-violet-800">+ Tambah Tujuan</button>
                        </div>
                         <div class="mt-8 text-right"><button type="submit" class="px-6 py-2.5 bg-violet-600 text-white font-semibold rounded-lg shadow-md hover:bg-violet-700 focus:outline-none focus-ring transition-all duration-200 transform hover:scale-105">Simpan Perubahan</button></div>
                    </form>
                </div>

                <div id="fasilitas" class="mb-8 p-6 bg-white rounded-xl shadow-lg">
                    <h2 class="text-2xl font-bold text-slate-800 mb-6 border-b pb-4">Fasilitas & Layanan</h2>
                    <form action="" method="POST">
                        <input type="hidden" name="section" value="fasilitas">
                        <div id="fasilitas-container" class="space-y-4">
                            <?php foreach ($universityData['fasilitas'] as $index => $item): ?>
                            <div class="dynamic-card border p-4 rounded-lg bg-slate-50 relative">
                                <button type="button" onclick="removeItem(this, '.dynamic-card')" class="absolute top-2 right-2 p-1.5 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="md:col-span-1"><label class="block text-sm font-medium text-slate-600 mb-1">Nama Fasilitas</label><input type="text" name="fasilitas[<?= $index ?>][nama]" value="<?= htmlspecialchars($item['nama']) ?>" class="w-full px-4 py-2 border border-slate-300 rounded-lg"></div>
                                    <div class="md:col-span-2"><label class="block text-sm font-medium text-slate-600 mb-1">Deskripsi</label><textarea name="fasilitas[<?= $index ?>][deskripsi]" rows="2" class="w-full px-4 py-2 border border-slate-300 rounded-lg"><?= htmlspecialchars($item['deskripsi']) ?></textarea></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-6 flex justify-between items-center">
                            <button type="button" onclick="addComplexItem('fasilitas')" class="px-5 py-2 bg-slate-200 text-slate-800 font-semibold rounded-lg hover:bg-slate-300 transition-all duration-200">+ Tambah Fasilitas</button>
                            <button type="submit" class="px-6 py-2.5 bg-violet-600 text-white font-semibold rounded-lg shadow-md hover:bg-violet-700 focus:outline-none focus-ring transition-all duration-200 transform hover:scale-105">Simpan Perubahan Fasilitas</button>
                        </div>
                    </form>
                </div>

                <div id="faq" class="p-6 bg-white rounded-xl shadow-lg">
                    <h2 class="text-2xl font-bold text-slate-800 mb-6 border-b pb-4">Tanya Jawab (FAQ)</h2>
                    <form action="" method="POST">
                        <input type="hidden" name="section" value="faq">
                        <div id="faq-container" class="space-y-4">
                             <?php foreach ($universityData['faq'] as $index => $item): ?>
                             <div class="dynamic-card border p-4 rounded-lg bg-slate-50 relative">
                                <button type="button" onclick="removeItem(this, '.dynamic-card')" class="absolute top-2 right-2 p-1.5 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors">
                                     <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                                <div class="space-y-2">
                                    <div><label class="block text-sm font-medium text-slate-600 mb-1">Pertanyaan</label><input type="text" name="faq[<?= $index ?>][pertanyaan]" value="<?= htmlspecialchars($item['pertanyaan']) ?>" class="w-full px-4 py-2 border border-slate-300 rounded-lg"></div>
                                    <div><label class="block text-sm font-medium text-slate-600 mb-1">Jawaban</label><textarea name="faq[<?= $index ?>][jawaban]" rows="3" class="w-full px-4 py-2 border border-slate-300 rounded-lg"><?= htmlspecialchars($item['jawaban']) ?></textarea></div>
                                </div>
                            </div>
                             <?php endforeach; ?>
                        </div>
                        <div class="mt-6 flex justify-between items-center">
                             <button type="button" onclick="addComplexItem('faq')" class="px-5 py-2 bg-slate-200 text-slate-800 font-semibold rounded-lg hover:bg-slate-300 transition-all duration-200">+ Tambah Pertanyaan</button>
                             <button type="submit" class="px-6 py-2.5 bg-violet-600 text-white font-semibold rounded-lg shadow-md hover:bg-violet-700 focus:outline-none focus-ring transition-all duration-200 transform hover:scale-105">Simpan Perubahan FAQ</button>
                        </div>
                    </form>
                </div>

            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <script>
        // --- LOGIKA FRONTEND JAVASCRIPT ---
        function removeItem(button, parentSelector) {
            button.closest(parentSelector).remove();
        }

        function addSimpleItem(type) {
            const container = document.getElementById(`${type}-container`);
            const newItem = document.createElement('div');
            newItem.className = `flex items-center space-x-2 mb-2 ${type}-item`;
            newItem.innerHTML = `
                <input type="text" name="visi_misi_tujuan[${type}][]" placeholder="Tulis ${type} baru..." class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-400">
                <button type="button" onclick="removeItem(this, '.${type}-item')" class="p-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            `;
            container.appendChild(newItem);
        }

        function addComplexItem(type) {
            const container = document.getElementById(`${type}-container`);
            const index = container.querySelectorAll('.dynamic-card').length;
            const newItem = document.createElement('div');
            newItem.className = 'dynamic-card border p-4 rounded-lg bg-slate-50 relative';
            
            let content = '';
            if (type === 'fasilitas') {
                content = `
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-1"><label class="block text-sm font-medium text-slate-600 mb-1">Nama Fasilitas</label><input type="text" name="fasilitas[${index}][nama]" placeholder="Cth: Perpustakaan" class="w-full px-4 py-2 border border-slate-300 rounded-lg"></div>
                        <div class="md:col-span-2"><label class="block text-sm font-medium text-slate-600 mb-1">Deskripsi</label><textarea name="fasilitas[${index}][deskripsi]" placeholder="Jelaskan tentang fasilitas ini..." rows="2" class="w-full px-4 py-2 border border-slate-300 rounded-lg"></textarea></div>
                    </div>`;
            } else if (type === 'faq') {
                content = `
                    <div class="space-y-2">
                        <div><label class="block text-sm font-medium text-slate-600 mb-1">Pertanyaan</label><input type="text" name="faq[${index}][pertanyaan]" placeholder="Tulis pertanyaan yang sering diajukan..." class="w-full px-4 py-2 border border-slate-300 rounded-lg"></div>
                        <div><label class="block text-sm font-medium text-slate-600 mb-1">Jawaban</label><textarea name="faq[${index}][jawaban]" placeholder="Berikan jawaban yang jelas dan lengkap..." rows="3" class="w-full px-4 py-2 border border-slate-300 rounded-lg"></textarea></div>
                    </div>`;
            }

            newItem.innerHTML = `
                <button type="button" onclick="removeItem(this, '.dynamic-card')" class="absolute top-2 right-2 p-1.5 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                ${content}
            `;
            container.appendChild(newItem);
        }

        const notification = document.getElementById('notification');
        if (notification) {
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => notification.style.display = 'none', 500);
            }, 5000);
        }
    </script>
</body>
</html>