<?
$XLS2ARRAYerr=0;

define("XLS_DIMENSIONS",0x0000); //& 0x0200
define("XLS_BLANK",		0x0001); //& 0x0201
define("XLS_INTEGER",	0x0002);
define("XLS_NUMBER",	0x0003); //& 0x0203
define("XLS_LABEL",		0x0004); //& 0x0204
define("XLS_BOOLERR",	0x0005); //& 0x0205
define("XLS_FORMULA",	0x0006); //& 0x0206, 0x0406
define("XLS_STRING",	0x0007); //& 0x0207
define("XLS_BOF", 		0x0009); //& 0x0[2,4,8]07
define("XLS_EOF", 		0x000A);
define("XLS_CONTINUE",	0x003C);

define("XLS_RK",		0x007E); //only 0x027E
define("XLS_MULRK", 	0x00BD);
define("XLS_MULBLANK",	0x00BE);
define("XLS_SST",		0x00FC);
define("XLS_LABELSST",	0x00FD);

function myfind($str1, $str2){
	$len = strlen($str2);
	$pos=0;
	while(strlen($str1)>=$len){
		if(!strncmp($str1, $str2, $len))
			return $pos;
		$str1 = substr($str1, 1);
		$pos++;
	}
	return -1;
}

function unpackV($str){
	return hexdec(bin2hex(strrev($str)));
}

function EUnicodeToWin($str){
	$j = strlen($str) / 2;
	$outstr = '';
	for($i=0; $i<$j; $i++){
		$hb=ord(substr($str,2*$i+1,1))*256;
		$lh = ord(substr($str,2*$i,1));
		$b = ($hb>0) ? $hb+$lh-848:$lh;
		$outstr .= chr($b);
	}
	return $outstr;		
}


function parse_excel($dir)  {
	global $XLS2ARRAYerr;
    if(!($fr = fopen($dir, 'rb'))) return false;
	$file_size    = filesize($dir);
	$find_string  = "W\x00o\x00r\x00k\x00b\x00o\x00o\x00k\x00";
	$find_string .= str_repeat("\x00", 64-strlen($find_string));
	$find_string .= "\x12\x00\x02\x01\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff";
	$find_string .= str_repeat("\x00", 36);
	//search BIFF8
	$length=0;
	unset($offset);
	$len = 0;
	$blocklen = 1024;
    while (!isset($offset))  {
		$excel_block='';
		$fpos = ftell($fr);
		$blen = $blocklen;
		if($fpos+$blen>$file_size)
			$blen = $file_size-$fpos;
		if ($blen>0)  {
			$excel_block = fread($fr, $blen);
			$pos = myfind($excel_block, $find_string);
			if($pos>=0){
				$m1 = substr($excel_block, $pos+strlen($find_string), 4);
				$m2 = substr($excel_block, $pos+strlen($find_string)+4, 4);
				$offset=512*(unpackV($m1)+1);
				$length=unpackV($m2);
			}
		}
		else // no found
			$offset=-1;
	}
	if($offset==-1) {
		// opps, next search BIFF5-7
		$find_string  = "B\x00o\x00o\x00k\x00";
		$find_string .= str_repeat("\x00", 64-strlen($find_string));
		$find_string .= "\x0A\x00\x02\x01\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff";
		$find_string .= str_repeat("\x00", 36);

		unset($offset);
		fseek($fr,0); // set again on file begininng
	    while (!isset($offset))  {
			$excel_block='';
			if ($excel_block = fread($fr, 512))  {
				$pos = myfind($excel_block, $find_string);
				if($pos>=0){
					$m1 = substr($excel_block, $pos+strlen($find_string), 4);
					$m2 = substr($excel_block, $pos+strlen($find_string)+4, 4);
					$offset=512*(unpackV($m1)+1);
					$length=unpackV($m2);
				}
			}
			else {
				// opps again! i'm dieng
				$offset=-1;
				$XLS2ARRAYerr=1;
				return false; //die("Can't find spreadsheet!");
			}
		}
	}

	$sheets=parse($fr,$offset,$length);
	fclose($fr);
	return $sheets;
}


