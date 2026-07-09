<?php require 'db.php'; require 'functions.php';
$sql="SELECT p.name,p.roll,p.department, SUM(CASE WHEN m.correct_team IS NULL THEN 0 WHEN pr.selected_team=m.correct_team THEN r.points ELSE -(r.points/2) END) AS total
FROM participants p JOIN predictions pr ON p.id=pr.participant_id JOIN matches m ON pr.match_id=m.id JOIN rounds r ON p.round_id=r.id
GROUP BY p.name,p.roll,p.department ORDER BY total DESC"; $rows=$pdo->query($sql)->fetchAll(); ?>
<!doctype html>
<html>
    <head>
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Leaderboard</title>
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
            <h1>Leaderboard</h1>
            <br>
            <div class="table-wrap">
                <table class="table">
                    <tr>
                        <th>Rank</th>
                        <th>Name</th>
                        <th>Roll</th>
                        <th>Department</th>
                        <th>Points</th>
                    </tr><?php $rank=1; foreach($rows as $r): ?>
                        <tr>
                            <td><?=$rank++?></td>
                            <td><?=h($r['name'])?></td>
                            <td><?=h($r['roll'])?></td>
                            <td><?=h($r['department'])?></td>
                            <td><b><?=number_format((float)$r['total'],1)?></b></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </main>
        </body>
        </html>