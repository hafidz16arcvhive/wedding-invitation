<?php
// ============================================================
// FILE: invitation/templates/_partials.php
// Komponen reusable: RSVP, Ucapan, Wedding Gift
// Di-include oleh semua file template
//
// Variabel yang tersedia (dari index.php router):
//   $wedding, $slug, $form_status, $messages, $rsvp_stat, $gallery
// ============================================================

// ── Helper: render form RSVP ──────────────────────────────────
function render_rsvp(array $wedding, string $slug, string $form_status,
                     array $rsvp_stat, string $btn_class = '',
                     string $input_class = '', string $bg_class = ''): void {
    $count = (int)($rsvp_stat['total_response'] ?? 0);
    $total = (int)($rsvp_stat['total_guests'] ?? 0);
    ?>
    <div id="rsvp">
      <?php if ($form_status === 'rsvp_ok'): ?>
      <div style="background:#f0fdf4;border-left:3px solid #16a34a;padding:12px 16px;
                  margin-bottom:20px;font-size:13px;color:#166534;display:flex;
                  align-items:center;gap:8px;">
        <i class="bi bi-check-circle-fill"></i>
        Terima kasih! Konfirmasi Anda sudah tercatat.
      </div>
      <?php endif; ?>

      <?php if ($count > 0): ?>
      <p style="font-size:12px;color:inherit;opacity:0.5;margin-bottom:16px;">
        <i class="bi bi-people" style="margin-right:4px;"></i>
        <strong><?= $count ?></strong> tamu (<?= $total ?> orang) sudah konfirmasi hadir
      </p>
      <?php endif; ?>

      <form method="POST" action="?slug=<?= $slug ?>#rsvp" style="display:flex;flex-direction:column;gap:14px;">
        <input type="hidden" name="action" value="rsvp">

        <div>
          <label style="display:block;font-size:10px;font-weight:600;letter-spacing:0.12em;
                        text-transform:uppercase;margin-bottom:6px;opacity:0.6;">
            Nama Lengkap *
          </label>
          <input type="text" name="guest_name" required
                 placeholder="Nama Anda"
                 style="width:100%;padding:11px 14px;border:1px solid currentColor;
                        border-opacity:0.2;background:transparent;font-size:14px;
                        color:inherit;outline:none;<?= $input_class ?>">
        </div>

        <div>
          <label style="display:block;font-size:10px;font-weight:600;letter-spacing:0.12em;
                        text-transform:uppercase;margin-bottom:6px;opacity:0.6;">
            No. WhatsApp
          </label>
          <input type="tel" name="guest_phone" placeholder="08xxxxxxxxxx"
                 style="width:100%;padding:11px 14px;border:1px solid currentColor;
                        border-opacity:0.2;background:transparent;font-size:14px;
                        color:inherit;outline:none;<?= $input_class ?>">
        </div>

        <div>
          <label style="display:block;font-size:10px;font-weight:600;letter-spacing:0.12em;
                        text-transform:uppercase;margin-bottom:8px;opacity:0.6;">
            Konfirmasi *
          </label>
          <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;">
            <?php foreach (['hadir'=>'Hadir','tidak_hadir'=>'Tidak Hadir','mungkin'=>'Mungkin'] as $v=>$l): ?>
            <label style="cursor:pointer;">
              <input type="radio" name="attendance" value="<?= $v ?>"
                     <?= $v==='hadir'?'checked':'' ?> style="display:none;"
                     class="att-radio">
              <div class="att-label" data-value="<?= $v ?>"
                   style="padding:9px 6px;text-align:center;border:1px solid currentColor;
                          border-opacity:0.3;font-size:11px;font-weight:500;
                          transition:all 0.2s;">
                <?= $l ?>
              </div>
            </label>
            <?php endforeach; ?>
          </div>
        </div>

        <div>
          <label style="display:block;font-size:10px;font-weight:600;letter-spacing:0.12em;
                        text-transform:uppercase;margin-bottom:6px;opacity:0.6;">
            Jumlah Tamu
          </label>
          <select name="total_guests"
                  style="width:100%;padding:11px 14px;border:1px solid currentColor;
                         border-opacity:0.2;background:transparent;font-size:14px;
                         color:inherit;outline:none;<?= $input_class ?>">
            <?php for ($i=1;$i<=10;$i++): ?>
            <option value="<?= $i ?>"><?= $i ?> orang</option>
            <?php endfor; ?>
          </select>
        </div>

        <div>
          <label style="display:block;font-size:10px;font-weight:600;letter-spacing:0.12em;
                        text-transform:uppercase;margin-bottom:6px;opacity:0.6;">
            Ucapan (opsional)
          </label>
          <textarea name="message" rows="3" placeholder="Ucapan dan doa..."
                    style="width:100%;padding:11px 14px;border:1px solid currentColor;
                           border-opacity:0.2;background:transparent;font-size:14px;
                           color:inherit;outline:none;resize:vertical;<?= $input_class ?>"></textarea>
        </div>

        <button type="submit" class="<?= $btn_class ?>"
                style="display:flex;align-items:center;justify-content:center;gap:8px;
                       padding:13px;font-size:11px;font-weight:600;letter-spacing:0.15em;
                       text-transform:uppercase;cursor:pointer;border:none;width:100%;">
          <i class="bi bi-send"></i> Kirim Konfirmasi
        </button>
      </form>
    </div>
    <?php
}

