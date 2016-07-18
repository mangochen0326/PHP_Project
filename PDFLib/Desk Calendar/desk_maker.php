<?
		/*=========================================================================================
    *  桌曆素材產生
    *
    *  2013/08/20 Arvin 桌曆萬年曆資料及背版PDF、預覽縮圖產生
    *
    *
    /*========================================================================================
		* Set timoeout & memory (設定執行timeout時間、使用記憶體)
		*========================================================================================*/
		ignore_user_abort(true); //忽略網頁關閉繼續執行
		set_time_limit(0);
		ini_set("memory_limit","300M");
		
		/*========================================================================================
		*  init
		=========================================================================================*/
    define('Document_root',dirname(dirname(dirname(__FILE__))));
    //===========================================================
    // 自動宣告物件
    //===========================================================
	
		
    function __autoload($classname) {
        $filename = Document_root."./transfer/class/". $classname .".php";
        if (is_file($filename)) {
            include_once($filename);
        }
    }
		include_once(Document_root."./inc/dblk.php");
		include_once(Document_root.'./album/myapi.php');
    include_once(Document_root.'./album/makepdf_api.php');
		
		$obj = Calendar::getInstance($GB_dblk);            //宣告VDP物件
		

		$obj->PDF_OUTPUT_PATH  = Document_root."./transfer/desk/pdf_output/";
		$obj->PRE_OUTPUT_PATH  = Document_root."./transfer/desk/pre_output/";
		$obj->THU_OUTPUT_PATH  = Document_root."./transfer/desk/pre_thumb/";
		$obj->SOURCE_PATH      = Document_root."./transfer/desk/source/";
		$obj->INI_FILE         = Document_root."./transfer/desk/Calendar.ini.php";
		
 
		$pdfhw = new PDFlib();     
		pdflib_font_parameter($pdfhw);
		
		
		$pdfhw->set_parameter("FontOutline", "GOTHIC=c:\\windows\\fonts\\GOTHIC.ttf");	//Century Gothic
		$pdfhw->set_parameter("FontOutline", "GOTHICB=c:\\windows\\fonts\\GOTHICB.ttf");	//Century Gothic BOLD
		$pdfhw->set_parameter("FontOutline", "CALIBRI=c:\\windows\\fonts\\CALIBRI.ttf");	//CALIBRI
		$pdfhw->set_parameter("FontOutline", "CHAPA=c:\\windows\\fonts\\ChaparralPro-Regular_0.otf");	//Chapa
		$pdfhw->set_parameter("FontOutline", "DFT=c:\\windows\\fonts\\DFT_7.TTC");	//DFT 華康麗中黑
		$pdfhw->set_parameter("FontOutline", "BKANT=c:\\windows\\fonts\\BKANT.TTF");	//DFT 華康麗中黑
		$pdfhw->set_parameter("FontOutline", "CAN=c:\\windows\\fonts\\Candara.ttf");	//CAN 
		$pdfhw->set_parameter("FontOutline", "BHEI01M=c:\\windows\\fonts\\bHEI01M.ttf"); 	// 文鼎中黑體
		
		
	
		$START=2017;
		$END=2017;
		
		//$obj->Make_calendar_date($START,$END);//產生萬年曆資料
		
		//die;
	
		$obj->search_pdf();
		
		$obj->search_data();
		
		
		for ($i=$START;$i<=$END;$i++) {
//				$obj->make_year_background($pdfhw,$i);
				//第三參數是分別掛曆跟桌曆，B是掛曆只產生預覽PDF素材不變
				$obj->make_day_background($pdfhw,$i,"");
		}
		
		echo "ok!!";
		die;

