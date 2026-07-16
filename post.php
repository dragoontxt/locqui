<?php
/**
 * post.php — single entry page for Locqui.rpg
 *
 * Every "read more" link across index.php points here as
 * post.php?slug=... This file has its own copy of $posts (same as
 * feed.php does) with one extra field, 'content', for the full body.
 * For a real site, pull all three (index.php, post.php, feed.php) from
 * one shared include/DB so you only maintain the array once.
 */

$posts = [
    [
        'slug'    => 'entry-001',
        'title'   => 'entry 001',
        'date'    => '2026-07-01',
        'display_date' => 'today, probably 2am',
        'excerpt' => 'This is where the yapping begins. Replace this array with real posts, or wire it up to a database.',
        'content' => "This is where the yapping begins.\n\nReplace this whole array with real posts, or wire it up to a database — the page you're looking at right now just loops over \$posts and renders whichever slug is in the URL.\n\nEverything past this paragraph is just placeholder text so you can see how a longer entry wraps, breaks into paragraphs, and sits inside the frame. Once you're writing for real, this is the 'content' field — separate from 'excerpt', which is what shows up in the Quest Log and the Status cards.",
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
        'content' => "Another placeholder entry, this time to show what a shorter post looks like on its own page instead of just as a preview card.\n\nNot every entry needs to be long. This one's just a couple of paragraphs, and that's fine — the layout doesn't demand a minimum length.",
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
        'content' => "A third entry, mostly here to prove the layout holds up once there's more than one or two posts to page through.\n\nThis one has a previous entry and a next entry — check the bottom of the page for those links, so people (or you) can move through the log without going back to the menu every time.",
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
        'content' => "An older entry, mostly here so Save Data has more than one year to group.\n\nThis one never got posted anywhere else — sat as a draft for months. Sometimes that's just what happens to an entry, and that's fine too.",
        'tags'    => ['throwback', 'diary'],
        'curated' => false,
        'note'    => '',
    ],
];

// sort newest first, same as the rest of the site
usort($posts, fn($a, $b) => strcmp($b['date'], $a['date']));

$slug = $_GET['slug'] ?? '';
$index = null;
foreach ($posts as $i => $p) {
    if ($p['slug'] === $slug) { $index = $i; break; }
}
$post = $index !== null ? $posts[$index] : null;
$prev = $index !== null && isset($posts[$index + 1]) ? $posts[$index + 1] : null; // older
$next = $index !== null && $index > 0 ? $posts[$index - 1] : null; // newer