// ── Helper: render wall ucapan ────────────────────────────────
function render_messages(array $wedding, string $slug, string $form_status,
                          array $messages, string $btn_class = '',
                          string $input_class = '', string $msg_style = ''): void {
    ?>
    <div id="ucapan">
      <?php if ($form_status === 'msg_ok'): ?>
      <div style="background:#f0fdf4;border-left:3px solid #16a34a;padding:12px 16px;
                  margin-bottom:20px;font-size:13px;color:#166534;">
        <i class="bi bi-check-circle-fill me-2"></i>Ucapan Anda terkirim!
      </div>
      <?php endif; ?>

      <!-- Form kirim ucapan -->
      <form method="POST" action="?slug=<?= $slug ?>#ucapan"
            style="display:flex;flex-direction:column;gap:12px;margin-bottom:32px;">
        <input type="hidden" name="action" value="message">
        <input type="text" name="msg_name" required placeholder="Nama Anda"
               style="padding:11px 14px;border:1px solid currentColor;border-opacity:0.2;
                      background:transparent;font-size:14px;color:inherit;outline:none;
                      <?= $input_class ?>">
        <textarea name="msg_text" required rows="3"
                  placeholder="Ucapan dan doa terbaik..."
                  style="padding:11px 14px;border:1px solid currentColor;border-opacity:0.2;
                         background:transparent;font-size:14px;color:inherit;outline:none;
                         resize:vertical;<?= $input_class ?>"></textarea>
        <button type="submit" class="<?= $btn_class ?>"
                style="display:flex;align-items:center;justify-content:center;gap:8px;
                       padding:12px;font-size:11px;font-weight:600;letter-spacing:0.15em;
                       text-transform:uppercase;cursor:pointer;border:none;width:100%;">
          <i class="bi bi-heart"></i> Kirim Ucapan
        </button>
      </form>

      <!-- Daftar ucapan -->
      <?php if (!empty($messages)): ?>
      <div style="display:flex;flex-direction:column;gap:1px;background:currentColor;
                  border:1px solid currentColor;border-opacity:0.1;opacity:0.99;">
        <?php foreach ($messages as $msg): ?>
        <div style="padding:18px 20px;<?= $msg_style ?>">
          <div style="font-size:13px;font-weight:600;margin-bottom:3px;">
            <?= clean($msg['guest_name']) ?>
          </div>
          <div style="font-size:11px;opacity:0.4;margin-bottom:8px;">
            <?= date('d M Y, H:i', strtotime($msg['created_at'])) ?>
          </div>
          <div style="font-size:14px;font-style:italic;opacity:0.7;line-height:1.6;">
            "<?= clean($msg['message']) ?>"
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
      <p style="text-align:center;font-size:13px;opacity:0.4;padding:24px 0;">
        Jadilah yang pertama memberikan ucapan ✉️
      </p>
      <?php endif; ?>
    </div>
    <?php
}

