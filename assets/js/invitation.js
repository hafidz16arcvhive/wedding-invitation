/* ============================================================
   FILE: assets/js/invitation.js
   Semua fitur interaktif undangan:
   - Buka undangan (cover screen)
   - Countdown timer
   - Scroll reveal
   - Musik background
   - Lightbox galeri
   - Copy rekening
   ============================================================ */

/* ── 1. BUKA UNDANGAN ───────────────────────────────────────── */
function openInvitation() {
  const cover = document.getElementById('cover-screen');
  if (!cover) return;

  // Tambah class 'closing' → CSS menjalankan animasi fade+scale out
  cover.classList.add('closing');

  // Setelah animasi selesai (800ms), sembunyikan cover & tampilkan konten
  setTimeout(() => {
    cover.style.display = 'none';
    document.getElementById('main-content').style.display = 'block';

    // Jalankan semua fitur setelah konten tampil
    initScrollReveal();
    startCountdown();
    tryAutoplayMusic();
  }, 800);
}

/* ── 2. COUNTDOWN TIMER ─────────────────────────────────────── */
function startCountdown() {
  // Ambil tanggal dari atribut data-date di elemen countdown
  const el = document.getElementById('countdown-grid');
  if (!el) return;

  const eventDate = new Date(el.dataset.date + 'T' + (el.dataset.time || '08:00:00'));

  function update() {
    const now  = new Date();
    const diff = eventDate - now;

    // Jika sudah lewat hari H
    if (diff <= 0) {
      el.innerHTML = `
        <div class="countdown-item" style="grid-column:span 4">
          <span class="countdown-number" style="font-size:28px">🎉 Selamat Menempuh Hidup Baru!</span>
        </div>`;
      return;
    }

    // Hitung hari, jam, menit, detik
    const days    = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours   = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

    // Update tampilan — padStart(2,'0') agar selalu 2 digit, contoh: 5 → "05"
    document.getElementById('cd-days').textContent    = String(days).padStart(2, '0');
    document.getElementById('cd-hours').textContent   = String(hours).padStart(2, '0');
    document.getElementById('cd-minutes').textContent = String(minutes).padStart(2, '0');
    document.getElementById('cd-seconds').textContent = String(seconds).padStart(2, '0');
  }

  update(); // Jalankan langsung
  setInterval(update, 1000); // Perbarui setiap 1 detik
}

/* ── 3. SCROLL REVEAL ───────────────────────────────────────── */
function initScrollReveal() {
  // IntersectionObserver = deteksi elemen masuk viewport saat scroll
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        // Setelah terlihat, berhenti observasi (animasi cukup sekali)
        observer.unobserve(entry.target);
      }
    });
  }, {
    threshold: 0.1,      // Muncul saat 10% elemen terlihat
    rootMargin: '0px 0px -40px 0px' // Sedikit offset dari bawah viewport
  });

  // Pasang observer ke semua elemen dengan class .reveal
  document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
}

/* ── 4. MUSIK BACKGROUND ────────────────────────────────────── */
let musicPlaying = false;

function tryAutoplayMusic() {
  const audio = document.getElementById('bg-music');
  const btn   = document.getElementById('music-btn');
  if (!audio || !btn) return;

  audio.volume = 0.35;

  // Browser modern blokir autoplay — kita coba diam-diam
  audio.play()
    .then(() => {
      musicPlaying = true;
      btn.querySelector('i').classList.add('music-spinning');
    })
    .catch(() => {
      // Autoplay diblokir — tidak apa-apa, user bisa klik tombol musik
      musicPlaying = false;
    });
}

function toggleMusic() {
  const audio = document.getElementById('bg-music');
  const icon  = document.getElementById('music-btn')?.querySelector('i');
  if (!audio) return;

  if (musicPlaying) {
    audio.pause();
    icon?.classList.remove('music-spinning');
    musicPlaying = false;
  } else {
    audio.play();
    icon?.classList.add('music-spinning');
    musicPlaying = true;
  }
}

/* ── 5. LIGHTBOX GALERI ─────────────────────────────────────── */
function openLightbox(src) {
  const lb  = document.getElementById('lightbox');
  const img = document.getElementById('lightbox-img');
  if (!lb || !img) return;
  img.src = src;
  lb.classList.add('active');
  document.body.style.overflow = 'hidden'; // Matikan scroll saat lightbox buka
}

function closeLightbox() {
  document.getElementById('lightbox')?.classList.remove('active');
  document.body.style.overflow = '';
}

// Tutup lightbox saat klik di luar gambar atau tekan Escape
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });

/* ── 6. COPY REKENING ───────────────────────────────────────── */
function copyText(text, btn) {
  navigator.clipboard.writeText(text).then(() => {
    const original = btn.innerHTML;
    btn.innerHTML  = '<i class="bi bi-check2"></i> Tersalin!';
    setTimeout(() => { btn.innerHTML = original; }, 2000);
  }).catch(() => {
    // Fallback untuk browser lama
    const el = document.createElement('textarea');
    el.value = text;
    document.body.appendChild(el);
    el.select();
    document.execCommand('copy');
    document.body.removeChild(el);
    alert('Tersalin: ' + text);
  });
}

/* ── 7. ATTENDANCE SELECTOR ─────────────────────────────────── */
// Jalankan setelah DOM siap
document.addEventListener('DOMContentLoaded', () => {
  // Pastikan radio attendance default sudah terstyle
  const defaultRadio = document.querySelector('.attendance-option input:checked');
  if (defaultRadio) {
    defaultRadio.closest('.attendance-option')
      .querySelector('.attendance-label')
      .style.cssText = 'background: var(--black); color: var(--white); border-color: var(--black);';
  }
});
