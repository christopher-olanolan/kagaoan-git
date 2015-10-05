<?
if (!defined('__CONTROL__')) die ("You Cannot Access This Script Directly");

function redirect($time, $topage) {
	echo "<meta http-equiv=\"refresh\" content=\"{$time}; url={$topage}\" /> ";
}

function encryption($string){
	$new = base64_encode(base64_encode(base64_encode(base64_encode(base64_encode(base64_encode($string))))));
	return $new;
}

function decryption($string){
	$new = base64_decode(base64_decode(base64_decode(base64_decode(base64_decode(base64_decode($string))))));
	return $new;
}

function printr($data){
	echo "<pre>";
	print_r($data);
	echo "</pre><br />";
}

function base64encode ($filename = string) {
    if (file_exists($filename)) {
		$handle = fopen($filename, "rb");
		$img = fread($handle, filesize($filename));
		fclose($handle);
        $string = chunk_split(base64_encode($img));
		//$string = preg_replace('/(.{64})/', '$1\n', $string);
		return $string;
    }
}

function base64_url_decode($input) {
    return base64_decode(strtr($input, '-_', '+/'));
}

function stringLimit($x){
  if(strlen($x)<= 40){
    return $x;
  } else {
    $y = substr($x,0,40) . '...';
    return $y;
  }
}

function randomPassword() {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); 
    $alphaLength = strlen($alphabet) - 1; 
    
    for ($i=0; $i<8; $i++):
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
	endfor;
    return implode($pass); 
}

function parse_signed_request($signed_request, $secret) {
  list($encoded_sig, $payload) = explode('.', $signed_request, 2); 

  // decode the data
  $sig = base64_url_decode($encoded_sig);
  $data = json_decode(base64_url_decode($payload), true);

  if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
    error_log('Unknown algorithm. Expected HMAC-SHA256');
    return null;
  }

  // check sig
  $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
  if ($sig !== $expected_sig) {
    error_log('Bad Signed JSON signature!');
    return null;
  }

  return $data;
}

function loading_page(){
	if (ob_get_level() == 0) {
		ob_start();
	}
	echo str_pad('Loading... ',4096)."<br />\n";
	
	for ($i = 0; $i < 25; $i++) {
		$d = $d + 11;
		$m=$d+10;
		//This div will show loading percents
		echo '<div class="percents">' . $i*4 . '%&nbsp;complete</div>';
		//This div will show progress bar
		echo '<div class="blocks" style="left: '.$d.'px">&nbsp;</div>';
		flush();
		ob_flush();
		sleep(1);
	}
	
	ob_end_flush();
}

function getRealIP(){
    if (!empty($_SERVER['HTTP_CLIENT_IP'])):   //check ip from share internet
		$ip = $_SERVER['HTTP_CLIENT_IP'];
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])):   //to check ip is pass from proxy
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else:
      $ip = $_SERVER['REMOTE_ADDR'];
	endif;
	
	return $ip;
}

function arrayCSV($file,$list){
	header( 'Content-Type: text/csv' );
    header( 'Content-Disposition: attachment;filename='.$file);
    $fp = fopen('php://output', 'w');
	foreach ($list as $fields):
	    fputcsv($fp, $fields);
	endforeach;
	fclose($fp);
}

function queryCSV($db_conn, $query, $filename, $attachment = false, $headers = true) {
    if ($attachment):
        // send response headers to the browser
        header( 'Content-Type: text/csv' );
        header( 'Content-Disposition: attachment;filename='.$filename);
        $fp = fopen('php://output', 'w');
	else:
        $fp = fopen($filename, 'w');
	endif;
   
    $result = mysql_query($query, $db_conn) or die( mysql_error( $db_conn ) );
   
    if ($headers):
        // output header row (if at least one row exists)
        $row = mysql_fetch_assoc($result);
        if($row):
            fputcsv($fp, array_keys($row));
            // reset pointer back to beginning
            mysql_data_seek($result, 0);
		endif;
	endif;
   
    while($row = mysql_fetch_assoc($result)):
        fputcsv($fp, $row);
	endwhile;
   
    fclose($fp);
}

