<?php
session_start();
include "config.php";
requirePembimbing(); // pastikan fungsi ini ada di config.php, atau ganti dengan requireLogin()

// ============================================================
//  dashboard_pembimbing.php
//  Statistik & panel khusus peran pembimbing:
//  - Siswa yang dibimbing
//  - Jurnal harian yang perlu divalidasi
//  - Laporan PKL yang perlu di-review
//  - Absensi PKL siswa bimbingan
//  - Progress bimbingan
// ============================================================

$pid = (int)$_SESSION['user']['id_user']; // id pembimbing yang login

// ---- Total siswa yang dibimbing oleh pembimbing ini ----
$total_siswa_bimbing = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(DISTINCT ketua_id) c FROM pkl_pengajuan WHERE pembimbing_id = $pid"))['c'];

// ---- Jurnal harian menunggu validasi dari siswa bimbingan ----
$total_jurnal_pending = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) c FROM jurnal_harian j
     INNER JOIN pkl_pengajuan p ON j.siswa_id = p.ketua_id
     WHERE p.pembimbing_id = $pid AND j.status_validasi = 'pending'"))['c'];

// ---- Total jurnal yang sudah divalidasi ----
$total_jurnal_valid = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) c FROM jurnal_harian j
     INNER JOIN pkl_pengajuan p ON j.siswa_id = p.ketua_id
     WHERE p.pembimbing_id = $pid AND j.status_validasi = 'valid'"))['c'];

// ---- Laporan PKL menunggu review ----
$total_laporan_pending = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) c FROM laporan_pkl l
     INNER JOIN pkl_pengajuan p ON l.siswa_id = p.ketua_id
     WHERE p.pembimbing_id = $pid AND l.status_pembimbing = 'pending'"))['c'];

// ---- Total laporan yang sudah disetujui ----
$total_laporan_approved = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) c FROM laporan_pkl l
     INNER JOIN pkl_pengajuan p ON l.siswa_id = p.ketua_id
     WHERE p.pembimbing_id = $pid AND l.status_pembimbing = 'disetujui'"))['c'];

// ---- Sesi bimbingan yang sudah tercatat ----
// $total_bimbingan = mysqli_fetch_assoc(mysqli_query($conn,
//     "SELECT COUNT(*) c FROM pkl_bimbingan WHERE pembimbing_id = $pid"))['c'] ?? 0;

$active_page = 'dashboard';
$page_title  = 'Dashboard Pembimbing';
include '_header_pembimbing.php';
?>

<div class="welcome-section delay-1">
  <div>
    <h1>Selamat Datang, <?php echo htmlspecialchars($_SESSION['user']['nama']); ?> 👋</h1>
    <p>Panel monitoring & bimbingan PKL — pantau perkembangan siswa Anda hari ini.</p>
  </div>
  <div class="status-badge">
    <div class="status-dot"></div>
    Online
  </div>
</div>

<div class="section-title"><i class="fas fa-book"></i> Ringkasan Bimbingan</div>
<div class="stat-grid">

  <div class="stat-card delay-1">
    <div class="card-icon">
      👨‍🎓
    </div>
    <h3>Siswa Bimbingan</h3>
    <div class="number"><?php echo $total_siswa_bimbing; ?></div>
    <div class="trend">Siswa yang Anda bimbing</div>
  </div>

  <div class="stat-card delay-2">
    <div class="card-icon">
      📚
    </div>
    <h3>Jurnal Menunggu</h3>
    <div class="number" style="color:#f59e0b;"><?php echo $total_jurnal_pending; ?></div>
    <div class="trend"><?php echo $total_jurnal_valid; ?> sudah divalidasi</div>
  </div>

  <div class="stat-card delay-3">
    <div class="card-icon">
      📙
    </div>
    <h3>Laporan Pending</h3>
    <div class="number" style="color:#ef4444;"><?php echo $total_laporan_pending; ?></div>
    <div class="trend"><?php echo $total_laporan_approved; ?> sudah disetujui</div>
  </div>

  <div class="stat-card delay-4">
    <div class="card-icon">
      👩‍🏫
    </div>
    <h3>Sesi Bimbingan</h3>
    <div class="trend">Total sesi tercatat</div>
  </div>

</div>

