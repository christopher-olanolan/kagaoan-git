<?
class UnicodeReplace {
	public function UTF8entities($content="") {
	    $contents = $this->unicodeStringToArray($content);
	    $swap = "";
	    $iCount = count($contents);
		
	    for ($o=0;$o<$iCount;$o++):
	        $contents[$o] = $this->unicodeEntityReplace($contents[$o]);
	        $swap .= $contents[$o];
		endfor;
		
	    return mb_convert_encoding($swap, "UTF-8");
	}
	
	public function unicodeStringToArray($string) {
	    $strlen = mb_strlen($string);
	    
	    while ($strlen):
	        $array[] = mb_substr( $string, 0, 1, "UTF-8" );
	        $string = mb_substr( $string, 1, $strlen, "UTF-8" );
	        $strlen = mb_strlen( $string );
	    endwhile;
		
	    return $array;
	}
	
	public function unicodeEntityReplace($c) {
	    $h = ord($c{0});
	    
	    if ($h <= 0x7F):
	        return $c;
	    elseif ($h < 0xC2):
			return $c;
		endif;
	
	    if ($h <= 0xDF):
	        $h = ($h & 0x1F) << 6 | (ord($c{1}) & 0x3F);
	        $h = "&#" . $h . ";";
			
	        return $h;
	    elseif ($h <= 0xEF):
			$h = ($h & 0x0F) << 12 | (ord($c{1}) & 0x3F) << 6 | (ord($c{2}) & 0x3F);
			$h = "&#" . $h . ";";
			
			return $h;
		elseif ($h <= 0xF4):
			$h = ($h & 0x0F) << 18 | (ord($c{1}) & 0x3F) << 12 | (ord($c{2}) & 0x3F) << 6 | (ord($c{3}) & 0x3F);
			$h = "&#" . $h . ";";
			
			return $h;
		endif;
	}
}
?>