function e($str) { return htmlspecialchars($str, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $post ? e($post['title']) . ' — Locqui.rpg' : 'Quest Not Found — Locqui.rpg' ?></title>
<link rel="icon" href="sailormoon-icon.ico">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Pinyon+Script&family=Cinzel:wght@600;700&family=EB+Garamond:wght@400;600&family=Press+Start+2P&display=swap" rel="stylesheet">
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
    --panel-line: #4a4470;
    --muted: #8f8ab0;
    --text: #d8d3e8;
    --gold: #c9a227;
  }
  * { box-sizing: border-box; }
  img { image-rendering: pixelated; }
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

  .topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 16px 22px;
    background: var(--panel);
    border-bottom: 3px solid var(--gold);
  }
  .topbar-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    font-family: 'Cinzel', serif;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    font-size: 0.85rem;
    color: var(--gold);
    text-decoration: none;
  }
  .topbar-brand img { width: 20px; height: 20px; }
  .back-link {
    font-family: 'Press Start 2P', monospace;
    font-size: 0.55rem;
    letter-spacing: 0.03em;
    color: var(--ring-light);
    text-decoration: none;
    border-bottom: 2px solid var(--blood);
    padding-bottom: 3px;
  }
  .back-link:hover { color: var(--paper); }

  main {
    max-width: 760px;
    margin: 0 auto;
    padding: 44px 24px 90px;
  }

  .entry-header { margin-bottom: 28px; }
  .entry-kicker {
    font-family: 'Press Start 2P', monospace;
    font-size: 0.55rem;
    letter-spacing: 0.05em;
    color: var(--ring-light);
    margin: 0 0 10px;
  }
  .entry-title {
    font-family: 'Cinzel', serif;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    font-size: clamp(1.4rem, 4vw, 2rem);
    color: var(--neon);
    margin: 0 0 10px;
    line-height: 1.3;
  }
  .entry-date {
    font-family: 'EB Garamond', serif;
    font-style: italic;
    font-size: 0.9rem;
    color: var(--muted);
  }

  .tag-row { margin-top: 14px; display: flex; flex-wrap: wrap; gap: 8px; }
  .tag-pill {
    font-family: 'Press Start 2P', monospace;
    font-size: 0.5rem;
    letter-spacing: 0.03em;
    color: var(--ink);
    background: var(--gold);
    padding: 4px 8px;
  }

  .entry-body {
    background: var(--panel);
    border: 1px solid var(--ring-light);
    outline: 1px solid var(--ink);
    outline-offset: 1px;
    box-shadow: 4px 4px 0 var(--ring-dark);
    padding: 26px 28px;
    margin-bottom: 30px;
  }
  .entry-body p {
    font-size: 1.02rem;
    line-height: 1.8;
    color: var(--text);
    margin: 0 0 18px;
  }
  .entry-body p:last-child { margin-bottom: 0; }

  .entry-nav {
    display: flex;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
    font-family: 'Press Start 2P', monospace;
    font-size: 0.55rem;
    letter-spacing: 0.03em;
  }
  .entry-nav a {
    color: var(--ring-light);
    text-decoration: none;
    border: 1px solid var(--panel-line);
    padding: 10px 14px;
    background: var(--panel);
  }
  .entry-nav a:hover { border-color: var(--gold); color: var(--paper); }
  .entry-nav .spacer { flex: 1; }

  .not-found {
    text-align: center;
    padding: 60px 20px;
  }
  .not-found h1 {
    font-family: 'Cinzel', serif;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--blood);
    font-size: 1.3rem;
  }
  .not-found p { color: var(--muted); font-family: 'EB Garamond', serif; }

  footer {
    text-align: center;
    font-family: 'Press Start 2P', monospace;
    font-size: 0.5rem;
    letter-spacing: 0.02em;
    line-height: 2;
    color: var(--muted);
    padding: 20px 20px 30px;
    text-transform: uppercase;
  }
  footer a { color: var(--ring-light); text-decoration: none; }
</style>
</head>
<body>

<div class="topbar">
  <a class="topbar-brand" href="index.php">
    <img src="sailormoon-icon.ico" alt="">
    Locqui.rpg
  </a>
  <a class="back-link" href="index.php">&#9668; back to quest log</a>
</div>

<main>
<?php if ($post): ?>

  <div class="entry-header">
    <p class="entry-kicker">&#9656; diary entry</p>
    <h1 class="entry-title"><?= e($post['title']) ?></h1>
    <time class="entry-date"><?= e($post['display_date']) ?></time>
    <?php if (!empty($post['tags'])): ?>
      <div class="tag-row">
        <?php foreach ($post['tags'] as $tag): ?>
          <span class="tag-pill"><?= e($tag) ?></span>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <div class="entry-body">
    <?php foreach (explode("\n\n", $post['content']) as $para): ?>
      <p><?= nl2br(e($para)) ?></p>
    <?php endforeach; ?>
  </div>

  <nav class="entry-nav">
    <?php if ($prev): ?>
      <a href="post.php?slug=<?= e($prev['slug']) ?>">&#9668; <?= e($prev['title']) ?></a>
    <?php else: ?>
      <span></span>
    <?php endif; ?>
    <div class="spacer"></div>
    <?php if ($next): ?>
      <a href="post.php?slug=<?= e($next['slug']) ?>"><?= e($next['title']) ?> &#9658;</a>
    <?php endif; ?>
  </nav>

<?php else: ?>

  <div class="not-found">
    <h1>Quest Not Found</h1>
    <p>No entry matches that slug. It may have been renamed, removed, or never existed.</p>
    <p><a class="back-link" href="index.php">&#9668; back to quest log</a></p>
  </div>

<?php endif; ?>
</main>

<footer>
  <p>locqui.amor-omnia.org &middot; back to <a href="https://amor-omnia.org">amor-omnia</a></p>
</footer>

</body>
</html>
