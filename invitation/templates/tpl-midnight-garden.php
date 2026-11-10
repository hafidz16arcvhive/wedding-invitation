<?php
// ============================================================
// FILE: invitation/templates/tpl-midnight-garden.php
// Auto-generated template
// ============================================================
include_once __DIR__ . '/_partials.php';
?>
<!DOCTYPE html><html lang="id">
<head>
<meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Undangan <?= $couple ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/invitation.css">
<style>

@import url("https://fonts.googleapis.com/css2?family=Rozha+One&family=Hind:wght@300;400;500&display=swap");
:root{--C1:#071210;--C2:#4DB87A;--C3:#0E1F1C;--C4:#2A6B4A;}
body{background:var(--C1);color:#E8F5E9;font-family:"Hind",sans-serif;}
.sec{padding:80px 24px;border-top:1px solid rgba(77,184,122,.08);}
.sec-alt{background:var(--C3);}
.con{max-width:680px;margin:0 auto;} .con-w{max-width:920px;margin:0 auto;}
.tag{font-size:10px;font-weight:500;letter-spacing:.3em;text-transform:uppercase;color:var(--C2);display:block;margin-bottom:12px;}
.ttl{font-family:"Rozha One",serif;font-size:clamp(30px,5vw,46px);font-weight:400;line-height:1.15;}
.div{width:48px;height:2px;background:var(--C2);margin:16px 0;opacity:.6;}
.btn-main{background:var(--C2);color:var(--C1);}

</style>
</head>
<body>

<!-- COVER -->
<div id="cover-screen" style="background:radial-gradient(ellipse at 50% 0%,rgba(77,184,122,.1),#071210 60%);background-color:#071210;position:fixed;inset:0;z-index:1000;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:24px;transition:opacity .8s ease,transform .8s ease;">
  
  <div class="cover-frame" style="border:1px solid rgba(255,255,255,.12);padding:44px 36px;text-align:center;max-width:360px;width:100%;position:relative;">
    <span class="corner-br"></span><span class="corner-tl"></span>
    <p class="tag" style="margin-bottom:28px;">Undangan Pernikahan</p>
    <div style="font-family:"Rozha One",serif;color:#E8F5E9;font-size:clamp(36px,8vw,60px);line-height:1.05;"><?= clean($wedding['groom_name']) ?></div>
    <div style="color:rgba(77,184,122,.3);font-size:.5em;display:block;margin:8px 0;">dan</div>
    <div style="font-family:"Rozha One",serif;color:#E8F5E9;font-size:clamp(36px,8vw,60px);line-height:1.05;margin-bottom:24px;"><?= clean($wedding['bride_name']) ?></div>
    <div style="width:28px;height:1px;background:rgba(255,255,255,.15);margin:0 auto 18px;"></div>
    <p style="font-size:12px;color:rgba(255,255,255,.35);margin-bottom:28px;letter-spacing:.05em;"><?= date('d F Y',strtotime($event_date)) ?></p>
    <button onclick="openInvitation()" style="background:var(--C2);color:var(--C1);width:100%;padding:13px;font-size:11px;font-weight:600;letter-spacing:.15em;text-transform:uppercase;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;">
      <i class="bi bi-envelope-open"></i> Buka Undangan
    </button>
  </div>
</div>

<div id="main-content" style="display:none;">
  <?php if(!empty($wedding['music_url'])): ?><audio id="bg-music" loop><source src="<?= BASE_URL?>/<?= clean($wedding['music_url'])?>" type="audio/mpeg"></audio><?php endif; ?>
  <button id="music-btn" onclick="toggleMusic()"><i class="bi bi-music-note"></i></button>

  <!-- HERO -->
  <section class="sec sec-dark" style="min-height:100vh;display:flex;align-items:center;justify-content:center;text-align:center;position:relative;overflow:hidden;">
    <?php if(!empty($wedding['couple_photo'])): ?>
    <div style="position:absolute;inset:0;"><img src="<?= BASE_URL?>/<?= clean($wedding['couple_photo'])?>" style="width:100%;height:100%;object-fit:cover;opacity:.15;filter:grayscale(40%);"></div>
    <?php endif; ?>
    <div class="con reveal" style="position:relative;z-index:1;">
      <span class="tag reveal" style="margin-bottom:16px;">Bismillahirrahmanirrahim</span>
      <div style="font-family:"Rozha One",serif;color:#E8F5E9;font-size:clamp(36px,8vw,60px);line-height:1.05;" class="reveal reveal-delay-1"><?= clean($wedding['groom_name']) ?>
        <div style="color:rgba(77,184,122,.3);font-size:.5em;display:block;margin:6px 0;">&amp;</div>
        <?= clean($wedding['bride_name']) ?>
      </div>
      <div class="div reveal reveal-delay-2" style="margin:20px auto;opacity:.3;"></div>
      <p class="reveal reveal-delay-3" style="font-size:12px;opacity:.4;letter-spacing:.1em;"><?= date('d F Y',strtotime($event_date)) ?><?php if(!empty($wedding['reception_venue'])): ?> · <?= clean($wedding['reception_venue'])?><?php endif;?></p>
    </div>
  </section>

  <!-- PEMBUKAAN -->
  <section class="sec" style="text-align:center;background:var(--C3);">
    <div class="con">
      <span class="tag reveal">Dengan Rahmat Allah SWT</span>
      <h2 class="ttl reveal reveal-delay-1">Kami mengundang<br>kehadiran Anda</h2>
      <div class="div reveal reveal-delay-2"></div>
      <p class="reveal reveal-delay-3" style="font-size:14px;opacity:.6;line-height:1.9;max-width:460px;margin:0 auto;">Merupakan suatu kebahagiaan apabila Bapak / Ibu / Saudara / i berkenan hadir dan memberikan doa restu.</p>
    </div>
  </section>

  <!-- MEMPELAI -->
  <section class="sec" style="background:#0A1A17;">
    <div class="con-w">
      <div style="text-align:center;"><span class="tag reveal">Kedua Mempelai</span><h2 class="ttl reveal reveal-delay-1">Yang Berbahagia</h2></div>
      <div style="display:grid;grid-template-columns:1fr auto 1fr;gap:24px;align-items:center;margin-top:40px;">
        <?php foreach([['groom','Pria'],['bride','Wanita']] as [$k,$role]): ?>
        <div style="text-align:center;" class="reveal">
          <?php if(!empty($wedding[$k.'_photo'])): ?><img src="<?= BASE_URL?>/<?= clean($wedding[$k.'_photo'])?>" style="width:130px;height:165px;object-fit:cover;margin:0 auto 16px;">
          <?php else: ?><div style="width:130px;height:165px;background:rgba(128,128,128,.1);margin:0 auto 16px;display:flex;align-items:center;justify-content:center;font-size:40px;opacity:.3;"><i class="bi bi-person"></i></div><?php endif; ?>
          <p class="tag" style="margin-bottom:6px;">Mempelai <?= $role ?></p>
          <h3 style="font-family:var(--display-font,inherit);font-size:24px;font-weight:400;"><?= clean($wedding[$k.'_name']) ?></h3>
          <?php if(!empty($wedding[$k.'_full_name'])): ?><p style="font-size:13px;opacity:.5;margin-top:4px;"><?= clean($wedding[$k.'_full_name'])?></p><?php endif; ?>
          <?php if(!empty($wedding[$k.'_father'])): ?><p style="font-size:12px;opacity:.4;margin-top:10px;line-height:1.8;">Putr<?= $role==='Pria'?'a':'i'?> dari<br><?= clean($wedding[$k.'_father'])?><?php if(!empty($wedding[$k.'_mother'])): ?> &amp; <?= clean($wedding[$k.'_mother'])?><?php endif;?></p><?php endif; ?>
        </div>
        <?php if($k==='groom'): ?><div style="font-size:44px;font-weight:300;font-style:italic;opacity:.2;text-align:center;" class="reveal reveal-delay-1">&amp;</div><?php endif; ?>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- COUNTDOWN -->
  <section class="sec sec-dark" style="text-align:center;">
    <div class="con">
      <span class="tag reveal">Menuju Hari Istimewa</span>
      <h2 class="ttl reveal reveal-delay-1"><?= date('d F Y',strtotime($event_date)) ?></h2>
      <?php render_countdown($event_date,$event_time); ?>
    </div>
  </section>

  <!-- ACARA -->
  <section class="sec" style="background:var(--C3);">
    <div class="con-w">
      <div style="text-align:center;"><span class="tag reveal">Rangkaian Acara</span><h2 class="ttl reveal reveal-delay-1">Waktu &amp; Tempat</h2><div class="div reveal" style="margin:14px auto;"></div></div>
      <div style="display:grid;grid-template-columns:<?= !empty($wedding['akad_date'])?'1fr 1fr':'1fr'?>;gap:1px;background:rgba(128,128,128,.15);margin-top:40px;">
        <?php foreach([['akad','Akad Nikah','Ijab Qabul'],['reception','Resepsi','Walimatul Ursy']] as [$key,$type,$name]):
          if(empty($wedding[$key.'_date'])) continue; ?>
        <div style="padding:28px;background:inherit;" class="reveal">
          <p class="tag" style="margin-bottom:12px;"><?= $type ?></p>
          <h3 style="font-family:"Rozha One",serif;font-size:22px;font-weight:400;margin-bottom:16px;"><?= $name ?></h3>
          <?php $items=[['bi-calendar3',date('l, d F Y',strtotime($wedding[$key.'_date']))],['bi-clock',(!empty($wedding[$key.'_time'])?date('H.i',strtotime($wedding[$key.'_time'])).' WIB':'')],['bi-geo-alt',($wedding[$key.'_venue']??'')],['bi-pin-map',($wedding[$key.'_address']??'')]];
          foreach($items as [$ico,$val]): if(empty($val)) continue; ?>
          <div style="display:flex;align-items:flex-start;gap:9px;margin-bottom:9px;font-size:13px;opacity:.7;">
            <i class="bi <?= $ico ?>" style="color:#4DB87A;margin-top:1px;flex-shrink:0;"></i><span><?= clean($val)?></span>
          </div>
          <?php endforeach; ?>
          <?php if(!empty($wedding[$key.'_maps_url'])): ?><a href="<?= $wedding[$key.'_maps_url']?>" target="_blank" style="display:inline-flex;align-items:center;gap:6px;margin-top:12px;padding:7px 14px;border:1px solid #4DB87A;font-size:11px;font-weight:500;letter-spacing:.1em;text-transform:uppercase;color:#4DB87A;text-decoration:none;"><i class="bi bi-map"></i> Lihat Peta</a><?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- LOVE STORY -->
  <?php if(!empty($wedding['love_story'])): ?>
  <section class="sec" style="background:#0A1A17;">
    <div class="con"><span class="tag reveal">Kisah Kami</span><h2 class="ttl reveal reveal-delay-1">Our Love Story</h2><div class="div reveal reveal-delay-2"></div>
    <p class="reveal reveal-delay-3" style="font-family:"Rozha One",serif;font-size:clamp(16px,2.5vw,20px);font-weight:300;font-style:italic;line-height:1.8;opacity:.65;border-left:2px solid #4DB87A;padding-left:20px;margin-top:24px;"><?= nl2br(clean($wedding['love_story']))?></p></div>
  </section>
  <?php endif; ?>

  <!-- GALERI -->
  <?php if(!empty($gallery)): ?>
  <section class="sec" style="background:var(--C3);">
    <div class="con-w"><div style="text-align:center;"><span class="tag reveal">Momen Kami</span><h2 class="ttl reveal reveal-delay-1">Galeri Foto</h2><div class="div reveal reveal-delay-2" style="margin:14px auto 32px;"></div></div>
    <?php render_gallery($gallery); ?></div>
  </section>
  <?php endif; ?>

  <!-- RSVP -->
  <section class="sec" style="background:#0A1A17;">
    <div class="con"><span class="tag reveal">Konfirmasi Kehadiran</span><h2 class="ttl reveal reveal-delay-1">RSVP</h2><div class="div reveal reveal-delay-2"></div>
    <div class="reveal" style="margin-top:24px;"><?php render_rsvp($wedding,$slug,$form_status,$rsvp_stat,'btn-main'); ?></div></div>
  </section>

  <!-- UCAPAN -->
  <section class="sec" style="background:var(--C3);">
    <div class="con"><span class="tag reveal">Dari Para Tamu</span><h2 class="ttl reveal reveal-delay-1">Ucapan &amp; Doa</h2><div class="div reveal reveal-delay-2"></div>
    <div class="reveal" style="margin-top:24px;"><?php render_messages($wedding,$slug,$form_status,$messages,'btn-main'); ?></div></div>
  </section>

  <!-- GIFT -->
  <?php if(!empty($wedding['qris_image'])||!empty($wedding['bank_account_number'])): ?>
  <section class="sec sec-dark" style="text-align:center;">
    <div class="con"><span class="tag reveal">Hadiah Pernikahan</span><h2 class="ttl reveal reveal-delay-1">Wedding Gift</h2><div class="div reveal" style="margin:14px auto;"></div>
    <p class="reveal" style="font-size:13px;opacity:.4;margin-bottom:28px;">Doa restu Anda adalah hadiah terbaik bagi kami.</p>
    <div class="reveal"><?php render_gift($wedding); ?></div></div>
  </section>
  <?php endif; ?>

  <!-- PENUTUP -->
  <section class="sec" style="text-align:center;background:var(--C3);">
    <div class="con"><span class="tag reveal">Penutup</span><h2 class="ttl reveal reveal-delay-1">Terima Kasih</h2><div class="div reveal" style="margin:14px auto;"></div>
    <p class="reveal" style="font-size:14px;opacity:.55;max-width:400px;margin:0 auto 36px;line-height:1.9;">Atas kehadiran dan doa restu Bapak / Ibu / Saudara / i, kami ucapkan terima kasih.</p>
    <div class="reveal" style="font-family:"Rozha One",serif;font-size:clamp(28px,6vw,46px);font-weight:400;margin-bottom:28px;"><?= clean($wedding['groom_name']) ?> &amp; <?= clean($wedding['bride_name']) ?></div>
    <a href="<?= BASE_URL ?>" style="font-size:11px;font-weight:500;letter-spacing:.12em;text-transform:uppercase;opacity:.4;border-bottom:1px solid currentColor;padding-bottom:2px;">← Kembali</a></div>
  </section>
</div>

<div id="lightbox" onclick="closeLightbox()"><button id="lightbox-close" onclick="closeLightbox()"><i class="bi bi-x-lg"></i></button><img id="lightbox-img" src="" alt="Foto"></div>
</body></html>