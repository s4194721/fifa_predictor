<?php
require 'db.php'; require 'functions.php';
$code=$_GET['round'] ?? 'r16'; $round=getRoundByCode($pdo,$code); if(!$round) die('Round not found');
$matches=getMatches($pdo,$round['id']); $msg=''; $err=''; $selectedTeams = [];
if($_SERVER['REQUEST_METHOD']==='POST'){
    $name=trim($_POST['name']??''); $roll=trim($_POST['roll']??''); $department=trim($_POST['department']??'');
    if($round['status']!=='open') $err='This form is closed.';
    elseif($name===''||$roll===''||$department==='') $err='No field can be left empty.';
    else{
        foreach($matches as $m){ if(empty($_POST['match_'.$m['id']])) $err='Please select a winner for every match.'; }
    }
    if(!$err){
        try{
            $pdo->beginTransaction();
            $s=$pdo->prepare('INSERT INTO participants (name,roll,department,round_id) VALUES (?,?,?,?)');
            $s->execute([$name,$roll,$department,$round['id']]); $pid=$pdo->lastInsertId();
            $p=$pdo->prepare('INSERT INTO predictions (participant_id,match_id,selected_team) VALUES (?,?,?)');
            foreach($matches as $m){

    $selected = $_POST['match_'.$m['id']];

    $selectedTeams[] = [
        'match' => $m['team1'].' vs '.$m['team2'],
        'winner' => $selected
    ];

    $p->execute([$pid,$m['id'],$selected]);
}
            $pdo->commit(); $msg='Prediction submitted successfully!';
        }catch(PDOException $e){ $pdo->rollBack(); if(str_contains($e->getMessage(),'unique_round_roll')) $err='This roll number already submitted for this round.'; else $err='Database error: '.$e->getMessage(); }
    }
}
function counts($pdo,$match){
    $s=$pdo->prepare(
        'SELECT selected_team, COUNT(DISTINCT participant_id) c
         FROM predictions
         WHERE match_id=?
         GROUP BY selected_team'
    );

    $s->execute([$match['id']]);

    $arr=[$match['team1']=>0,$match['team2']=>0];

    foreach($s as $r){
        $arr[$r['selected_team']]=$r['c'];
    }

    return $arr;
}

?>
<!doctype html>
<html>
<head>
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=h($round['name'])?></title>
<link rel="stylesheet" href="style.css">
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
<h1><?=h($round['name'])?></h1>
<p>Status: <span class="badge <?=h($round['status'])?>"><?=strtoupper(h($round['status']))?></span></p><br>
<?php if($msg): ?><div class="msg success"><?=h($msg)?></div>
    <?php endif; ?>
    <?php if(!empty($selectedTeams)): ?>
<div class="card">
    <h2 style="color:#00e676;">Your Submitted Predictions</h2>
    <br>

    <?php foreach($selectedTeams as $prediction): ?>

        <p>
            <strong><?= h($prediction['match']) ?></strong><br>
            Winner: <b><?= h($prediction['winner']) ?></b>
        </p>

        <br>

    <?php endforeach; ?>

</div>
<?php endif; ?>
    <?php if($err): ?><div class="msg error"><?=h($err)?></div><?php endif; ?>
<?php if($round['status']==='open'): ?>

    <?php if(!$msg): ?>
<form method="post" class="card">
<label>Name</label><input name="name" required>
<label>Roll</label><input name="roll" required>
<label>Department</label><input name="department" required>
<?php foreach($matches as $m): ?>
    <div class="match">
    <h3>Match <?=h($m['match_no'])?>: <?=h($m['team1'])?> vs <?=h($m['team2'])?></h3>
    <div class="teams">
    <label class="radio-box"><input type="radio" name="match_<?=h($m['id'])?>" value="<?=h($m['team1'])?>" required><?=h($m['team1'])?></label>
    <div class="vs">VS</div>
    <label class="radio-box"><input type="radio" name="match_<?=h($m['id'])?>" value="<?=h($m['team2'])?>" required><?=h($m['team2'])?></label>
    </div>
    </div>
    <?php endforeach; ?>