// ── Helper: render wedding gift ───────────────────────────────
function render_gift(array $wedding, string $btn_class = ''): void {
    if (empty($wedding['qris_image']) && empty($wedding['bank_account_number'])) return;
    ?>
    <div id="gift">
      <div style="display:grid;grid-template-columns:<?= (!empty($wedding['qris_image']) && !empty($wedding['bank_account_number'])) ? '1fr 1fr' : '1fr' ?>;gap:1px;background:currentColor;border:1px solid currentColor;border-opacity:0.15;">

        <?php if (!empty($wedding['qris_image'])): ?>
        <div style="padding:28px;text-align:center;background:inherit;">
          <p style="font-size:10px;font-weight:600;letter-spacing:0.2em;text-transform:uppercase;
                    opacity:0.4;margin-bottom:16px;">Scan QRIS</p>
          <img src="<?= BASE_URL ?>/<?= clean($wedding['qris_image']) ?>"
               alt="QRIS"
               style="width:140px;height:140px;object-fit:contain;margin:0 auto 12px;
                      background:white;padding:8px;">
          <p style="font-size:11px;opacity:0.35;">Scan dengan aplikasi bank / e-wallet apapun</p>
        </div>
        <?php endif; ?>

        <?php if (!empty($wedding['bank_account_number'])): ?>
        <div style="padding:28px;text-align:center;background:inherit;">
          <p style="font-size:10px;font-weight:600;letter-spacing:0.2em;text-transform:uppercase;
                    opacity:0.4;margin-bottom:16px;">Transfer Bank</p>
          <i class="bi bi-bank2" style="font-size:28px;opacity:0.2;display:block;margin-bottom:12px;"></i>
          <p style="font-size:18px;font-weight:300;margin-bottom:4px;">
            <?= clean($wedding['bank_name'] ?? '') ?>
          </p>
          <p style="font-size:20px;font-weight:400;letter-spacing:0.1em;margin-bottom:4px;">
            <?= clean($wedding['bank_account_number']) ?>
          </p>
          <p style="font-size:12px;opacity:0.4;margin-bottom:14px;">
            a.n <?= clean($wedding['bank_account_name'] ?? '') ?>
          </p>
          <button class="<?= $btn_class ?>"
                  onclick="copyText('<?= clean($wedding['bank_account_number']) ?>', this)"
                  style="padding:8px 18px;font-size:11px;font-weight:500;letter-spacing:0.1em;
                         text-transform:uppercase;cursor:pointer;border:1px solid currentColor;
                         border-opacity:0.3;background:transparent;color:inherit;">
            <i class="bi bi-clipboard me-1"></i>Salin Nomor
          </button>
        </div>
        <?php endif; ?>

      </div>
    </div>
    <?php
}

// ── Helper: render galeri foto ────────────────────────────────
function render_gallery(array $gallery, string $extra_style = ''): void {
    if (empty($gallery)) return;
    ?>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:4px;<?= $extra_style ?>">
      <?php foreach (array_slice($gallery, 0, 7) as $i => $photo): ?>
      <div onclick="openLightbox('<?= BASE_URL ?>/<?= clean($photo) ?>')"
           style="<?= $i===0 ? 'grid-column:span 2;' : '' ?>
                  position:relative;overflow:hidden;cursor:pointer;
                  aspect-ratio:<?= $i===0 ? '2/1' : '1/1' ?>;">
        <img src="<?= BASE_URL ?>/<?= clean($photo) ?>"
             alt="Foto <?= $i+1 ?>" loading="lazy"
             style="position:absolute;inset:0;width:100%;height:100%;
                    object-fit:cover;transition:transform 0.5s ease;"
             onmouseover="this.style.transform='scale(1.05)'"
             onmouseout="this.style.transform='scale(1)'">
      </div>
      <?php endforeach; ?>
    </div>
    <?php
}

// ── Helper: render countdown timer ───────────────────────────
function render_countdown(string $event_date, string $event_time,
                           string $number_style = '', string $label_style = ''): void {
    ?>
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1px;background:currentColor;
                border:1px solid currentColor;border-opacity:0.15;margin-top:32px;"
         id="countdown-grid" data-date="<?= $event_date ?>" data-time="<?= $event_time ?>">
      <?php foreach (['cd-days'=>'Hari','cd-hours'=>'Jam','cd-minutes'=>'Menit','cd-seconds'=>'Detik'] as $id=>$label): ?>
      <div style="padding:24px 12px;text-align:center;background:inherit;">
        <span id="<?= $id ?>"
              style="display:block;font-size:clamp(28px,5vw,48px);font-weight:300;
                     line-height:1;letter-spacing:-0.02em;<?= $number_style ?>">
          00
        </span>
        <span style="display:block;font-size:10px;font-weight:500;letter-spacing:0.2em;
                     text-transform:uppercase;margin-top:6px;opacity:0.5;<?= $label_style ?>">
          <?= $label ?>
        </span>
      </div>
      <?php endforeach; ?>
    </div>
    <?php
}
?>

<!-- ── JS Shared (dipakai semua template) ── -->
<script src="<?= BASE_URL ?>/assets/js/invitation.js"></script>
<script>
// Attendance selector styling
document.querySelectorAll('.att-radio').forEach(function(radio) {
  radio.addEventListener('change', function() {
    document.querySelectorAll('.att-label').forEach(function(l) {
      l.style.background = 'transparent';
      l.style.opacity = '1';
    });
    this.nextElementSibling.style.background = 'currentColor';
    this.nextElementSibling.style.opacity = '0.15';
  });
});
// Style default (hadir)
const def = document.querySelector('.att-radio:checked');
if (def) { def.nextElementSibling.style.background='currentColor'; def.nextElementSibling.style.opacity='0.15'; }
</script>