<div class="section-title delay-2"><i class="fas fa-bolt"></i> Aksi Cepat</div>
<div class="quick-grid">
  <a href="pembimbing-jurnal.php"    class="quick-card delay-1">
    <i class="fas fa-check-circle"></i> Validasi Jurnal
    <?php if ($total_jurnal_pending > 0): ?>
      <span style="margin-left:auto;background:rgba(245,158,11,.15);color:#f59e0b;
                   border:1px solid rgba(245,158,11,.3);border-radius:99px;
                   font-size:.7rem;padding:2px 8px;font-weight:600;">
        <?php echo $total_jurnal_pending; ?> baru
      </span>
    <?php endif; ?>
  </a>
  <a href="pembimbing-laporan.php"   class="quick-card delay-2">
    <i class="fas fa-file-signature"></i>  Review Laporan
    <?php if ($total_laporan_pending > 0): ?>
      <span style="margin-left:auto;background:rgba(239,68,68,.12);color:#ef4444;
                   border:1px solid rgba(239,68,68,.25);border-radius:99px;
                   font-size:.7rem;padding:2px 8px;font-weight:600;">
        <?php echo $total_laporan_pending; ?> baru
      </span>
    <?php endif; ?>
  </a>
  <a href="absensi_pembmibing.php"   class="quick-card delay-3"><i class="fas fa-calendar-alt"></i> Cek Absensi</a>
  <a href="pembimbing-nilai.php"     class="quick-card delay-4"><i class="fas fa-star"></i>          Input Nilai</a>
  <a href="pembimbing-bimbingan.php" class="quick-card delay-5"><i class="fas fa-comment-dots"></i>  Catat Bimbingan</a>
  <a href="pembimbing-siswa.php"     class="quick-card delay-6"><i class="fas fa-users"></i>         Daftar Siswa</a>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:24px;" class="two-col-panels">

  <div class="panel delay-3">
    <div class="panel-header">
      <h3><i class="fas fa-clock" style="color:#f59e0b;"></i> Jurnal Menunggu Validasi</h3>
      <a href="pembimbing-jurnal.php"><i class="fas fa-arrow-right"></i> Lihat semua</a>
    </div>
    <?php
    $res_jurnal = mysqli_query($conn,
        "SELECT j.id, j.kegiatan, j.tanggal, j.status_validasi,
                u.nama_depan, u.nama_belakang
         FROM jurnal_harian j
         INNER JOIN pkl_pengajuan p ON j.siswa_id = p.ketua_id
         LEFT JOIN  users u ON j.siswa_id = u.id
         WHERE p.pembimbing_id = $pid AND j.status_validasi = 'pending'
         ORDER BY j.id DESC LIMIT 5");
    if (!$res_jurnal || mysqli_num_rows($res_jurnal) === 0): ?>
      <p style="text-align:center;padding:20px 0;color:#64748b;font-size:.82rem;">
        <i class="fas fa-check-circle" style="color:#4ade80;"></i>&nbsp; Tidak ada jurnal yang menunggu validasi.
      </p>
    <?php else: while ($jrn = mysqli_fetch_assoc($res_jurnal)): ?>
    <div class="activity-item">
      <div class="activity-icon">
        <i class="fas fa-book" style="color:#f59e0b;"></i>
      </div>
      <div class="activity-text" style="flex:1;">
        <p><?php echo htmlspecialchars(mb_strimwidth($jrn['kegiatan'], 0, 58, '…')); ?></p>
        <span>
          <?php echo htmlspecialchars($jrn['nama_depan'] . ' ' . $jrn['nama_belakang']); ?>
          &mdash; <?php echo date('d M Y', strtotime($jrn['tanggal'])); ?>
        </span>
      </div>
      <a href="pembimbing-jurnal.php?validasi=<?php echo $jrn['id']; ?>"
         class="btn btn-warning btn-sm" style="white-space:nowrap;">
        <i class="fas fa-check"></i> Validasi
      </a>
    </div>
    <?php endwhile; endif; ?>
  </div>

  <div class="panel delay-4">
    <div class="panel-header">
      <h3><i class="fas fa-file-alt" style="color:#ef4444;"></i> Laporan Menunggu Review</h3>
      <a href="pembimbing-laporan.php"><i class="fas fa-arrow-right"></i> Lihat semua</a>
    </div>
    <?php
    $res_laporan = mysqli_query($conn,
        "SELECT l.id, l.judul_laporan, l.jenis_laporan, l.created_at,
                u.nama_depan, u.nama_belakang
         FROM laporan_pkl l
         INNER JOIN pkl_pengajuan p ON l.siswa_id = p.ketua_id
         LEFT JOIN  users u ON l.siswa_id = u.id
         WHERE p.pembimbing_id = $pid AND l.status_pembimbing = 'pending'
         ORDER BY l.id DESC LIMIT 5");
    if (!$res_laporan || mysqli_num_rows($res_laporan) === 0): ?>
      <p style="text-align:center;padding:20px 0;color:#64748b;font-size:.82rem;">
        <i class="fas fa-check-circle" style="color:#4ade80;"></i>&nbsp; Tidak ada laporan yang menunggu review.
      </p>
    <?php else: while ($lpr = mysqli_fetch_assoc($res_laporan)): ?>
    <div class="activity-item">
      <div class="activity-icon">
        <i class="fas fa-file-alt" style="color:#ef4444;"></i>
      </div>
      <div class="activity-text" style="flex:1;">
        <p>
          <?php echo htmlspecialchars($lpr['judul_laporan'] ?? '(Tanpa judul)'); ?>
          <span style="color:#94a3b8;font-size:.73rem;">[<?= ucfirst($lpr['jenis_laporan']) ?>]</span>
        </p>
        <span>
          <?php echo htmlspecialchars($lpr['nama_depan'] . ' ' . $lpr['nama_belakang']); ?>
          &mdash; <?php echo date('d M Y', strtotime($lpr['created_at'])); ?>
        </span>
      </div>
      <a href="pembimbing-laporan.php?review=<?php echo $lpr['id']; ?>"
         class="btn btn-sm" style="white-space:nowrap;background:rgba(239,68,68,.12);
         color:#ef4444;border:1px solid rgba(239,68,68,.25);">
        <i class="fas fa-eye"></i> Review
      </a>
    </div>
    <?php endwhile; endif; ?>
  </div>

</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:20px;" class="two-col-panels">

  <div class="panel delay-5">
    <div class="panel-header">
      <h3><i class="fas fa-chart-line" style="color:#c4b5fd;"></i> Progress Jurnal Siswa</h3>
      <a href="pembimbing-siswa.php"><i class="fas fa-info-circle"></i> Detail</a>
    </div>
    <?php
    $res_progress = mysqli_query($conn,
        "SELECT u.nama_depan, u.nama_belakang,
                SUM(CASE WHEN j.status_validasi = 'valid'   THEN 1 ELSE 0 END) AS valid,
                SUM(CASE WHEN j.status_validasi = 'pending' THEN 1 ELSE 0 END) AS pending,
                COUNT(j.id) AS total
         FROM pkl_pengajuan p
         LEFT JOIN users u ON p.ketua_id = u.id
         LEFT JOIN jurnal_harian j ON j.siswa_id = p.ketua_id
         WHERE p.pembimbing_id = $pid
         GROUP BY p.ketua_id, u.nama_depan, u.nama_belakang
         LIMIT 5");
    if (!$res_progress || mysqli_num_rows($res_progress) === 0): ?>
      <p style="text-align:center;padding:20px 0;color:#64748b;font-size:.82rem;">Belum ada siswa bimbingan.</p>
    <?php else: while ($prg = mysqli_fetch_assoc($res_progress)):
        $total   = max((int)$prg['total'], 1);
        $pct     = round(($prg['valid'] / $total) * 100);
        $bar_col = $pct >= 70 ? '#4ade80' : ($pct >= 40 ? '#f59e0b' : '#ef4444');
    ?>
    <div style="margin-bottom:14px;">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:5px;">
        <span style="font-size:.8rem;color:#cbd5e1;">
          <?php echo htmlspecialchars($prg['nama_depan'] . ' ' . $prg['nama_belakang']); ?>
        </span>
        <span style="font-size:.72rem;color:#64748b;">
          <?php echo $prg['valid']; ?>/<?php echo $total; ?> jurnal
          <?php if ($prg['pending'] > 0): ?>
            <span style="color:#f59e0b;margin-left:4px;">(<?= $prg['pending'] ?> pending)</span>
          <?php endif; ?>
        </span>
      </div>
      <div class="progress-bar">
        <div class="progress-fill" style="width:<?php echo $pct; ?>%;background:<?php echo $bar_col; ?>;"></div>
      </div>
    </div>
    <?php endwhile; endif; ?>
  </div>

  <div class="panel delay-6">
    <div class="panel-header">
      <h3><i class="fas fa-history" style="color:#93c5fd;"></i> Bimbingan Terakhir</h3>
      <a href="pembimbing-bimbingan.php"><i class="fas fa-arrow-right"></i> Lihat semua</a>
    </div>
    <?php
    // Coba ambil dari tabel pkl_bimbingan jika ada; fallback graceful jika tidak
    $tbl_bimbing = mysqli_query($conn, "SHOW TABLES LIKE 'pkl_bimbingan'");
    if (mysqli_num_rows($tbl_bimbing) > 0):
        $res_bimbing = mysqli_query($conn,
            "SELECT b.catatan, b.tanggal,
                    u.nama_depan, u.nama_belakang
             FROM pkl_bimbingan b
             LEFT JOIN users u ON b.siswa_id = u.id
             WHERE b.pembimbing_id = $pid
             ORDER BY b.id DESC LIMIT 5");
        if (!$res_bimbing || mysqli_num_rows($res_bimbing) === 0): ?>
          <p style="text-align:center;padding:20px 0;color:#64748b;font-size:.82rem;">Belum ada catatan bimbingan.</p>
        <?php else: while ($bim = mysqli_fetch_assoc($res_bimbing)): ?>
        <div class="activity-item">
          <div class="activity-icon">
            <i class="fas fa-comment-dots" style="color:#93c5fd;"></i>
          </div>
          <div class="activity-text">
            <p><?php echo htmlspecialchars(mb_strimwidth($bim['catatan'] ?? '-', 0, 56, '…')); ?></p>
            <span>
              <?php echo htmlspecialchars($bim['nama_depan'] . ' ' . $bim['nama_belakang']); ?>
              &mdash; <?php echo date('d M Y', strtotime($bim['tanggal'])); ?>
            </span>
          </div>
        </div>
        <?php endwhile; endif;
    else: ?>
      <p style="text-align:center;padding:20px 0;color:#64748b;font-size:.82rem;">
        <i class="fas fa-info-circle" style="color:#93c5fd;"></i>&nbsp;
        Tabel <code>pkl_bimbingan</code> belum tersedia.
      </p>
    <?php endif; ?>
  </div>

</div>

<div class="panel delay-5" style="margin-top:20px;margin-bottom:0;">
  <div class="panel-header">
    <h3><i class="fas fa-user-check" style="color:#4ade80;"></i> Rekap Absensi Siswa Bimbingan</h3>
    <a href=absensi_pembimbing.php"><i class="fas fa-arrow-right"></i> Detail lengkap</a>
  </div>
  <?php
  $tbl_absensi = mysqli_query($conn, "SHOW TABLES LIKE 'pkl_absensi'");
  if (mysqli_num_rows($tbl_absensi) > 0):
      $res_absensi = mysqli_query($conn,
          "SELECT u.nama_depan, u.nama_belakang,
                  SUM(CASE WHEN a.status = 'hadir'  THEN 1 ELSE 0 END) AS hadir,
                  SUM(CASE WHEN a.status = 'izin'   THEN 1 ELSE 0 END) AS izin,
                  SUM(CASE WHEN a.status = 'alpha'  THEN 1 ELSE 0 END) AS alpha,
                  COUNT(a.id) AS total_hari
           FROM pkl_pengajuan p
           LEFT JOIN users u ON p.ketua_id = u.id
           LEFT JOIN pkl_absensi a ON a.siswa_id = p.ketua_id
           WHERE p.pembimbing_id = $pid
           GROUP BY p.ketua_id, u.nama_depan, u.nama_belakang
           LIMIT 5");
      if ($res_absensi && mysqli_num_rows($res_absensi) > 0): ?>
  <div class="table-responsive">
    <table style="font-size:.82rem;">
      <thead>
        <tr>
          <th><i class="fas fa-user"></i> Nama Siswa</th>
          <th style="text-align:center;"><i class="fas fa-check"></i> Hadir</th>
          <th style="text-align:center;"><i class="fas fa-info"></i> Izin</th>
          <th style="text-align:center;"><i class="fas fa-times"></i> Alpha</th>
          <th><i class="fas fa-percentage"></i> Kehadiran</th>
        </tr>
      </thead>
      <tbody>
      <?php while ($ab = mysqli_fetch_assoc($res_absensi)):
          $tot   = max((int)$ab['total_hari'], 1);
          $pct_h = round(($ab['hadir'] / $tot) * 100);
          $bar   = $pct_h >= 80 ? '#4ade80' : ($pct_h >= 60 ? '#f59e0b' : '#ef4444');
      ?>
      <tr>
        <td><?php echo htmlspecialchars($ab['nama_depan'] . ' ' . $ab['nama_belakang']); ?></td>
        <td style="text-align:center;"><span class="pill pill-green"><?= $ab['hadir'] ?></span></td>
        <td style="text-align:center;"><span class="pill pill-yellow"><?= $ab['izin'] ?></span></td>
        <td style="text-align:center;"><span class="pill pill-red"><?= $ab['alpha'] ?></span></td>
        <td style="min-width:120px;">
          <div style="display:flex;align-items:center;gap:8px;">
            <div class="progress-bar" style="flex:1;">
              <div class="progress-fill" style="width:<?= $pct_h ?>%;background:<?= $bar ?>;"></div>
            </div>
            <span style="font-size:.7rem;color:#64748b;white-space:nowrap;"><?= $pct_h ?>%</span>
          </div>
        </td>
      </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
    <p style="text-align:center;padding:20px 0;color:#64748b;font-size:.82rem;">Belum ada data absensi.</p>
  <?php endif;
  else: ?>
    <p style="text-align:center;padding:20px 0;color:#64748b;font-size:.82rem;">
      <i class="fas fa-info-circle" style="color:#93c5fd;"></i>&nbsp;
      Tabel <code>pkl_absensi</code> belum tersedia di database.
    </p>
  <?php endif; ?>
</div>

</body>
</html>