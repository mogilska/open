<?

$OP->inc("inc/chat_rend.php");
$OP->inc("inc/notify.php");
$OP->init();
$msg=$OP->format($_POST['msg'], true);
$to=$_POST['to'];
$udte=false;
if($_P && $msg!="" && $to!=""){
 $sql=$OP->dbh->prepare("SELECT fid FROM conn WHERE uid=:who AND fid=:fid AND uid IN (SELECT fid FROM conn WHERE uid=:fid)");
 $sql->execute(array(":who"=>$who, ":fid"=>$to));
 if($sql->rowCount()==0){
  $OP->ser();
 }
 $sql=$OP->dbh->prepare("SELECT uid,msg FROM chat WHERE (uid=? AND fid=?) OR (uid=? AND fid=?) ORDER BY id DESC LIMIT 1");
 $sql->execute(array($who, $to, $to, $who));
 while($r=$sql->fetch()){
  $lu=$r['uid'];
  $lm=$r['msg'];
 }
 if($lu==$who){
  $sql=$OP->dbh->prepare("UPDATE chat SET msg=?,red='0',posted=NOW() WHERE uid=? AND fid=? ORDER BY id DESC LIMIT 1");
  $sql->execute(array($lm."<br/>".$msg, $who, $to));
  $udte=true;
 }else{
  $sql=$OP->dbh->prepare("INSERT INTO chat (uid,fid,msg,posted) VALUES (?,?,?,NOW())");
  $sql->execute(array($who, $to, $msg));
 }
 if(get("status", $to)=="off"){
  notify("msg", $msg,0, $to, $who);
 }
 $sql=$OP->dbh->prepare("SELECT id FROM chat WHERE uid=? AND fid=? ORDER BY id DESC LIMIT 1");
 $sql->execute(array($who, $to));
 while($r=$sql->fetch()){
  $cid=$r['id'];
 }
 $ht=$OP->rendFilt(show_chat($to, $cid));
?>
  p="<?echo$ht;?>";
  <?if($udte==false){?>
   if($("#<?echo$to;?>.msgs .msg").length==0){
    $("#<?echo$to;?>.msgs").html(p);
   }else{
    $("#<?echo$to;?>.msgs").append(p);
   }
  <?}else{?>
   $("#<?echo$cid;?>.msg").replaceWith(p);
  <?}?>
  $("#<?echo$to;?>.chat_form")[0].reset();mcTop();
<?
}else{
 $OP->ser();
}
?>
