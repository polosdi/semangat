<?php
session_start();
include "config.php";
requirePembimbing();

$pid = (int)$_SESSION['user']['id_user'];
$active_page = 'nilai';
$page_title = 'Input Nilai Siswa';

// --- PROSES SIMPAN NILAI ---
if (isset($_POST['simpan_nilai'])) {
    $siswa_id = (int)$_POST['siswa_id'];
    $n_disiplin = (int)$_POST['n_disiplin'];
    $n_kerja_sama = (int)$_POST['n_kerja_sama'];
    $n_inisiatif = (int)$_POST['n_inisiatif'];
    $n_teknis = (int)$_POST['n_teknis'];
    
    // Hitung Rata-rata
    $rata_rata = ($n_disiplin + $n_kerja_sama + $n_inisiatif + $n_teknis) / 4;

    // Cek apakah sudah ada nilai sebelumnya
    $cek = mysqli_query($conn, "SELECT id FROM nilai_pkl WHERE siswa_id = $siswa_id AND pembimbing_id = $pid");
    
    if (mysqli_num_rows($cek) > 0) {
        // Update jika sudah ada
        $sql = "UPDATE nilai_pkl SET 
                nilai_disiplin = '$n_disiplin', 
                nilai_kerja_sama = '$n_kerja_sama', 
                nilai_inisiatif = '$n_inisiatif', 
                nilai_teknis = '$n_teknis', 
                nilai_rata_rata = '$rata_rata' 
                WHERE siswa_id = $siswa_id AND pembimbing_id = $pid";
    } else {
        // Insert jika baru
        $sql = "INSERT INTO nilai_pkl (siswa_id, pembimbing_id, nilai_disiplin, nilai_kerja_sama, nilai_inisiatif, nilai_teknis, nilai_rata_rata) 
                VALUES ($siswa_id, $pid, '$n_disiplin', '$n_kerja_sama', '$n_inisiatif', '$n_teknis', '$rata_rata')";
    }

    if (mysqli_query($conn, $sql)) {
        header("Location: pembimbing-nilai.php?status=success");
        exit();
    }
}

include '_header_pembimbing.php';
?>

<div class="panel">
    <div class="panel-header">
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="background: rgba(245, 158, 11, 0.1); padding: 10px; border-radius: 10px;">
                <i class="fas fa-star" style="color: #f59e0b; font-size: 1.5rem;"></i>
            </div>
            <div>
                <h3 style="margin: 0;">Input Nilai PKL</h3>
                <p style="color: #8e8ea9; font-size: 0.8rem; margin: 0;">Berikan penilaian objektif berdasarkan performa siswa di lapangan.</p>
            </div>
        </div>
    </div>

    <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 12px; border-radius: 8px; margin-top: 20px; font-size: 0.9rem; border: 1px solid rgba(16,185,129,0.2);">
            <i class="fas fa-check-circle"></i> Nilai berhasil disimpan dan diperbarui!
        </div>
    <?php endif; ?>

    <div class="table-responsive" style="margin-top: 25px;">
        <table class="table-nilai">
            <thead>
                <tr>
                    <th>Nama Siswa</th>
                    <th style="text-align: center; width: 100px;">Disiplin</th>
                    <th style="text-align: center; width: 100px;">Kerja Sama</th>
                    <th style="text-align: center; width: 100px;">Inisiatif</th>
                    <th style="text-align: center; width: 100px;">Teknis</th>
                    <th style="text-align: center;">Rata-rata</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Ambil daftar siswa bimbingan dan nilai mereka (jika sudah ada)
                $query = "SELECT u.id, u.nama_depan, u.nama_belakang, 
                          n.nilai_disiplin, n.nilai_kerja_sama, n.nilai_inisiatif, n.nilai_teknis, n.nilai_rata_rata
                          FROM pkl_pengajuan p
                          JOIN users u ON p.ketua_id = u.id
                          LEFT JOIN nilai_pkl n ON u.id = n.siswa_id AND n.pembimbing_id = $pid
                          WHERE p.pembimbing_id = $pid AND p.status_pembimbing = 'disetujui'";
                $res = mysqli_query($conn, $query);

                while($d = mysqli_fetch_assoc($res)):
                ?>
                <form method="POST">
                    <input type="hidden" name="siswa_id" value="<?= $d['id'] ?>">
                    <tr>
                        <td>
                            <div style="font-weight: 600; color: #fff;"><?= htmlspecialchars($d['nama_depan'].' '.$d['nama_belakang']) ?></div>
                            <div style="font-size: 0.7rem; color: #6366f1;">ID Siswa: #<?= $d['id'] ?></div>
                        </td>
                        <td><input type="number" name="n_disiplin" class="input-nilai" value="<?= $d['nilai_disiplin'] ?: 0 ?>" min="0" max="100"></td>
                        <td><input type="number" name="n_kerja_sama" class="input-nilai" value="<?= $d['nilai_kerja_sama'] ?: 0 ?>" min="0" max="100"></td>
                        <td><input type="number" name="n_inisiatif" class="input-nilai" value="<?= $d['nilai_inisiatif'] ?: 0 ?>" min="0" max="100"></td>
                        <td><input type="number" name="n_teknis" class="input-nilai" value="<?= $d['nilai_teknis'] ?: 0 ?>" min="0" max="100"></td>
                        <td style="text-align: center;">
                            <span class="avg-badge"><?= number_format($d['nilai_rata_rata'] ?: 0, 1) ?></span>
                        </td>
                        <td style="text-align: center;">
                            <button type="submit" name="simpan_nilai" class="btn-save">
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
.table-nilai th { padding: 12px; color: #475569; font-size: 0.75rem; text-transform: uppercase; text-align: left; }
.table-nilai td { padding: 15px 10px; border-bottom: 1px solid rgba(255,255,255,0.05); }

.input-nilai {
    width: 70px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #fff;
    padding: 8px;
    border-radius: 8px;
    text-align: center;
    font-weight: 600;
}

.input-nilai:focus { outline: none; border-color: #6366f1; background: rgba(99, 102, 241, 0.1); }

.avg-badge {
    background: #1e293b;
    color: #f59e0b;
    padding: 5px 12px;
    border-radius: 20px;
    font-weight: 700;
    font-size: 0.85rem;
    border: 1px solid rgba(245, 158, 11, 0.3);
}

.btn-save {
    background: #6366f1;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
}

.btn-save:hover { background: #4f46e5; transform: translateY(-2px); }
</style>