function RK_to_num($RK) {
	$type = $RK & 0x3; // flags
//$type & 2 >0 - integer value
//$type & 2 =0 - floating value
	$val = ($type & 2)? $RK>>2 : implode('',unpack("d", "\0\0\0\0".pack("V", $RK^$type)));
//$type & 1 >0 - must be devided by 100;
	if($type &1) $val /= 100.0;
	return $val;
}

function GetFlags($ofs){
	$bUni = ($ofs & 1)!=0;
	$bFE  = ($ofs & 4)!=0;
	$bRT  = ($ofs & 8)!=0;
	return array($bUni, $bFE, $bRT);
}


function parse ($file, $offset, $fsize){
   $sheet=0;
   $sheets=array();

	$o = 0; // it's offset into worksheet
	while ($o<$fsize) {
		fseek($file,$offset+$o,0);
		$rtype = get_word(fread($file,2),0);
		$type = $rtype & 0x0FF;
		$l = unpackV(fread($file,2));
		$buf = ($l>0) ? fread($file,$l) : "";
		$o+=4; // plus len of header

		if (XLS_DIMENSIONS == $type) { //DIMENSIONS
		} 
		elseif (XLS_BOOLERR == $type) {
			list ($row, $col, $style, $data, $type) = get_struct("WWWBB", $buf, 0);
			if ($type==0)  { // boolean value
				$sheets[$sheet][$row][$col]=$data;
			}else{  // error
				if ($data == 0)  { //type of error
					$sheets[$sheet][$row][$col]='#NULL!';
				}elseif ($data == 7)  {
			    	$sheets[$sheet][$row][$col]='#DIV/0';
				}elseif ($data == 0x0f)  {
					$sheets[$sheet][$row][$col]='#VALUE!';
				}elseif ($data == 0x17)  {
					$sheets[$sheet][$row][$col]='#REF!';
				}elseif ($data == 0x1d)  {
					$sheets[$sheet][$row][$col]='#NAME?';
				}elseif ($data == 0x24)  {
					$sheets[$sheet][$row][$col]='#NUM!';
				}elseif ($data == 0x2a)  {
					$sheets[$sheet][$row][$col]='#N/A';
				}
			}
//		}elseif (XLS_STRING == $type) {//непонятно как использовать
//			list ($row, $col, $style, $RK) = get_struct('WWWL', $buf, 0);
//			$sheets[$sheet][$row][$col]=RK_to_num($RK);
		} elseif (XLS_FORMULA == $type) { // Cell: formula
			list ($row, $col, $XFindex, $dec) = get_struct('WWWD', $buf, 0);
			$sheets[$sheet][$row][$col]=$dec;
		} elseif (XLS_MULRK == $type) { //MULRK
			list($row, $col) = get_nword(2, $buf, 0);
			$n = ($l - 6) / 6; 
			for ($i=0; $i<$n; $i++) {
				list($style, $RK) = get_struct('WL', $buf, 4+$i*6);
				$sheets[$sheet][$row][$col+$i]=RK_to_num($RK);
			}
		} elseif (XLS_MULBLANK == $type) { //MULBLANK
			list($row, $col) = get_nword(2, $buf, 0);
			$n = ($l - 6) / 2;	
			$style = get_nword($n, $buf, 4);
			for ($i=0; $i<$n; $i++) {
				$sheets[$sheet][$row][$col+$i]='';
			}
		} elseif (XLS_SST == $type) {// SST
			$strings = array();
			$os=0; //offset into $buf
			$n = get_long($buf, $os); //total number of strings in the workbook
//	print "total number of strings in the workbook = $n<br>\n";
			$os+=4;
			$n2 = get_long($buf,$os); //number of following strings
//	print "number of following strings = $n2\n";
			$os+=4;
			// format string:
			// len: 2 b
			// ofs: 1b
			// if ($ofs & 8)!=0 + 4b
			// if ($ofs & 4)!=0 + 2b
			// string if ($ofs & 1)!=0 + 2*len b else len b
			$sect_len=$l;
			$c=8;  // текущая длина секции (для отслеживания обрыва, при переполнении)
			for ($i=0; $i<$n2; $i++) {
				$start = "";
				$l1 = get_word($buf, $os); // length of the string 
				$ofs = get_byte($buf, $os+2); // Option flags
				list($bUni, $bFE, $bRT) = GetFlags($ofs);
				if($bUni) $l1 *= 2;
				
				$c+= 3; //накидываем на длину и флаги
				$os+=3; //KCM накидываем на длину и флаги
				// skip areas for Rich-text & Far_east spec data
				if($bRT) {
					$nRTR = get_word($buf, $os); // KCM number of Rich-Text Format Runs
					$os += 2; $c+=2;
				}
				if($bFE) {
					$nFED = get_long($buf, $os); //накидываем на Far-East флаги
					$os += 4; $c+=4;
				}
				if ($c+$l1 > $sect_len)  { // обрыв секции
					$ost = $sect_len-$c;
			    	$tmp = substr($buf,$os, $ost); //KCM: по идее секция кончилась
			    	$start = $bUni ? EUnicodeToWin($tmp) : $tmp;
					$l1 -= $ost; //остаток длины строки
					
					$o += $l; // на начало следующей секции
					fseek($file,$offset+$o,0); // header: type
					// по идее CONTINUE (0x003C)
					$rtype = get_word(fread($file,2),0);
					$type = $rtype & 0x0FF;
					$l=unpackV(fread($file,2)); // header: len
					$buf = fread($file,$l);
					$ofs = get_byte($buf, 0); // Option flags
					$os=1;
					
					$bUniold = $bUni; // save on case if $bUni will change

					list($bUni, $bFE, $bRT) = GetFlags($ofs);
					if($bUni){  if(!$bUniold) $l1 *= 2;	}
					else{		if($bUniold) $l1 /= 2;	}
					// skip areas for Rich-text & Far_east spec data
					if($bRT) {
						$nRTR = get_word($buf, $os); // KCM number of Rich-Text Format Runs
						$os += 2;	$c+=2;
					}
					if($bFE) {
						$nFED = get_long($buf, $os); //накидываем на Far-East флаги
						$os += 4;	$c+=4;
					}

					$o += 4; 
					$sect_len=$l;
					$c=$l1+1;

			    	$tmp = substr($buf,$os,$l1);
			    	if($bUni) $tmp=EUnicodeToWin($tmp);
					$strings[] = $start.$tmp;
					$os+=$l1;
					//здесь может быть ошибка, если строка перекрывает более двух секций
					// для BIFF8 длина секции 8224b (с заголовком)
					// для BIFF2-7 - 2084b
				}
			    elseif ($c+$l1 == $sect_len)  { // ровно на конец секции
			    	$tmp = substr($buf, $os, $l1);//KCM
			    	if($bUni) $tmp=EUnicodeToWin($tmp);
					$strings[] = $tmp;

					$o+=$l;
					fseek($file,$offset+$o,0);
					$rtype = get_word(fread($file,2),0);
					$type = $rtype & 0x0FF;
					$l = unpackV(fread($file,2)); // section len
					$buf = fread($file,$l); // section body
					
					$o+=4;
					$sect_len=$l;
					$os=0;
					$c=0;
				}else 
				{//нет обрыва секции
			    	$tmp = substr($buf, $os, $l1); // читаем строку длиной l1
					$strings[] = ($bUni?EUnicodeToWin($tmp):$tmp);
					$os+=$l1; // KCM set current pos on the end of a string in the buffer
					$c+=$l1; //длина секции увеличена на длину строки
				}
				// (next is my addon [KCM])
				// поправить длины на дополнительные данные
				if($bRT){	$c += 4*$nRTR;	$os += 4*$nRTR;	}
				if($bFE) {	$c += $nFED;	$os += $nFED; 	}
				// читать следующую строку 
			}
		} elseif (XLS_LABELSST == $type) { // LABELSST
			list ($row, $col, $style, $i) = get_struct("WWWL", $buf, 0);
			$sheets[$sheet][$row][$col]=$strings[$i];
		} elseif (XLS_BLANK == $type) { //BLANK
			list ($row, $col, $style) = get_nword(3, $buf, 0);
			$sheets[$sheet][$row][$col]="";
		} elseif (XLS_NUMBER == $type) { // NUMBER
			list ($row, $col, $style, $float) = get_struct("WWWD", $buf, 0);
			$sheets[$sheet][$row][$col]=$float;
		} elseif (XLS_LABEL == $type) {
			list($row, $col, $style, $len, $ofs) = get_struct("WWWWB", $buf, 0);
			$bUni = ($ofs & 1)!=0;
			if($bUni) $len *= 2;
			$tmp = substr($buf, 9, $len);
			$sheets[$sheet][$row][$col]= $bUni ? EUnicodeToWin($tmp) : $tmp;
		} elseif (XLS_RK == $type) { // Cell: RK number
			list ($row, $col, $style, $RK) = get_struct("WWWL", $buf, 0);
			$sheets[$sheet][$row][$col]=RK_to_num($RK);
		} elseif (XLS_BOF == $type) { //BOF
/*		
			$version = dechex(get_word($buf, 0));
			$dtype = dechex(get_word($buf, 2));
			switch($dtype){
			case '5': $dtname = 'Workbook globals';
			break;
			case '6': $dtname = 'VB module';
			break;
			case '10': $dtname = 'Worksheet';
			break;
			case '20': $dtname = 'Chart';
			break;
			case '40': $dtname = 'BIFF4 Macrosheet';
			break;
			case '100': $dtname = 'Workbook globals';
			break;
			}
			$build = get_word($buf, 4);
			$year  = get_word($buf, 6);
			$lver  = ($l==16)?"; lowest Excel version ".dechex(get_long($buf, 12)):"";
			print "BOF version 0x0$version; data type 0x0$dtype ($dtname); build $build; year $year$lver<br>";
*/			
			$sheet+=1;
		}
		$o += $l;
   }
//   print("<pre>");
// print_r($strings);
//   print("</pre>");
   return $sheets;
}


