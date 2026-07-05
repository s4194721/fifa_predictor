<?php
function getRounds($pdo){ return $pdo->query('SELECT * FROM rounds ORDER BY id')->fetchAll(); }
function getRoundByCode($pdo,$code){$s=$pdo->prepare('SELECT * FROM rounds WHERE code=?');$s->execute([$code]);return $s->fetch();}
function getMatches($pdo,$round_id){$s=$pdo->prepare('SELECT * FROM matches WHERE round_id=? ORDER BY match_no');$s->execute([$round_id]);return $s->fetchAll();}
function h($v){return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');}
function scoreFor($points,$selected,$correct){ if(!$correct) return 0; return $selected===$correct ? (float)$points : -((float)$points/2); }
?>