echo'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="pragma" content="no-cache">';
for ($s_y=$START;$s_y<=$END;$s_y++) {
		$y=$s_y;
		//$SQL="Delete DESK_MASTER where left(YEAR,4)='$y'";
		//mssql_query($SQL);
		echo '<table width="100%" border="0" cellspacing="2" cellpadding="1">
		<tr bgcolor="#CCFFCC"> 
		<td align="center" colspan="4">'.$y.'年</td>
		</tr>
		<tr>';
		for ($m=1;$m<=12;$m++) {
				$aa = (++$a % 4) ? '' : '</tr><tr>';

				echo '<td align="center" valign="top" bgcolor="#CC6633"> 
				<table width="100%" border="0" cellspacing="1" cellpadding="1">
				<tr> 
				<td bgcolor="#CCCCFF" colspan="7" align="center">'.$m.' 月</td>
				</tr>
				<tr align="center"> 
				<td bgcolor="#FFCCCC">日</td>
				<td bgcolor="#CCCCCC">一</td>
				<td bgcolor="#CCCCCC">二</td>
				<td bgcolor="#CCCCCC">三</td>
				<td bgcolor="#CCCCCC">四</td>
				<td bgcolor="#CCCCCC">五</td>
				<td bgcolor="#CCCCCC">六</td>
				</tr>
				<tr>';
				$ii=1;
				for ($d=1;$d<=42;$d++) {
						if(checkdate($m,$d,$y)=="1"){
								$b = date ("w",mktime(0,0,0,$m,$d,$y));
								$w = date ("w",mktime(0,0,0,$m,$d,$y));
								if($d==1) {
										$cc = str_repeat("<td></td>",$b);
										$ii+=$b;
								} else {
										$cc = "";
								}
								$bb = (++$b % 7) ? '' : '</tr><tr>';
								if($b==1){$bg = "#FFCCCC";} else {$bg = "#FFFFCC";}
								$ch_date=$obj->convertSolarToLunar($y, $m, $d);
								
								$season=$obj->getFestival($y, $m, $d);
								$season=   $obj->getJieQi($y,$m,$d);
								
								$tmp_days=$obj->getLunarMonthDays($y,$m);
								
								if ($ch_date[7]!='0' and ($ch_date[4] > $ch_date[7])) {
										$insert_m=($ch_date[4]-1);
								} else {
										$insert_m=$ch_date[4];
								}
								if ($ch_date[5]==1) {
										if ($tmp_days==29)  {
												$bs="小";
										} else {
												$bs="大";
										}						
										$show_lunar=$ch_date[1].$bs;
								} else {
										if ($ch_date[7]!='0') {
												if ($ch_date[4] > $ch_date[7]) {
														$show_lunar=$insert_m."/".$ch_date[5];
												} else {
														$show_lunar=$insert_m."/".$ch_date[5];
												}
										} else {
												$show_lunar=$ch_date[4]."/".$ch_date[5];
												$insert_m=$ch_date[4];
										}
								}
								//echo "$cc"."<td align=\"center\" bgcolor=\"$bg\">國：".$d."[".$ii."]<br>農：".$show_lunar."[".$season."]</td>"."$bb";
								echo "$cc"."<td align=\"center\" bgcolor=\"$bg\">".$d."</td>"."$bb";
								
								if ($ii>35) {
										$ii=$ii-7;
								}
								
								//$SQL="insert into DESK_MASTER (YEAR,MONTH,DAY,CHMONTH,CHDAY,WEEK,POSITION) values(\"".$y."\",\"".sprintf("%02d",$m)."\",\"".sprintf("%02d",$d)."\",\"".sprintf("%02d",$ch_date[4])."\",\"".sprintf("%02d",$ch_date[5])."\",\"".$w."\",\"".$ii."\")";
								//$QUERY    = mssql_query($SQL);
								//echo $SQL;
								//if ($season!='') {
								///		$SQL="Insert into DESK_CHDAY (CHMD,NAME) values('".sprintf("%02d",$ch_date[0]).sprintf("%02d",$ch_date[4]).sprintf("%02d",$ch_date[5])."','". iconv('UTF-8', 'BIG5', $season)."')";
								//		$SQL1="Insert into DESK_CHDAY (CHMD,NAME) values('".sprintf("%02d",$ch_date[0]).sprintf("%02d",$ch_date[4]).sprintf("%02d",$ch_date[5])."','".$season."')";
										//echo $SQL1;
										//$QUERY    = mssql_query($SQL);
								//}
								$ii++;
						} else{
								break;
						}
				}
				echo '</table>';
				echo "</td>$aa";
		}
}
echo '</tr></table>';

		


/*
echo '
<form action="'.$PHP_SELF.'" method="POST">
年度：<input type="text" name="year">
<input type="submit" value="送出">
</form>';
*/

echo '</body></html>';

function pdf_head($obj,$pdf) {
		$obj->begin_document($pdf, "optimize=true compatibility=1.6");
		$obj->set_parameter("textformat", "utf8");
		$obj->set_info("Creator", "YFP");
		$obj->set_info("Author",  "Arvin");
		$obj->set_info("Title",   "VDP");
}

function	LL_PdfClosePage($pobj, $doc, $page)		{
		$pobj->close_pdi_page( $page);
		$pobj->close_pdi_document( $doc);
		$pobj->end_page_ext("");
		$pobj->end_document("");
}

function	LL_NewPage($pobj, &$doc, $pdi)		{
		$doc = $pobj->open_pdi_document($pdi, "");
		$pagehw = $pobj->open_pdi_page($doc, 1, "");
		return	$pagehw;
}

?>