function actionButtons($module_access,$request_id,$status_id,$user_epms_id,$_EPMS_ID,$control){
	$actionButtons ="";

	if ($module_access[$control]['access'] == 1):
        $actionButtons .="<input type=\"button\" class=\"clean previewPop ico ico_preview\" name=\"action[single-preview]\" id=\"preview_".$request_id."\" value=\"".$request_id."\" /> ";
		// $actionButtons .="<input type=\"button\" class=\"clean printPop print ico ico_print\" rel=\"\" name=\"action[single-print]\" id=\"print_".$request_id."\" value=\"".$request_id."\" /> ";
		
		if ($module_access[$control]['publish'] == 1 && $status_id == '4'):
			$actionButtons .="<input type=\"submit\" class=\"clean forpublishPop ico ico_forpublish\" name=\"action[single-forpublish]\" id=\"forpublish_".$request_id."\" value=\"".$request_id."\" /> ";
		endif;
		
		if ($module_access[$control]['publish'] == 1 && $status_id == '8'):
			$actionButtons .="<input type=\"submit\" class=\"clean publishPop ico ico_publish\" name=\"action[single-publish]\" id=\"publish_".$request_id."\" value=\"".$request_id."\" /> ";
		endif;
    
		if ($module_access[$control]['edit'] == 1 && $user_epms_id == $_EPMS_ID):
	    	if ($status_id == '1' || $status_id == '6'):
	    		$actionButtons .="<input type=\"button\" class=\"clean editPop ico ico_edit\" name=\"action[single-edit]\" id=\"edit_".$request_id."\" value=\"".$request_id."\" /> ";
	    	endif;
	    	
	    	if ($status_id == '1'):
	    		$actionButtons .="<input type=\"submit\" class=\"clean forapprovalPop ico ico_forapproval\" name=\"action[single-forapproval]\" id=\"forapproval_".$request_id."\" value=\"".$request_id."\" /> ";
	    	elseif ($status_id == '7' || $status_id == '6'):
	    		$actionButtons .="<input type=\"submit\" class=\"clean draftPop ico ico_draft\" name=\"action[single-draft]\" id=\"draft_".$request_id."\" value=\"".$request_id."\" /> ";
	    	endif;
	    endif;
	    
	    if ($module_access[$control]['approve'] == 1 && $status_id == '2'): 
	    		$actionButtons .="<input type=\"submit\" class=\"clean approvePop ico ico_approve\" name=\"action[single-approve]\" id=\"approve_".$request_id."\" value=\"".$request_id."\" /> ";
	    		$actionButtons .="<input type=\"submit\" class=\"clean rejectPop ico ico_reject\" name=\"action[single-reject]\" id=\"reject_".$request_id."\" value=\"".$request_id."\" /> ";
	    endif;
	    
	    if ($module_access[$control]['endorse'] == 1 && $status_id == '3'): 
	    		$actionButtons .="<input type=\"submit\" class=\"clean endorsePop ico ico_active\" name=\"action[single-endorse]\" id=\"endorse_".$request_id."\" value=\"".$request_id."\" /> ";
	    		$actionButtons .="<input type=\"submit\" class=\"clean cancelPop ico ico_cancelled\" name=\"action[single-cancel]\" id=\"cancel_".$request_id."\" value=\"".$request_id."\" /> ";
	    endif;
	    
	    if ($module_access[$control]['delete'] == 1 && $status_id == '1' && $user_epms_id == $_EPMS_ID): 
	    	$actionButtons .="<input type=\"submit\" class=\"clean deletePop ico ico_delete\" name=\"action[single-delete]\" id=\"delete_".$request_id."\" value=\"".$request_id."\" /> ";
	    endif;
	endif;
	
	return $actionButtons;
}

