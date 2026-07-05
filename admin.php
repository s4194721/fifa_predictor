<?php
session_start(); require 'db.php'; require 'functions.php';
if(isset($_POST['admin_pass'])){ 
    if($_POST['admin_pass']===$admin_password) $_SESSION['admin']=true; 
    else $login_error='Wrong password'; }
if(isset($_GET['logout'])){
    session_destroy();
    header('Location: admin.php');
    exit;}
if(empty($_SESSION['admin'])): ?>
<!doctype html>
<html>
    <head>
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Admin Login</title>
        <link rel="stylesheet" href="style.css">
        </head>
        <body>
            <main class="container">
                <div class="card" style="max-width:420px;margin:80px auto">
                    <h1>Admin Login</h1>
                    <?php if(!empty($login_error)):?>
                    <div class="msg error"><?=$login_error?>
                    </div>
                    <?php endif;?>
                    <form method="post">
                        <label>Password</label>
                        <input type="password" name="admin_pass" required><br><br>
                        <button class="btn">Login</button>
                        </form>
                        </div>
                        </main>
                        </body>
                        </html><?php exit; 
                        endif;
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action'])){
 if($_POST['action']==='status'){$s=$pdo->prepare('UPDATE rounds SET status=? WHERE id=?');$s->execute([$_POST['status'],$_POST['round_id']]);}
 if($_POST['action']==='teams'){$s=$pdo->prepare('UPDATE matches SET team1=?, team2=? WHERE id=?');
 foreach($_POST['team1'] as $id=>$t1){$s->execute([$t1,$_POST['team2'][$id],$id]);}}
 if($_POST['action']==='results'){$s=$pdo->prepare('UPDATE matches SET correct_team=? WHERE id=?');
 foreach($_POST['winner'] as $id=>$w){$s->execute([$w?:null,$id]);}}
 header('Location: admin.php');
 exit;
}
$rounds=getRounds($pdo);
?>
<!doctype html>
<html>
    <head>
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Admin</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <header>
            <div class="topbar">
                <div class="logo">Admin Panel</div>
                <div class="nav"><a href="index.php">Home</a>
                <a href="leaderboard.php">Leaderboard</a>
                <a href="admin.php?logout=1">Logout</a>
            </div>
        </div>
    </header>
    <main class="container">
        <h1>Admin Panel</h1>
        <p>Open/close forms, update teams, and set correct answers here.</p><br>
<div class="admin-grid">
    <?php foreach($rounds as $r): $matches=getMatches($pdo,$r['id']); ?>
    <div class="card">
        <h2><?=h($r['name'])?></h2>
        <p>Status: <span class="badge <?=h($r['status'])?>"><?=h($r['status'])?></span></p>
        <form method="post"><input type="hidden" name="action" value="status">
        <input type="hidden" name="round_id" value="<?=h($r['id'])?>">
        <label>Change status</label>
        <select name="status">
            <option value="open" <?=$r['status']==='open'?'selected':''?>>open</option>
            <option value="closed" <?=$r['status']==='closed'?'selected':''?>>closed</option>
        </select><br><br>
        <button class="btn small">Save Status</button>
    </form>
</div>
    <?php endforeach; ?>
</div>
<h2>Update Teams</h2>
<form method="post" class="card">
    <input type="hidden" name="action" value="teams">
    <?php foreach($rounds as $r): ?>
        <h3><?=h($r['name'])?></h3>
        <?php foreach(getMatches($pdo,$r['id']) as $m): ?>
            <div class="match"><b>Match <?=h($m['match_no'])?></b>
            <label>Team 1</label>
            <input name="team1[<?=h($m['id'])?>]" value="<?=h($m['team1'])?>" required>
            <label>Team 2</label>
            <input name="team2[<?=h($m['id'])?>]" value="<?=h($m['team2'])?>" required>
        </div>
        <?php endforeach; ?><?php endforeach; ?>
        <br>
        <button class="btn">Save Teams</button>
    </form>
<h2>Set Correct Answers</h2>
<form method="post" class="card">
    <input type="hidden" name="action" value="results">
    <?php foreach($rounds as $r): ?><h3><?=h($r['name'])?></h3>
        <?php foreach(getMatches($pdo,$r['id']) as $m): ?>
            <label>Match <?=h($m['match_no'])?>: <?=h($m['team1'])?> vs <?=h($m['team2'])?></label>
            <select name="winner[<?=h($m['id'])?>]">
                <option value="">Not fixed yet</option>
                <option value="<?=h($m['team1'])?>" <?=$m['correct_team']===$m['team1']?'selected':''?>><?=h($m['team1'])?></option>
                <option value="<?=h($m['team2'])?>" <?=$m['correct_team']===$m['team2']?'selected':''?>><?=h($m['team2'])?></option>
            </select><?php endforeach; ?><?php endforeach; ?><br><br>
            <button class="btn">Save Correct Answers</button>
        </form>
    </main>
</body>
</html>
