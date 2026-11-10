<?php
if(session_status()!==PHP_SESSION_ACTIVE){session_set_cookie_params(['httponly'=>true,'samesite'=>'Lax','secure'=>!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off']);session_start();}
require_once __DIR__.'/../config.php';
function e($v): string{return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
function require_role(string $r):void{if(empty($_SESSION['user_id'])||($_SESSION['role']??'')!==$r){header('Location: login.php');exit;}}
function csrf():string{if(empty($_SESSION['csrf']))$_SESSION['csrf']=bin2hex(random_bytes(32));return $_SESSION['csrf'];}
function check_csrf():void{if(!hash_equals($_SESSION['csrf']??'',$_POST['csrf']??'')){http_response_code(403);exit('Invalid form token. Refresh and retry.');}}
function notice(string $m,string $type='success'):void{$_SESSION['notice']=['m'=>$m,'t'=>$type];}
function show_notice():void{if(isset($_SESSION['notice'])){$n=$_SESSION['notice'];echo '<p class="notice '.e($n['t']).'">'.e($n['m']).'</p>';unset($_SESSION['notice']);}}
