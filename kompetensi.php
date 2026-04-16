<?php
session_start();
include "config.php";
requirePembimbing();

$pid = (int)$_SESSION['user']['id_user'];
$active_page = 'kompetensi'; 
$page_title = 'Input Nilai Kompetensi';

// --- PROSES SIMPAN NILAI ---
if (isset($_POST['simpan_kompetensi'])) {
    $siswa_id = (int)$_POST['siswa_id'];
    
    // Ambil nilai dari form
    $n_analisis = (int)$_POST['n_analisis'];
    $n_db       = (int)$_POST['n_db'];
    $n_front    = (int)$_POST['n_front'];
    $n_back     = (int)$_POST['n_back'];
    $n_git      = (int)$_POST['n_git'];

    $data_komp = [
        'analisis' => $n_analisis,
        'database' => $n_db,
        'frontend' => $n_front,
        'backend'  => $n_back,
        'git'      => $n_git
    ];

    foreach($data_komp as $key => $val) {
        $cek = mysqli_query($conn, "SELECT id FROM kompetensi_siswa WHERE siswa_id = $siswa_id AND kompetensi_key = '$key'");
        if(mysqli_num_rows($cek) > 0) {
            mysqli_query($conn, "UPDATE kompetensi_siswa SET nilai = $val WHERE siswa_id = $siswa_id AND kompetensi_key = '$key'");
        } else {
            mysqli_query($conn, "INSERT INTO kompetensi_siswa (siswa_id, kompetensi_key, nilai) VALUES ($siswa_id, '$key', $val)");
        }
    }
    setFlash('success', 'Data Kompetensi Berhasil Disimpan!');
    header("Location: kompetensi.php");
    exit();
}

include '_header_pembimbing.php';
?>

<div class="panel">
    <div class="panel-header">
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="background: rgba(99, 102, 241, 0.1); padding: 10px; border-radius: 10px;">
                <i class="fas fa-star" style="color: #6366f1; font-size: 1.5rem;"></i>
            </div>
            <div>
                <h3 style="margin: 0;">Input Nilai Kompetensi</h3>
                <p style="color: #64748b; font-size: 0.8rem; margin: 0;">Isi skor kompetensi teknis siswa (0-100).</p>
            </div>
        </div>
    </div>

    <div class="table-responsive" style="margin-top: 20px;">
        <table class="table-nilai">
            <thead>
                <tr>
                    <th>Identitas Siswa</th>
                    <th style="text-align:center;">Analisis</th>
                    <th style="text-align:center;">Database</th>
                    <th style="text-align:center;">Frontend</th>
                    <th style="text-align:center;">Backend</th>
                    <th style="text-align:center;">Git/Odoo</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $q = mysqli_query($conn, "SELECT u.id, u.nama_depan, u.nama_belakang FROM pkl_pengajuan p JOIN users u ON p.ketua_id = u.id WHERE p.pembimbing_id = $pid AND p.status_pembimbing = 'disetujui'");
                while($s = mysqli_fetch_assoc($q)):
                    $sid = $s['id'];
                    // Ambil nilai lama dari DB
                    $v1 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nilai FROM kompetensi_siswa WHERE siswa_id=$sid AND kompetensi_key='analisis'"))['nilai'] ?? 0;
                    $v2 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nilai FROM kompetensi_siswa WHERE siswa_id=$sid AND kompetensi_key='database'"))['nilai'] ?? 0;
                    $v3 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nilai FROM kompetensi_siswa WHERE siswa_id=$sid AND kompetensi_key='frontend'"))['nilai'] ?? 0;
                    $v4 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nilai FROM kompetensi_siswa WHERE siswa_id=$sid AND kompetensi_key='backend'"))['nilai'] ?? 0;
                    $v5 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nilai FROM kompetensi_siswa WHERE siswa_id=$sid AND kompetensi_key='git'"))['nilai'] ?? 0;
                ?>
                <form action="" method="POST">
                    <input type="hidden" name="siswa_id" value="<?= $sid ?>">
                    <tr>
                        <td style="font-weight: 600; color: #fff;">
                            <?= htmlspecialchars($s['nama_depan'].' '.$s['nama_belakang']) ?>
                        </td>
                        <td style="text-align:center;"><input type="number" name="n_analisis" value="<?= $v1 ?>" class="input-nilai" min="0" max="100"></td>
                        <td style="text-align:center;"><input type="number" name="n_db" value="<?= $v2 ?>" class="input-nilai" min="0" max="100"></td>
                        <td style="text-align:center;"><input type="number" name="n_front" value="<?= $v3 ?>" class="input-nilai" min="0" max="100"></td>
                        <td style="text-align:center;"><input type="number" name="n_back" value="<?= $v4 ?>" class="input-nilai" min="0" max="100"></td>
                        <td style="text-align:center;"><input type="number" name="n_git" value="<?= $v5 ?>" class="input-nilai" min="0" max="100"></td>
                        <td style="text-align:center;">
                            <button type="submit" name="simpan_kompetensi" class="btn-save">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                        </td>
                    </tr>
                </form>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.table-nilai { width: 100%; border-collapse: collapse; }
.table-nilai th { padding: 12px; color: #475569; font-size: 0.75rem; text-transform: uppercase; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.05); }
.table-nilai td { padding: 15px 10px; border-bottom: 1px solid rgba(255,255,255,0.05); }

.input-nilai {
    width: 80px; /* Lebarin dikit biar panahnya muat */
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #fff;
    padding: 8px;
    border-radius: 8px;
    text-align: center;
    font-weight: 600;
    display: inline-block;
}

/* BAGIAN PENTING: Memunculkan kembali panah naik turun */
.input-nilai::-webkit-inner-spin-button, 
.input-nilai::-webkit-outer-spin-button { 
    opacity: 1 !important; 
    height: 30px;
}

.input-nilai:focus { outline: none; border-color: #6366f1; background: rgba(99, 102, 241, 0.1); }

.btn-save {
    background: #6366f1;
    color: #fff;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
}
.btn-save:hover { background: #4f46e5; transform: translateY(-2px); }
</style>