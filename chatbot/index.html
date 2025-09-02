<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - Asisten AI UAP (Revisi)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Konfigurasi Dark Mode di Tailwind
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: { 50: '#f5f3ff', 100: '#ede9fe', 200: '#ddd6fe', 300: '#c4b5fd', 400: '#a78bfa', 500: '#8b5cf6', 600: '#7c3aed', 700: '#6d28d9', 800: '#5b21b6', 900: '#4c1d95' },
                        dark: { bg: '#111827', 'bg-secondary': '#1f2937', text: '#d1d5db', 'text-secondary': '#9ca3af', border: '#374151' }
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            font-family: 'Inter', sans-serif;
            transition: color 0.3s ease, background-color 0.3s ease, border-color 0.3s ease, transform 0.2s ease;
        }
        body { background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); }
        .dark body { background: linear-gradient(135deg, #111827 0%, #1f2937 100%); }
        #chat-window::-webkit-scrollbar { width: 8px; }
        #chat-window::-webkit-scrollbar-track { background: rgba(241, 245, 249, 0.5); border-radius: 10px; }
        .dark #chat-window::-webkit-scrollbar-track { background: rgba(31, 41, 55, 0.5); }
        #chat-window::-webkit-scrollbar-thumb { background: linear-gradient(135deg, #c4b5fd, #a78bfa); border-radius: 10px; }
        #chat-window::-webkit-scrollbar-thumb:hover { background: linear-gradient(135deg, #a78bfa, #8b5cf6); }
        .chat-bubble-user { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); border-radius: 24px 24px 8px 24px; box-shadow: 0 10px 20px -5px rgba(139, 92, 246, 0.3); }
        .chat-bubble-bot { background: rgba(255, 255, 255, 0.95); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 24px 24px 24px 8px; box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.08); }
        .dark .chat-bubble-bot { background: rgba(55, 65, 81, 0.8); backdrop-filter: blur(20px); border: 1px solid rgba(75, 85, 99, 0.3); box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.2); }
        .chat-bubble-bot ul { list-style-type: disc; padding-left: 20px; margin-top: 8px; margin-bottom: 8px; }
        .chat-bubble-bot li { margin-bottom: 6px; line-height: 1.6; }
        .typing-dot { animation: typing-pulse 1.4s ease-in-out infinite both; }
        @keyframes typing-pulse { 0%, 80%, 100% { transform: scale(0.8); opacity: 0.5; } 40% { transform: scale(1.2); opacity: 1; } }
        .chat-table { width: 100%; border-collapse: collapse; margin: 16px 0; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); background: white; }
        .dark .chat-table { background: #1f2937; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3); }
        .chat-table th, .chat-table td { padding: 12px 16px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .dark .chat-table th, .dark .chat-table td { border-bottom: 1px solid #374151; }
        .chat-table th { background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; font-weight: 600; font-size: 14px; text-transform: uppercase; }
        .dark .chat-table td { color: #d1d5db; }
        .glass-header { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(20px); border-bottom: 1px solid rgba(230, 230, 230, 0.7); box-shadow: 0 4px 32px rgba(0, 0, 0, 0.1); }
        .dark .glass-header { background: rgba(31, 41, 55, 0.85); border-bottom: 1px solid #374151; }
        .message-enter { animation: slideInUp 0.5s cubic-bezier(0.4, 0, 0.2, 1); }
        @keyframes slideInUp { from { opacity: 0; transform: translateY(20px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }
        .interactive-hover:hover { transform: translateY(-2px); }
        .loading-spinner { animation: spin 1s linear infinite; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    </style>
</head>

<body class="flex flex-col h-screen">

    <header class="sticky top-0 z-50 glass-header">
        <div class="max-w-5xl px-6 py-4 mx-auto flex items-center justify-between">
            <div class="flex items-center">
                 <button onclick="window.location.href='../'" class="mr-4 text-gray-500 hover:text-primary-600 p-3 rounded-2xl hover:bg-primary-50 dark:text-dark-text-secondary dark:hover:bg-dark-bg-secondary dark:hover:text-primary-400 interactive-hover focus-ring transition-all duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <div class="w-14 h-14 rounded-2xl mr-4 flex items-center justify-center text-white font-bold text-xl bg-gradient-to-br from-primary-500 to-primary-700">
                    <i class="fas fa-robot relative z-10"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 text-xl dark:text-dark-text">Asisten AI UAP</h3>
                    <p class="text-sm text-green-600 font-semibold flex items-center">
                        <span class="inline-block w-2.5 h-2.5 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                        Online & Siap Membantu
                    </p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <button onclick="clearChat()" class="text-gray-500 hover:text-red-600 p-3 rounded-2xl hover:bg-red-50 dark:text-dark-text-secondary dark:hover:bg-red-900/50 dark:hover:text-red-400 interactive-hover focus-ring transition-all duration-300 group" title="Hapus Percakapan">
                    <svg class="w-6 h-6 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
                <button onclick="toggleDarkMode()" class="text-gray-500 hover:text-primary-600 p-3 rounded-2xl hover:bg-primary-50 dark:text-dark-text-secondary dark:hover:bg-dark-bg-secondary dark:hover:text-primary-400 interactive-hover focus-ring transition-all duration-300 group" title="Ganti Tema">
                    <svg id="theme-icon-light" class="w-6 h-6 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    <svg id="theme-icon-dark" class="w-6 h-6 group-hover:scale-110 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </button>
            </div>
        </div>
    </header>

    <main class="flex-1 max-w-5xl w-full p-6 mx-auto flex flex-col overflow-hidden">
        <div id="chat-window" class="flex-1 overflow-y-auto space-y-6 mb-6 p-2">
            <!-- Pesan akan ditambahkan di sini oleh JavaScript -->
        </div>

        <div class="bg-white/80 dark:bg-dark-bg-secondary/80 backdrop-blur-2xl p-4 rounded-3xl shadow-xl border border-white/30 dark:border-dark-border">
            <form id="chat-form" class="flex items-center space-x-4">
                <input type="text" id="message-input" placeholder="Tanya apa saja tentang UAP... 💬" autocomplete="off" class="w-full px-6 py-4 text-base rounded-2xl focus-ring transition-all duration-300 placeholder-gray-400 dark:placeholder-dark-text-secondary border-2 border-transparent focus:border-primary-400 bg-white/80 dark:bg-dark-bg-secondary/80">
                <button type="submit" class="bg-gradient-to-br from-primary-500 to-primary-700 text-white p-4 rounded-2xl focus-ring disabled:cursor-not-allowed group interactive-hover" id="submit-button" title="Kirim Pesan">
                    <svg id="send-icon" class="w-6 h-6 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="m3 3 3 9-3 9 19-9Z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 12h16" /></svg>
                    <svg id="loading-icon" class="w-6 h-6 loading-spinner hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                </button>
            </form>

            <div id="quick-actions" class="mt-4 flex flex-wrap gap-2">
                <button onclick="sendQuickMessage('Ceritakan tentang UAP')" class="px-4 py-2 bg-primary-100 text-primary-700 rounded-full text-sm font-medium hover:bg-primary-200 dark:bg-primary-900/50 dark:text-primary-300 dark:hover:bg-primary-900 interactive-hover"><i class="fas fa-university mr-2"></i>Tentang UAP</button>
                <button onclick="sendQuickMessage('Program studi apa saja yang tersedia?')" class="px-4 py-2 bg-primary-100 text-primary-700 rounded-full text-sm font-medium hover:bg-primary-200 dark:bg-primary-900/50 dark:text-primary-300 dark:hover:bg-primary-900 interactive-hover"><i class="fas fa-graduation-cap mr-2"></i>Program Studi</button>
                <button onclick="sendQuickMessage('Berapa biaya kuliah di UAP?')" class="px-4 py-2 bg-primary-100 text-primary-700 rounded-full text-sm font-medium hover:bg-primary-200 dark:bg-primary-900/50 dark:text-primary-300 dark:hover:bg-primary-900 interactive-hover"><i class="fas fa-money-bill-wave mr-2"></i>Biaya Kuliah</button>
                <button onclick="window.open('https://pmb.aisyahuniversity.ac.id/', '_blank')" class="px-4 py-2 bg-green-100 text-green-700 rounded-full text-sm font-medium hover:bg-green-200 dark:bg-green-900/50 dark:text-green-300 dark:hover:bg-green-900 interactive-hover"><i class="fas fa-user-plus mr-2"></i>Daftar Sekarang!</button>
            </div>
        </div>
    </main>

    <script>
        const GEMINI_API_KEY = "AIzaSyDWtyoLD3EAhjfw6S9-eD3io0_inCfj-Oc"; // Ganti dengan API Key Anda
        let universityData = {};
        let conversationHistory = [];

        const chatWindow = document.getElementById('chat-window');
        const chatForm = document.getElementById('chat-form');
        const messageInput = document.getElementById('message-input');
        const submitButton = document.getElementById('submit-button');
        const sendIcon = document.getElementById('send-icon');
        const loadingIcon = document.getElementById('loading-icon');
        const quickActions = document.getElementById('quick-actions');
        const themeIconLight = document.getElementById('theme-icon-light');
        const themeIconDark = document.getElementById('theme-icon-dark');

        function getSmarterContext() {
            return `
            Anda adalah "Asisten AI UAP", chatbot yang sangat cerdas, ramah, dan solutif untuk Universitas Aisyah Pringsewu.
            Anda memiliki akses ke data internal (JSON).
            
            ATURAN HIRARKI PENGETAHUAN (PENTING):
            1.  **Prioritas 1: Data Lokal (JSON)**: Untuk pertanyaan tentang UAP (prodi, biaya, dosen, visi misi, fasilitas, FAQ), SELALU gunakan data JSON yang disediakan sebagai sumber utama. Jawaban dari sini harus akurat dan sesuai data.
            2.  **Prioritas 2: Pengetahuan Umum AI**: Jika pertanyaan bersifat umum, konseptual, atau sama sekali tidak terkait dengan data lokal (contoh: "jelaskan apa itu javascript?"), barulah gunakan pengetahuan umum Anda.

            ATURAN CARA BERPIKIR (LOGIC RULES):
            - **SINTESIS DATA (PRIORITAS UTAMA)**: Jika sebuah topik (contoh: 'Perpustakaan') muncul di beberapa bagian JSON (misalnya di 'unit_kerja' DAN 'fasilitas'), Anda WAJIB menggabungkan semua informasi yang relevan dari kedua bagian tersebut.
            - **PENCARIAN NAMA FLEKSIBEL (SANGAT PENTING)**: Jika pengguna memberikan nama seseorang (dosen, pimpinan, admin), Anda harus melakukan pencarian parsial. Artinya, jika pengguna mengetik "ahlun", Anda harus mencari di seluruh data (dosen, pimpinan, admin_prodi) untuk nama lengkap yang **mengandung** kata "ahlun", seperti "Ahmad Ahlun Nazar, S.Kom". Jika ditemukan, berikan informasi lengkap tentang orang tersebut. Jangan melakukan pencocokan nama yang persis sama (exact match).
            - **FILTERING DATA**: Jika pengguna bertanya "Dosen FTI", filter data dosen yang memiliki "fakultas": "Fakultas Teknologi dan Informatika".
            - **Inferensi (Penyimpulan)**: Simpulkan maksud pengguna. "Dosen S3" berarti cari dosen dengan gelar "Dr." atau "Ph.D.".

            ATURAN INTERAKSI PROAKTIF:
            - Setelah menjawab pertanyaan tentang **biaya kuliah** suatu prodi, selalu tawarkan informasi tentang **beasiswa yang tersedia** atau **cara mendaftar**.
            - Jika pengguna bertanya tentang **satu prodi**, tawarkan juga untuk melihat prodi lain yang masih dalam **satu fakultas yang sama**.
            - Jika pertanyaan pengguna **terlalu umum** (contoh: "biayanya berapa?"), minta klarifikasi dengan sopan, "Tentu, maksud Anda biaya untuk program studi apa ya?".

            ATURAN CARA PENYAJIAN (FORMATTING RULES):
            - **Gunakan Tabel (WAJIB)**: Untuk data daftar/panjang (semua prodi, semua biaya, daftar dosen), WAJIB gunakan format tabel Markdown.
            - **Gunakan Daftar Poin/Bullet List**: Untuk enumerasi atau daftar pendek.
            - **Format Teks**: Gunakan **teks tebal** untuk menyorot informasi kunci. Gunakan emoji yang relevan.
            - **Data Tidak Ditemukan**: Jika informasi tidak ditemukan di JSON, jawab dengan sopan: "Maaf, saya tidak dapat menemukan informasi yang Anda cari saat ini."

            ---
            Berikut adalah data JSON UNIVERSITAS (Gunakan untuk Prioritas 1):
            ${JSON.stringify(universityData, null, 2)}
            `;
        }

        async function loadUniversityData() {
            try {
                const response = await fetch('../data/university_data.json');
                if (!response.ok) throw new Error('Network response was not ok');
                universityData = await response.json();
                console.log('Data universitas berhasil dimuat.');
            } catch (error) {
                console.error('Gagal memuat data universitas:', error);
                addMessage("❌ **Error**: Gagal memuat data informasi kampus.", 'bot');
            }
        }

        function formatResponse(message) {
            let formatted = message;
            // 1. Format Tabel Markdown
            const tableRegex = /\|(.+)\|\s*\n\|( *[-:]+ *\|)+\s*\n((?:\|(?:.*)\|\s*\n?)*)/g;
            formatted = formatted.replace(tableRegex, (match, header, separator, body) => {
                const headers = header.split('|').map(h => h.trim()).filter(Boolean);
                const rows = body.trim().split('\n').map(row => row.split('|').map(cell => cell.trim()).filter(Boolean));
                let tableHtml = '<div class="overflow-x-auto my-4"><table class="chat-table">';
                tableHtml += '<thead><tr>';
                headers.forEach(h => tableHtml += `<th>${h}</th>`);
                tableHtml += '</tr></thead>';
                tableHtml += '<tbody>';
                rows.forEach(row => {
                    tableHtml += '<tr>';
                    row.forEach(cell => tableHtml += `<td>${cell}</td>`);
                    tableHtml += '</tr>';
                });
                tableHtml += '</tbody></table></div>';
                return tableHtml;
            });

            // 2. Format Teks Tebal
            formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            // 3. Format Link
            formatted = formatted.replace(/\[([^\]]+)\]\((https?:\/\/[^\)]+)\)/g, '<a href="$2" target="_blank" rel="noopener noreferrer" class="text-primary-600 dark:text-primary-400 hover:underline font-semibold">$1</a>');
            // 4. Format Email menjadi link mailto:
            formatted = formatted.replace(/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9_-]+)/gi, '<a href="mailto:$1" class="text-primary-600 dark:text-primary-400 hover:underline font-semibold">$1</a>');
            // 5. Format Nomor Telepon menjadi link tel: (untuk format Indonesia)
            formatted = formatted.replace(/(08[0-9]{8,12})/g, '<a href="tel:+$1" class="text-primary-600 dark:text-primary-400 hover:underline font-semibold">$1</a>');

            // 6. Proses Paragraf dan Bullet List
            return formatted.split('\n\n').map(paragraph => {
                const listRegex = /((?:\n|^)\s*[\*\-]\s.+)+/g;
                if (listRegex.test(paragraph)) {
                    const items = paragraph.trim().split('\n');
                    const listItems = items.map(item => `<li>${item.trim().substring(2).trim()}</li>`).join('');
                    return `<ul>${listItems}</ul>`;
                }
                return `<p>${paragraph.replace(/\n/g, '<br>')}</p>`;
            }).join('').replace(/<p><\/p>/g, '');
        }

        function addMessage(message, sender) {
            const time = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            const messageElement = document.createElement('div');
            if (sender === 'user') {
                messageElement.className = 'flex justify-end message-enter';
                messageElement.innerHTML = `<div class="chat-bubble-user p-5 max-w-xl text-white"><div class="text-base leading-relaxed font-medium">${message}</div><div class="text-xs text-purple-200 mt-3 text-right opacity-75">${time}</div></div>`;
                quickActions.style.display = 'none';
            } else if (sender === 'typing') {
                messageElement.id = 'typing-indicator';
                messageElement.className = 'flex justify-start message-enter';
                messageElement.innerHTML = `<div class="chat-bubble-bot p-5"><div class="flex items-center space-x-2"><div class="flex space-x-1"><div class="w-2.5 h-2.5 bg-primary-400 rounded-full typing-dot"></div><div class="w-2.5 h-2.5 bg-primary-400 rounded-full typing-dot" style="animation-delay: 0.2s;"></div><div class="w-2.5 h-2.5 bg-primary-400 rounded-full typing-dot" style="animation-delay: 0.4s;"></div></div><span class="text-sm text-gray-500 dark:text-dark-text-secondary ml-3">Sedang mengetik...</span></div></div>`;
            } else {
                const formattedMessage = formatResponse(message);
                messageElement.className = 'flex justify-start message-enter';
                messageElement.innerHTML = `<div class="chat-bubble-bot p-5 max-w-4xl w-full"><div class="text-base leading-relaxed text-gray-800 dark:text-dark-text">${formattedMessage}</div><div class="text-xs text-gray-500 dark:text-dark-text-secondary mt-3 text-left opacity-75 flex items-center"><i class="fas fa-robot mr-2"></i>${time}</div></div>`;
            }
            chatWindow.appendChild(messageElement);
            chatWindow.scrollTop = chatWindow.scrollHeight;
        }

        async function callGeminiAPI(userMessage) {
            setLoadingState(true);
            addMessage('', 'typing');
            conversationHistory.push({ role: 'user', parts: [{ text: userMessage }] });
            const apiUrl = `https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=${GEMINI_API_KEY}`;
            const contents = [
                { role: 'user', parts: [{ text: getSmarterContext() }] },
                { role: 'model', parts: [{ text: "Tentu, saya siap membantu. Apa yang ingin Anda ketahui tentang Universitas Aisyah Pringsewu?" }] },
                ...conversationHistory
            ];
            try {
                const response = await fetch(apiUrl, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ contents }) });
                if (!response.ok) { const errorData = await response.json(); throw new Error(errorData.error?.message || 'Unknown API error'); }
                const data = await response.json();
                const result = data.candidates?.[0]?.content?.parts?.[0]?.text.trim() || "Maaf, saya tidak dapat memberikan jawaban saat ini.";
                conversationHistory.push({ role: 'model', parts: [{ text: result }] });
                return result;
            } catch (error) {
                console.error("Gagal menghubungi Gemini API:", error);
                conversationHistory.pop();
                return "🔄 Maaf, terjadi gangguan saat menghubungi sistem AI. Silakan coba lagi.";
            }
        }

        function setLoadingState(isLoading) {
            submitButton.disabled = isLoading;
            sendIcon.classList.toggle('hidden', isLoading);
            loadingIcon.classList.toggle('hidden', !isLoading);
            messageInput.disabled = isLoading;
        }

        function clearChat() {
            if (chatWindow.children.length <= 1) return;
            chatWindow.innerHTML = '';
            conversationHistory = [];
            addWelcomeMessage();
            quickActions.style.display = 'flex';
        }

        function addWelcomeMessage() {
            const welcomeElement = document.createElement('div');
            welcomeElement.className = 'bg-gradient-to-br from-slate-100 to-gray-200 dark:from-dark-bg-secondary dark:to-dark-bg p-6 rounded-2xl shadow-md message-enter';
            welcomeElement.innerHTML = `<div class="text-center"><div class="w-20 h-20 bg-gradient-to-br from-primary-500 to-primary-700 rounded-3xl mx-auto mb-5 flex items-center justify-center shadow-lg"><i class="fas fa-robot text-white text-4xl"></i></div><h3 class="text-2xl font-bold text-gray-800 dark:text-dark-text mb-2">Selamat Datang!</h3><p class="text-gray-600 dark:text-dark-text-secondary">Saya Asisten AI UAP, siap membantu Anda menemukan informasi seputar kampus. Silakan ajukan pertanyaan atau gunakan tombol di bawah.</p></div>`;
            chatWindow.appendChild(welcomeElement);
        }

        function sendQuickMessage(message) {
            messageInput.value = message;
            messageInput.focus();
            chatForm.dispatchEvent(new Event('submit'));
        }

        function applyTheme(isDark) {
            document.documentElement.classList.toggle('dark', isDark);
            themeIconLight.classList.toggle('hidden', isDark);
            themeIconDark.classList.toggle('hidden', !isDark);
        }

        function toggleDarkMode() {
            const isDarkMode = document.documentElement.classList.contains('dark');
            localStorage.setItem('darkMode', !isDarkMode);
            applyTheme(!isDarkMode);
        }

        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const userMessage = messageInput.value.trim();
            if (userMessage === '') return;
            addMessage(userMessage, 'user');
            messageInput.value = '';
            const botResponse = await callGeminiAPI(userMessage);
            document.getElementById('typing-indicator')?.remove();
            addMessage(botResponse, 'bot');
            setLoadingState(false);
            messageInput.focus();
        });

        messageInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                chatForm.dispatchEvent(new Event('submit'));
            }
        });

        window.addEventListener('load', async () => {
            const prefersDark = localStorage.getItem('darkMode') === 'true' || (localStorage.getItem('darkMode') === null && window.matchMedia('(prefers-color-scheme: dark)').matches);
            applyTheme(prefersDark);
            await loadUniversityData();
            addWelcomeMessage();
            messageInput.focus();
        });
    </script>

</body>
</html>
