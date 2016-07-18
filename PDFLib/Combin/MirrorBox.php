<?php
    /*=========================================================================================
     *  鏡盒直落拼板併版PDF功能
     *
     *  2013/04/16 Arvin 圓形鏡盒
     *  2013/06/17 Arvin 增加訂單號碼標在旁邊顯示
		 *  2013/09/23 Arvin 調整影像位置 調整基準為64x 64 mm 的PDF內畫直徑64mm圓的外框線能完整印出
		 *  2013/12/13 Arvin 直落位置改由左下右上
		 *  2014/01/21 Arvin 直落清單改紀錄於PORDER_ERP1資料表內由後台查詢顯示 
     *  2014/02/11 Lucy  改png遮罩
		 *  2014/03/18 Arvin 依您印反應訂單要再往左一點才不會噴到邊緣，調整往左1mm
		 *  2014/07/08 Lucy  改無訂單不寄信 		 
     *  2014/12/09 Arvin 將訂單號碼由垂直文字改為橫向文字並移動到印件下方
     *  2015/02/16 Arvin 因應直噴機依您印搬回去，檔案改FTP到依您印主機處理
     *  2015/2/24 Lucy 通知依您印mail        
    ==========================================================================================
		 Set Root Dir (設定根目錄)
		========================================================================================*/
		ignore_user_abort(true);
		$ROOT_DIR = dirname(dirname(realpath( __FILE__ ))).DIRECTORY_SEPARATOR;
    /*========================================================================================
		* Set timoeout & memory (設定執行timeout時間、使用記憶體)
		*========================================================================================*/
		set_time_limit(0);
		ini_set("memory_limit","300M");
		/*========================================================================================
		*  include file
		=========================================================================================*/
		include_once($ROOT_DIR."./inc/dblk.php");		  //連結資料庫
		include_once($ROOT_DIR.'./inc/session.php');	//session
		include_once($ROOT_DIR.'./album/myapi.php');
    include_once($ROOT_DIR.'./inc/GBVars.php');
    include_once($ROOT_DIR.'./album/makepdf_api.php');
   	$pdfhw = new PDFlib();                        //宣告物件
   	pdflib_font_parameter($pdfhw);
   	/*========================================================================================
  	*  Environment Variable Set
		*========================================================================================*/
    $DATE_NAME =time();

    $this_day   =date("d",$DATE_NAME);
    $this_month =date("m",$DATE_NAME);
    $this_year  =date("Y",$DATE_NAME);

    $MAIL_DIR  =date("Ymd",$DATE_NAME);

    $RAR_ARRAY=array();

    $RIP=true;  //是否直接丟列印資料夾，測試的時候可以關起來(false)
    $MAIL_ADDR="arvin.chen@email.yfp.com.tw,junior@email.yfp.com.tw,herlonglong@email.yfp.com.tw,chu61@email.yfp.com.tw,yifen@e0in.com";
    //$MAIL_ADDR="arvin.chen@email.yfp.com.tw";

    $SAVE_LIST =true; //直落資料是否存入資料庫
    $make_newsingel=true; //是否重做單模
    $px=2.8346456;
    //單位mm
    $make_width = 64*$px;		//單張寬
    $make_height = 64*$px;	//單張高

    $pdf_width =300*$px;      //pdf 文件寬
    $pdf_height=420*$px;      //pdf 文件高
    /***************************************************************************************
		*  Html head
		****************************************************************************************/
		$html='';
		$html.='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "DTD/xhtml1-strict.dtd">';
		$html.='<html>';
		$html.='<head>';
		$html.='<meta http-equiv="Content-type" content="text/html; charset=utf-8" />';
		$html.='<meta name="Description" content="an online application for photo album editing." />';
		$html.='<meta name="Keywords" content="photo,VOW,album,book,w2p,wtp,相本,相簿,線上編輯,印刷" />';
		$html.='</head>';
		$html.='<body>';
    /***************************************************************************************
		*  Process
		****************************************************************************************/
    $mailpath=$ROOT_DIR.'./transfer/mail/combin/'.$MAIL_DIR;
		if (!is_dir($mailpath)) {
				mkdir($mailpath);
		}
    $logpath=$ROOT_DIR.'./transfer/log/'.$MAIL_DIR."/";
    if (!is_dir($logpath)) {
				mkdir($logpath);
		}
    $thislog=$ROOT_DIR.'./transfer/log/'.$MAIL_DIR."/".$MAIL_DIR."_mirrorbox.log";

    $imgsrc= $ROOT_DIR.'./transfer/resource/mirrorbox2.png';
  	$SingleMaskImgSrc= $ROOT_DIR.'./transfer/resource/mbox_cut3.png'; //獨立遮罩	
	
    $O_FILE   =array();
    
    //$s_date=mktime(0,0,0,$this_month,3,$this_year);
    //$e_date=mktime(23,59,59,$this_month,3,$this_year);
		
		$s_date=mktime(0,0,0,$this_month,$this_day-1,$this_year);
    $e_date=mktime(23,59,59,$this_month,$this_day-1,$this_year);
    
    $SQL='Select A.BOID from pordereport A join PORDER B on A.BOID=B.BOID join WBOOK C on B.BID=C.WBID ';
    //$SQL='Select B.BOID from PORDER B join WBOOK C on B.BID=C.WBID ';
    $SQL.=' where (C.BTYPE=\'46\') and A.NOCOMBIN <> \'Y\' and B.BOSTATUS=\'12\' and A.TSNEW between \''.$s_date.'\' and \''.$e_date.'\' ';
    //$SQL.=" where C.BTYPE='46' and B.BOID in ('1309142056','1309142057','1309142058')";
    //$SQL.=" where C.BTYPE='46' and B.YFPBOID in ('VVE21713')";
    
    $QUERY    = mssql_query($SQL,$GB_dblk);
    $rows     = mssql_num_rows($QUERY);
    //echo $SQL;
    if ($rows > 0) {
        ll_echo("[".date("Y/m/d H:i:s")."]訂單編號：");
        $SQL  = "SELECT C.ITEM,C.V1,A.BOID,A.BONUM,A.BID,B.WBFLOW,A.YFPBOID,B.BTYPE ";
        $SQL .= " FROM PORDER A join WBOOK B on A.BID=B.WBID ";
        $SQL .= " join W2POP C on A.BOID=C.BOID ";
        $SQL .= " where (C.ITEM='PPID' or C.ITEM='NAME')  ";
        $sub_sql="";
    		$SQL.= " AND A.BOID in (";
        $count=0;
        while ($rs=mssql_fetch_array($QUERY)) {
            if ($sub_sql=="") {
    						$sub_sql="'".trim($rs[BOID])."'";
    				} else {
    						$sub_sql.=",'".trim($rs[BOID])."'";
    				}
            $count++;
            ll_echo(trim($rs[BOID]));
        }
    		$SQL.=$sub_sql.") ";
        $SQL.=" order by C.V1 desc ,A.YFPBOID asc";
      
        ll_echo("[".date("Y/m/d H:i:s")."]共：".$count."筆");

    		$QUERYSQL    = mssql_query($SQL,$GB_dblk);
    		while($REC = mssql_fetch_array($QUERYSQL)) {
    				$BONUM   = trim($REC[BONUM]);   //數量
            $BOID    = trim($REC[BOID]);
            $WBID    = trim($REC[BID]);     // BID
            $WBFLOW  = trim($REC[WBFLOW]);  //WBFLOW
            $YFPBOID = trim($REC[YFPBOID]); //訂單編號
            
           
            $MAP[WBID]["$BOID"]=$WBID;
            $MAP[WBFLOW]["$BOID"]=$WBFLOW;
            $MAP[BONUM]["$BOID"]=$BONUM;
            $MAP[YFPBOID]["$BOID"]=$YFPBOID;
           
           
    		}
        ll_echo("[".date("Y/m/d H:i:s")."]PDF檔案陣列整理");
        //===================================================================================================================
        //  整理實際處理的PDF清單陣列
        //===================================================================================================================
        $err_msg="";
        $k=0;
        foreach ($MAP[WBFLOW] as $v_boid => $v_tmp) {
            switch ($MAP[WBFLOW]["$v_boid"]) {
                //===========================================================================================================
                //  FTP上傳的訂單
                //===========================================================================================================
                case "1":
                    $source=$GB_BOOKPATH.$MAP["WBID"]["$v_boid"]."/pdf/001.pdf";
                    $rar_source=$ROOT_DIR.'./transfer/mirrorbox_tmp/'.$MAP["YFPBOID"]["$v_boid"].".pdf";
                    copy($source,$rar_source);
                    for ($s=0;$s<$MAP[BONUM]["$v_boid"];$s++) {
                        $k++;
                        $TEMPLATE[$k]=$source.",1";
                        $MAP[BOID][$k]=$v_boid;
                        if (!in_array($rar_source,$RAR_ARRAY)) {
                            $RAR_ARRAY[]=$rar_source;
                        }
                    }
                    break;
                //===========================================================================================================
                //  編輯器的訂單
                //===========================================================================================================
                case "2":
                    ll_echo("[".date("Y/m/d H:i:s")."]".$v_boid."單模重新製作");
                    $source=$GB_BOOKPATH_PDF."\\spool\\".$MAP["WBID"]["$v_boid"].".pdf";
                    $rar_source=$ROOT_DIR.'./transfer/mirrorbox_tmp/'.$MAP["YFPBOID"]["$v_boid"].".pdf";
                    if ($make_newsingel) {
                        fopen_exec($GB_W2PPDF."/album/makepdf.php?BID=".$MAP["WBID"]["$v_boid"]."&ORD=".$MAP["YFPBOID"]["$v_boid"].sprintf("%'02s",$MAP["BONUM"]["$v_boid"]));
                    }
                    copy($source,$rar_source);
                    for ($n=0;$n<$MAP["BONUM"]["$v_boid"];$n++) {
                        if (file_exists($source)) {
                            $k++;
                            $TEMPLATE[$k]=$source.",1";
                            $MAP[BOID][$k]=$v_boid;
                            if (!in_array($rar_source,$RAR_ARRAY)) {
                                $RAR_ARRAY[]=$rar_source;
                            }
                        } else {
                            ll_echo("[".date("Y/m/d H:i:s")."]".$v_boid.":".$source."不存在");
                            $err_msg.=$v_boid."訂單".$source."不存在 \r\n";
                        }
                    }
                    break;
            }
        }
        if ($err_msg!='') {
            send_mail("鏡盒缺檔通知",$err_msg);
        }
        $real_loop=ceil(count($TEMPLATE)/20);
      
				$err_msg="";
        try {
            $COMBIN=0;
            $sub_html="";
            for ($p=0;$p<$real_loop;$p++) {
                $COMBIN++;
                $new_pdf =$GB_BOOKPATH_PDF."spool/".date("YmdHi",$DATE_NAME)."_MirrorBox_".$COMBIN."(".$real_loop.").pdf";
                ll_echo("[".date("Y/m/d H:i:s")."]大版：".basename($new_pdf));
                $IN_BOID=array();
                $O_FILE[]=basename($new_pdf);

                PDF_HEAD($pdfhw,$new_pdf);
                //=====================================================================================================
                //  實際印刷訂單內容
                //=====================================================================================================
                $x=0;
                $y=5;
                $pdfhw->begin_page_ext($pdf_width, $pdf_height, "topdown");		//開啟一個新PDF工作頁
                $min=($p*20)+1;
                $max=($p*20)+20;
				        $SingleMaskImage = $pdfhw->load_image('auto', $SingleMaskImgSrc, "passthrough=true"); // Load 獨立遮罩			
                for ($t=$min;$t<=$max;$t++) {
                    if (empty($TEMPLATE["$t"])) {
                        continue;
                    }
                    $tmp_array=explode(",",$TEMPLATE["$t"]);
                    $source=$tmp_array[0];
                    $filepage=$tmp_array[1];
                    $page = LL_NewPage($pdfhw, $doc, $source);
                    
                    //起點X座標
                    $startX=7.4*$px;//9.4
                    //起點Y座標
                    $startY=0;//2*$px;//+$make_height;//5.25
                    $gapx = 9.73*$px;//9.34
										$gapy = 18.7*$px;//3.95
                    $pastX=$startX+$make_width*$x+$gapx*$x+1;
                    $pastY=$startY+$make_height*$y+$gapy*$y-(8.6*$px);
                    //}
                    $t_boid=$MAP["BOID"][$t];
                    //$YFPBOID=$MAP[YFPBOID]["$t_boid"];
                    $BONUM  =$MAP[BONUM]["$t_boid"];
                    if (!in_array($t_boid,$IN_BOID)) {
                        $IN_BOID[]=$t_boid;
                    }
                    $real_width=$make_width;
                    $real_height=$make_height;
                    $real_x=$pastX;
                    $real_y=$pastY;
                    $rotate=0;
                    LL_FitPdiPage($pdfhw, $doc, $real_x, $real_y, $real_width, $real_height, $filepage,$rotate);
                    
					// 貼獨立遮罩 (2014/04/09 by Ouscar) ---------------------
					$mask_w = $real_width+4;
	  				$mask_h = $real_height+4;
					$pdfhw->fit_image( $SingleMaskImage, $real_x-2, $real_y+2, "boxsize={"."$mask_w $mask_h"."} fitmethod=entire");
					
					// -------------------------------------------------------
					
					LL_ClosePage($pdfhw, $doc, $page);	//關閉pdi
                   
										$x++;
                    
                    if($x==4){$x=0;$y--;}
                    
                    
                    /*
                    if ($x==2 and $y%2==0) {
                        $x=0;$y--;
                    } elseif ($x==3 and $y%2==1) {
                        $x=0;$y--;
                    }*/
                }
				$pdfhw->close_image( $SingleMaskImage); //關閉獨立遮罩Image
				/* //舊的做法:貼上整張製具MASK
                $image = $pdfhw->load_image('auto', $imgsrc, "passthrough=true");
                $pdfhw->fit_image( $image, 0, $pdf_height, "boxsize={"."$pdf_width $pdf_height"."} fitmethod=meet");
                $pdfhw->close_image( $image);
                */
               
                //2013/06/17 Arvin 增加訂單號碼標在旁邊顯示
                $x=0;
                $y=5;
                $min=($p*20)+1;
                $max=($p*20)+20;
                for ($t=$min;$t<=$max;$t++) {
                    if (empty($TEMPLATE["$t"])) {
                        continue;
                    }
                    
                        //起點X座標
                       
               			
               			$startX=9.39*$px;
                    //起點Y座標
                    $startY=0.05*$px;
                    $gapx = 9.7*$px;
										$gapy = 19*$px;
                    $pastX=$startX+$make_width*$x+$gapx*$x+1;
                    $pastY=$startY+$make_height*$y+$gapy*$y-(8.5*$px);
                    

                    $t_boid=$MAP["BOID"][$t];
                    $YFPBOID=$MAP[YFPBOID]["$t_boid"];
                    $BONUM  =$MAP[BONUM]["$t_boid"];
                    
                    $real_width=$make_width;
                    $real_height=$make_height;
                    $real_x=$pastX;
                    $real_y=$pastY;
                   
                    $x++;
                    if($x==4){$x=0;$y--;}
                    pdflib_font($pdfhw, '黑體', 12,$YFPBOID.sprintf("%'02s",$BONUM),$real_x+50, $real_y+25);
                }
                $pdfhw->end_page_ext("");
                $pdfhw->end_document("");
                $sub_html.=make_html(basename($new_pdf),$IN_BOID);
            }
            ll_echo("[".date("Y/m/d H:i:s")."]鏡盒保護殼檔案清單");
            $html_mail ='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
        		$html_mail.='<html xmlns="http://www.w3.org/1999/xhtml">';
        		$html_mail.='<head>';
      	  	$html_mail.='<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
      		  $html_mail.='<title>Iphone5直落清單'.date("Y-m-d H:i",$DATE_NAME).'</title>';
      		  $html_mail.='</head>';
      		  $html_mail.='<body>';
            $html_mail.='<table width="700" border="1">';
            $html_mail.='<tr><th width="50%">檔案名稱</th>';
    				$html_mail.='<th width="50%">包含訂單</th>';
        		$html_mail.='</tr>';
            $html_mail.=$sub_html;
            $html_mail.='</table>';
    			  $html_mail.='</body>';
    			  $html_mail.='</html>';

            $s_html = $DATE_NAME."_MirrorBox.html";
      	    $fileopen = fopen($ROOT_DIR.'./transfer/mail/combin/'.$MAIL_DIR.'/'.$s_html,"w+");
      	    fseek($fileopen,0);
      	    fwrite($fileopen,$html_mail);
      	    fclose($fileopen);
          	$title ='鏡盒單處理明細'.date("Y-m-d H:i",$DATE_NAME);
            $subject = "=?UTF-8?B?" . base64_encode($title) . "?=";
            $boundary = uniqid( "");

            $headers="From: webmaster@www.cloudw2p.com"."\r\n";
            $headers.="Reply-To: webmaster@www.cloudw2p.com"."\r\n";
            $headers.="X-Priority: 1"."\r\n";
            $headers.="X-MSMail-Priority: High"."\r\n";
            $headers.="Content-type: multipart/mixed; boundary=\"$boundary\"";

            $read = base64_encode($html_mail);
            $read = chunk_split($read);

            $emailBody .= '--'.$boundary."\n";
            $emailBody .= 'Content-type: text/html; charset="utf-8"'."\n";
            $emailBody .= 'Content-transfer-encoding: base64'."\n\n";
            $emailBody .= $read."\n"; // 本文內容
            $emailBody .= '--'.$boundary."\n";
            $emailBody .= 'Content-type: text/html; name='.$DATE_NAME.'_MirrorBox.html'."\n";
            $emailBody .= 'Content-transfer-encoding: base64'."\n";
            $emailBody .= 'Content-disposition: inline; filename'.$DATE_NAME.'_MirrorBox.html'."\n\n";
            $emailBody.= $read."\n";
            $emailBody.="--$boundary--";
            if ($real_loop >0) {
        			  $result=mail($MAIL_ADDR, $subject, $emailBody, $headers);
								
								if ($err_msg!='') {
										send_mail("圓形鏡盒直落訂單重複",$err_msg);
								}
								
                if ($RIP) {
                    ll_echo("[".date("Y/m/d H:i:s")."]檔案丟Enhance及壓縮單模檔案");
										$result_ftp=choose_ftp(23);
                    
										if(!@$ftp_conn_id = ftp_connect($result_ftp[FTPIP])) {
												send_mail("依您印FTP連線失敗[圓形鏡盒]","");
										} else {
                        if (@ftp_login($ftp_conn_id, $result_ftp[FTPUSER], $result_ftp[FTPPASS])) {
                            @ftp_chdir($ftp_conn_id,"0946");
                            @ftp_chdir($ftp_conn_id,"mimaki");
														ftp_chdir($ftp_conn_id,"MirrorBox");
														foreach ($O_FILE as $o_value) {
																$FILE     =$GB_BOOKPATH_PDF."spool\\$o_value ";
																if(!@ftp_put($ftp_conn_id, iconv("UTF-8","BIG5",$o_value), $FILE, FTP_BINARY)) {
																		send_mail("直噴檔案上傳失敗[圓形鏡盒]",$FILE);
																}
														}
														foreach ($RAR_ARRAY as $r_file) {
																$WORK_FILE.=$r_file." ";
														}
														$rar_file=$ROOT_DIR.'./transfer/mirrorbox_tmp/'.date("YmdHi",$DATE_NAME).".rar";
														$CMD_RAR = "c:/app/winrar/rar.exe a -ep -m0 $rar_file  $WORK_FILE";
														exec($CMD_RAR);
														if(!@ftp_put($ftp_conn_id, iconv("UTF-8","BIG5",basename($rar_file)), $rar_file, FTP_BINARY)) {
																send_mail("鏡盒 RAR檔案上傳失敗",basename($rar_file));
														}
                            @ftp_close($ftp_conn_id);
												} else {
                             send_mail("依您印FTP帳號登入失敗[圓形鏡盒]","");
                        }
										}
                }
            }

            unset($MAP);
            unset($O_FILE);
            unset($IN_BOID);
            unset($tmp_array);
            ll_echo("[".date("Y/m/d H:i:s")."]作業完畢");
            $del_cmd = "rmdir /s/q D:\\www\\transfer\\mirrorbox_tmp";
            exec($del_cmd);
            sleep(3);
          	if (!is_dir("D:\\www\\transfer\\mirrorbox_tmp")) {
            		mkdir("D:\\www\\transfer\\mirrorbox_tmp");
         		}
        } catch (PDFlibException $e) {
            $errnum = $e->get_errnum();
            $apiname = $e->get_apiname();
            $errmsg = $e->get_errmsg();
        } catch (Exception $e) {
            die($e);
        }

    } else {
         ll_echo("[".date("Y/m/d H:i:s")."]無鏡盒訂單!!");

         ///send_mail("無鏡盒訂單".date("Y-m-d",$DATE_NAME),"");
    }
    
    /***************************************************************************************
    *  Html End
    *****************************************************************************************/
    $html.='</body>';
    $html.='</html>';
    print($html);

    
    die;