<br><button class="btn" type="button" onclick="reviewPredictions()">Review Predictions</button>
<div class="modal" id="reviewModal">
    <div class="modal-box">
        <button class="close" type="button" onclick="closeReview()">×</button>
        <h2 class="review-title">Review Your Predictions</h2>
        <div class="review-body">
            <div id="reviewContent"></div>
            <br>
            <button class="btn" type="submit">Confirm & Submit</button>
            <button class="btn secondary" type="button" onclick="closeReview()">Go Back & Edit</button>
        </div>
    </div>
</div>
</form>
<?php endif; ?>
<?php elseif($round['status']==='closed'): ?>
    <h2>Voting Results</h2>
    <?php foreach($matches as $m): $c=counts($pdo,$m);$total=$c[$m['team1']]+$c[$m['team2']];$p1=$total?round($c[$m['team1']]/$total*100):0;$p2=100-$p1; ?>
    <div class="card"><h3><?=h($m['team1'])?> vs <?=h($m['team2'])?></h3>
    <div class="pie-row">
    <div class="pie" onclick="showPeople(<?=h($m['id'])?>,'<?=h($m['team1'])?>')" style="background:conic-gradient(#00e676 0 <?=$p1?>%, #40c4ff <?=$p1?>% 100%)"></div>
    <div class="legend">
    <div><b><?=h($m['team1'])?>:</b> <?=$p1?>%</div>
    <div><b><?=h($m['team2'])?>:</b> <?=$p2?>%</div>
    <button class="btn secondary small" onclick="showPeople(<?=h($m['id'])?>,'<?=h($m['team1'])?>')">Show <?=h($m['team1'])?> pickers</button> 
    <button class="btn secondary small" onclick="showPeople(<?=h($m['id'])?>,'<?=h($m['team2'])?>')">Show <?=h($m['team2'])?> pickers</button>
    </div>
    </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
    </main>
<div class="modal" id="modal">
<div class="modal-box">
<button class="close" onclick="closeModal()">×</button>
<h2 id="modalTitle"></h2>
<div id="modalBody"></div>
</div>
</div>
<script>
function showPeople(matchId, team){
    fetch('people.php?match_id='+matchId+'&team='+encodeURIComponent(team)).then(r=>r.text()).then(html=>{document.getElementById('modalTitle').innerText=team+' pickers';document.getElementById('modalBody').innerHTML=html;document.getElementById('modal').style.display='flex';});
    }
function closeModal(){
    document.getElementById('modal').style.display='none';
    }
</script>
<script>
function reviewPredictions(){
    const form = document.querySelector('form');
    const formData = new FormData(form);

    let name = formData.get('name');
    let roll = formData.get('roll');
    let department = formData.get('department');

    if(!name || !roll || !department){
        alert('Please fill in name, roll and department.');
        return;
    }

    let html = `
        <p><b>Name:</b> ${name}</p>
        <p><b>Roll:</b> ${roll}</p>
        <p><b>Department:</b> ${department}</p>
        <br>
        <h3>Your Picks</h3>
        <br>
    `;

    const matches = document.querySelectorAll('.match');

    for(let match of matches){
        let title = match.querySelector('h3').innerText;
        let selected = match.querySelector('input[type="radio"]:checked');

        if(!selected){
            alert('Please select a winner for every match.');
            return;
        }

        html += `
            <p>
                <b>${title}</b><br>
                Winner: <b>${selected.value}</b>
            </p>
            <br>
        `;
    }

    document.getElementById('reviewContent').innerHTML = html;
    document.getElementById('reviewModal').style.display = 'flex';
}

function closeReview(){
    document.getElementById('reviewModal').style.display = 'none';
}
</script>
</body>
</html>