function previewButtons($module_access,$request_id,$status_id,$epms_id,$_EPMS_ID,$control){
	$previewButtons ="";

	$previewButtons .=" <input type=\"button\" class=\"clean button small_button float_left print\" rel=\"printarea\" name=\"action[single-print]\" id=\"print\" value=\"Print\" />";
	$previewButtons .="<input name=\"back\" type=\"button\" value=\"Back\" class=\"button small_button back\" id=\"back\" /> ";
	
	if ($module_access[$control]['access'] == 1):
		
		if ($module_access[$control]['publish'] == 1 && $status_id == '4'):
			$previewButtons .="<input type=\"submit\" class=\"small_button button\" name=\"action[preview-forpublish]\" id=\"forpublish_".$request_id."\" value=\"For Publishing\" /> ";
		endif;
		
		if ($module_access[$control]['publish'] == 1 && $status_id == '8'):
			$previewButtons .="<input type=\"submit\" class=\"small_button button\" name=\"action[preview-publish]\" id=\"publish_".$request_id."\" value=\"Published\" /> ";
		endif;
		
		if ($module_access[$control]['edit'] == 1 && $epms_id == $_EPMS_ID):
        	if ($status_id == '1' || $status_id == '6'):
        		$previewButtons .="<input type=\"button\" class=\"small_button button edit\" value=\"Edit\" /> ";
        	endif;
                	
        	if ($status_id == '1'):
        		$previewButtons .="<input type=\"submit\" class=\"small_button button\" name=\"action[preview-forapproval]\" id=\"forapproval_".$request_id."\" value=\"For Approval\" /> ";
        	elseif ($status_id == '7' || $status_id == '6'):
        		$previewButtons .="<input type=\"submit\" class=\"small_button button\" name=\"action[preview-draft]\" id=\"draft_".$request_id."\" value=\"Save as Draft\" /> ";
        	endif;
        endif;
        
        if ($module_access[$control]['approve'] == 1 && $status_id == '2'):
        	if ($status_id != '3'):
        		$previewButtons .="<input type=\"submit\" class=\"small_button button\" name=\"action[preview-approve]\" value=\"Approve\" id=\"approve_".$request_id."\" /> ";
        	endif;
        	
        	if ($status_id != '6'):
        		$previewButtons .="<input type=\"submit\" class=\"small_button button\" name=\"action[preview-reject]\" value=\"Reject\" id=\"reject_".$request_id."\" /> ";
        	endif;
        endif;
		
        if ($module_access[$control]['endorse'] == 1 && $status_id == '3'):
        	if ($status_id != '4'):
        		$previewButtons .="<input type=\"submit\" class=\"small_button button\" name=\"action[preview-endorse]\" value=\"Endorse\" id=\"endorse_".$request_id."\" /> ";
        	endif;
        	
        	if ($status_id != '5'):
        		$previewButtons .="<input type=\"submit\" class=\"small_button button\" name=\"action[preview-cancel]\" value=\"Cancel\" id=\"cancel_".$request_id."\" /> ";
        	endif;
        endif;
        
        if ($module_access[$control]['delete'] == 1 && $status_id == '1' && $epms_id == $_EPMS_ID):
        	$previewButtons .="<input type=\"submit\" class=\"small_button button\" name=\"action[preview-delete]\" value=\"Delete\" id=\"delete_".$request_id."\" /> ";
        endif;
     endif;

	return $previewButtons;
}

