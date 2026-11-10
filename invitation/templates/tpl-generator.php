<?php
// ============================================================
// FILE: invitation/templates/tpl-generator.php
// Script helper untuk generate semua template
// Jalankan sekali: php tpl-generator.php
// ============================================================

$templates = [

// ──────────────────────────────────────────────────────────────
// TEMPLATE 1: Royal Garden — Gold Dark
// ──────────────────────────────────────────────────────────────
'tpl-royal-garden.php' => [
'css' => '
:root{--C1:#1A1200;--C2:#C9A84C;--C3:#FAF6EC;--C4:#8B6914;}
body{background:var(--C3);color:var(--C1);}
.sec{padding:80px 24px;} .sec-dark{background:var(--C1);color:var(--C3);}
.sec-gold{background:var(--C2);color:var(--C1);}
.con{max-width:680px;margin:0 auto;} .con-w{max-width:920px;margin:0 auto;}
.tag{font-size:10px;font-weight:500;letter-spacing:.3em;text-transform:uppercase;color:var(--C2);display:block;margin-bottom:12px;}
.sec-dark .tag{color:var(--C2);}
.ttl{font-family:"Cormorant Garamond",serif;font-size:clamp(32px,5vw,50px);font-weight:400;line-height:1.15;letter-spacing:-.01em;}
.div{width:60px;height:1px;background:var(--C2);margin:18px 0;}
.orn{color:var(--C2);font-size:20px;text-align:center;letter-spacing:8px;display:block;margin:12px 0;}
.btn-main{background:var(--C2);color:var(--C1);}
@import url("https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;1,300;1,400&family=Jost:wght@300;400;500&display=swap");
body{font-family:"Jost",sans-serif;}
',
'cover_bg' => 'background:var(--C1);',
'cover_ornament' => '<div style="position:absolute;inset:0;background:radial-gradient(ellipse at 50% 0%,rgba(201,168,76,0.15),transparent 60%);pointer-events:none;"></div>',
'names_style' => 'font-family:"Cormorant Garamond",serif;color:white;font-size:clamp(38px,8vw,64px);font-weight:400;',
'sep_style' => 'font-family:"Cormorant Garamond",serif;font-weight:300;font-style:italic;color:rgba(201,168,76,0.5);font-size:.5em;',
'btn_cover' => 'background:var(--C2);color:var(--C1);',
'hero_extra' => '<div style="position:absolute;inset:0;background:radial-gradient(ellipse at 50% 0%,rgba(201,168,76,0.12),transparent 60%);pointer-events:none;"></div>',
'accent_color' => '#C9A84C',
'body_font' => '"Jost",sans-serif',
'display_font' => '"Cormorant Garamond",serif',
'section_bg_2' => 'background:white;',
'section_bg_3' => 'background:var(--C3);',
],

// ──────────────────────────────────────────────────────────────
// TEMPLATE 3: Rustic Bohemian — Earth Tone
// ──────────────────────────────────────────────────────────────
'tpl-rustic-bohemian.php' => [
'css' => '
@import url("https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Nunito:wght@300;400;500&display=swap");
:root{--C1:#3D2B1F;--C2:#A67C5B;--C3:#FDF6EE;--C4:#6B4C3B;}
body{background:var(--C3);color:var(--C1);font-family:"Nunito",sans-serif;}
.sec{padding:80px 24px;} .sec-dark{background:var(--C1);color:var(--C3);}
.sec-warm{background:#F5E6D3;}
.con{max-width:680px;margin:0 auto;} .con-w{max-width:920px;margin:0 auto;}
.tag{font-size:10px;font-weight:600;letter-spacing:.25em;text-transform:uppercase;color:var(--C2);display:block;margin-bottom:12px;}
.ttl{font-family:"Libre Baskerville",serif;font-size:clamp(30px,5vw,46px);font-weight:400;line-height:1.2;}
.div{width:48px;height:2px;background:var(--C2);margin:16px 0;}
.btn-main{background:var(--C1);color:var(--C3);}
.orn{display:block;text-align:center;color:var(--C2);font-size:24px;margin:12px 0;}
',
'cover_bg' => 'background:var(--C1);',
'names_style' => 'font-family:"Libre Baskerville",serif;color:white;font-size:clamp(36px,8vw,60px);font-weight:400;',
'sep_style' => 'font-style:italic;color:rgba(166,124,91,0.6);font-size:.5em;',
'btn_cover' => 'background:var(--C2);color:var(--C1);',
'accent_color' => '#A67C5B',
'display_font' => '"Libre Baskerville",serif',
'body_font' => '"Nunito",sans-serif',
'section_bg_2' => 'background:white;',
'section_bg_3' => 'background:#F5E6D3;',
],

// ──────────────────────────────────────────────────────────────
// TEMPLATE 4: Luxury Gold — Dark Mewah
// ──────────────────────────────────────────────────────────────
'tpl-luxury-gold.php' => [
'css' => '
@import url("https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600&family=Raleway:wght@300;400;500&display=swap");
:root{--C1:#0D0D0D;--C2:#D4AF37;--C3:#1A1A1A;--C4:#888;}
body{background:var(--C1);color:white;font-family:"Raleway",sans-serif;}
.sec{padding:80px 24px;border-top:1px solid rgba(212,175,55,0.1);}
.sec-light{background:#0F0F0F;}
.con{max-width:680px;margin:0 auto;} .con-w{max-width:920px;margin:0 auto;}
.tag{font-size:9px;font-weight:500;letter-spacing:.4em;text-transform:uppercase;color:var(--C2);display:block;margin-bottom:14px;}
.ttl{font-family:"Cinzel",serif;font-size:clamp(28px,4vw,42px);font-weight:400;line-height:1.2;letter-spacing:.05em;}
.div{width:80px;height:1px;background:linear-gradient(90deg,transparent,var(--C2),transparent);margin:20px 0;}
.btn-main{background:linear-gradient(135deg,var(--C2),#9A7B2F);color:var(--C1);}
',
'cover_bg' => 'background:radial-gradient(ellipse at 50% 0%,rgba(212,175,55,0.12),#0D0D0D 60%);',
'names_style' => 'font-family:"Cinzel",serif;color:white;font-size:clamp(32px,7vw,56px);font-weight:400;letter-spacing:.08em;',
'sep_style' => 'color:rgba(212,175,55,0.4);font-size:.5em;letter-spacing:.3em;',
'btn_cover' => 'background:linear-gradient(135deg,#D4AF37,#9A7B2F);color:#0D0D0D;',
'accent_color' => '#D4AF37',
'display_font' => '"Cinzel",serif',
'body_font' => '"Raleway",sans-serif',
'section_bg_2' => 'background:#0F0F0F;',
'section_bg_3' => 'background:#111;',
],

// ──────────────────────────────────────────────────────────────
// TEMPLATE 5: Soft Floral — Pink Rose
// ──────────────────────────────────────────────────────────────
'tpl-soft-floral.php' => [
'css' => '
@import url("https://fonts.googleapis.com/css2?family=Gilda+Display&family=Lato:wght@300;400;500&display=swap");
:root{--C1:#2D1B1B;--C2:#C9746A;--C3:#FFF8F7;--C4:#E8A09A;}
body{background:var(--C3);color:var(--C1);font-family:"Lato",sans-serif;}
.sec{padding:80px 24px;} .sec-rose{background:#FDECEA;}
.sec-dark{background:#2D1B1B;color:var(--C3);}
.con{max-width:680px;margin:0 auto;} .con-w{max-width:920px;margin:0 auto;}
.tag{font-size:10px;font-weight:500;letter-spacing:.25em;text-transform:uppercase;color:var(--C2);display:block;margin-bottom:12px;}
.ttl{font-family:"Gilda Display",serif;font-size:clamp(30px,5vw,48px);font-weight:400;line-height:1.15;}
.div{width:48px;height:2px;background:var(--C2);margin:16px 0;}
.btn-main{background:var(--C2);color:white;}
.orn{display:block;text-align:center;font-size:22px;margin:10px 0;color:var(--C2);}
',
'cover_bg' => 'background:linear-gradient(160deg,#2D1B1B,#4A2525);',
'names_style' => 'font-family:"Gilda Display",serif;color:white;font-size:clamp(36px,8vw,62px);font-weight:400;',
'sep_style' => 'color:rgba(201,116,106,0.5);font-style:italic;font-size:.5em;',
'btn_cover' => 'background:var(--C2);color:white;',
'accent_color' => '#C9746A',
'display_font' => '"Gilda Display",serif',
'body_font' => '"Lato",sans-serif',
'section_bg_2' => 'background:white;',
'section_bg_3' => 'background:#FDECEA;',
],

// ──────────────────────────────────────────────────────────────
// TEMPLATE 6: Tropical Paradise — Fresh Green
// ──────────────────────────────────────────────────────────────
'tpl-tropical-paradise.php' => [
'css' => '
@import url("https://fonts.googleapis.com/css2?family=Tenor+Sans&family=Poppins:wght@300;400;500&display=swap");
:root{--C1:#1A2E1A;--C2:#4A8C5C;--C3:#F2FAF3;--C4:#7BBF8A;}
body{background:var(--C3);color:var(--C1);font-family:"Poppins",sans-serif;}
.sec{padding:80px 24px;} .sec-green{background:#E8F5EB;}
.sec-dark{background:var(--C1);color:var(--C3);}
.con{max-width:680px;margin:0 auto;} .con-w{max-width:920px;margin:0 auto;}
.tag{font-size:10px;font-weight:500;letter-spacing:.25em;text-transform:uppercase;color:var(--C2);display:block;margin-bottom:12px;}
.ttl{font-family:"Tenor Sans",serif;font-size:clamp(30px,5vw,46px);font-weight:400;line-height:1.15;letter-spacing:.02em;}
.div{width:48px;height:2px;background:var(--C2);margin:16px 0;}
.btn-main{background:var(--C1);color:white;}
',
'cover_bg' => 'background:linear-gradient(160deg,#1A2E1A,#2D4A2D);',
'names_style' => 'font-family:"Tenor Sans",serif;color:white;font-size:clamp(36px,8vw,62px);font-weight:400;',
'sep_style' => 'color:rgba(74,140,92,0.5);font-size:.5em;',
'btn_cover' => 'background:var(--C2);color:white;',
'accent_color' => '#4A8C5C',
'display_font' => '"Tenor Sans",serif',
'body_font' => '"Poppins",sans-serif',
'section_bg_2' => 'background:white;',
'section_bg_3' => 'background:#E8F5EB;',
],

// ──────────────────────────────────────────────────────────────
// TEMPLATE 7: Vintage Classic — Sepia
// ──────────────────────────────────────────────────────────────
'tpl-vintage-classic.php' => [
'css' => '
@import url("https://fonts.googleapis.com/css2?family=IM+Fell+English:ital@0;1&family=Crimson+Text:ital,wght@0,400;0,600;1,400&display=swap");
:root{--C1:#2C1810;--C2:#8B5E3C;--C3:#FBF5EC;--C4:#C4956A;}
body{background:var(--C3);color:var(--C1);font-family:"Crimson Text",serif;}
.sec{padding:80px 24px;} .sec-warm{background:#F5ECD8;}
.sec-dark{background:var(--C1);color:var(--C3);}
.con{max-width:680px;margin:0 auto;} .con-w{max-width:920px;margin:0 auto;}
.tag{font-size:10px;font-weight:400;letter-spacing:.3em;text-transform:uppercase;color:var(--C2);display:block;margin-bottom:12px;font-family:"Crimson Text",serif;}
.ttl{font-family:"IM Fell English",serif;font-size:clamp(30px,5vw,46px);font-weight:400;line-height:1.2;}
.div{width:80px;height:1px;background:var(--C2);margin:18px 0;opacity:.5;}
.btn-main{background:var(--C1);color:var(--C3);font-family:"Crimson Text",serif;font-size:14px;}
',
'cover_bg' => 'background:linear-gradient(160deg,#2C1810,#4A2C1A);',
'names_style' => 'font-family:"IM Fell English",serif;color:white;font-size:clamp(36px,8vw,60px);',
'sep_style' => 'font-style:italic;color:rgba(196,149,106,0.5);font-size:.5em;',
'btn_cover' => 'background:var(--C4);color:var(--C1);',
'accent_color' => '#8B5E3C',
'display_font' => '"IM Fell English",serif',
'body_font' => '"Crimson Text",serif',
'section_bg_2' => 'background:white;',
'section_bg_3' => 'background:#F5ECD8;',
],

// ──────────────────────────────────────────────────────────────
// TEMPLATE 8: Pastel Dream — Warna Warni Lembut
// ──────────────────────────────────────────────────────────────
'tpl-pastel-dream.php' => [
'css' => '
@import url("https://fonts.googleapis.com/css2?family=Kaisei+Opti:wght@400;700&family=Quicksand:wght@400;500;600&display=swap");
:root{--C1:#3D3453;--C2:#B07FD4;--C3:#FBF8FF;--C4:#F9B8D4;}
body{background:var(--C3);color:var(--C1);font-family:"Quicksand",sans-serif;}
.sec{padding:80px 24px;} .sec-lavender{background:#F3EEFF;}
.sec-dark{background:var(--C1);color:white;}
.con{max-width:680px;margin:0 auto;} .con-w{max-width:920px;margin:0 auto;}
.tag{font-size:10px;font-weight:600;letter-spacing:.25em;text-transform:uppercase;color:var(--C2);display:block;margin-bottom:12px;}
.ttl{font-family:"Kaisei Opti",serif;font-size:clamp(30px,5vw,46px);font-weight:400;line-height:1.2;}
.div{width:48px;height:2px;background:linear-gradient(90deg,var(--C2),var(--C4));margin:16px 0;}
.btn-main{background:linear-gradient(135deg,var(--C2),var(--C4));color:white;}
',
'cover_bg' => 'background:linear-gradient(160deg,#3D3453,#5A4A6E);',
'names_style' => 'font-family:"Kaisei Opti",serif;color:white;font-size:clamp(36px,8vw,60px);',
'sep_style' => 'color:rgba(176,127,212,0.4);font-style:italic;font-size:.5em;',
'btn_cover' => 'background:linear-gradient(135deg,var(--C2),var(--C4));color:white;',
'accent_color' => '#B07FD4',
'display_font' => '"Kaisei Opti",serif',
'body_font' => '"Quicksand",sans-serif',
'section_bg_2' => 'background:white;',
'section_bg_3' => 'background:#F3EEFF;',
],

// ──────────────────────────────────────────────────────────────
// TEMPLATE 9: Royal Blue — Biru Megah
// ──────────────────────────────────────────────────────────────
'tpl-royal-blue.php' => [
'css' => '
@import url("https://fonts.googleapis.com/css2?family=Josefin+Serif:ital,wght@0,300;0,400;1,300;1,400&family=Josefin+Sans:wght@300;400;500&display=swap");
:root{--C1:#0D1B3E;--C2:#4169C8;--C3:#F5F7FF;--C4:#C0CBEF;}
body{background:var(--C3);color:var(--C1);font-family:"Josefin Sans",sans-serif;}
.sec{padding:80px 24px;} .sec-blue{background:#EBF0FF;}
.sec-dark{background:var(--C1);color:white;}
.con{max-width:680px;margin:0 auto;} .con-w{max-width:920px;margin:0 auto;}
.tag{font-size:10px;font-weight:400;letter-spacing:.3em;text-transform:uppercase;color:var(--C2);display:block;margin-bottom:12px;}
.ttl{font-family:"Josefin Serif",serif;font-size:clamp(30px,5vw,46px);font-weight:300;line-height:1.15;letter-spacing:.03em;}
.div{width:48px;height:2px;background:var(--C2);margin:16px 0;}
.btn-main{background:var(--C1);color:white;}
',
'cover_bg' => 'background:linear-gradient(160deg,#0D1B3E,#1A2E6B);',
'names_style' => 'font-family:"Josefin Serif",serif;color:white;font-size:clamp(36px,8vw,60px);font-weight:300;letter-spacing:.04em;',
'sep_style' => 'color:rgba(65,105,200,0.4);font-style:italic;font-size:.5em;',
'btn_cover' => 'background:var(--C2);color:white;',
'accent_color' => '#4169C8',
'display_font' => '"Josefin Serif",serif',
'body_font' => '"Josefin Sans",sans-serif',
'section_bg_2' => 'background:white;',
'section_bg_3' => 'background:#EBF0FF;',
],

// ──────────────────────────────────────────────────────────────
// TEMPLATE 10: Midnight Garden — Dark Green
// ──────────────────────────────────────────────────────────────
'tpl-midnight-garden.php' => [
'css' => '
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
',
'cover_bg' => 'background:radial-gradient(ellipse at 50% 0%,rgba(77,184,122,.1),#071210 60%);background-color:#071210;',
'names_style' => 'font-family:"Rozha One",serif;color:#E8F5E9;font-size:clamp(36px,8vw,60px);',
'sep_style' => 'color:rgba(77,184,122,.3);font-size:.5em;',
'btn_cover' => 'background:var(--C2);color:var(--C1);',
'accent_color' => '#4DB87A',
'display_font' => '"Rozha One",serif',
'body_font' => '"Hind",sans-serif',
'section_bg_2' => 'background:var(--C3);',
'section_bg_3' => 'background:#0A1A17;',
],

// ──────────────────────────────────────────────────────────────
// TEMPLATE 11: Sakura Blossom — Jepang Pink
// ──────────────────────────────────────────────────────────────
'tpl-sakura-blossom.php' => [
'css' => '
@import url("https://fonts.googleapis.com/css2?family=Noto+Serif:ital,wght@0,400;0,700;1,400&family=Noto+Sans:wght@300;400;500&display=swap");
:root{--C1:#2D1B2D;--C2:#E8759A;--C3:#FFF5F8;--C4:#F4B8CE;}
body{background:var(--C3);color:var(--C1);font-family:"Noto Sans",sans-serif;}
.sec{padding:80px 24px;} .sec-pink{background:#FFECF2;}
.sec-dark{background:var(--C1);color:white;}
.con{max-width:680px;margin:0 auto;} .con-w{max-width:920px;margin:0 auto;}
.tag{font-size:10px;font-weight:500;letter-spacing:.25em;text-transform:uppercase;color:var(--C2);display:block;margin-bottom:12px;}
.ttl{font-family:"Noto Serif",serif;font-size:clamp(30px,5vw,46px);font-weight:400;line-height:1.15;}
.div{width:48px;height:2px;background:var(--C2);margin:16px 0;}
.btn-main{background:var(--C2);color:white;}
',
'cover_bg' => 'background:linear-gradient(160deg,#2D1B2D,#4A2040);',
'names_style' => 'font-family:"Noto Serif",serif;color:white;font-size:clamp(36px,8vw,60px);',
'sep_style' => 'color:rgba(232,117,154,.4);font-style:italic;font-size:.5em;',
'btn_cover' => 'background:var(--C2);color:white;',
'accent_color' => '#E8759A',
'display_font' => '"Noto Serif",serif',
'body_font' => '"Noto Sans",sans-serif',
'section_bg_2' => 'background:white;',
'section_bg_3' => 'background:#FFECF2;',
],

// ──────────────────────────────────────────────────────────────
// TEMPLATE 12: Desert Rose — Terrakota Hangat
// ──────────────────────────────────────────────────────────────
'tpl-desert-rose.php' => [
'css' => '
@import url("https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,300;0,400;1,300;1,400&family=Plus+Jakarta+Sans:wght@300;400;500&display=swap");
:root{--C1:#2C1A10;--C2:#C4623A;--C3:#FDF6EE;--C4:#E8A882;}
body{background:var(--C3);color:var(--C1);font-family:"Plus Jakarta Sans",sans-serif;}
.sec{padding:80px 24px;} .sec-warm{background:#FBE8D8;}
.sec-dark{background:var(--C1);color:#FDF6EE;}
.con{max-width:680px;margin:0 auto;} .con-w{max-width:920px;margin:0 auto;}
.tag{font-size:10px;font-weight:500;letter-spacing:.25em;text-transform:uppercase;color:var(--C2);display:block;margin-bottom:12px;}
.ttl{font-family:"Fraunces",serif;font-size:clamp(30px,5vw,46px);font-weight:300;line-height:1.15;}
.div{width:48px;height:2px;background:var(--C2);margin:16px 0;}
.btn-main{background:var(--C2);color:white;}
',
'cover_bg' => 'background:linear-gradient(160deg,#2C1A10,#4A2C1A);',
'names_style' => 'font-family:"Fraunces",serif;color:white;font-size:clamp(36px,8vw,60px);font-weight:300;',
'sep_style' => 'color:rgba(196,98,58,.4);font-style:italic;font-size:.5em;',
'btn_cover' => 'background:var(--C2);color:white;',
'accent_color' => '#C4623A',
'display_font' => '"Fraunces",serif',
'body_font' => '"Plus Jakarta Sans",sans-serif',
'section_bg_2' => 'background:white;',
'section_bg_3' => 'background:#FBE8D8;',
],

// ──────────────────────────────────────────────────────────────
// TEMPLATE 13: Emerald Luxury — Hijau Emas Glamor
// ──────────────────────────────────────────────────────────────
'tpl-emerald-luxury.php' => [
'css' => '
@import url("https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,wght@0,400;0,500;1,400&family=Montserrat:wght@300;400;500&display=swap");
:root{--C1:#061A10;--C2:#B8975A;--C3:#0A2016;--C4:#1A4030;}
body{background:var(--C1);color:#F0F7F2;font-family:"Montserrat",sans-serif;}
.sec{padding:80px 24px;border-top:1px solid rgba(184,151,90,.08);}
.sec-alt{background:var(--C3);}
.con{max-width:680px;margin:0 auto;} .con-w{max-width:920px;margin:0 auto;}
.tag{font-size:9px;font-weight:500;letter-spacing:.35em;text-transform:uppercase;color:var(--C2);display:block;margin-bottom:14px;}
.ttl{font-family:"Bodoni Moda",serif;font-size:clamp(30px,5vw,46px);font-weight:400;line-height:1.15;letter-spacing:.01em;}
.div{width:80px;height:1px;background:linear-gradient(90deg,transparent,var(--C2),transparent);margin:20px 0;}
.btn-main{background:linear-gradient(135deg,var(--C2),#8A6A30);color:var(--C1);}
',
'cover_bg' => 'background:radial-gradient(ellipse at 50% 0%,rgba(184,151,90,.12),#061A10 60%);background-color:#061A10;',
'names_style' => 'font-family:"Bodoni Moda",serif;color:#F0F7F2;font-size:clamp(36px,8vw,60px);',
'sep_style' => 'color:rgba(184,151,90,.3);font-size:.5em;',
'btn_cover' => 'background:linear-gradient(135deg,var(--C2),#8A6A30);color:#061A10;',
'accent_color' => '#B8975A',
'display_font' => '"Bodoni Moda",serif',
'body_font' => '"Montserrat",sans-serif',
'section_bg_2' => 'background:var(--C3);',
'section_bg_3' => 'background:var(--C4);',
],

]; // end $templates array

// ── Generate setiap file template ────────────────────────────
foreach ($templates as $filename => $t) {

$content = <<<PHPEOF
<?php
// ============================================================
// FILE: invitation/templates/{$filename}
// Auto-generated template
// ============================================================
include_once __DIR__ . '/_partials.php';
?>
<!DOCTYPE html><html lang="id">
<head>
<meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Undangan <?= \$couple ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/invitation.css">
<style>
{$t['css']}
</style>
</head>
<body>

<!-- COVER -->
<div id="cover-screen" style="{$t['cover_bg']}position:fixed;inset:0;z-index:1000;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:24px;transition:opacity .8s ease,transform .8s ease;">
  {$t['cover_ornament']}
  <div class="cover-frame" style="border:1px solid rgba(255,255,255,.12);padding:44px 36px;text-align:center;max-width:360px;width:100%;position:relative;">
    <span class="corner-br"></span><span class="corner-tl"></span>
    <p class="tag" style="margin-bottom:28px;">Undangan Pernikahan</p>
    <div style="{$t['names_style']}line-height:1.05;"><?= clean(\$wedding['groom_name']) ?></div>
    <div style="{$t['sep_style']}display:block;margin:8px 0;">dan</div>
    <div style="{$t['names_style']}line-height:1.05;margin-bottom:24px;"><?= clean(\$wedding['bride_name']) ?></div>
    <div style="width:28px;height:1px;background:rgba(255,255,255,.15);margin:0 auto 18px;"></div>
    <p style="font-size:12px;color:rgba(255,255,255,.35);margin-bottom:28px;letter-spacing:.05em;"><?= date('d F Y',strtotime(\$event_date)) ?></p>
    <button onclick="openInvitation()" style="{$t['btn_cover']}width:100%;padding:13px;font-size:11px;font-weight:600;letter-spacing:.15em;text-transform:uppercase;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;">
      <i class="bi bi-envelope-open"></i> Buka Undangan
    </button>
  </div>
</div>

<div id="main-content" style="display:none;">
  <?php if(!empty(\$wedding['music_url'])): ?><audio id="bg-music" loop><source src="<?= BASE_URL?>/<?= clean(\$wedding['music_url'])?>" type="audio/mpeg"></audio><?php endif; ?>
  <button id="music-btn" onclick="toggleMusic()"><i class="bi bi-music-note"></i></button>

  <!-- HERO -->
  <section class="sec sec-dark" style="min-height:100vh;display:flex;align-items:center;justify-content:center;text-align:center;position:relative;overflow:hidden;">
    <?php if(!empty(\$wedding['couple_photo'])): ?>
    <div style="position:absolute;inset:0;"><img src="<?= BASE_URL?>/<?= clean(\$wedding['couple_photo'])?>" style="width:100%;height:100%;object-fit:cover;opacity:.15;filter:grayscale(40%);"></div>
    <?php endif; ?>
    <div class="con reveal" style="position:relative;z-index:1;">
      <span class="tag reveal" style="margin-bottom:16px;">Bismillahirrahmanirrahim</span>
      <div style="{$t['names_style']}line-height:1.05;" class="reveal reveal-delay-1"><?= clean(\$wedding['groom_name']) ?>
        <div style="{$t['sep_style']}display:block;margin:6px 0;">&amp;</div>
        <?= clean(\$wedding['bride_name']) ?>
      </div>
      <div class="div reveal reveal-delay-2" style="margin:20px auto;opacity:.3;"></div>
      <p class="reveal reveal-delay-3" style="font-size:12px;opacity:.4;letter-spacing:.1em;"><?= date('d F Y',strtotime(\$event_date)) ?><?php if(!empty(\$wedding['reception_venue'])): ?> · <?= clean(\$wedding['reception_venue'])?><?php endif;?></p>
    </div>
  </section>

  <!-- PEMBUKAAN -->
  <section class="sec" style="text-align:center;{$t['section_bg_2']}">
    <div class="con">
      <span class="tag reveal">Dengan Rahmat Allah SWT</span>
      <h2 class="ttl reveal reveal-delay-1">Kami mengundang<br>kehadiran Anda</h2>
      <div class="div reveal reveal-delay-2"></div>
      <p class="reveal reveal-delay-3" style="font-size:14px;opacity:.6;line-height:1.9;max-width:460px;margin:0 auto;">Merupakan suatu kebahagiaan apabila Bapak / Ibu / Saudara / i berkenan hadir dan memberikan doa restu.</p>
    </div>
  </section>

  <!-- MEMPELAI -->
  <section class="sec" style="{$t['section_bg_3']}">
    <div class="con-w">
      <div style="text-align:center;"><span class="tag reveal">Kedua Mempelai</span><h2 class="ttl reveal reveal-delay-1">Yang Berbahagia</h2></div>
      <div style="display:grid;grid-template-columns:1fr auto 1fr;gap:24px;align-items:center;margin-top:40px;">
        <?php foreach([['groom','Pria'],['bride','Wanita']] as [\$k,\$role]): ?>
        <div style="text-align:center;" class="reveal">
          <?php if(!empty(\$wedding[\$k.'_photo'])): ?><img src="<?= BASE_URL?>/<?= clean(\$wedding[\$k.'_photo'])?>" style="width:130px;height:165px;object-fit:cover;margin:0 auto 16px;">
          <?php else: ?><div style="width:130px;height:165px;background:rgba(128,128,128,.1);margin:0 auto 16px;display:flex;align-items:center;justify-content:center;font-size:40px;opacity:.3;"><i class="bi bi-person"></i></div><?php endif; ?>
          <p class="tag" style="margin-bottom:6px;">Mempelai <?= \$role ?></p>
          <h3 style="font-family:var(--display-font,inherit);font-size:24px;font-weight:400;"><?= clean(\$wedding[\$k.'_name']) ?></h3>
          <?php if(!empty(\$wedding[\$k.'_full_name'])): ?><p style="font-size:13px;opacity:.5;margin-top:4px;"><?= clean(\$wedding[\$k.'_full_name'])?></p><?php endif; ?>
          <?php if(!empty(\$wedding[\$k.'_father'])): ?><p style="font-size:12px;opacity:.4;margin-top:10px;line-height:1.8;">Putr<?= \$role==='Pria'?'a':'i'?> dari<br><?= clean(\$wedding[\$k.'_father'])?><?php if(!empty(\$wedding[\$k.'_mother'])): ?> &amp; <?= clean(\$wedding[\$k.'_mother'])?><?php endif;?></p><?php endif; ?>
        </div>
        <?php if(\$k==='groom'): ?><div style="font-size:44px;font-weight:300;font-style:italic;opacity:.2;text-align:center;" class="reveal reveal-delay-1">&amp;</div><?php endif; ?>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- COUNTDOWN -->
  <section class="sec sec-dark" style="text-align:center;">
    <div class="con">
      <span class="tag reveal">Menuju Hari Istimewa</span>
      <h2 class="ttl reveal reveal-delay-1"><?= date('d F Y',strtotime(\$event_date)) ?></h2>
      <?php render_countdown(\$event_date,\$event_time); ?>
    </div>
  </section>

  <!-- ACARA -->
  <section class="sec" style="{$t['section_bg_2']}">
    <div class="con-w">
      <div style="text-align:center;"><span class="tag reveal">Rangkaian Acara</span><h2 class="ttl reveal reveal-delay-1">Waktu &amp; Tempat</h2><div class="div reveal" style="margin:14px auto;"></div></div>
      <div style="display:grid;grid-template-columns:<?= !empty(\$wedding['akad_date'])?'1fr 1fr':'1fr'?>;gap:1px;background:rgba(128,128,128,.15);margin-top:40px;">
        <?php foreach([['akad','Akad Nikah','Ijab Qabul'],['reception','Resepsi','Walimatul Ursy']] as [\$key,\$type,\$name]):
          if(empty(\$wedding[\$key.'_date'])) continue; ?>
        <div style="padding:28px;background:inherit;" class="reveal">
          <p class="tag" style="margin-bottom:12px;"><?= \$type ?></p>
          <h3 style="font-family:{$t['display_font']};font-size:22px;font-weight:400;margin-bottom:16px;"><?= \$name ?></h3>
          <?php \$items=[['bi-calendar3',date('l, d F Y',strtotime(\$wedding[\$key.'_date']))],['bi-clock',(!empty(\$wedding[\$key.'_time'])?date('H.i',strtotime(\$wedding[\$key.'_time'])).' WIB':'')],['bi-geo-alt',(\$wedding[\$key.'_venue']??'')],['bi-pin-map',(\$wedding[\$key.'_address']??'')]];
          foreach(\$items as [\$ico,\$val]): if(empty(\$val)) continue; ?>
          <div style="display:flex;align-items:flex-start;gap:9px;margin-bottom:9px;font-size:13px;opacity:.7;">
            <i class="bi <?= \$ico ?>" style="color:{$t['accent_color']};margin-top:1px;flex-shrink:0;"></i><span><?= clean(\$val)?></span>
          </div>
          <?php endforeach; ?>
          <?php if(!empty(\$wedding[\$key.'_maps_url'])): ?><a href="<?= \$wedding[\$key.'_maps_url']?>" target="_blank" style="display:inline-flex;align-items:center;gap:6px;margin-top:12px;padding:7px 14px;border:1px solid {$t['accent_color']};font-size:11px;font-weight:500;letter-spacing:.1em;text-transform:uppercase;color:{$t['accent_color']};text-decoration:none;"><i class="bi bi-map"></i> Lihat Peta</a><?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- LOVE STORY -->
  <?php if(!empty(\$wedding['love_story'])): ?>
  <section class="sec" style="{$t['section_bg_3']}">
    <div class="con"><span class="tag reveal">Kisah Kami</span><h2 class="ttl reveal reveal-delay-1">Our Love Story</h2><div class="div reveal reveal-delay-2"></div>
    <p class="reveal reveal-delay-3" style="font-family:{$t['display_font']};font-size:clamp(16px,2.5vw,20px);font-weight:300;font-style:italic;line-height:1.8;opacity:.65;border-left:2px solid {$t['accent_color']};padding-left:20px;margin-top:24px;"><?= nl2br(clean(\$wedding['love_story']))?></p></div>
  </section>
  <?php endif; ?>

  <!-- GALERI -->
  <?php if(!empty(\$gallery)): ?>
  <section class="sec" style="{$t['section_bg_2']}">
    <div class="con-w"><div style="text-align:center;"><span class="tag reveal">Momen Kami</span><h2 class="ttl reveal reveal-delay-1">Galeri Foto</h2><div class="div reveal reveal-delay-2" style="margin:14px auto 32px;"></div></div>
    <?php render_gallery(\$gallery); ?></div>
  </section>
  <?php endif; ?>

  <!-- RSVP -->
  <section class="sec" style="{$t['section_bg_3']}">
    <div class="con"><span class="tag reveal">Konfirmasi Kehadiran</span><h2 class="ttl reveal reveal-delay-1">RSVP</h2><div class="div reveal reveal-delay-2"></div>
    <div class="reveal" style="margin-top:24px;"><?php render_rsvp(\$wedding,\$slug,\$form_status,\$rsvp_stat,'btn-main'); ?></div></div>
  </section>

  <!-- UCAPAN -->
  <section class="sec" style="{$t['section_bg_2']}">
    <div class="con"><span class="tag reveal">Dari Para Tamu</span><h2 class="ttl reveal reveal-delay-1">Ucapan &amp; Doa</h2><div class="div reveal reveal-delay-2"></div>
    <div class="reveal" style="margin-top:24px;"><?php render_messages(\$wedding,\$slug,\$form_status,\$messages,'btn-main'); ?></div></div>
  </section>

  <!-- GIFT -->
  <?php if(!empty(\$wedding['qris_image'])||!empty(\$wedding['bank_account_number'])): ?>
  <section class="sec sec-dark" style="text-align:center;">
    <div class="con"><span class="tag reveal">Hadiah Pernikahan</span><h2 class="ttl reveal reveal-delay-1">Wedding Gift</h2><div class="div reveal" style="margin:14px auto;"></div>
    <p class="reveal" style="font-size:13px;opacity:.4;margin-bottom:28px;">Doa restu Anda adalah hadiah terbaik bagi kami.</p>
    <div class="reveal"><?php render_gift(\$wedding); ?></div></div>
  </section>
  <?php endif; ?>

  <!-- PENUTUP -->
  <section class="sec" style="text-align:center;{$t['section_bg_2']}">
    <div class="con"><span class="tag reveal">Penutup</span><h2 class="ttl reveal reveal-delay-1">Terima Kasih</h2><div class="div reveal" style="margin:14px auto;"></div>
    <p class="reveal" style="font-size:14px;opacity:.55;max-width:400px;margin:0 auto 36px;line-height:1.9;">Atas kehadiran dan doa restu Bapak / Ibu / Saudara / i, kami ucapkan terima kasih.</p>
    <div class="reveal" style="font-family:{$t['display_font']};font-size:clamp(28px,6vw,46px);font-weight:400;margin-bottom:28px;"><?= clean(\$wedding['groom_name']) ?> &amp; <?= clean(\$wedding['bride_name']) ?></div>
    <a href="<?= BASE_URL ?>" style="font-size:11px;font-weight:500;letter-spacing:.12em;text-transform:uppercase;opacity:.4;border-bottom:1px solid currentColor;padding-bottom:2px;">← Kembali</a></div>
  </section>
</div>

<div id="lightbox" onclick="closeLightbox()"><button id="lightbox-close" onclick="closeLightbox()"><i class="bi bi-x-lg"></i></button><img id="lightbox-img" src="" alt="Foto"></div>
</body></html>
PHPEOF;

    // Tulis file
    file_put_contents(__DIR__ . '/' . $filename, $content);
    echo "✅ Generated: $filename\n";
}

echo "\n✅ Semua template berhasil dibuat!\n";
echo "Akses: http://localhost/wedding-invitation/invitation/templates/tpl-generator.php\n";
echo "⚠️ Hapus file ini setelah selesai!\n";