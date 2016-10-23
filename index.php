<?php
mb_internal_encoding("UTF-8"); // 内部文字エンコーディング設定
$act = $_POST["act"];
$param["stamp_name"] = $_POST["name"];
$param["stamp_date"] = $_POST["date"];

function stamp_image_get( $param = array() ){
	$tmp_dir = "./tmp/";
	$font_type = "./font/msgothic.ttf";
	$imagefile_path = "./img/stamp_frame.png";
	$imagefile = imagecreatefrompng($imagefile_path);
	$text_color = imagecolorallocate($imagefile, 255, 0, 0);

	// 引数
	$stamp_date = $param["stamp_date"]==""?date("Y/m/d"):$param["stamp_date"];
	$stamp_name = $param["stamp_name"]==""?"櫻井":$param["stamp_name"];
	$unique = $param["unique"]==""?0:$param["unique"];
	$export_type = $param["export_type"]==""?"file":$param["export_type"];

	// 日付
	$font_size = 9;
	$font_angle = 0;
	imagettftext( $imagefile, $font_size, $font_angle, 8, 42, $text_color, $font_type, $stamp_date );

	// スタンプネーム
	$font_size = 11;
	$font_angle = 0;
	switch( mb_strlen($stamp_name) ){
	case 1:
		imagettftext( $imagefile, $font_size, $font_angle, 29, 20, $text_color, $font_type, $stamp_name );
		break;
	case 2:
		imagettftext( $imagefile, $font_size, $font_angle, 29, 20, $text_color, $font_type, mb_substr($stamp_name,0,1) );
		imagettftext( $imagefile, $font_size, $font_angle, 29, 64, $text_color, $font_type, mb_substr($stamp_name,1,1) );
		break;
	case 3:
		imagettftext( $imagefile, $font_size, $font_angle, 22, 20, $text_color, $font_type, mb_substr($stamp_name,0,2) );
		imagettftext( $imagefile, $font_size, $font_angle, 29, 64, $text_color, $font_type, mb_substr($stamp_name,2,1) );
		break;
	case 4:
		imagettftext( $imagefile, $font_size, $font_angle, 22, 20, $text_color, $font_type, mb_substr($stamp_name,0,2) );
		imagettftext( $imagefile, $font_size, $font_angle, 22, 64, $text_color, $font_type, mb_substr($stamp_name,2,2) );
		break;
	}

	// 出力
	if( $export_type == "file" ){
		$tmp_stamp_path = sprintf( "%stmp_stamp_%s.png", $tmp_dir, $unique);
		imagepng($imagefile, $tmp_stamp_path);
		imagedestroy($imagefile);
		return $tmp_stamp_path;
	}elseif( $export_type == "nofile" ){
		imagepng($imagefile);
		imagedestroy($imagefile);
	}
}

// 画面に表示
if( $act == "nofile" ){
	header("content-type: image/png");
	$param["export_type"] = "nofile";
	stamp_image_get($param);
	exit;
}
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<script type="text/javascript">
function on_submit(act){
	document.getElementById('act').value = act;
	document.fm.submit();
}
</script>
</head>
<body>
<form name="fm" method="POST">
<input type="hidden" name="act" id="act" />
<table>
<tr><td>name</td><td><input type="text" name="name" value="<?=htmlspecialchars( $_POST['name'] )?>" /></td></tr>
<tr><td>date</td><td><input type="text" name="date" value="<?=htmlspecialchars( $_POST['date'] )?>" /></td></tr>
<tr><td></td><td>
<input type="button" value="画面に表示" onclick="on_submit('nofile');" />
<input type="button" value="ファイル生成" onclick="on_submit('file');" />
</td></tr>
</table>
</form>
<?php
if( $act == "file" ){
	$param["export_type"] = "file";
	$path = stamp_image_get($param);
	printf('<a href="%s" target="_blank">Image Path</a>', $path );
}
?>
</body>
</html>