function paginate($page,$prev,$next,$max,$end,$pos){
	$pagination = "";
	if ($page > 1):
    	$pagination .="<input type=\"button\" class=\"navigation pagination_button nav-first\" value=\"|â—„\" id=\"nav-".$pos."-first\">";
    endif;
    
	if ($prev >= 0):
    	$pagination .="<input type=\"button\" class=\"navigation pagination_button nav-prev\" value=\"Prev â—„\" id=\"nav-".$pos."-prev\">";
    endif;
    
    // FIRST 2 PAGINATION
    if ($page-2 >= 0):
		$pagination .="
		<script type=\"text/javascript\">
		$(document).ready(function() {
			$(\"#nav-".$pos."-page-".($page-2)."\").click(function() {
				ajaxLoad(loadURL(".($page-2).",0),\"GET\");
			});
		});
		</script>
		<input type=\"button\" class=\"navigation pagination_button\" value=\"".($page-1)."\" id=\"nav-".$pos."-page-".($page-2)."\">";
	endif;
	
	if ($page-1 >= 0):
		$pagination .="
		<script type=\"text/javascript\">
		$(document).ready(function() {
			$(\"#nav-".$pos."-page-".($page-1)."\").click(function() {
				ajaxLoad(loadURL(".($page-1).",0),\"GET\");
			});
		});
		</script>
		<input type=\"button\" class=\"navigation pagination_button\" value=\"".($page)."\" id=\"nav-".$pos."-page-".($page-1)."\">";
	endif;
	
    // CURRENT +3 PAGINATION
    for ($i=$page,$n=$page+1,$after=$page+3;$i<$after && $i<$max;$i++,$n++):
		$pagination .="
		<script type=\"text/javascript\">
		$(document).ready(function() {
			$(\"#nav-".$pos."-page-".$i."\").click(function() {
				ajaxLoad(loadURL(".$i.",0),\"GET\");
			});
		});
		</script>
		<input type=\"button\" class=\"navigation".($page==$i?'-active':'')." pagination_button\" value=\"".$n."\" id=\"nav-".$pos."-page-".$i."\">";
	endfor;
	
	// LAST 2 PAGINATION
	$last = $end-6;
	if ($page < $last):
		$pagination .="
		<script type=\"text/javascript\">
		$(document).ready(function() {
			$(\"#nav-".$pos."-page-".$end."\").click(function() {
				ajaxLoad(loadURL(".$end.",0),\"GET\");
			});

			$(\"#nav-".$pos."-page-".($end-1)."\").click(function() {
				ajaxLoad(loadURL(".($end-1).",0),\"GET\");
			});
		});
		</script>
		... 
		<input type=\"button\" class=\"navigation pagination_button\" value=\"".$end."\" id=\"nav-".$pos."-page-".($end-1)."\"> 
		<input type=\"button\" class=\"navigation pagination_button\" value=\"".($end+1)."\" id=\"nav-".$pos."-page-".$end."\">";
	endif;

	// NEXT PAGINATION
	if ($next < $max): 
		$pagination .="<input type=\"button\" class=\"navigation pagination_button nav-next\" value=\"Next â–º\" id=\"nav-".$pos."-next\">";
    endif;
	
	if ($next < $max-2):
    	$pagination .="<input type=\"button\" class=\"navigation pagination_button nav-end\" value=\"â–º|\" id=\"nav-".$pos."-end\">";
	endif;
	
	return $pagination;
}

/**
 *
 * $tmpname = $_FILES['source']['tmp_name'];   
 * $size - max width size
 * $save_dir - destination folder
 * $save_name - tnumb new name
 *
 * @img_resize( $_FILES['source']['tmp_name'] , 200 , "../album" , "thumb_album.jpg"); 
 * 
 */ 
 
function imgResize($tmpname, $size, $save_dir, $save_name) {
    $save_dir .= (substr($save_dir,-1) != "/") ? "/" : "";
    $gis = getimagesize($tmpname);
    $type = $gis[2];
    
    switch($type):
        case "1": $imorig = imagecreatefromgif($tmpname); 	break;
        case "2": $imorig = imagecreatefromjpeg($tmpname);	break;
        case "3": $imorig = imagecreatefrompng($tmpname); 	break;
        default:  $imorig = imagecreatefromjpeg($tmpname);	break;
	endswitch;

    $x = imagesx($imorig);
    $y = imagesy($imorig);

	if($gis[0] <= $size):
        $aw = $x;
        $ah = $y;
	else:
		$yc = $y*1.3333333;
		$d = $x>$yc ? $x:$yc;
		$c = $d>$size ? $size/$d : $size;
		$av = $x*$c; 
		$ah = $y*$c; 
	endif;

	$im = imagecreate($av,$ah); 
    $im = imagecreatetruecolor($av,$ah);
	// imagefill($im, 0, 120 - 1, imagecolorallocate($im, 255, 255, 255));
	// imagefill($im, 0, 0, imagecolorallocate($im, 255, 255, 255));
	
    if (imagecopyresampled($im,$imorig,0,0,0,0,$aw,$ah,$x,$y)):
        if (imagejpeg($im,$save_name)):
			imagedestroy($im);
        	imagedestroy($imorig);
            return true;
		else:
			imagedestroy($im);
        	imagedestroy($imorig);
			return false;
		endif;
	endif;
}

