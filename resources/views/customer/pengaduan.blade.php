<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Form Pengaduan - Forum Pelayanan Pelanggan</title>
    <link rel="stylesheet" href="{{ asset('css/customer.css') }}">
</head>
<body>
    <div class="customer-container">
        <header class="customer-header">
            <h1>Forum Pelayanan Pelanggan</h1>
            <div class="header-actions">
                <span class="user-info">Halo, {{ Auth::user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-logout">Logout</button>
                </form>
            </div>
        </header>

        <main class="customer-main">
            <div class="form-card">
                <h2 class="form-title">Kirim Pengaduan</h2>
                
                <div id="alert-container"></div>

                <form id="pengaduanForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="nomor_tiket" class="form-label">Nomor Tiket (Otomatis)</label>
                        <input type="text" id="nomor_tiket" name="nomor_tiket" class="form-input readonly" value="{{ $nomorTiket }}" readonly>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-input" value="{{ old('email', Auth::user()->email) }}" required>
                        <span class="error-message" id="error-email"></span>
                    </div>

                    <div class="form-group">
                        <label for="nama" class="form-label">Nama Pelapor</label>
                        <input type="text" id="nama" name="nama" class="form-input" value="{{ old('nama', Auth::user()->name) }}" required>
                        <span class="error-message" id="error-nama"></span>
                    </div>

                    <div class="form-group">
                        <label for="no_telp" class="form-label">Nomor HP</label>
                        <input type="text" id="no_telp" name="no_telp" class="form-input" placeholder="Contoh: 081234567890" required>
                        <span class="error-message" id="error-no_telp"></span>
                    </div>

                    <div class="form-group">
                        <label for="kategori" class="form-label">Kategori Pengaduan</label>
                        <input type="text" id="kategori" name="kategori" class="form-input" placeholder="Masukkan kategori pengaduan" required>
                        <span class="error-message" id="error-kategori"></span>
                    </div>

                    <div class="form-group">
                        <label for="keluhan" class="form-label">Keluhan</label>
                        <textarea id="keluhan" name="keluhan" class="form-textarea" rows="5" placeholder="Tuliskan keluhan Anda" required></textarea>
                        <span class="error-message" id="error-keluhan"></span>
                    </div>

                    <div class="form-group">
                        <label for="foto" class="form-label">Upload Foto Bukti (Opsional)</label>
                        <input type="file" id="foto" name="foto" class="form-input-file" accept="image/jpeg,image/png,image/jpg,image/gif">
                        <small class="form-help">Format: JPG, JPEG, PNG, GIF. Maksimal 2MB</small>
                        <span class="error-message" id="error-foto"></span>
                    </div>

                    <button type="submit" id="btnKirim" class="btn-submit">Kirim Pengaduan</button>
                </form>

                <div id="success-info" style="display: none; margin-top: 20px; padding: 15px; background: #f0f0f0; border: 1px solid #333;">
                    <strong>Pengaduan berhasil dikirim!</strong><br>
                    Nomor Tiket Anda: <strong id="tiket-berhasil"></strong>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Submit form tanpa reload menggunakan Fetch API
        document.getElementById('pengaduanForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btnKirim = document.getElementById('btnKirim');
            const alertContainer = document.getElementById('alert-container');
            const successInfo = document.getElementById('success-info');
            
            // Bersihkan pesan error sebelumnya
            const errorMessages = document.querySelectorAll('.error-message');
            errorMessages.forEach(el => el.textContent = '');
            alertContainer.innerHTML = '';
            successInfo.style.display = 'none';
            
            // Cegah submit ganda dengan menonaktifkan tombol
            btnKirim.disabled = true;
            btnKirim.textContent = 'Mengirim...';
            
            // Buat FormData
            const formData = new FormData(this);
            
            // Kirim menggunakan Fetch API
            fetch('{{ route("customer.pengaduan.submit") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Tampilkan notifikasi sukses
                    alertContainer.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
                    // Tampilkan nomor tiket konfirmasi
                    document.getElementById('tiket-berhasil').textContent = data.nomor_tiket;
                    successInfo.style.display = 'block';
                    
                    // Kosongkan field yang perlu diisi ulang, pertahankan nomor tiket
                    document.getElementById('no_telp').value = '';
                    document.getElementById('kategori').value = '';
                    document.getElementById('keluhan').value = '';
                    document.getElementById('foto').value = '';
                    
                    // Scroll ke atas untuk melihat pesan sukses
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    // Tampilkan pesan kesalahan
                    if (data.errors) {
                        // Tampilkan error per field
                        for (const key in data.errors) {
                            const errorElement = document.getElementById('error-' + key);
                            if (errorElement) {
                                errorElement.textContent = data.errors[key][0];
                            }
                        }
                    }
                    if (data.message) {
                        alertContainer.innerHTML = '<div class="alert alert-error">' + data.message + '</div>';
                    }
                }
            })
            .catch(error => {
                // Tampilkan pesan kesalahan jaringan/server
                alertContainer.innerHTML = '<div class="alert alert-error">Terjadi kesalahan jaringan atau server. Silakan coba lagi.</div>';
            })
            .finally(() => {
                // Pulihkan tombol
                btnKirim.disabled = false;
                btnKirim.textContent = 'Kirim Pengaduan';
            });
        });
    </script>
</body>
</html>