/***********************  Function ******************************************************/

/****************************************************************************************/
// -------------------------------------------------
// -------------------------------------------------
// PDF 開新的一頁
function	LL_NewPage($pobj, &$doc, $pdi)		{
global	$makelog, $pdf_width, $pdf_height, $pdf_pagecount;

  $doc = $pobj->open_pdi_document($pdi, "");
  $pagehw = $pobj->open_pdi_page($doc, 1, "");

return	$pagehw;
}


// -------------------------------------------------
// PDF 關閉目前頁
function	LL_ClosePage($pobj, $doc, $page)		{
global	$makelog,$pdf_pagecount;
global	$pdf_width,$pdf_height;

 
  $pobj->close_pdi_page( $page);
  $pobj->close_pdi_document( $doc);


return	$pdf_status;
}


// -------------------------------------------------
// PDF 關閉目前頁
function	LL_PdfClosePage($pobj, $doc, $page)		{
global	$makelog,$pdf_pagecount;
global	$pdf_width,$pdf_height;

  $pobj->end_page_ext("");
  $pobj->close_pdi_page( $page);
  $pobj->close_pdi_document( $doc);

  if($makelog)	error_log("[makepdf2.php:201],關閉一個完成頁\n", 3, "debug.log");

return	$pdf_status;
}

