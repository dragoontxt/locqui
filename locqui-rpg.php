<?php
/**
 * locqui.amor-omnia.org — Locqui.rpg variant
 * A retro-JRPG / Pretty Soldier Sailor Moon "Another Story"-inspired
 * reskin of locqui. Drop this in as index.php on the locqui subdomain.
 *
 * Interactivity layer: Alpine.js (CDN) — a small reactive layer that sits
 * next to PHP. PHP still owns the data (the $posts array below); Alpine
 * handles command-menu switching, the save-data (archive) grouping, and
 * the auto-placed world map without a page reload or a build step.
 */

// --- example post source: swap this for a DB/flat-file loop later ---
// 'tags' drives the World Map: each unique tag gets a fixed anchor point,
// and a post's map position is the average of its tags' anchors (plus a
// tiny deterministic jitter). Add/change tags and the map re-lays itself
// out automatically — no manual x/y, no coordinate-picking. See the
// "Help" command in the nav for the full how-to.
//
// 'curated' + 'note' feed the Item Bag — posts you hand-pick yourself,
// independent of the tag system.
$posts = [
    [
        'slug'    => 'entry-001',
        'title'   => 'entry 001',
        'date'    => '2026-07-01',
        'display_date' => 'today, probably 2am',
        'excerpt' => 'This is where the yapping begins. Replace this array with real posts, or wire it up to a database.',
        'tags'    => ['diary', 'late-night'],
        'curated' => true,
        'note'    => 'the one that started it',
    ],
    [
        'slug'    => 'entry-002',
        'title'   => 'entry 002',
        'date'    => '2026-06-14',
        'display_date' => 'some other cursed hour',
        'excerpt' => 'Another placeholder entry so you can see how the list stacks up.',
        'tags'    => ['rant', 'internet'],
        'curated' => false,
        'note'    => '',
    ],
    [
        'slug'    => 'entry-003',
        'title'   => 'entry 003',
        'date'    => '2026-05-02',
        'display_date' => 'whenever the static clears',
        'excerpt' => 'A third card to prove the layout holds up under real content.',
        'tags'    => ['diary', 'reflection'],
        'curated' => true,
        'note'    => 'reread this one sometimes',
    ],
    [
        'slug'    => 'entry-004',
        'title'   => 'entry 004',
        'date'    => '2025-12-19',
        'display_date' => 'last december, unsent',
        'excerpt' => 'Older entry for the archive tab, so grouping-by-year has something to group.',
        'tags'    => ['throwback', 'diary'],
        'curated' => false,
        'note'    => '',
    ],
];

$posts_json = json_encode($posts, JSON_UNESCAPED_SLASHES);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pretty Soldier: Locqui</title>
<link rel="icon" href="sailormoon-icon.ico">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Pinyon+Script&family=Cinzel:wght@600;700&family=EB+Garamond:wght@400;600&family=Press+Start+2P&display=swap" rel="stylesheet">
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.5/cdn.min.js"></script>
<!-- Fengari: a Lua 5.3 VM compiled to JS. Runs a tiny Lua script that
     generates the rotating battle-message line under the title — a
     second language alongside PHP/JS, running client-side. -->
<script src="https://cdn.jsdelivr.net/npm/fengari-web@0.1.4/dist/fengari-web.js"></script>
<script type="application/lua">
local js = require "js"

local captions = {
  "a wild blog post appeared!",
  "press any key to continue yapping",
  "your party gained 12 exp in overthinking",
  "the archive awaits, soldier",
  "critical thought landed!",
  "save your progress before you spiral",
  "a new quest has been added to your log",
  "reception... static... locqui.rpg loading",
}

math.randomseed(os.time())

