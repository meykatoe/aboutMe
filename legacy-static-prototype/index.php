<?php
declare(strict_types=1);

$data = require __DIR__ . '/config.php';
$profile = $data['profile'];
$navGroups = $data['nav_groups'];

function h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function initials(string $name): string
{
    $parts = preg_split('/\s+/', trim($name));
    $letters = array_map(fn($p) => mb_substr($p, 0, 1), array_filter($parts));
    return mb_strtoupper(implode('', array_slice($letters, 0, 2)));
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= h($profile['name']) ?> · 個人導覽頁</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<header class="profile">
    <?php if (!empty($profile['avatar'])): ?>
        <img class="avatar" src="<?= h($profile['avatar']) ?>" alt="<?= h($profile['name']) ?>">
    <?php else: ?>
        <div class="avatar avatar-fallback"><?= h(initials($profile['name'])) ?></div>
    <?php endif; ?>

    <h1><?= h($profile['name']) ?></h1>
    <p class="title"><?= h($profile['title']) ?></p>
    <p class="bio"><?= h($profile['bio']) ?></p>

    <div class="contact">
        <?php if (!empty($profile['email'])): ?>
            <a href="mailto:<?= h($profile['email']) ?>">✉️ <?= h($profile['email']) ?></a>
        <?php endif; ?>
    </div>

    <?php if (!empty($profile['socials'])): ?>
        <ul class="socials">
            <?php foreach ($profile['socials'] as $s): ?>
                <li><a href="<?= h($s['url']) ?>" target="_blank" rel="noopener"><?= h($s['name']) ?></a></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</header>

<main class="nav">
    <?php foreach ($navGroups as $group): ?>
        <section class="nav-group">
            <h2><?= h($group['category']) ?></h2>
            <div class="nav-cards">
                <?php foreach ($group['links'] as $link): ?>
                    <a class="nav-card" href="<?= h($link['url']) ?>" target="_blank" rel="noopener">
                        <span class="nav-card-name"><?= h($link['name']) ?></span>
                        <?php if (!empty($link['desc'])): ?>
                            <span class="nav-card-desc"><?= h($link['desc']) ?></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>
</main>

<footer>
    <p>&copy; <?= date('Y') ?> <?= h($profile['name']) ?></p>
</footer>

</body>
</html>