// -------------------------------------------------
// PDF 開頭
function PDF_HEAD($obj,$pdf) {
    global $x,$y;
    $obj->begin_document($pdf, "optimize=true compatibility=1.6");
   	$obj->set_parameter("textformat", "utf8");
 		$obj->set_info("Creator", "YFP");
    $obj->set_info("Author",  "Arvin");
    $obj->set_info("Title",   "Coasters");
    $x=0;$y=0;

}

// -------------------------------------------------
//印刷指示區
function	  LL_PrintInfo( $pobj, $x, $y, $w, $h)   {

	$pobj->setcolor("fillstroke", "cmyk", 0, 0, 0, 1.0);
	$pobj->rect($x, $y ,$w ,$h);
	$pobj->stroke();

	$info_date = date("Y/m/d H:i:s");
	pdflib_font( $pobj, '黑體', 12, "作業說明: $info_date", $x+5, $y-2);
}


/***********************************************************************************************/
/*  函式名稱：make_html($_pdf_name,$_BOID)
/*  函式參數： $_pdf_name : 檔案名稱
/*             $_BOID     : 平台號碼     
/*  回傳值  ：
/*  函式功能：產生直落工作單
/***********************************************************************************************/
function make_html($_pdf_name="",$_BOID=array()) {
    global $GB_dblk,$MAP,$DATE_NAME,$err_msg,$SAVE_LIST;
    $html ='<tr>';
    $html.='<td>'.$_pdf_name.'</td>';
		$html.='<td>';
    foreach ($_BOID as $value) {
        $html.=$MAP["YFPBOID"][$value]."<br>";
				
        if ($SAVE_LIST) {
            $SQL="Select * from PORDER_ERP1 where BOID='".$value."' and TIME<>'".$DATE_NAME."' and TIME2 < '".date("Y-m-d H:i:s",$DATE_NAME)."' ";
            $QUERY    = mssql_query($SQL,$GB_dblk);
            $rows     = mssql_num_rows($QUERY);
            if ($rows < 1) {
                $INS_SQL ="Insert into PORDER_ERP1(BOID,TIME,PAPER_ID,WORK_ID,FILENAME,YFPBOID) ";
                $INS_SQL.=" values('".$value."','".$DATE_NAME."','','','".basename($_pdf_name,".pdf")."','".$MAP["YFPBOID"][$value]."')";
                $QUERY    = mssql_query($INS_SQL,$GB_dblk);
            } else {
                ll_echo("[".date("d-M-y H:i:s")."]直落資料重複!!");
                $err_msg.=$value."訂單編號：".$MAP["YFPBOID"][$value]."\r\n ";
                
            }
        }
    }
    $html.='</td>';
		$html.='</tr>';

    return $html;
}


 /***********************************************************************************************/
