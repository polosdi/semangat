<?php
session_start();
include "config.php";
requirePembimbing();

$pid = (int)$_SESSION['user']['id_user'];
$active_page = 'absensi';
$page_title = 'Monitoring Absensi Siswa';

// Header ini yang menangani Sidebar & Wrapper agar tidak berantakan
include '_header_pembimbing.php'; 
?>

<div class="panel">
    <div class="panel-header">
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="background: rgba(99, 102, 241, 0.1); padding: 10px; border-radius: 10px;">
                <i class="fas fa-calendar-check" style="color: #6366f1; font-size: 1.5rem;"></i>
            </div>
            <div>
                <h3 style="margin: 0;">Monitoring Absensi</h3>
                <p style="color: #8e8ea9; font-size: 0.8rem; margin: 0;">Rekap kehadiran harian siswa bimbingan Anda secara real-time.</p>
            </div>
        </div>
    </div>

    <div class="table-responsive" style="margin-top: 20px;">
        <table>
            <thead>
                <tr>
                    <th><i class="fas fa-user"></i> Nama Siswa</th>
                    <th style="text-align: center;"><i class="fas fa-calendar-alt"></i> Tanggal</th>
                    <th style="text-align: center;"><i class="fas fa-clock"></i> Jam Masuk</th>
                    <th style="text-align: center;"><i class="fas fa-door-open"></i> Jam Pulang</th>
                    <th style="text-align: center;"><i class="fas fa-info-circle"></i> Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Query ambil data absensi
                $query_absensi = "
                    SELECT a.*, u.nama_depan, u.nama_belakang 
                    FROM absensi a
                    JOIN users u ON a.siswa_id = u.id
                    JOIN pkl_pengajuan p ON u.id = p.ketua_id
                    WHERE p.pembimbing_id = $pid
                    ORDER BY a.tanggal DESC, a.jam_masuk DESC
                ";
                $res = mysqli_query($conn, $query_absensi);

                if(mysqli_num_rows($res) > 0):
                    while($d = mysqli_fetch_assoc($res)):
                        $st = strtolower($d['status']);
                        $pill = ($st == 'hadir') ? 'pill-green' : (($st == 'izin' || $st == 'sakit') ? 'pill-yellow' : 'pill-red');
                ?>
                <tr>
                    <td>
                        <div style="font-weight: 600; color: #fff;"><?= htmlspecialchars($d['nama_depan'].' '.$d['nama_belakang']) ?></div>
                    </td>
                    <td style="text-align: center; color: #8e8ea9;">
                        <?= date('d/m/Y', strtotime($d['tanggal'])) ?>
                    </td>
                    <td style="text-align: center; color: #4ade80; font-family: monospace; font-weight: bold;">
                        <?= $d['jam_masuk'] ?: '--:--' ?>
                    </td>
                    <td style="text-align: center; color: #f87171; font-family: monospace; font-weight: bold;">
                        <?= $d['jam_keluar'] ?: '--:--' ?>
                    </td>
                    <td style="text-align: center;">
                        <span class="pill <?= $pill ?>"><?= strtoupper($d['status']) ?></span>
                    </td>
                </tr>
                <?php 
                    endwhile;
                else: 
                ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 40px; color: #8e8ea9;">
                        <i class="fas fa-folder-open" style="display: block; font-size: 2rem; margin-bottom: 10px; opacity: 0.3;"></i>
                        Belum ada data absensi untuk siswa bimbingan Anda.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
/* Style tambahan biar sama persis dengan Daftar Siswa */
.pill-red { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
.pill-yellow { background: rgba(245, 158, 11, 0.1); color: #fbbf24; }
.pill-green { background: rgba(34, 197, 94, 0.1); color: #4ade80; }
</style>