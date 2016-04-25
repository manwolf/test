<?php
class tokenGen extends spController {
	protected static function keyED($txt, $encrypt_key) {
		$encrypt_key = md5 ( $encrypt_key );
		$ctr = 0;
		$tmp = "";
		for($i = 0; $i < strlen ( $txt ); $i ++) {
			if ($ctr == strlen ( $encrypt_key ))
				$ctr = 0;
			$tmp .= substr ( $txt, $i, 1 ) ^ substr ( $encrypt_key, $ctr, 1 );
			$ctr ++;
		}
		//echo '$tmp2='.$tmp."<br>";
		return $tmp;
	}
	public static function encrypt($txt) {
		$txt = empty ( $txt ) ? tokenGen::randomkeys ( 16 ) : $txt;
		
		$key = 'eteacherGZZ';
		$encrypt_key = md5 ( (( float ) date ( "YmdHis" ) + rand ( 10000000000000000, 99999999999999999 )) . rand ( 100000, 999999 ) );
		
		$ctr = 0;
		$tmp = "";
		for($i = 0; $i < strlen ( $txt ); $i ++) {
			if ($ctr == strlen ( $encrypt_key ))
				$ctr = 0;
			$tmp .= substr ( $encrypt_key, $ctr, 1 ) . (substr ( $txt, $i, 1 ) ^ substr ( $encrypt_key, $ctr, 1 ));
			$ctr ++;
		}
		//echo '$tmp1='.$tmp."<br>";
		return base64_encode ( self::keyED ( $tmp, $key ) );
	}
	public static function randomkeys($length) {
		$pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
		for($i = 0; $i < $length; $i ++) {
			$key .= $pattern {mt_rand ( 0, 35 )}; // 生成php随机数
		}
		return $key;
	}
	public static function decrypt($txt) {
		$key = 'eteacherGZZ';
		$txt = self::keyED ( base64_decode ( $txt ), $key );
		$tmp = "";
		for($i = 0; $i < strlen ( $txt ); $i ++) {
			$md5 = substr ( $txt, $i, 1 );
			$i ++;
			$tmp .= (substr ( $txt, $i, 1 ) ^ $md5);
		}
		return $tmp;
	}
}


?> 