/*  函式名稱：choose_ftp($_FACID)
/*  函式參數： $_FACID   : 工廠代號
/*
/*  回傳值  ： 生產工廠 FTP 相關資訊
/*  函式功能：回傳生產工廠FTP相關資訊
/*
/*  條件有變動須一併更改批次轉檔
/***********************************************************************************************/
function choose_ftp($_FACID) {
    global $GB_dblk;
    $query_string="Select * from W2PFAC where FACID='$_FACID'";
    $query=mssql_query($query_string,$GB_dblk);
    while ($rs=mssql_fetch_array($query)) {
        $result[FTPIP]=trim($rs[FFTPIP]);      // FTP 位址
        $result[FTPUSER]=trim($rs[FFTPNAME]);  // FTP 帳號
        $result[FTPPASS]=trim($rs[FFTPPASS]);  // FTP 密碼
        $result[FACODE]=trim($rs[FACODE]);     // XML 工廠 ID
        $result[FACYFP]=trim($rs[FACYFP]);     // 是否開立永豐訂單
        $result[FACNAME]=iconv('BIG5','UTF-8',trim($rs[FACNAME])); //工廠名稱
    }
    return $result;
}

/***********************************************************************************************/
/*  函式名稱：send_mail($_title=,$_content)
/*  函式參數： $_title   : 信件標題
/*             $_content : 信件內容
/*  回傳值  ：
/*  函式功能：Email通知信寄發
/***********************************************************************************************/
function send_mail($_title="",$_content="") {
		global $MAIL_ADDR;
		$mail_title       = "『".$_title."』";

		$MailHeader = "MIME-Version: 1.0\r\n
		Content-type: text/html; charset=UTF-8\r\n
		From: WebMaster@cloudw2p.com\r\n
		Reply-To: WebMaster@cloudw2p.com\r\n
		X-Priority: 1\r\n
		X-MSMail-Priority: High\r\n
		X-Mailer: PHP/".phpversion()."\r\n";

	  $HeaderFirst = "=?UTF-8?B?" . base64_encode($mail_title) . "?=";

		$AX = mail($MAIL_ADDR,$HeaderFirst,$_content,$MailHeader);
    
}


function	fopen_exec($URL)	{
		$timeout = 1800;

		set_time_limit(0);
		ini_set ('user_agent', $_SERVER['HTTP_USER_AGENT']);
		$old = ini_set('default_socket_timeout', $timeout);
		$file = fopen($URL, 'r');
		if($file)		{
			return	true;
		}	else {
			return	false;
		}
}
function ll_echo($str)	{
		global	$thislog;
		error_log("$str \r\n", 3, $thislog);

		echo $str."<br>"; ob_flush(); flush();sleep(1);
}

?>
