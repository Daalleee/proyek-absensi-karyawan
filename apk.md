🧭 Smart Attendance System (Face + Location Based)
📘 Deskripsi Proyek

Smart Attendance System adalah aplikasi web berbasis Laravel yang digunakan untuk absensi karyawan secara otomatis menggunakan pengenalan wajah (Face Recognition) dan validasi lokasi GPS (Geolocation).

Sistem ini memastikan bahwa setiap karyawan hanya dapat melakukan absensi jika benar-benar berada di lokasi kerja yang telah ditentukan oleh Admin atau Ketua Lapangan.

Aplikasi ini cocok untuk perusahaan dengan sistem kerja lapangan, proyek konstruksi, kantor cabang, dan perusahaan yang memerlukan kontrol kehadiran berbasis lokasi.

🚀 Fitur Utama
👨‍💼 Untuk Admin / Ketua Lapangan

Login dan Manajemen Akun
Admin dan ketua lapangan dapat login untuk mengelola data dan lokasi kerja.

Manajemen Karyawan
Tambah, ubah, hapus, dan kelola data karyawan termasuk foto wajah untuk face recognition.

Penentuan Lokasi Kerja (GeoFence)
Admin atau ketua lapangan dapat menentukan lokasi kerja harian berupa koordinat (latitude & longitude) serta radius area validasi (misalnya 100 meter).
Lokasi bisa dipilih langsung dari peta interaktif (Leaflet.js / Google Maps).

Penjadwalan Lokasi Kerja
Admin dapat membuat jadwal lokasi kerja harian atau mingguan.
Contoh: Hari ini di Proyek A, besok di Proyek B.

Dashboard Kehadiran
Melihat data absensi karyawan secara real-time:

Siapa yang sudah absen

Lokasi absensi (latitude & longitude)

Validasi wajah dan posisi

Deteksi absen di luar lokasi

Laporan Absensi

Export data ke PDF/Excel

Filter berdasarkan tanggal, karyawan, atau lokasi

Tanda warna (valid / di luar lokasi)

👷‍♂️ Untuk Karyawan

Login dan Profil
Karyawan login menggunakan akun masing-masing.

Absensi Masuk & Keluar

Sistem meminta akses kamera dan GPS

Ambil foto wajah langsung dari browser

Ambil koordinat lokasi (latitude, longitude) menggunakan GPS perangkat

Sistem memvalidasi wajah + posisi lokasi

Jika valid → absensi berhasil disimpan

Jika tidak valid → tampilkan pesan error (“Anda tidak berada di lokasi kerja”)

Riwayat Absensi

Melihat absensi harian

Status (Hadir, Terlambat, Tidak Hadir)

Waktu dan lokasi absensi

Verifikasi wajah (ikon centang / silang)

🧠 Alur Sistem (Step by Step)

Admin Menentukan Lokasi Kerja

Pilih lokasi dari peta

Simpan latitude, longitude, dan radius

Lokasi berlaku untuk tanggal tertentu (misal hari ini)

Karyawan Login

Mengakses halaman absensi melalui browser

Sistem meminta izin kamera dan GPS

Karyawan Mengambil Absensi

Browser mengambil posisi (GeoLocation)

Browser mengambil foto wajah

Data dikirim ke backend Laravel:

Laravel Memproses Data

Cek lokasi kerja aktif hari ini dari tabel lokasi_kerja

Hitung jarak antara lokasi karyawan dan titik lokasi kerja menggunakan Haversine Formula

Jika jarak ≤ radius → lanjut ke validasi wajah

Validasi Wajah (AI Face Recognition)

Laravel mengirim foto ke API Python (Flask)

Flask menggunakan OpenCV + face_recognition untuk mencocokkan wajah dengan data yang disimpan

Jika wajah cocok → kirim respon match=true

Jika tidak cocok → respon match=false

Simpan Absensi

Jika posisi dan wajah valid → simpan ke tabel absensi

Simpan waktu, lokasi GPS, dan hasil verifikasi

Tampilkan pesan sukses kepada karyawan

Admin Melihat Laporan

Dashboard menampilkan absensi real-time

Map view menampilkan posisi titik absensi karyawan

🧩 Teknologi yang Digunakan
🧱 Backend

Laravel 11 — Framework utama backend dan frontend

Eloquent ORM — Manajemen database relasional

Laravel Livewire / Inertia.js (opsional) — Interaktivitas tanpa JavaScript manual

Laravel Sanctum — Autentikasi token (jika ingin integrasi API)

Laravel Excel / DomPDF — Export laporan absensi

🧠 AI (Face Recognition Service)

Python 3 + Flask — REST API untuk pengenalan wajah

Library:

face_recognition — Deteksi & pencocokan wajah

opencv-python — Proses gambar

numpy — Operasi numerik

🎨 Frontend (di Laravel)

Blade Template Engine — Tampilan halaman

TailwindCSS — Desain modern dan responsif

Alpine.js / Livewire — Interaktivitas UI ringan

HTML5 Geolocation API — Mengambil posisi GPS

MediaDevices API (Camera) — Mengambil foto wajah langsung dari browser

Leaflet.js / Google Maps API — Menampilkan peta interaktif

🗄️ Database

MySQL — Database utama sistem
