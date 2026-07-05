<?php require 'db.php'; 
require 'functions.php'; 
$rounds=getRounds($pdo); ?>
<!doctype html>
<html>
    <head>
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>FIFA World Cup Predictor (RUET 89)</title>
        <link rel="stylesheet" href="style.css?v=6">
    </head>
    <body>
<header>
    <div class="topbar">
        <div class="logo">FIFA Predictor</div>
        <div class="nav">
            <a href="index.php">Home</a>
        <a href="leaderboard.php">Leaderboard</a>
        <a href="admin.php">Admin</a>
    </div>
</div>
</header>
<main class="container">
    <section class="hero">
        <h1>Predict the Winner</h1>
        <p>Submit your predictions for each World Cup round.</p>
    </section>
<div class="grid">
<?php foreach($rounds as $r): ?>
<div class="card round-card">
    <h2><?=h($r['name'])?></h2>
    <p><?=h($r['points'])?> points for correct answer. Wrong answer deducts 50%.</p><br>
    <span class="badge <?=h($r['status'])?>"><?=strtoupper(h($r['status']))?></span><br><br>
<a class="btn" href="form.php?round=<?=h($r['code'])?>">Open Round</a>
</div>
<?php endforeach; ?>
</div>
</main>
</body>
</html>
