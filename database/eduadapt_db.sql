-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 23 Apr 2026 pada 16.45
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eduadapt_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `diagnostic_questions`
--

CREATE TABLE `diagnostic_questions` (
  `id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `option_a` varchar(255) NOT NULL,
  `option_b` varchar(255) NOT NULL,
  `option_c` varchar(255) NOT NULL,
  `option_d` varchar(255) NOT NULL,
  `correct_option` enum('a','b','c','d') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `diagnostic_questions`
--

INSERT INTO `diagnostic_questions` (`id`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `created_at`) VALUES
(1, 'Apa kepanjangan dari HTML?', 'Hyper Text Markup Language', 'High Tech Multi Language', 'Hyper Tool Multi Level', 'Home Tool Markup Language', 'a', '2026-04-21 13:06:29'),
(2, 'Tag mana yang digunakan untuk membuat link?', '<link>', '<a>', '<url>', '<href>', 'b', '2026-04-21 13:06:29'),
(3, 'Properti CSS mana yang digunakan untuk mengubah warna teks?', 'font-color', 'text-style', 'color', 'background-color', 'c', '2026-04-21 13:06:29'),
(4, 'Manakah yang merupakan framework JavaScript?', 'Laravel', 'Django', 'React', 'Flask', 'c', '2026-04-21 13:06:29'),
(5, 'Simbol apa yang digunakan untuk ID di CSS?', '. (titik)', '# (pagar)', '* (bintang)', '@ (at)', 'b', '2026-04-21 13:06:29'),
(6, 'Cara membuat komentar di HTML adalah...', '// komentar', '/* komentar */', '<!-- komentar -->', '\' komentar', 'c', '2026-04-21 13:06:29'),
(7, 'Atribut mana yang wajib ada pada tag <img>?', 'src', 'href', 'link', 'url', 'a', '2026-04-21 13:06:29'),
(8, 'Apa fungsi dari \'flexbox\' di CSS?', 'Mengelola database', 'Membuat tata letak responsif', 'Membuat animasi 3D', 'Mengamankan data', 'b', '2026-04-21 13:06:29'),
(9, 'Tag untuk judul paling besar di HTML adalah?', '<h6>', '<head>', '<header>', '<h1>', 'd', '2026-04-21 13:06:29'),
(10, 'JavaScript adalah bahasa pemrograman jenis?', 'Markup', 'Styling', 'Programming', 'Query', 'c', '2026-04-21 13:06:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `modules`
--

CREATE TABLE `modules` (
  `id` int(11) NOT NULL,
  `level` enum('Beginner','Intermediate','Advanced') NOT NULL,
  `title` varchar(150) NOT NULL,
  `type` varchar(50) NOT NULL,
  `content_text` text DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `sequence_order` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `modules`
--

INSERT INTO `modules` (`id`, `level`, `title`, `type`, `content_text`, `video_url`, `sequence_order`, `created_at`) VALUES
(2, 'Beginner', 'Pengenalan HTML Dasar', 'Video & Bacaan', '<h3>Apa itu HTML?</h3>\n<p>HTML (HyperText Markup Language) adalah bahasa standar yang digunakan untuk membuat dan menyusun konten di halaman web. HTML menggunakan sistem <strong>tag</strong> untuk mendefinisikan elemen-elemen seperti teks, gambar, link, dan lainnya.</p>\n\n<h4>Struktur Dasar HTML</h4>\n<p>Setiap halaman HTML memiliki struktur dasar yang terdiri dari:</p>\n<ul>\n    <li><code>&lt;!DOCTYPE html&gt;</code> — Deklarasi tipe dokumen</li>\n    <li><code>&lt;html&gt;</code> — Elemen root</li>\n    <li><code>&lt;head&gt;</code> — Metadata halaman (title, link CSS, dll)</li>\n    <li><code>&lt;body&gt;</code> — Konten yang terlihat di browser</li>\n</ul>\n\n<h4>Tag-Tag Penting</h4>\n<p>Beberapa tag dasar yang wajib kamu ketahui:</p>\n<ul>\n    <li><code>&lt;h1&gt;</code> sampai <code>&lt;h6&gt;</code> — Heading/judul</li>\n    <li><code>&lt;p&gt;</code> — Paragraf</li>\n    <li><code>&lt;a href=\"...\"&gt;</code> — Link/hyperlink</li>\n    <li><code>&lt;img src=\"...\" alt=\"...\"&gt;</code> — Gambar</li>\n    <li><code>&lt;ul&gt;</code>, <code>&lt;ol&gt;</code>, <code>&lt;li&gt;</code> — Daftar</li>\n</ul>\n\n<div class=\"alert alert-info mt-3\">\n    <strong>Tips:</strong> Selalu tutup tag-tag HTML dengan benar. Tag yang tidak ditutup bisa menyebabkan tampilan berantakan!\n</div>', 'https://www.youtube.com/embed/jD_BAS-tmWw', 1, '2026-04-21 13:06:29'),
(3, 'Beginner', 'Styling dengan CSS', 'Video & Kuis', '<h3>Mengenal CSS (Cascading Style Sheets)</h3>\n<p>CSS digunakan untuk mengatur tampilan dan layout halaman web. Tanpa CSS, halaman web akan terlihat polos dan membosankan seperti dokumen biasa.</p>\n\n<h4>3 Cara Menulis CSS</h4>\n<ol>\n    <li><strong>Inline CSS</strong> — langsung di atribut <code>style=\"\"</code></li>\n    <li><strong>Internal CSS</strong> — di dalam tag <code>&lt;style&gt;</code> di head</li>\n    <li><strong>External CSS</strong> — file <code>.css</code> terpisah (DIREKOMENDASIKAN)</li>\n</ol>\n\n<h4>Selector Dasar</h4>\n<ul>\n    <li><code>element</code> — Memilih semua elemen (contoh: <code>p { }</code>)</li>\n    <li><code>.class</code> — Memilih elemen berdasarkan class</li>\n    <li><code>#id</code> — Memilih elemen berdasarkan ID</li>\n</ul>\n\n<h4>Properti CSS Populer</h4>\n<ul>\n    <li><code>color</code> — Warna teks</li>\n    <li><code>background-color</code> — Warna latar belakang</li>\n    <li><code>font-size</code> — Ukuran font</li>\n    <li><code>margin</code> dan <code>padding</code> — Jarak elemen</li>\n    <li><code>border</code> — Garis tepi</li>\n</ul>\n\n<div class=\"alert alert-warning mt-3\">\n    <strong> Ingat:</strong> CSS bersifat <em>cascading</em>, artinya aturan yang ditulis terakhir akan menimpa aturan sebelumnya jika selector-nya sama!\n</div>', 'https://www.youtube.com/embed/1PnVor36_40', 2, '2026-04-21 13:06:29'),
(4, 'Beginner', 'Struktur Halaman Web', 'Projek Kecil', '<h3>HTML5 Semantic Elements</h3>\n<p>HTML5 memperkenalkan tag-tag semantik yang membuat struktur halaman web lebih bermakna dan mudah dipahami, baik oleh manusia maupun mesin pencari.</p>\n\n<h4>Tag Semantik Utama</h4>\n<ul>\n    <li><code>&lt;header&gt;</code> — Bagian atas halaman (logo, navbar)</li>\n    <li><code>&lt;nav&gt;</code> — Menu navigasi</li>\n    <li><code>&lt;main&gt;</code> — Konten utama halaman</li>\n    <li><code>&lt;article&gt;</code> — Konten mandiri (postingan blog, berita)</li>\n    <li><code>&lt;section&gt;</code> — Pengelompokan konten tematik</li>\n    <li><code>&lt;aside&gt;</code> — Konten sampingan (sidebar)</li>\n    <li><code>&lt;footer&gt;</code> — Bagian bawah halaman</li>\n</ul>\n\n<h4>Kenapa Semantik Penting?</h4>\n<ol>\n    <li><strong>Aksesibilitas</strong> — Screen reader bisa memahami struktur</li>\n    <li><strong>SEO</strong> — Mesin pencari memahami konten lebih baik</li>\n    <li><strong>Maintainability</strong> — Kode lebih mudah dibaca developer lain</li>\n</ol>\n\n<div class=\"alert alert-success mt-3\">\n    <strong> Projek Mini:</strong> Coba buat halaman profil sederhana menggunakan semua tag semantik di atas. Upload hasilnya sebagai file HTML!\n</div>', 'https://www.youtube.com/embed/kUMe1FH4CHE', 3, '2026-04-21 13:06:29'),
(5, 'Intermediate', 'CSS Flexbox & Grid', 'Video & Bacaan', '<h3>Layout Modern dengan Flexbox & Grid</h3>\n<p>Dua fitur CSS terpenting untuk membuat layout modern yang responsif.</p>\n\n<h4>Flexbox</h4>\n<p>Flexbox dirancang untuk layout <strong>satu dimensi</strong> (baris ATAU kolom).</p>\n<pre><code>.container {\n    display: flex;\n    justify-content: center;    /* Rata tengah horizontal */\n    align-items: center;        /* Rata tengah vertikal */\n    gap: 16px;                  /* Jarak antar item */\n}</code></pre>\n\n<h4>CSS Grid</h4>\n<p>Grid dirancang untuk layout <strong>dua dimensi</strong> (baris DAN kolom sekaligus).</p>\n<pre><code>.grid-container {\n    display: grid;\n    grid-template-columns: repeat(3, 1fr);  /* 3 kolom sama rata */\n    grid-gap: 20px;\n}</code></pre>\n\n<h4>Kapan Pakai Flexbox vs Grid?</h4>\n<ul>\n    <li><strong>Flexbox</strong> → Navbar, card row, centering sederhana</li>\n    <li><strong>Grid</strong> → Gallery foto, dashboard layout, layout kompleks</li>\n</ul>\n\n<div class=\"alert alert-info mt-3\">\n    <strong>Pro Tip:</strong> Kamu bahkan bisa mengkombinasikan Flexbox dan Grid! Grid untuk layout besar, Flexbox untuk detail dalam setiap grid item.\n</div>', 'https://www.youtube.com/embed/tXIhdp5R7sc', 1, '2026-04-21 13:06:29'),
(6, 'Intermediate', 'Logika Dasar JavaScript', 'Video & Kuis', '<h3>JavaScript — Bahasa Pemrograman Web</h3>\n<p>JavaScript membuat halaman web menjadi <strong>interaktif</strong>. JS berjalan di browser tanpa perlu kompilasi.</p>\n\n<h4>Variabel & Tipe Data</h4>\n<pre><code>let nama = \"Budi\";           // String\nconst umur = 17;              // Number\nlet sudahLulus = false;       // Boolean\nlet hobi = [\"coding\",\"game\"]; // Array</code></pre>\n\n<h4>Kondisional</h4>\n<pre><code>if (nilai >= 80) {\n    console.log(\"Lulus! 🎉\");\n} else if (nilai >= 60) {\n    console.log(\"Remedial dulu ya...\");\n} else {\n    console.log(\"Belajar lagi! \");\n}</code></pre>\n\n<h4>Perulangan</h4>\n<pre><code>for (let i = 0; i < 5; i++) {\n    console.log(\"Iterasi ke-\" + i);\n}\n\n// Array loop\nhobi.forEach(h => console.log(h));</code></pre>\n\n<h4>Fungsi</h4>\n<pre><code>// Function declaration\nfunction hitungLuas(p, l) {\n    return p * l;\n}\n\n// Arrow function (ES6)\nconst sapa = (nama) => `Halo, ${nama}!`;</code></pre>\n\n<div class=\"alert alert-warning mt-3\">\n    <strong>Perhatian:</strong> Gunakan <code>===</code> (strict equality) daripada <code>==</code> untuk menghindari type coercion yang tidak diinginkan!\n</div>', 'https://www.youtube.com/embed/W6NZfCO5SIk', 2, '2026-04-21 13:06:29'),
(7, 'Intermediate', 'Manipulasi DOM', 'Projek Kecil', '<h3>Document Object Model (DOM)</h3>\n<p>DOM adalah representasi terstruktur halaman HTML yang bisa dimanipulasi menggunakan JavaScript. Inilah yang membuat halaman web <em>hidup</em>!</p>\n\n<h4>Mengakses Elemen</h4>\n<pre><code>// By ID\nlet judul = document.getElementById(\"judul\");\n\n// By Class\nlet cards = document.getElementsByClassName(\"card\");\n\n// CSS Selector (REKOMENDASI)\nlet btn = document.querySelector(\".btn-primary\");\nlet allBtns = document.querySelectorAll(\".btn\");</code></pre>\n\n<h4>Mengubah Konten & Style</h4>\n<pre><code>judul.textContent = \"Judul Baru\";\njudul.innerHTML = \"<em>Judul Italic</em>\";\njudul.style.color = \"#4f46e5\";\njudul.classList.add(\"active\");</code></pre>\n\n<h4>Event Listener</h4>\n<pre><code>btn.addEventListener(\"click\", function() {\n    alert(\"Tombol diklik! \");\n});\n\n// Atau dengan arrow function\nbtn.addEventListener(\"click\", () => {\n    judul.style.display = \"none\";\n});</code></pre>\n\n<div class=\"alert alert-success mt-3\">\n    <strong>🎯 Projek Mini:</strong> Buat To-Do List sederhana menggunakan DOM manipulation — tambah item, hapus item, tandai selesai!\n</div>', 'https://www.youtube.com/embed/y17RuWkWdn8', 3, '2026-04-21 13:06:29'),
(8, 'Advanced', 'React Fundamentals', 'Video & Bacaan', '<h3>React.js — Library UI Modern</h3>\n<p>React adalah library JavaScript yang dikembangkan oleh Meta (Facebook) untuk membangun <strong>user interface</strong> yang dinamis dan efisien.</p>\n\n<h4>Konsep Utama React</h4>\n<ul>\n    <li><strong>Component-Based</strong> — UI dipecah menjadi komponen-komponen kecil yang reusable</li>\n    <li><strong>JSX</strong> — Sintaks template yang menggabungkan HTML dengan JavaScript</li>\n    <li><strong>Virtual DOM</strong> — React hanya update bagian DOM yang berubah (efisien!)</li>\n    <li><strong>One-Way Data Flow</strong> — Data mengalir dari parent ke child melalui props</li>\n</ul>\n\n<h4>Membuat Komponen</h4>\n<pre><code>// Functional Component\nfunction Greeting({ name }) {\n    return (\n        &lt;div className=\"greeting\"&gt;\n            &lt;h2&gt;Halo, {name}! &lt;/h2&gt;\n        &lt;/div&gt;\n    );\n}\n\n// Penggunaan\n&lt;Greeting name=\"Budi\" /&gt;</code></pre>\n\n<h4>useState Hook</h4>\n<pre><code>import { useState } from \"react\";\n\nfunction Counter() {\n    const [count, setCount] = useState(0);\n    \n    return (\n        &lt;button onClick={() =&gt; setCount(count + 1)}&gt;\n            Diklik {count} kali\n        &lt;/button&gt;\n    );\n}</code></pre>\n\n<div class=\"alert alert-info mt-3\">\n    <strong> Fun Fact:</strong> Instagram, Netflix, dan Tokopedia dibangun menggunakan React! \n</div>', 'https://www.youtube.com/embed/Tn6-PIqc4UM', 1, '2026-04-21 13:06:29'),
(9, 'Advanced', 'State Management', 'Video & Kuis', '<h3>State Management di React</h3>\n<p>Saat aplikasi semakin kompleks, mengelola <em>state</em> (data/kondisi) menjadi tantangan utama. React menyediakan beberapa solusi untuk ini.</p>\n\n<h4>useState vs useReducer</h4>\n<pre><code>// useState — untuk state sederhana\nconst [name, setName] = useState(\"\");\n\n// useReducer — untuk logika state kompleks\nconst [state, dispatch] = useReducer(reducer, initialState);\n\nfunction reducer(state, action) {\n    switch (action.type) {\n        case \"INCREMENT\": return { count: state.count + 1 };\n        case \"DECREMENT\": return { count: state.count - 1 };\n        default: return state;\n    }\n}</code></pre>\n\n<h4>useEffect Hook</h4>\n<pre><code>useEffect(() => {\n    // Jalankan saat component mount atau dependency berubah\n    fetchData();\n    \n    return () => {\n        // Cleanup saat component unmount\n    };\n}, [dependency]);</code></pre>\n\n<h4>Context API</h4>\n<pre><code>// Membuat context\nconst ThemeContext = createContext(\"light\");\n\n// Provider\n&lt;ThemeContext.Provider value=\"dark\"&gt;\n    &lt;App /&gt;\n&lt;/ThemeContext.Provider&gt;\n\n// Consumer (hook)\nconst theme = useContext(ThemeContext);</code></pre>\n\n<div class=\"alert alert-warning mt-3\">\n    <strong>Golden Rule:</strong> Lift state up! Pindahkan state ke parent terdekat yang membutuhkannya, hindari prop drilling yang berlebihan.\n</div>', 'https://www.youtube.com/embed/35lXWvCuM8o', 2, '2026-04-21 13:06:29'),
(10, 'Advanced', 'Integrasi API Eksternal', 'Projek Besar', '<h3>Mengintegrasikan API ke Aplikasi Web</h3>\n<p>API (Application Programming Interface) memungkinkan aplikasi web kamu berkomunikasi dengan server dan layanan eksternal untuk mendapatkan atau mengirim data.</p>\n\n<h4>Fetch API</h4>\n<pre><code>// GET Request\nasync function getUsers() {\n    const response = await fetch(\"https://api.example.com/users\");\n    const data = await response.json();\n    console.log(data);\n}\n\n// POST Request\nasync function createUser(userData) {\n    const response = await fetch(\"https://api.example.com/users\", {\n        method: \"POST\",\n        headers: { \"Content-Type\": \"application/json\" },\n        body: JSON.stringify(userData)\n    });\n    return await response.json();\n}</code></pre>\n\n<h4>Error Handling</h4>\n<pre><code>try {\n    const res = await fetch(url);\n    if (!res.ok) throw new Error(\"HTTP Error: \" + res.status);\n    const data = await res.json();\n} catch (error) {\n    console.error(\"Gagal fetch:\", error.message);\n}</code></pre>\n\n<h4>HTTP Status Codes Penting</h4>\n<ul>\n    <li><code>200</code> — OK (berhasil)</li>\n    <li><code>201</code> — Created (data berhasil dibuat)</li>\n    <li><code>400</code> — Bad Request (request salah)</li>\n    <li><code>401</code> — Unauthorized (belum login)</li>\n    <li><code>404</code> — Not Found (data tidak ditemukan)</li>\n    <li><code>500</code> — Internal Server Error (error di server)</li>\n</ul>\n\n<div class=\"alert alert-success mt-3\">\n    <strong>Projek Besar:</strong> Buat aplikasi web yang menampilkan data dari API publik (contoh: cuaca, berita, atau anime). Gunakan fetch API + DOM manipulation!\n</div>', 'https://www.youtube.com/embed/cuEtnrL9-H0', 3, '2026-04-21 13:06:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `module_quizzes`
--

CREATE TABLE `module_quizzes` (
  `id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `option_a` varchar(255) NOT NULL,
  `option_b` varchar(255) NOT NULL,
  `option_c` varchar(255) NOT NULL,
  `option_d` varchar(255) NOT NULL,
  `correct_option` enum('a','b','c','d') NOT NULL,
  `feedback` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `module_quizzes`
--

INSERT INTO `module_quizzes` (`id`, `module_id`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `feedback`) VALUES
(1, 2, 'Tag yang digunakan untuk membuat paragraf adalah?', '<p>', '<para>', '<text>', '<paragraph>', 'a', 'Mantap! <p> itu singkatan dari paragraph. Kamu udah selangkah lebih dekat jadi web developer! 🚀'),
(2, 2, 'Atribut \'href\' biasanya digunakan pada tag?', '<img>', '<a>', '<p>', '<div>', 'b', 'Yee bener! href = Hypertext REFerence. Tag <a> ini paspor internet kamu buat jalan-jalan ke halaman lain!'),
(3, 2, 'Tag untuk membuat daftar terurut (bernomor) adalah?', '<ul>', '<li>', '<ol>', '<dl>', 'c', 'Oke banget! <ol> = Ordered List. Kalau mau daftar tanpa nomor, baru pakai <ul> ya!'),
(4, 2, 'Ekstensi file HTML yang benar adalah?', '.htm atau .html', '.web', '.page', '.css', 'a', 'Benar! File HTML bisa pakai .htm atau .html. Dua-duanya diterima browser dengan lapang dada 😂'),
(5, 2, 'Tag untuk menampilkan gambar di HTML adalah?', '<picture>', '<image>', '<img>', '<photo>', 'c', 'Tepat sekali! <img> itu self-closing tag, jadi gak perlu tag penutup. Praktis kan? 📸'),
(6, 3, 'Cara menghubungkan file CSS eksternal ke HTML adalah menggunakan tag?', '<style>', '<link>', '<css>', '<script>', 'b', 'Mantul! Tag <link> di <head> dengan rel=\'stylesheet\'. CSS kamu sekarang rapi dan terorganisir! 💅'),
(7, 3, 'Properti CSS untuk mengubah ukuran font adalah?', 'text-size', 'font-style', 'font-size', 'text-font', 'c', 'Yap, font-size! Mau font segede papan billboard atau sekecil semut, font-size jawabannya 🔤'),
(8, 3, 'Selector untuk memilih elemen berdasarkan class adalah?', '# (pagar)', '. (titik)', '* (bintang)', '@ (at)', 'b', 'Class pakai titik, ID pakai pagar. Gampang diingat: Class itu banyak murid, jadi titik-titik aja! 😄'),
(9, 3, 'Properti CSS untuk memberi jarak LUAR elemen adalah?', 'padding', 'border', 'margin', 'spacing', 'c', 'Margin = jarak luar, Padding = jarak dalam. Kayak bantal (padding) di dalam sarung bantal (margin) 🛏️'),
(10, 3, 'Nilai CSS \'color: #4f46e5\' menghasilkan warna?', 'Merah', 'Hijau', 'Indigo/Biru Tua', 'Kuning', 'c', 'Tepat! Itu warna indigo yang kece. Hex code dimulai dari 00 (gelap) sampai FF (terang) per channel RGB 🎨'),
(11, 4, 'Tag semantik HTML5 untuk bagian atas halaman (logo, navbar) adalah?', '<top>', '<header>', '<head>', '<heading>', 'b', 'Bener! <header> beda sama <head> ya. <header> muncul di halaman, <head> cuma metadata. Jangan ketuker! 😅'),
(12, 4, 'Tag untuk menyimpan konten utama halaman adalah?', '<content>', '<body>', '<main>', '<center>', 'c', 'Mantap! <main> adalah konten inti halaman. Hanya boleh ada SATU <main> per halaman. Spesial kayak kamu! ⭐'),
(13, 4, 'Tag semantik untuk navigasi menu adalah?', '<menu>', '<nav>', '<links>', '<navbar>', 'b', 'Yes! <nav> memberitahu browser dan screen reader: \'Hei, ini area navigasi!\' Accessibility matters! ♿'),
(14, 4, 'Tag untuk bagian paling bawah halaman (copyright, dll) adalah?', '<bottom>', '<end>', '<footer>', '<base>', 'c', 'Footer = kaki halaman. Biasanya isi copyright, link penting, dan contact info. Simpel tapi penting! 👣'),
(15, 4, 'Apa tujuan utama menggunakan tag semantik?', 'Mempercepat loading', 'Membuat halaman lebih berwarna', 'Memberi makna/arti pada struktur konten', 'Menambah animasi otomatis', 'c', 'Semantik = bermakna. Mesin pencari dan screen reader jadi paham struktur halaman kamu. SEO naik! 📈'),
(16, 5, 'Properti CSS untuk mengaktifkan Flexbox adalah?', 'display: block', 'display: flex', 'position: flex', 'layout: flexbox', 'b', 'Oke gasss! display: flex mengubah container jadi flex container. Anak-anaknya otomatis jadi flex items! 💪'),
(17, 5, 'Properti flex untuk meratakan item di sumbu utama (horizontal) adalah?', 'align-items', 'flex-align', 'justify-content', 'text-align', 'c', 'justify-content itu horizontal, align-items itu vertikal. Ingat: Justify = Jalan horizontal! 🛤️'),
(18, 5, 'Cara membuat layout grid dengan 3 kolom sama rata?', 'grid-columns: 3', 'grid-template-columns: repeat(3, 1fr)', 'columns: 3 equal', 'display: grid-3', 'b', '1fr = 1 fraction. repeat(3, 1fr) bikin 3 kolom yang bagi ruang sama rata. Adil kayak bagi kue! 🍰'),
(19, 5, 'Properti CSS untuk mengaktifkan CSS Grid adalah?', 'display: table', 'display: grid', 'layout: grid', 'position: grid', 'b', 'display: grid — dan boom! Kamu punya superpowers untuk bikin layout 2 dimensi. Rows AND columns! 🦸'),
(20, 5, 'Properti flex-direction: column akan mengatur item secara?', 'Horizontal (berjajar)', 'Vertikal (bertumpuk)', 'Diagonal', 'Melingkar', 'b', 'flex-direction: column bikin item numpuk dari atas ke bawah. Default-nya row (kiri ke kanan). Gampang kan? 📐'),
(21, 6, 'Cara mendeklarasikan variabel di JavaScript modern (ES6) adalah?', 'var saja', 'let dan const', 'dim dan set', 'new variable', 'b', 'let untuk yang bisa berubah, const untuk yang tetap. var udah jadul, kayak HP Nokia 3310 — masih jalan sih, tapi... 📱'),
(22, 6, 'Operator perbandingan ketat (strict equality) di JavaScript adalah?', '==', '===', '!=', '=', 'b', '=== cek NILAI dan TIPE. == cuma cek nilai. \'5\' == 5 itu true, tapi \'5\' === 5 itu false. Strict lebih aman! 🔒'),
(23, 6, 'Fungsi untuk menampilkan output di console browser?', 'print()', 'echo()', 'console.log()', 'System.out()', 'c', 'console.log() — sahabat terbaik developer! F12 → Console, dan mulai debugging kayak detektif 🕵️'),
(24, 6, 'Cara membuat arrow function (ES6) adalah?', 'function => {}', 'const fn = () => {}', 'arrow fn() => {}', 'def fn(): =>', 'b', 'Arrow function itu singkat dan kece: const greet = (name) => `Halo projects!`. Less code, more power! ⚡'),
(25, 6, 'Tipe data Boolean hanya berisi nilai?', '0 dan 1', 'ya dan tidak', 'true dan false', 'benar dan salah', 'c', 'true atau false — itu aja. Boolean kayak saklar lampu: ON atau OFF. Simpel tapi fundamental! 💡'),
(26, 7, 'Method JS untuk mengambil elemen berdasarkan ID?', 'querySelector()', 'getElementById()', 'getElement()', 'findById()', 'b', 'document.getElementById(\'namaId\') — cara klasik dan tetap powerful untuk akses elemen spesifik! 🎯'),
(27, 7, 'Properti untuk mengubah isi HTML di dalam sebuah elemen?', 'textContent', 'innerHTML', 'outerHTML', 'value', 'b', 'innerHTML bisa set HTML lengkap termasuk tag. Kalau cuma teks, pakai textContent yang lebih aman dari XSS! 🛡️'),
(28, 7, 'Method untuk menambahkan event listener pada elemen?', 'onClick()', 'addEvent()', 'addEventListener()', 'onEvent()', 'c', 'addEventListener(\'event\', callback) — ini cara modern dan fleksibel! Bisa pasang banyak listener di satu elemen 👂'),
(29, 7, 'Cara mengambil elemen menggunakan CSS selector?', 'document.find()', 'document.querySelector()', 'document.select()', 'document.css()', 'b', 'querySelector pakai syntax CSS selector. Mau \'#id\', \'.class\', atau \'div > p\', semua bisa! Serbaguna! 🛠️'),
(30, 7, 'Method untuk membuat elemen HTML baru via JavaScript?', 'document.createElement()', 'document.newElement()', 'document.make()', 'document.build()', 'a', 'createElement(\'tag\') lalu appendChild() untuk masukkan ke DOM. Kayak LEGO — bikin piece lalu pasang! 🧱'),
(31, 8, 'Perintah CLI untuk membuat project React terbaru adalah?', 'npm init react', 'npx create-react-app', 'react new project', 'npm start react', 'b', 'npx create-react-app my-app — dan BOOM, project React siap dalam hitungan menit! Magic! ✨'),
(32, 8, 'Hook untuk mengelola state di functional component?', 'useRef', 'useState', 'useEffect', 'useContext', 'b', 'const [value, setValue] = useState(initial). Simpel, powerful, dan jadi dasar semua interaktivitas React! 🎮'),
(33, 8, 'Cara mengirim data dari parent component ke child component?', 'State', 'Props', 'Context', 'Redux', 'b', 'Props = Properties. Parent kasih data, child terima. One-way delivery, kayak paket JNE satu arah! 📦'),
(34, 8, 'Sintaks template di React yang mirip HTML tapi di dalam JavaScript disebut?', 'XML', 'JSX', 'TSX', 'XHTML', 'b', 'JSX = JavaScript XML. Kamu nulis \'HTML\' di dalam JS, dan React yang compile jadi JavaScript murni. Keren! 🧪'),
(35, 8, 'Virtual DOM di React berfungsi untuk?', 'Mengganti seluruh halaman', 'Membandingkan perubahan dan update efisien', 'Membuat halaman offline', 'Menyimpan data di cloud', 'b', 'Virtual DOM = copy ringan dari real DOM. React bandingkan, cari bedanya, lalu update yang berubah aja. Hemat! ⚡'),
(36, 9, 'Hook React untuk menangani efek samping (side effects) adalah?', 'useState', 'useEffect', 'useMemo', 'useCallback', 'b', 'useEffect(() => { ... }, [deps]). Cocok buat fetch data, subscribe, timer, dan efek samping lainnya! 🌊'),
(37, 9, 'Library state management populer untuk React adalah?', 'jQuery', 'Bootstrap', 'Redux', 'Laravel', 'c', 'Redux — predictable state container. Untuk app besar dengan state kompleks. Motto: Single source of truth! 📚'),
(38, 9, 'Cara mengupdate state di React functional component?', 'this.setState()', 'Setter function dari useState', 'state = newValue', 'updateState()', 'b', 'const [count, setCount] = useState(0); lalu setCount(newValue). JANGAN langsung ubah state! Immutability! 🚫'),
(39, 9, 'Konsep \'one-way data flow\' di React artinya?', 'Data hanya bisa string', 'Data mengalir dari parent ke child', 'Data otomatis sinkron ke server', 'Data tidak bisa berubah', 'b', 'Data turun via props, event naik via callback. Aliran searah bikin app lebih predictable dan mudah di-debug! 🔄'),
(40, 9, 'Hook untuk mengakses React Context tanpa Consumer component?', 'useRef', 'useReducer', 'useContext', 'useMemo', 'c', 'const value = useContext(MyContext) — lebih clean daripada nesting Consumer component berkali-kali! 🧹'),
(41, 10, 'Method HTTP yang digunakan untuk MENGAMBIL data dari server?', 'POST', 'PUT', 'DELETE', 'GET', 'd', 'GET = ambil data. Kayak browsing toko online — kamu cuma lihat-lihat, gak ngubah apa-apa di server! 🛒'),
(42, 10, 'Fungsi JavaScript modern untuk melakukan HTTP request?', 'XMLHttpRequest()', 'fetch()', 'request()', 'http()', 'b', 'fetch() — promise-based, clean syntax, built-in di browser modern. Bye-bye XMLHttpRequest yang ribet! 👋'),
(43, 10, 'Format data yang paling umum digunakan dalam API web?', 'XML', 'CSV', 'JSON', 'HTML', 'c', 'JSON = JavaScript Object Notation. Ringan, mudah dibaca manusia DAN mesin. Standard de facto untuk web API! 📋'),
(44, 10, 'HTTP Status Code yang menandakan request berhasil (OK)?', '100', '200', '300', '400', 'b', '200 OK — server bilang \'Oke bos, request kamu berhasil!\' Kalau 404, berarti data hilang kayak kaos kaki di mesin cuci 🧦'),
(45, 10, 'Method JavaScript untuk mengubah JSON string menjadi objek?', 'JSON.stringify()', 'JSON.parse()', 'JSON.convert()', 'JSON.decode()', 'b', 'JSON.parse() = string → objek. JSON.stringify() = objek → string. Dua arah, seperti Google Translate tapi buat data! 🔄');

-- --------------------------------------------------------

--
-- Struktur dari tabel `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `student_id` varchar(10) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `score` int(11) DEFAULT NULL,
  `teacher_feedback` text DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `projects`
--

INSERT INTO `projects` (`id`, `student_id`, `file_name`, `file_path`, `score`, `teacher_feedback`, `submitted_at`) VALUES
(1, 'S001', 'Word.docx', 'uploads/S001_1776955216_Word.docx', 90, 'Good Job', '2026-04-23 14:40:16');

-- --------------------------------------------------------

--
-- Struktur dari tabel `student_progress`
--

CREATE TABLE `student_progress` (
  `id` int(11) NOT NULL,
  `student_id` varchar(10) NOT NULL,
  `module_id` int(11) NOT NULL,
  `score` int(11) DEFAULT 0,
  `is_completed` tinyint(1) DEFAULT 0,
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `student_progress`
--

INSERT INTO `student_progress` (`id`, `student_id`, `module_id`, `score`, `is_completed`, `completed_at`) VALUES
(1, 'S001', 2, 100, 1, '2026-04-21 09:02:10'),
(2, 'S001', 3, 100, 1, '2026-04-23 14:07:40'),
(3, 'S001', 4, 100, 1, '2026-04-23 09:08:23');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','student') NOT NULL DEFAULT 'student',
  `classification_level` enum('Beginner','Intermediate','Advanced') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `classification_level`, `created_at`) VALUES
('G001', 'Hasanah Nur Aini', 'guru@gmail.com', '$2y$10$7paCc.4EOnTKPhQIJrEgC.KYJM8t5QAiH0nQzOtY/npx.DzOvErTu', 'admin', NULL, '2026-04-21 13:06:29'),
('S001', 'Jaka Perdana', 'siswa1@gmail.com', '$2y$10$CQR5H8jExK.HZDib2vzs5OtKpL8AIdV.pc9VK92xdpM/cv3iIiZ/u', 'student', 'Beginner', '2026-04-21 13:06:29'),
('S002', 'Firda Nabilah', 'siswa2@gmail.com', '$2y$10$CQR5H8jExK.HZDib2vzs5OtKpL8AIdV.pc9VK92xdpM/cv3iIiZ/u', 'student', 'Intermediate', '2026-04-21 13:06:29'),
('S003', 'Azri Anggia Putri', 'siswa3@gmail.com', '$2y$10$CQR5H8jExK.HZDib2vzs5OtKpL8AIdV.pc9VK92xdpM/cv3iIiZ/u', 'student', 'Advanced', '2026-04-21 13:06:29');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `diagnostic_questions`
--
ALTER TABLE `diagnostic_questions`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `module_quizzes`
--
ALTER TABLE `module_quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `module_id` (`module_id`);

--
-- Indeks untuk tabel `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indeks untuk tabel `student_progress`
--
ALTER TABLE `student_progress`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_student_module` (`student_id`,`module_id`),
  ADD KEY `module_id` (`module_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `diagnostic_questions`
--
ALTER TABLE `diagnostic_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `modules`
--
ALTER TABLE `modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `module_quizzes`
--
ALTER TABLE `module_quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT untuk tabel `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `student_progress`
--
ALTER TABLE `student_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `module_quizzes`
--
ALTER TABLE `module_quizzes`
  ADD CONSTRAINT `module_quizzes_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `student_progress`
--
ALTER TABLE `student_progress`
  ADD CONSTRAINT `student_progress_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_progress_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