function js.global.rollCaption()
  return captions[math.random(#captions)]
end

js.global.LUA_CAPTION = js.global.rollCaption()
</script>
<style>
  :root {
    --ring-dark: #2f3b28;
    --ring-mid: #4a5d3a;
    --ring-mid2: #6b8f4e;
    --ring-light: #cbb6e0;
    --neon: #ff6f91;
    --blood: #7a2333;
    --ink: #14141f;
    --paper: #f5ead0;
    --panel: #232136;
    --panel-hover: #2c2a45;
    --panel-line: #4a4470;
    --muted: #8f8ab0;
    --text: #d8d3e8;
    --gold: #c9a227;
  }

  * { box-sizing: border-box; image-rendering: pixelated; image-rendering: crisp-edges; }
  img, svg.pixelate { image-rendering: pixelated; }

  body {
    margin: 0;
    background:
      linear-gradient(rgba(45,90,30,0.10) 1px, transparent 1px) 0 0/4px 4px,
      linear-gradient(90deg, rgba(45,90,30,0.10) 1px, transparent 1px) 0 0/4px 4px,
      var(--ink);
    color: var(--text);
    font-family: 'EB Garamond', serif;
    min-height: 100vh;
  }

  a { color: inherit; }

  /* ================= HEADER: Pretty Soldier: Locqui ================= */
  .hero {
    position: relative;
    overflow: hidden;
    padding: 30px 20px 34px;
    text-align: center;
    background: var(--ink);
    border-bottom: 4px solid var(--gold);
  }

  .hero .scan {
    position: absolute;
    inset: 0;
    z-index: 0;
    background: radial-gradient(ellipse at 50% -10%, rgba(255,111,145,0.25), transparent 55%),
                linear-gradient(160deg, var(--ink) 0%, var(--panel) 45%, var(--ring-dark) 100%);
    background-size: 100% 100%, 300% 300%;
    animation: drift-bg 20s ease infinite;
  }
  .hero .stars {
    position: absolute;
    inset: 0;
    z-index: 0;
    background-image:
      radial-gradient(1px 1px at 10% 20%, var(--paper) 100%, transparent),
      radial-gradient(1px 1px at 80% 30%, var(--paper) 100%, transparent),
      radial-gradient(1px 1px at 40% 10%, var(--gold) 100%, transparent),
      radial-gradient(1px 1px at 65% 60%, var(--paper) 100%, transparent),
      radial-gradient(1px 1px at 25% 70%, var(--gold) 100%, transparent),
      radial-gradient(1px 1px at 90% 80%, var(--paper) 100%, transparent);
    opacity: 0.6;
    animation: twinkle 3s ease-in-out infinite alternate;
  }
  @keyframes twinkle { from { opacity: 0.35; } to { opacity: 0.75; } }
  @keyframes drift-bg { 0% { background-position: 50% 0%, 0% 50%; } 50% { background-position: 50% 0%, 100% 50%; } 100% { background-position: 50% 0%, 0% 50%; } }

  .hero::after {
    content: "";
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at center, transparent 35%, var(--ink) 95%);
    z-index: 1;
  }

  .hero-content { position: relative; z-index: 2; }

  .hero .kicker {
    font-family: 'Cinzel', serif;
    font-weight: 600;
    font-size: clamp(0.7rem, 1.6vw, 0.95rem);
    letter-spacing: 0.35em;
    text-transform: uppercase;
    color: var(--gold);
    margin: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }
  .kicker-icon {
    width: 18px;
    height: 18px;
    image-rendering: pixelated;
  }

  .hero .site-title {
    font-family: 'Pinyon Script', cursive;
    font-weight: 400;
    font-size: clamp(3.2rem, 11vw, 6rem);
    letter-spacing: 0.01em;
    margin: 4px 0 0;
    line-height: 1.35;
    padding-bottom: 0.15em;
    background: linear-gradient(100deg, var(--gold) 0%, var(--paper) 18%, var(--neon) 36%, var(--paper) 54%, var(--gold) 72%, var(--neon) 100%);
    background-size: 300% auto;
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    filter: drop-shadow(0 0 10px rgba(255,111,145,0.5));
    animation: shine 5s linear infinite;
  }
  @keyframes shine { to { background-position: -300% center; } }

  .hero .subtitle {
    font-family: 'Cinzel', serif;
    font-weight: 700;
    font-size: clamp(0.85rem, 2vw, 1.15rem);
    letter-spacing: 0.28em;
    text-transform: uppercase;
    color: var(--paper);
    margin: 22px 0 0;
  }

  .hero .caption {
    font-family: 'Press Start 2P', monospace;
    font-size: 0.55rem;
    letter-spacing: 0.03em;
    line-height: 1.8;
    color: var(--ring-light);
    opacity: 0.9;
    margin: 18px 0 0;
    min-height: 1.2em;
    cursor: pointer;
  }
  .hero .caption::before { content: "\25B8 "; color: var(--gold); }

  /* ================= BOTTOM JRPG COMMAND MENU ================= */
  .command-bar {
    position: fixed;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 30;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
    gap: 4px;
    background: var(--panel);
    border-top: 4px solid var(--gold);
    box-shadow: 0 -8px 24px rgba(0,0,0,0.5);
    padding: 10px 10px;
  }
  .nav-icon {
    width: 22px;
    height: 22px;
    image-rendering: pixelated;
    margin-right: 6px;
  }
  .command-bar button {
    display: flex;
    align-items: center;
    gap: 6px;
    background: transparent;
    border: 2px solid transparent;
    color: var(--paper);
    font-family: 'Press Start 2P', monospace;
    font-size: 0.55rem;
    letter-spacing: 0.02em;
    text-transform: uppercase;
    padding: 10px 14px;
    cursor: pointer;
    transition: background 0.1s, border-color 0.1s;
    white-space: nowrap;
  }
  .command-bar button .cursor {
    color: var(--gold);
    opacity: 0;
  }
  .command-bar button:hover { border-color: var(--ring-light); }
  .command-bar button.active {
    background: var(--blood);
    border-color: var(--gold);
  }
  .command-bar button.active .cursor {
    opacity: 1;
    animation: blink 0.9s steps(2) infinite;
  }
  @keyframes blink { 50% { opacity: 0; } }

  @media (max-width: 640px) {
    .command-bar button { flex: 1 1 30%; justify-content: center; font-size: 0.5rem; padding: 10px 6px; }
    .nav-icon { display: none; }
  }

  /* ================= LAYOUT SHELL ================= */
  main {
    max-width: 1100px;
    margin: 0 auto;
    padding: 44px 24px 130px;
  }

  .screen-label {
    display: inline-block;
    font-family: 'Press Start 2P', monospace;
    font-size: 0.6rem;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: var(--ink);
    background: var(--gold);
    padding: 5px 10px;
    margin-bottom: 18px;
  }

  .panel-intro {
    font-size: 0.95rem;
    line-height: 1.75;
    color: var(--text);
    border-left: 4px solid var(--blood);
    padding-left: 18px;
    margin: 0 0 34px;
  }
  .panel-intro strong { color: var(--gold); }

  /* ---- HOME: status window ---- */
  .status-window {
    background: var(--panel);
    border: 1px solid var(--gold);
    outline: 1px solid var(--ink);
    outline-offset: 1px;
    box-shadow: 4px 4px 0 var(--ring-dark);
    padding: 22px 24px;
    margin-bottom: 30px;
  }
  .status-window .emblem {
    width: 46px;
    height: 46px;
    margin: 0 auto 14px;
    display: block;
  }
  .status-row {
    display: flex;
    justify-content: space-between;
    font-family: 'Press Start 2P', monospace;
    font-size: 0.6rem;
    letter-spacing: 0.03em;
    color: var(--text);
    padding: 7px 0;
    border-bottom: 1px dashed var(--panel-line);
  }
  .status-row:last-child { border-bottom: none; }
  .status-row span:first-child { color: var(--muted); }
  .status-row span:last-child { color: var(--gold); }

  /* ---- HOME: featured latest post ---- */
  .featured-card {
    background: var(--panel);
    border: 1px solid var(--ring-light);
    outline: 1px solid var(--ink);
    outline-offset: 1px;
    box-shadow: 4px 4px 0 var(--ring-dark);
    padding: 18px 20px;
    margin: 4px 3px 30px 3px;
    position: relative;
  }
  .featured-label {
    display: block;
    font-family: 'Press Start 2P', monospace;
    font-size: 0.6rem;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: var(--ring-light);
    margin-bottom: 10px;
    line-height: 1.6;
  }
  .featured-card h2 {
    font-family: 'Cinzel', serif;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-size: 1.1rem;
    line-height: 1.5;
    color: var(--neon);
    margin: 0 0 8px;
  }
  .featured-card time {
    display: block;
    font-size: 0.72rem;
    color: var(--muted);
    font-family: 'EB Garamond', serif;
    margin-bottom: 10px;
  }
  .featured-card p { margin: 0 0 12px; color: var(--text); line-height: 1.6; }

  .new-badge {
    position: absolute;
    top: -10px;
    right: -8px;
    overflow: hidden;
    background: var(--blood);
    color: var(--paper);
    font-family: 'Press Start 2P', monospace;
    font-size: 0.5rem;
    letter-spacing: 0.05em;
    padding: 5px 8px;
    border: 1px solid var(--gold);
    box-shadow: 2px 2px 0 var(--ink);
    animation: new-wobble 2.4s ease-in-out infinite;
    transform-origin: 70% 30%;
  }
  .new-badge::after {
    content: "";
    position: absolute;
    top: 0;
    left: -60%;
    width: 40%;
    height: 100%;
    background: linear-gradient(115deg, transparent, rgba(255,255,255,0.75), transparent);
    animation: new-shine 2.4s ease-in-out infinite;
  }
  @keyframes new-wobble {
    0%, 100% { transform: rotate(-6deg); }
    50% { transform: rotate(6deg); }
  }
  @keyframes new-shine {
    0% { left: -60%; }
    35%, 100% { left: 130%; }
  }

  .featured-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 18px;
    margin: 4px 0 30px;
  }
  @media (min-width: 620px) {
    .featured-grid { grid-template-columns: 1fr 1fr; }
    .featured-grid .featured-card { margin: 0; }
  }

  /* ---- HOME: two-column layout with sidebar ---- */
  .home-layout {
    display: grid;
    grid-template-columns: 1fr;
    gap: 34px;
  }
  @media (min-width: 900px) {
    .home-layout { grid-template-columns: 1fr 260px; align-items: start; }
  }

  .home-sidebar { display: flex; flex-direction: column; gap: 18px; }

  .side-box {
    background: var(--panel);
    border: 1px solid var(--gold);
    outline: 1px solid var(--ink);
    outline-offset: 1px;
    box-shadow: 4px 4px 0 var(--ring-dark);
    padding: 16px;
  }
  .side-box-title {
    font-family: 'Press Start 2P', monospace;
    font-size: 0.6rem;
    letter-spacing: 0.03em;
    color: var(--gold);
    margin: 0 0 10px;
  }
  .side-status {
    font-family: 'EB Garamond', serif;
    font-style: italic;
    font-size: 0.82rem;
    color: var(--muted);
    margin: 0 0 8px;
  }
  .side-entry {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    color: var(--text);
    padding: 6px 0;
    border-bottom: 1px dashed var(--panel-line);
  }
  .side-entry:last-child { border-bottom: none; }
  .side-entry img {
    width: 34px;
    height: 48px;
    object-fit: cover;
    border: 2px solid var(--panel-line);
    flex-shrink: 0;
  }
  .side-entry span { display: flex; flex-direction: column; gap: 2px; }
  .side-entry b {
    font-family: 'EB Garamond', serif;
    font-weight: 600;
    font-size: 0.82rem;
    color: var(--neon);
    line-height: 1.25;
  }
  .side-entry em {
    font-family: 'Press Start 2P', monospace;
    font-size: 0.45rem;
    letter-spacing: 0.03em;
    color: var(--muted);
    font-style: normal;
  }
  .side-manual-list {
    list-style: none;
    margin: 0 0 10px;
    padding: 0;
    font-family: 'EB Garamond', serif;
    font-size: 0.85rem;
    color: var(--text);
  }
  .side-manual-list li { padding: 4px 0; border-bottom: 1px dashed var(--panel-line); }
  .side-manual-list em { color: var(--muted); font-style: italic; }
  .side-link {
    display: inline-block;
    margin-top: 6px;
    font-family: 'Press Start 2P', monospace;
    font-size: 0.5rem;
    letter-spacing: 0.03em;
    color: var(--ring-light);
    text-decoration: none;
    border-bottom: 1px solid var(--blood);
    padding-bottom: 2px;
  }
  .side-link:hover { color: var(--paper); }


  /* ---- HOME: diary-page showcase for latest entries (interactive flip cards) ---- */
  .diary-section-label {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 14px;
    margin-bottom: 18px;
  }
  .diary-sprite {
    width: 40px;
    height: auto;
    image-rendering: pixelated;
  }
  .diary-section-title {
    font-family: 'Cinzel', serif;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    font-size: 0.85rem;
    color: var(--paper);
    text-align: center;
  }
  .sprite-credit {
    display: block;
    font-family: 'EB Garamond', serif;
    font-style: italic;
    font-size: 0.68rem;
    color: var(--muted);
    text-align: center;
    margin-top: -10px;
    margin-bottom: 26px;
  }

  .diary-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 22px;
    margin-bottom: 30px;
  }
  @media (min-width: 620px) {
    .diary-grid { grid-template-columns: 1fr 1fr; }
  }

  .diary-card {
    position: relative;
    perspective: 1200px;
    aspect-ratio: 1 / 1;
    cursor: pointer;
  }
  .diary-inner {
    position: relative;
    width: 100%;
    height: 100%;
    transition: transform 0.65s cubic-bezier(.4,.2,.2,1);
    transform-style: preserve-3d;
  }
  .diary-card.is-open .diary-inner { transform: rotateY(180deg); }

  .diary-face {
    position: absolute;
    inset: 0;
    backface-visibility: hidden;
    border-radius: 34px;
    border: 6px solid #8a6a45;
    outline: 3px solid var(--gold);
    outline-offset: -12px;
    background: linear-gradient(160deg, #f7edd0, #e8d9ac);
    box-shadow: 6px 6px 0 var(--ring-dark);
    padding: 24px 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    overflow: hidden;
  }
  .diary-face::before {
    /* spiral binding, evokes the diary-page menu screenshot */
    content: "";
    position: absolute;
    left: 12px;
    top: 8%;
    bottom: 8%;
    width: 10px;
    background-image: repeating-radial-gradient(circle, #4a4a4a 0 2.6px, transparent 2.6px 13px);
    opacity: 0.85;
  }
  .diary-face.diary-back { transform: rotateY(180deg); background: linear-gradient(160deg, #f0e2ba, #ddc994); }

  .diary-kicker {
    font-family: 'Press Start 2P', monospace;
    font-size: 0.5rem;
    letter-spacing: 0.04em;
    color: #7a5a34;
    margin-bottom: 10px;
  }
  .diary-face h2 {
    font-family: 'Cinzel', serif;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    font-size: 1.05rem;
    color: #5b3a29;
    margin: 0 0 8px;
    padding-left: 14px;
  }
  .diary-face time {
    font-family: 'EB Garamond', serif;
    font-style: italic;
    font-size: 0.78rem;
    color: #8a6a45;
    margin-bottom: 10px;
    padding-left: 14px;
  }
  .diary-prompt {
    font-family: 'Press Start 2P', monospace;
    font-size: 0.48rem;
    letter-spacing: 0.03em;
    color: #7a5a34;
    margin-top: 14px;
    padding-left: 14px;
  }
  .diary-back p {
    font-family: 'EB Garamond', serif;
    font-size: 0.85rem;
    color: #4a3524;
    line-height: 1.55;
    margin: 0 0 12px;
    padding-left: 14px;
  }
  .diary-back .read-more { color: #7a2333; border-bottom-color: #7a2333; }
  .diary-back .read-more:hover { color: #4a3524; }


  /* ---- BLOG -> QUEST LOG ---- */
  .channel-list { display: flex; flex-direction: column; gap: 8px; }
  .channel-row {
    display: grid;
    grid-template-columns: 56px 1fr auto;
    align-items: center;
    gap: 16px;
    background: var(--panel);
    border: 2px solid var(--panel-line);
    padding: 14px 18px;
    border-left: 6px solid var(--ring-light);
    box-shadow: 4px 4px 0 rgba(0,0,0,0.4);
    transition: border-color 0.1s, background 0.1s;
  }
  .channel-row:hover { border-left-color: var(--blood); background: var(--panel-hover); }
  .channel-num {
    font-family: 'Press Start 2P', monospace;
    font-size: 0.7rem;
    color: var(--ring-light);
    text-align: center;
    line-height: 1.5;
  }
  .channel-info h2 {
    font-family: 'Cinzel', serif;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    font-size: 0.95rem;
    line-height: 1.5;
    color: var(--neon);
    margin: 0 0 6px;
  }
  .channel-info time {
    display: block;
    font-size: 0.7rem;
    color: var(--muted);
    font-family: 'EB Garamond', serif;
    margin-bottom: 6px;
  }
  .channel-info p { margin: 0; color: var(--text); line-height: 1.55; font-size: 0.85rem; }
  .read-more {
    font-family: 'Press Start 2P', monospace;
    font-size: 0.55rem;
    letter-spacing: 0.02em;
    text-transform: uppercase;
    color: var(--ring-light);
    text-decoration: none;
    border-bottom: 2px solid var(--blood);
    padding-bottom: 3px;
    white-space: nowrap;
  }
  .read-more:hover { color: var(--paper); }
  .read-more::after { content: " \2192"; }

  /* ---- ARCHIVE -> SAVE DATA ---- */
  .archive-year { margin-bottom: 30px; }
  .archive-year h3 {
    font-family: 'Cinzel', serif;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    font-size: 0.95rem;
    color: var(--ring-light);
    border-bottom: 2px solid var(--panel-line);
    padding-bottom: 10px;
    margin-bottom: 12px;
  }
  .archive-year h3::before { content: "\1F4BE  "; }
  .archive-item {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    padding: 8px 0;
    border-bottom: 1px dashed var(--panel-line);
    font-size: 0.88rem;
  }
  .archive-item a { color: var(--text); text-decoration: none; }
  .archive-item a:hover { color: var(--blood); }
  .archive-item time { color: var(--muted); font-size: 0.72rem; font-family: 'EB Garamond', serif; white-space: nowrap; }

  /* ---- BLOG MAP -> WORLD MAP, auto-placed by tag ---- */
  .map-wrap {
    position: relative;
    width: 100%;
    aspect-ratio: 16 / 9;
    background:
      radial-gradient(ellipse at 30% 20%, rgba(107,143,78,0.35), transparent 55%),
      radial-gradient(ellipse at 75% 70%, rgba(122,35,51,0.3), transparent 55%),
      var(--panel);
    border: 3px solid var(--gold);
    outline: 2px solid var(--ink);
    outline-offset: 2px;
    box-shadow: 6px 6px 0 var(--ring-dark);
    overflow: hidden;
  }
  .map-node {
    position: absolute;
    transform: translate(-50%, -50%);
    width: 18px;
    height: 18px;
    clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
    filter: drop-shadow(0 0 3px var(--ink)) drop-shadow(0 0 4px rgba(255,255,255,0.35));
    cursor: pointer;
    transition: transform 0.15s;
  }
  .map-node:hover, .map-node.active { transform: translate(-50%, -50%) scale(1.5); }
  .map-detail {
    margin-top: 18px;
    background: var(--panel);
    border: 2px solid var(--panel-line);
    border-left: 6px solid var(--blood);
    box-shadow: 4px 4px 0 rgba(0,0,0,0.4);
    padding: 16px 20px;
  }
  .map-detail h2 {
    font-family: 'Cinzel', serif;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--neon);
    font-size: 1rem;
    line-height: 1.5;
    margin: 0 0 6px;
  }
  .map-detail time { color: var(--muted); font-size: 0.72rem; font-family: 'EB Garamond', serif; }
  .map-hint {
    font-family: 'EB Garamond', serif;
    font-size: 0.85rem;
    color: var(--muted);
    margin-bottom: 14px;
    letter-spacing: 0.02em;
  }
  .map-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
    margin-top: 14px;
  }
  .legend-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-family: 'EB Garamond', serif;
    font-size: 0.78rem;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: 0.02em;
  }
  .legend-chip i {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 0;
    outline: 1px solid var(--ink);
  }

  /* ---- .TXT -> ITEM BAG ---- */
  .txt-file {
    background: var(--ink);
    border: 3px solid var(--gold);
    outline: 2px solid var(--ink);
    outline-offset: 2px;
    box-shadow: 6px 6px 0 var(--ring-dark);
    overflow: hidden;
    font-family: 'EB Garamond', serif;
  }
  .txt-titlebar {
    background: var(--panel);
    border-bottom: 2px solid var(--panel-line);
    padding: 9px 14px;
    font-family: 'Press Start 2P', monospace;
    font-size: 0.6rem;
    color: var(--gold);
    letter-spacing: 0.05em;
  }
  .txt-body { padding: 18px 20px; }
  .txt-line {
    padding: 8px 0;
    font-size: 0.95rem;
    color: var(--text);
  }
  .txt-prompt { color: var(--neon); margin-right: 8px; }
  .txt-line a { color: var(--paper); text-decoration: none; border-bottom: 1px dotted var(--muted); }
  .txt-line a:hover { color: var(--blood); border-color: var(--blood); }
  .txt-note { color: var(--muted); font-size: 0.85rem; }

  /* ---- HELP: tutorial screen ---- */
  .help-box {
    background: var(--panel);
    border: 3px solid var(--gold);
    outline: 2px solid var(--ink);
    outline-offset: 2px;
    box-shadow: 6px 6px 0 var(--ring-dark);
    padding: 20px 22px;
    margin-bottom: 22px;
  }
  .help-box h3 {
    font-family: 'Cinzel', serif;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--neon);
    font-size: 1rem;
    margin: 0 0 12px;
  }
  .help-box ol {
    margin: 0;
    padding-left: 22px;
    color: var(--text);
    line-height: 1.8;
    font-size: 0.95rem;
  }
  .help-box li { margin-bottom: 8px; }
  .help-box code {
    background: var(--ink);
    border: 1px solid var(--panel-line);
    color: var(--gold);
    padding: 1px 6px;
    font-size: 0.85em;
  }
  .help-tip {
    font-family: 'Press Start 2P', monospace;
    font-size: 0.55rem;
    letter-spacing: 0.03em;
    color: var(--ink);
    background: var(--gold);
    padding: 8px 12px;
    margin-top: 14px;
    line-height: 1.7;
  }

  /* ---- CREDITS: diary-menu style credits list, echoes the LJ screenshot ---- */
  .credits-box { background: #1a1830; }
  .credits-list {
    list-style: none;
    margin: 0;
    padding: 0;
  }
  .credits-list li {
    font-family: 'Press Start 2P', monospace;
    font-size: 0.62rem;
    line-height: 2.1;
    color: #b0459a;
    letter-spacing: 0.01em;
  }
  .credits-list .diamond { color: #4747d6; margin-right: 10px; }
  .credits-list a { color: var(--gold); text-decoration: underline; }
  .credits-list a:hover { color: var(--paper); }

  .owner-notes { margin-top: 34px; }
  .owner-notes-label {
    font-family: 'Press Start 2P', monospace;
    font-size: 0.55rem;
    letter-spacing: 0.03em;
    text-align: center;
    color: var(--muted);
    margin-bottom: 20px;
  }

  footer {
    text-align: center;
    font-family: 'Press Start 2P', monospace;
    font-size: 0.55rem;
    letter-spacing: 0.02em;
    line-height: 2;
    color: var(--muted);
    padding: 30px 20px 20px;
    text-transform: uppercase;
  }
  footer a { color: var(--ring-light); text-decoration: none; }
</style>
</head>
<body x-data="siteApp()" x-cloak>

<header class="hero">
  <div class="scan" aria-hidden="true"></div>
  <div class="stars" aria-hidden="true"></div>
  <div class="hero-content">
    <p class="kicker"><img src="sailormoon-icon.ico" alt="" class="kicker-icon"> Pretty Soldier</p>
    <h1 class="site-title">Locqui</h1>
    <p class="subtitle">Another Story</p>
    <p class="caption" @click="rollCaption()" title="click to change the message" x-text="caption"></p>
  </div>
</header>

<!-- bottom JRPG command menu -->
<nav class="command-bar">
  <img src="sailormoon-icon.ico" alt="" class="nav-icon" title="icon: NatSpectrum">
  <button :class="{active: tab === 'home'}" @click="tab = 'home'"><span class="cursor">&#9656;</span>Status</button>
  <button :class="{active: tab === 'blog'}" @click="tab = 'blog'"><span class="cursor">&#9656;</span>Quest Log</button>
  <button :class="{active: tab === 'archive'}" @click="tab = 'archive'"><span class="cursor">&#9656;</span>Save Data</button>
  <button :class="{active: tab === 'map'}" @click="tab = 'map'"><span class="cursor">&#9656;</span>World Map</button>
  <button :class="{active: tab === 'txt'}" @click="tab = 'txt'"><span class="cursor">&#9656;</span>Item Bag</button>
  <button :class="{active: tab === 'help'}" @click="tab = 'help'"><span class="cursor">&#9656;</span>Credits</button>
</nav>

<main>

  <!-- HOME -> STATUS -->
  <section x-show="tab === 'home'" x-transition.opacity>
    <span class="screen-label">Status</span>

    <div class="home-layout">
      <div class="home-main">
        <div class="status-window">
          <svg class="emblem" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M24 4c-8 4-12 11-12 18 0 9 7 17 17 20-11 1-22-7-22-20C7 12 14 5 24 4z" fill="var(--gold)"/>
            <path d="M35 16l2 5 5 2-5 2-2 5-2-5-5-2 5-2z" fill="var(--neon)"/>
          </svg>
          <div class="status-row"><span>Party</span><span>Locqui</span></div>
          <div class="status-row"><span>Location</span><span>amor-omnia.org</span></div>
          <div class="status-row"><span>Status Effect</span><span>Yapping</span></div>
          <div class="status-row"><span>Quests Logged</span><span x-text="posts.length"></span></div>
        </div>
        <p class="panel-intro">
          <strong>locqui</strong> is where the yapping happens — unfiltered thoughts,
          late-night spirals, and whatever else doesn't fit anywhere else on
          <strong>amor-omnia.org</strong>. Consider this a save file for the parts
          of the story that don't make the main quest.
        </p>

        <div class="diary-section-label">
          <img src="queen-serenity-1.gif" alt="Queen Serenity sprite" class="diary-sprite">
          <span class="diary-section-title">&#9733; latest entries &#9733;</span>
          <img src="queen-serenity-2.gif" alt="Queen Serenity sprite" class="diary-sprite">
        </div>
        <span class="sprite-credit">Queen Serenity sprites by missa-pxl (LiveJournal), used with permission</span>

        <div class="featured-grid">
          <template x-for="(post, i) in latestTwoPosts" :key="post.slug">
            <div class="featured-card">
              <span class="new-badge" x-show="i === 0">&#9733; NEW</span>
              <span class="featured-label">&#9733; latest event &#9733;</span>
              <h2 x-text="post.title"></h2>
              <time x-text="post.display_date"></time>
              <p x-text="post.excerpt"></p>
              <a class="read-more" :href="'post.php?slug=' + post.slug">read more</a>
            </div>
          </template>
        </div>

        <p style="color:var(--text); line-height:1.7; font-size:0.9rem;">
          Open <strong>Quest Log</strong> for entries in order, <strong>Save Data</strong>
          to browse everything by year, <strong>World Map</strong> to see posts
          auto-clustered by tag, and <strong>Item Bag</strong> for the ones I've
          hand-picked myself.
        </p>
      </div>

      <aside class="home-sidebar">
        <!-- AniList: public GraphQL API, no auth needed, fetched client-side -->
        <div class="side-box" x-data="anilistWidget()" x-init="load()">
          <p class="side-box-title">&#9656; AniList</p>
          <template x-if="loading">
            <p class="side-status">loading signal from the moon kingdom&#8230;</p>
          </template>
          <template x-if="error">
            <p class="side-status">couldn't reach AniList right now.</p>
          </template>
          <template x-if="!loading && !error">
            <div>
              <template x-for="entry in entries" :key="entry.id">
                <a class="side-entry" :href="entry.url" target="_blank" rel="noopener">
                  <img :src="entry.cover" :alt="entry.title">
                  <span>
                    <b x-text="entry.title"></b>
                    <em x-text="entry.status"></em>
                  </span>
                </a>
              </template>
              <p class="side-status" x-show="entries.length === 0">nothing currently in progress</p>
            </div>
          </template>
          <a class="side-link" href="https://anilist.co/user/TuxedoCaim/" target="_blank" rel="noopener">full profile &#8594;</a>
        </div>

        <!-- MyDramaList: no public API available right now, so this is a
             manually-updated block rather than a live feed. Edit the list
             below by hand when your watching status changes. -->
        <div class="side-box">
          <p class="side-box-title">&#9656; MyDramaList</p>
          <p class="side-status">no public API yet &mdash; update this by hand:</p>
          <ul class="side-manual-list">
            <li>Currently watching: <em>&#8212;</em></li>
            <li>Plan to watch: <em>&#8212;</em></li>
          </ul>
          <a class="side-link" href="https://mydramalist.com/profile/Blizzaja" target="_blank" rel="noopener">full profile &#8594;</a>
        </div>

        <!-- Last.fm: real API (ws.audioscrobbler.com), free key required.
             Set LASTFM_API_KEY and LASTFM_USERNAME below and this goes
             live exactly like the AniList box — same fetch pattern. -->
        <div class="side-box" x-data="lastfmWidget()" x-init="load()">
          <p class="side-box-title">&#9656; Last.fm</p>
          <template x-if="!configured">
            <p class="side-status">add a free Last.fm API key in the script to activate this.</p>
          </template>
          <template x-if="configured && loading">
            <p class="side-status">tuning in&#8230;</p>
          </template>
          <template x-if="configured && error">
            <p class="side-status">couldn't reach Last.fm right now.</p>
          </template>
          <template x-if="configured && !loading && !error">
            <div>
              <template x-for="track in tracks" :key="track.id">
                <a class="side-entry" :href="track.url" target="_blank" rel="noopener">
                  <img :src="track.cover" :alt="track.name">
                  <span>
                    <b x-text="track.name"></b>
                    <em x-text="track.nowPlaying ? 'now playing' : track.artist"></em>
                  </span>
                </a>
              </template>
              <p class="side-status" x-show="tracks.length === 0">nothing scrobbled recently</p>
            </div>
          </template>
          <a class="side-link" href="https://www.last.fm/" target="_blank" rel="noopener">full profile &#8594;</a>
        </div>

        <!-- What I'm Reading (Books): Goodreads' public API was
             discontinued and StoryGraph has no public API, so this stays
             a manual list, same pattern as MyDramaList. -->
        <div class="side-box">
          <p class="side-box-title">&#9656; Reading &middot; Books</p>
          <p class="side-status">no public API available &mdash; update this by hand:</p>
          <ul class="side-manual-list">
            <li>Currently reading: <em>&#8212;</em></li>
            <li>Up next: <em>&#8212;</em></li>
          </ul>
        </div>

        <!-- What I'm Reading (Fanfiction): AO3 has no official public API
             either, so this is also manual — just link straight to the
             work/bookmark when you update it. -->
        <div class="side-box">
          <p class="side-box-title">&#9656; Reading &middot; Fanfic</p>
          <p class="side-status">no public API available &mdash; update this by hand:</p>
          <ul class="side-manual-list">
            <li>Currently reading: <em>&#8212;</em></li>
            <li>Bookmarked: <em>&#8212;</em></li>
          </ul>
        </div>
      </aside>
    </div>
  </section>

  <!-- BLOG -> QUEST LOG -->
  <section x-show="tab === 'blog'" x-transition.opacity>
    <span class="screen-label">Quest Log</span>
    <p class="panel-intro"><strong>active quests</strong> — the latest entries, newest first.</p>
    <div class="channel-list">
      <template x-for="(post, i) in posts" :key="post.slug">
        <div class="channel-row">
          <div class="channel-num" x-text="'Q' + String(i + 1).padStart(2, '0')"></div>
          <div class="channel-info">
            <h2 x-text="post.title"></h2>
            <time x-text="post.display_date"></time>
            <p x-text="post.excerpt"></p>
          </div>
          <a class="read-more" :href="'post.php?slug=' + post.slug">read more</a>
        </div>
      </template>
    </div>
  </section>

  <!-- ARCHIVE -> SAVE DATA -->
  <section x-show="tab === 'archive'" x-transition.opacity>
    <span class="screen-label">Save Data</span>
    <p class="panel-intro"><strong>load a chapter</strong> — everything, sorted by year.</p>
    <template x-for="year in archiveYears" :key="year">
      <div class="archive-year">
        <h3 x-text="'Chapter ' + year"></h3>
        <template x-for="post in postsByYear(year)" :key="post.slug">
          <div class="archive-item">
            <a :href="'post.php?slug=' + post.slug" x-text="post.title"></a>
            <time x-text="post.display_date"></time>
          </div>
        </template>
      </div>
    </template>
  </section>

  <!-- BLOG MAP -> WORLD MAP: auto-placed by tag, no manual coordinates -->
  <section x-show="tab === 'map'" x-transition.opacity>
    <span class="screen-label">World Map</span>
    <p class="panel-intro"><strong>the world map</strong> — posts place themselves. Each tag anchors a location; a post lands at the average of its own tags, so shared tags cluster related entries near each other.</p>
    <p class="map-hint">click a marker to preview the entry</p>
    <div class="map-wrap">
      <template x-for="post in posts" :key="post.slug">
        <div class="map-node"
             :class="{active: selected && selected.slug === post.slug}"
             :style="'left:' + postPos(post).x + '%; top:' + postPos(post).y + '%; border-bottom-color:' + tagColor(post.tags[0]) + ';'"
             @click="selected = post"
             :title="post.title + ' — ' + post.tags.join(', ')"></div>
      </template>
    </div>
    <div class="map-legend">
      <template x-for="tag in allTags" :key="tag">
        <span class="legend-chip">
          <i :style="'background:' + tagColor(tag) + ';'"></i>
          <span x-text="tag"></span>
        </span>
      </template>
    </div>
    <div class="map-detail" x-show="selected" x-transition.opacity>
      <template x-if="selected">
        <div>
          <h2 x-text="selected.title"></h2>
          <time x-text="selected.display_date"></time>
          <p style="color:var(--text); margin-top:8px;" x-text="selected.excerpt"></p>
          <p style="color:var(--muted); font-size:0.75rem; font-family:'EB Garamond',serif; margin-top:6px;" x-text="'tags: ' + selected.tags.join(', ')"></p>
          <a class="read-more" :href="'post.php?slug=' + selected.slug" x-text="'read more'"></a>
        </div>
      </template>
    </div>
  </section>

  <!-- .TXT -> ITEM BAG: hand-picked / self-categorized posts -->
  <section x-show="tab === 'txt'" x-transition.opacity>
    <span class="screen-label">Item Bag</span>
    <p class="panel-intro"><strong>key items</strong> — hand-picked entries, sorted and noted myself, outside the tag system.</p>
    <div class="txt-file">
      <div class="txt-titlebar">&#9733; curated.txt</div>
      <div class="txt-body">
        <template x-for="post in curatedPosts" :key="post.slug">
          <div class="txt-line">
            <span class="txt-prompt">&gt;</span>
            <a :href="'post.php?slug=' + post.slug" x-text="post.title"></a>
            <span class="txt-note" x-show="post.note" x-text="post.note ? ' // ' + post.note : ''"></span>
          </div>
        </template>
        <div class="txt-line" x-show="curatedPosts.length === 0">
          <span class="txt-prompt">&gt;</span>
          <span style="color: var(--muted);">bag is empty — flag posts as 'curated' to add them here</span>
        </div>
      </div>
    </div>
  </section>

  <!-- CREDITS: fan-content attribution + owner's personal how-to reference -->
  <section x-show="tab === 'help'" x-transition.opacity>
    <span class="screen-label">Credits</span>
    <p class="panel-intro"><strong>credits</strong> — everything borrowed, referenced, or used with permission on this page.</p>

    <div class="help-box credits-box">
      <ul class="credits-list">
        <li><span class="diamond">&#9670;</span>SAILOR MOON: ANOTHER STORY &mdash; UI &amp; typography reference</li>
        <li><span class="diamond">&#9670;</span>Queen Serenity sprites by <strong>missa-pxl</strong> (LiveJournal), used with permission</li>
        <li><span class="diamond">&#9670;</span>Sailor Moon icon by <a href="https://www.steamgriddb.com/profile/76561197987889134" target="_blank" rel="noopener"><strong>NatSpectrum</strong></a></li>
        <li><span class="diamond">&#9670;</span>Fan-made, non-commercial, personal blog theme</li>
      </ul>
    </div>

    <div class="owner-notes">
      <p class="owner-notes-label">&#9656; the rest of this page is for you, not visitors &#9656;</p>

      <div class="help-box">
        <h3>Adding a new post</h3>
        <ol>
          <li>Open <code>index.php</code> and find the <code>$posts</code> array near the top.</li>
          <li>Copy one of the existing entries (the square-bracketed blocks) and paste it as a new item in the array.</li>
          <li>Fill in the fields:
            <ul>
              <li><code>slug</code> — a short, unique, URL-safe id, e.g. <code>'entry-005'</code>.</li>
              <li><code>title</code> — the entry's title.</li>
              <li><code>date</code> — ISO format, <code>'YYYY-MM-DD'</code>, used for sorting and Save Data grouping.</li>
              <li><code>display_date</code> — the human, flavor-text version shown on the page.</li>
              <li><code>excerpt</code> — a short preview shown in the diary cards / Quest Log.</li>
              <li><code>tags</code> — see below, this drives the World Map.</li>
              <li><code>curated</code> — set to <code>true</code> to have it appear in the Item Bag.</li>
              <li><code>note</code> — optional personal note, only shown in the Item Bag.</li>
            </ul>
          </li>
          <li>Save the file. The new entry appears automatically in the Status diary (if one of the two newest), Quest Log, Save Data, and the World Map — no other file needs editing.</li>
        </ol>
      </div>

      <div class="help-box">
        <h3>How tagging works</h3>
        <ol>
          <li>Tags go in the <code>tags</code> array for each post, e.g. <code>['diary', 'late-night']</code>.</li>
          <li>Every unique tag across all your posts automatically gets a fixed spot on the World Map — you never place these by hand.</li>
          <li>A post's marker position is the average of its own tags' spots, so posts sharing a tag naturally land near each other.</li>
          <li>Reusing a tag across posts is how you build clusters/relationships — there's no separate category or folder system to maintain.</li>
          <li>A post can have as many tags as you like; more shared tags with another post pulls them closer together on the map.</li>
        </ol>
        <p class="help-tip">TIP: keep tags lowercase and consistent (e.g. always <code>late-night</code>, not sometimes <code>Late Night</code>) so posts actually cluster instead of scattering.</p>
      </div>
    </div>
  </section>

</main>

<footer>
  <p>locqui.amor-omnia.org &middot; back to <a href="https://amor-omnia.org">amor-omnia</a></p>
</footer>

<script>
  // Alpine.js component — reads the PHP-rendered $posts array once, then
  // handles all command-menu / save-data / world-map interactivity client-side.
  // AniList widget — hits the public AniList GraphQL API directly from the
  // browser (https://graphql.anilist.co). No auth needed for public profile
  // data, no server-side code required. Shows anime/manga with status
  // CURRENT (i.e. "currently watching/reading") for the given username.
  function anilistWidget() {
    return {
      loading: true,
      error: false,
      entries: [],
      async load() {
        const username = 'TuxedoCaim';
        const query = `
          query ($name: String) {
            MediaListCollection(userName: $name, type: ANIME, status: CURRENT) {
              lists { entries { id media { title { romaji } coverImage { medium } siteUrl } } }
            }
            manga: MediaListCollection(userName: $name, type: MANGA, status: CURRENT) {
              lists { entries { id media { title { romaji } coverImage { medium } siteUrl } } }
            }
          }`;
        try {
          const res = await fetch('https://graphql.anilist.co', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ query, variables: { name: username } })
          });
          if (!res.ok) throw new Error('bad response');
          const data = await res.json();
          const animeEntries = (data.data.MediaListCollection.lists || [])
            .flatMap(l => l.entries)
            .map(e => ({ id: 'a' + e.id, title: e.media.title.romaji, cover: e.media.coverImage.medium, url: e.media.siteUrl, status: 'watching' }));
          const mangaEntries = (data.data.manga.lists || [])
            .flatMap(l => l.entries)
            .map(e => ({ id: 'm' + e.id, title: e.media.title.romaji, cover: e.media.coverImage.medium, url: e.media.siteUrl, status: 'reading' }));
          this.entries = [...animeEntries, ...mangaEntries].slice(0, 5);
        } catch (e) {
          this.error = true;
        } finally {
          this.loading = false;
        }
      }
    };
  }

  // Last.fm widget — Last.fm's read API (ws.audioscrobbler.com) needs a
  // free API key (register at last.fm/api/account/create). It's a plain
  // GET request, callable straight from the browser like AniList — no
  // server code needed. Fill in the two constants below to activate.
  const LASTFM_API_KEY = ''; // <- paste your free Last.fm API key here
  const LASTFM_USERNAME = ''; // <- your Last.fm username

  function lastfmWidget() {
    return {
      configured: Boolean(LASTFM_API_KEY && LASTFM_USERNAME),
      loading: true,
      error: false,
      tracks: [],
      async load() {
        if (!this.configured) { this.loading = false; return; }
        const url = `https://ws.audioscrobbler.com/2.0/?method=user.getrecenttracks&user=${encodeURIComponent(LASTFM_USERNAME)}&api_key=${LASTFM_API_KEY}&limit=3&format=json`;
        try {
          const res = await fetch(url);
          if (!res.ok) throw new Error('bad response');
          const data = await res.json();
          this.tracks = (data.recenttracks.track || []).map((t, i) => ({
            id: i,
            name: t.name,
            artist: t.artist['#text'],
            cover: (t.image && t.image[1] && t.image[1]['#text']) || '',
            url: t.url,
            nowPlaying: t['@attr'] && t['@attr'].nowplaying === 'true'
          }));
        } catch (e) {
          this.error = true;
        } finally {
          this.loading = false;
        }
      }
    };
  }

  function siteApp() {
    return {
      tab: 'home',
      selected: null,
      flipped: null,
      posts: <?= $posts_json ?>,
      caption: (typeof window.LUA_CAPTION !== 'undefined') ? window.LUA_CAPTION : 'a wild blog post appeared!',

      rollCaption() {
        // calls back into the Fengari-run Lua function to pick a new line
        if (typeof window.rollCaption === 'function') {
          this.caption = window.rollCaption();
        }
      },

      get latestTwoPosts() {
        return [...this.posts].sort((a, b) => b.date.localeCompare(a.date)).slice(0, 2);
      },

      get archiveYears() {
        const years = [...new Set(this.posts.map(p => p.date.slice(0, 4)))];
        return years.sort().reverse();
      },
      postsByYear(year) {
        return this.posts
          .filter(p => p.date.slice(0, 4) === year)
          .sort((a, b) => b.date.localeCompare(a.date));
      },

      get latestPost() {
        if (!this.posts.length) return null;
        return [...this.posts].sort((a, b) => b.date.localeCompare(a.date))[0];
      },

      get curatedPosts() {
        return this.posts.filter(p => p.curated);
      },

      get allTags() {
        return [...new Set(this.posts.flatMap(p => p.tags || []))].sort();
      },

      // simple string hash -> stable number, used for both tag anchoring
      // and per-post jitter so layout is deterministic across reloads
      hash(str) {
        let h = 0;
        for (let i = 0; i < str.length; i++) {
          h = (h * 31 + str.charCodeAt(i)) >>> 0;
        }
        return h;
      },

      // each unique tag gets a fixed point on a circle (golden-angle
      // spread keeps them from bunching even as tags are added)
      tagAnchor(tag) {
        const tags = this.allTags;
        const i = tags.indexOf(tag);
        const angle = i * 137.508 * (Math.PI / 180); // golden angle
        const radius = 34;
        return {
          x: 50 + radius * Math.cos(angle),
          y: 50 + radius * Math.sin(angle) * 0.85, // slight ellipse for the aspect ratio
        };
      },

      tagColor(tag) {
        const hue = this.hash(tag) % 360;
        return `hsl(${hue}, 55%, 55%)`;
      },

      // a post's map position = average of its tags' anchors, nudged by
      // a tiny deterministic jitter so identical tag-sets don't overlap
      postPos(post) {
        const tags = post.tags && post.tags.length ? post.tags : ['untagged'];
        const anchors = tags.map(t => this.tagAnchor(t));
        const avgX = anchors.reduce((s, a) => s + a.x, 0) / anchors.length;
        const avgY = anchors.reduce((s, a) => s + a.y, 0) / anchors.length;
        const h = this.hash(post.slug);
        const jitterX = ((h % 100) / 100 - 0.5) * 8;
        const jitterY = (((h >> 8) % 100) / 100 - 0.5) * 8;
        return {
          x: Math.min(96, Math.max(4, avgX + jitterX)),
          y: Math.min(96, Math.max(4, avgY + jitterY)),
        };
      }
    };
  }
</script>

</body>
</html>