function get_struct ($format, &$buf, $off){
	$rar = array();
	$os=0;
	for($i=0; $i<strlen($format); $i++){
		switch(substr($format, $i, 1)){
		case "B":
			$rar[] = unpackV(substr($buf, $off+$os, 1));
			$os+=1;
		break;
		case "W":
			$rar[] = unpackV(substr($buf, $off+$os, 2));
			$os+=2;
		break;
		case "L":
			$rar[] = unpackV(substr($buf, $off+$os, 4));
			$os+=4;
		break;
		case "R":
			$rar[] = implode('',unpack('f', substr($buf, $off+$os, 4)));
			$os+=4;
		break;
		case "D":
			$rar[] = implode('',unpack('d', substr($buf, $off+$os, 8)));
			$os+=8;
		break;
		}
	}
	return $rar;
}

function get_nword ($n, &$buf, $off){
	$rar = array();
	for ($i=0; $i<$n; $i++)
		$rar[] = unpackV(substr($buf, $off+$i*2, 2));
	return $rar;
}

function get_byte(&$buf, $os)   { return unpackV(substr($buf, $os, 1)); }
function get_word(&$buf, $os)   { return unpackV(substr($buf, $os, 2)); }
function get_long(&$buf, $os)   { return unpackV(substr($buf, $os, 4)); }


?>