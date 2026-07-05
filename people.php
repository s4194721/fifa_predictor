<?php
require 'db.php';
require 'functions.php';

$match_id = $_GET['match_id'] ?? 0;
$team = $_GET['team'] ?? '';

$s = $pdo->prepare(
    'SELECT p.name, p.roll, p.department
     FROM predictions pr
     JOIN participants p ON pr.participant_id = p.id
     WHERE pr.match_id = ? AND pr.selected_team = ?
     ORDER BY p.name'
);

$s->execute([$match_id, $team]);
$rows = $s->fetchAll();

if (!$rows) {
    echo '<p>No one selected this option.</p>';
    exit;
}

echo '<div class="table-wrap">';
echo '<table class="table">';
echo '<tr><th>Name</th><th>Roll</th><th>Department</th></tr>';

foreach ($rows as $r) {
    echo '<tr>';
    echo '<td>' . h($r['name']) . '</td>';
    echo '<td>' . h($r['roll']) . '</td>';
    echo '<td>' . h($r['department']) . '</td>';
    echo '</tr>';
}

echo '</table>';
echo '</div>';
?>