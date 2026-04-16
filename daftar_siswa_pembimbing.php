<?php
session_start();
include "config.php";
requirePembimbing();

$pid = (int)$_SESSION['user']['id_user'];
$active_page = 'siswa';
$page_title = 'Daftar Siswa Bimbingan';
include '_header_pembimbing.php';
?>

<div class="panel">
    <div class="panel-header">
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="background: rgba(92, 103, 255, 0.1); padding: 10px; border-radius: 10px;">
                <i class="fas fa-user-graduate" style="color: #5c67ff; font-size: 1.5rem;"></i>
            </div>
            <div>
                <h3 style="margin: 0;">Siswa Bimbingan</h3>
                <p style="color: #8e8ea9; font-size: 0.8rem; margin: 0;">Monitoring data dan status PKL siswa bimbingan Anda.</p>
            </div>
        </div>
    </div>

    <div class="table-responsive" style="margin-top: 20px;">
        <table>
            <thead>
                <tr>
                    <th><i class="fas fa-id-badge"></i> Identitas Siswa</th>
                    <th><i class="fas fa-building"></i> Tempat PKL</th>
                    <th><i class="fas fa-info-circle"></i> Status</th>
                    <th style="text-align: center;"><i class="fas fa-cog"></i> Opsi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Query yang sudah diperbaiki
                $q = mysqli_query($conn, "SELECT 
                        u.id, 
                        u.nama_depan, 
                        u.nama_belakang, 
                        u.email, -- Menggunakan email sebagai pengganti username
                        p.nama_perusahaan, -- Nama kolom yang benar sesuai SQL
                        p.status_pembimbing -- Menggunakan status_pembimbing dari tabel pengajuan
                    FROM pkl_pengajuan p 
                    JOIN users u ON p.ketua_id = u.id 
                    WHERE p.pembimbing_id = $pid");

                if(mysqli_num_rows($q) == 0) {
                    echo "<tr><td colspan='4' style='text-align:center; padding: 40px; color: #8e8ea9;'>Belum ada siswa yang terdaftar dalam bimbingan Anda.</td></tr>";
                }

                while($d = mysqli_fetch_assoc($q)):
                ?>
                <tr>
                    <td>
                        <div style="font-weight: 600; color: #f1f5f9;"><?= htmlspecialchars($d['nama_depan'].' '.$d['nama_belakang']) ?></div>
                        <div style="font-size: 0.75rem; color: #5c67ff; font-family: monospace;"><?= htmlspecialchars($d['email']) ?></div>
                    </td>
                    <td>
                        <div style="font-size: 0.9rem;"><?= htmlspecialchars($d['nama_perusahaan']) ?></div>
                    </td>
                    <td>
                        <?php 
                            $status = strtolower($d['status_pembimbing']);
                            $pill_class = ($status == 'disetujui') ? 'pill-green' : 'pill-yellow';
                        ?>
                        <span class="pill <?= $pill_class ?>"><?= strtoupper($d['status_pembimbing']) ?></span>
                    </td>
                    <td style="text-align: center;">
                        <a href="pembimbing-absensi.php?id=<?= $d['id'] ?>" class="btn-action">
                            <i class="fas fa-chart-line"></i> Pantau
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
/* Style tambahan biar makin mirip Admin Panel */
.btn-action {
    background: rgba(92, 103, 255, 0.1);
    color: #5c67ff;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 600;
    border: 1px solid rgba(92, 103, 255, 0.2);
    transition: 0.3s;
}
.btn-action:hover {
    background: #5c67ff;
    color: #fff;
}
th i { margin-right: 5px; font-size: 0.8rem; opacity: 0.7; }
</style>