/* $num = 4; 
	$zerofill= 3; 
 	returns "004"
*/

function zerofill ($num,$zerofill) {
    while (strlen($num)<$zerofill) {
        $num = "0".$num;
    }
    return $num;
}

function getTimeAgo($tm,$rcs = 0) {
   $cur_tm = time(); $dif = $cur_tm-$tm;
   $pds = array('second','minute','hour','day','week','month','year','decade');
   $lngh = array(1,60,3600,86400,604800,2630880,31570560,315705600);
   for($v = sizeof($lngh)-1; ($v >= 0)&&(($no = $dif/$lngh[$v])<=1); $v--); if($v < 0) $v = 0; $_tm = $cur_tm-($dif%$lngh[$v]);

   $no = floor($no); if($no <> 1) $pds[$v] .='s'; $x=sprintf("%d %s ",$no,$pds[$v]);
   if(($rcs == 1)&&($v >= 1)&&(($cur_tm-$_tm) > 0)) $x .= getTimeAgo($_tm);
   return $x.' ago';
}

function getTimeDifference($datetime){
	$m = time()-$datetime; 
	$o = 'Just now';
    $t = array(
    	'year' => 31556926,
    	'month' => 2629744,
    	'week' => 604800,
		'day' => 86400,
		'hour' => 3600,
		'minute' => 60,
		'second' => 1
	);
	
    foreach($t as $u=>$s):
		if($s<=$m):
			$v = floor($m/$s);
			$o = "$v $u".($v==1?'':'s').' ago';
			break;
		endif;
    endforeach;
		
    return $o;
}

function cleanText($str){
	$str = utf8_encode($str);
	
	$str = str_replace("Ã‘" ,"&#209;", $str);
	$str = str_replace("Ã±" ,"&#241;", $str);
	$str = str_replace("Ã±" ,"&#241;", $str);
	$str = str_replace("Ã�","&#193;", $str);
	$str = str_replace("Ã¡","&#225;", $str);
	$str = str_replace("Ã‰","&#201;", $str);
	$str = str_replace("Ã©","&#233;", $str);
	$str = str_replace("Ãº","&#250;", $str);
	$str = str_replace("Ã¹","&#249;", $str);
	$str = str_replace("Ã�","&#205;", $str);
	$str = str_replace("Ã­","&#237;", $str);
	$str = str_replace("Ã“","&#211;", $str);
	$str = str_replace("Ã³","&#243;", $str);
	$str = str_replace("â€œ","&#8220;", $str);
	$str = str_replace("â€�","&#8221;", $str);
	
	$str = str_replace("â€˜","&#8216;", $str);
	$str = str_replace("â€™","&#8217;", $str);
	$str = str_replace("â€”","&#8212;", $str);
	
	$str = str_replace("â€“","&#8211;", $str);
	$str = str_replace("â„¢","&trade;", $str);
	$str = str_replace("Ã¼","&#252;", $str);
	$str = str_replace("Ãœ","&#220;", $str);
	$str = str_replace("ÃŠ","&#202;", $str);
	$str = str_replace("Ãª","&#238;", $str);
	$str = str_replace("Ã‡","&#199;", $str);
	$str = str_replace("Ã§","&#231;", $str);
	$str = str_replace("Ãˆ","&#200;", $str);
	$str = str_replace("Ã¨","&#232;", $str);
	$str = str_replace("â€¢","&#149;" , $str);
	
	$str = str_replace("Â¼","&#188;" , $str);
	$str = str_replace("Â½","&#189;" , $str);
	$str = str_replace("Â¾","&#190;" , $str);
	$str = str_replace("Â½","&#189;" , $str);
	
	return utf8_decode($str);
}

function logger($str){
	echo  "<script>console.log({$str});</script>";
}
?>