<?php
	function gzfile_get_contents($filename, $use_include_path = 0)
	{
	    //File does not exist
	    if( !@file_exists($filename) )
	    {    return false;    }

	    //Read and imploding the array to produce a one line string
	   $data = gzfile($filename, $use_include_path);
	   $data = implode($data);
	   return $data;
	}

	function normalize($str){
    	return html_entity_decode( strval( $str ), ENT_QUOTES);
    }

	function safe($str, $normalize = true){
        $str = str_replace( '\\', '\\\\', $str);
        //$str = str_replace( '&#61692;', '&mdash;', $str);

        //$str = preg_replace('@\&amp;#\d+;@', '&mdash;', $str);


        if( $normalize )
            $str = normalize( $str );

    	$str = htmlspecialchars( $str, ENT_QUOTES);
    	$str = preg_replace('@\&amp;#\d+;@', '&mdash;', $str);

    	return $str;
    }

    function toUTF8($inStr)
	{
		return @iconv('windows-1251','utf-8',$inStr);
	}

?>