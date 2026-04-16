<?php
session_start();
include "config.php";
requirePembimbing();

$pid = (int)$_SESSION['user']['id_user']; // Sesuaikan dengan key session login kamu
$active_page = 'jurnal';
$page_title = 'Validasi Jurnal Harian';
include '_header_pembimbing.php';

// Proses Validasi Jurnal
if (isset($_GET['validasi']) && isset($_GET['status'])) {
    $jid = (int)$_GET['validasi'];
    $status = ($_GET['status'] == 'tolak') ? 'tolak' : 'valid';
    mysqli_query($conn, "UPDATE jurnal_harian SET status_validasi = '$status' WHERE id = $jid");
    echo "<script>alert('Status jurnal berhasil diupdate!'); window.location='pembimbing-jurnal.php';</script>";
}
?>
<div class="panel">
    <div class="panel-header">
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="background: rgba(245, 158, 11, 0.1); padding: 10px; border-radius: 10px;">
                <i class="fas fa-book-open" style="color: #f59e0b; font-size: 1.5rem;"></i>
            </div>
            <div>
                <h3 style="margin: 0;">Validasi Jurnal Harian</h3>
                <p style="color: #8e8ea9; font-size: 0.8rem; margin: 0;">Review dan validasi kegiatan harian siswa bimbingan.</p>
            </div>
        </div>
    </div>
    <div class="table-responsive" style="margin-top: 20px;">
        <table>
            <thead>
                <tr>
                    <th><i class="fas fa-user"></i> Siswa</th>
                    <th><i class="fas fa-calendar-alt"></i> Tanggal & Waktu</th>
                    <th><i class="fas fa-tasks"></i> Kegiatan</th>
                    <th><i class="fas fa-info-circle"></i> Status</th>
                    <th style="text-align: center;"><i class="fas fa-cog"></i> Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $q = mysqli_query($conn, "SELECT j.*, u.nama_depan, u.nama_belakang 
                    FROM jurnal_harian j 
                    JOIN pkl_pengajuan p ON j.siswa_id = p.ketua_id 
                    JOIN users u ON j.siswa_id = u.id 
                    WHERE p.pembimbing_id = $pid 
                    ORDER BY j.tanggal DESC");
                if(mysqli_num_rows($q) == 0) {
                    echo "<tr><td colspan='5' style='text-align:center; padding: 40px; color: #8e8ea9;'>Belum ada data jurnal yang diisi.</td></tr>";
                }
                while($d = mysqli_fetch_assoc($q)):
                ?>
                <tr>
                    <td>
                        <div style="font-weight: 600; color: #f1f5f9;"><?= htmlspecialchars($d['nama_depan'].' '.$d['nama_belakang']) ?></div>
                    </td>
                    <td>
                        <div style="font-size: 0.9rem;"><?= date('d M Y', strtotime($d['tanggal'])) ?></div>
                        <div style="font-size: 0.75rem; color: #8e8ea9;">
                            <?= substr($d['jam_masuk'],0,5) ?> - <?= substr($d['jam_keluar'],0,5) ?>
                        </div>
                    </td>
                    <td><div style="font-size: 0.85rem; max-width: 250px; white-space: normal;"><?= htmlspecialchars($d['kegiatan']) ?></div></td>
                    <td>
                        <?php 
                            $status = strtolower($d['status_validasi']);
                            $pill_class = $status == 'valid' ? 'pill-green' : ($status == 'tolak' ? 'pill-red' : 'pill-yellow');
                        ?>
                        <span class="pill <?= $pill_class ?>"><?= strtoupper($status) ?></span>
                    </td>
                    <td style="text-align: center; display: flex; gap: 5px; justify-content: center;">
                        <?php if($status == 'pending'): ?>
                        <a href="?validasi=<?= $d['id'] ?>&status=valid" class="btn-action" style="color: #4ade80; border-color: rgba(74, 222, 128, 0.2); background: rgba(74, 222, 128, 0.1);" title="Setujui">
                            <i class="fas fa-check"></i>
                        </a>
                        <a href="?validasi=<?= $d['id'] ?>&status=tolak" class="btn-action" style="color: #ef4444; border-color: rgba(239, 68, 68, 0.2); background: rgba(239, 68, 68, 0.1);" title="Tolak">
                            <i class="fas fa-times"></i>
                        </a>
                        <?php else: ?>
                        <span style="color: #8e8ea9; font-size: 0.8rem;">Selesai</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<style>
.btn-action { padding: 6px 10px; border-radius: 6px; font-size: 0.9rem; transition: 0.3s; display: inline-block; }
.btn-action:hover { opacity: 0.8; }
th i { margin-right: 5px; font-size: 0.8rem; opacity: 0.7; }
</style>