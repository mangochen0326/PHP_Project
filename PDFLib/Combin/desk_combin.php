<?php
    /*=========================================================================================
     *  桌曆直落拼板併版PDF功能
     *
     *  2011/12/30 Arvin 桌曆直落
     *  2013/01/07新增檔名log
		 *  2013/08/07 Arvin 變更生管收件者為倪哥
		 *  2013/10/24 Arvin 桌曆改為全直落，彙整直落條件改為頁數，資訊頁移到最後。
		 *  2014/06/26 Arvin 調整RIP Hot Folder
		 *  2015/11/20 Arvin 調整桌曆實際上傳RIP檔案，重複的就不丟進去RIP
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
   	//include_once($ROOT_DIR.'./album/makepdf_font.php');		          //設定字體資源
		pdflib_font_parameter($pdfhw); //設定字體資源
   	/*========================================================================================
  	*  Environment Variable Set
		*========================================================================================*/
    $DATE_NAME =time();
    $MAIL_DIR  =date("Ymd");
    /***************************************************************************************
		*  PDF 尺寸
		****************************************************************************************/
    $single_pdf=true;  //是否重做單模  (測試的時候false 正式的時候記得打開 true)
    $IS_RIP    =true;  //是否傳檔到RIP並背份檔案 (測試的時候false 正式的時候記得打開 true)
		$SAVE_LIST =true; //是否記錄於後台彙整資料表，測試的時候要關起來，不然會造成測試資料後台彙整查詢出來(false)
    $mail_addr="arvin.chen@email.yfp.com.tw,chu61@email.yfp.com.tw,herlonglong@email.yfp.com.tw"; //寄信通知收件者
    //$mail_addr="arvin.chen@email.yfp.com.tw"; //寄信通知收件者
    
		if (!is_dir("D:\\www\\transfer\\desk_tmp")) {
				mkdir("D:\\www\\transfer\\desk_tmp");
		}
		
		
    $px=2.8346456;
			//Indigo
	  $pdf_width  = 460*$px;						//PDF的寬 A3
		$pdf_height = 320*$px;						//PDF的高 A3

    $make_height=153;
    $make_width =202;
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
		echo "<pre>";
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
    $thislog=$ROOT_DIR.'./transfer/log/'.$MAIL_DIR."/".$MAIL_DIR."_DESK.log";

    
    //起點X座標
    $startX=($pdf_width-(($make_width*2+5)*$px))/2;
    //起點Y座標
    $startY=($pdf_height-(($make_height*2)*$px))/2+$make_height*$px;

    $DESK_ARY=array();
    $O_FILE   =array();
    $make_already=array();

    $week=date("w",time());

    
    $this_day   =date("d");
    $this_month =date("m");
    $this_year  =date("Y");
    $s_date=mktime(0,0,0,$this_month,$this_day-1,$this_year);
    $e_date=mktime(23,59,59,$this_month,$this_day-1,$this_year);
		
		//$s_date=mktime(0,0,0,$this_month,7,$this_year);
    //$e_date=mktime(23,59,59,$this_month,9,$this_year);
		

    $SQL='Select A.BOID from pordereport A join PORDER B on A.BOID=B.BOID join WBOOK C on B.BID=C.WBID ';
		
    $SQL.=' where B.BONUM <\'200\' and  A.NOCOMBIN <>\'Y\' and B.BOSTATUS=\'12\' and (C.BTYPE=\'31\' or C.BTYPE=\'30\') and B.FACID=\'10\' and A.TSNEW between \''.$s_date.'\' and \''.$e_date.'\' ';
		
    //$SQL.=" where A.BOID in ('1510062135', '1510062154') and B.BONUM <'200' and (C.BTYPE='31' or C.BTYPE='30') and B.FACID='10' ";
    //$SQL.=' where  (C.BTYPE=\'31\' or C.BTYPE=\'30\') AND B.FACID=\'10\' and B.YFPBOID in (\'VBF90533\')';

		//echo $SQL;
			

    $QUERY    = mssql_query($SQL,$GB_dblk);
    $rows     = mssql_num_rows($QUERY);
  
    if ($rows > 0) {
        ll_echo("[".date("Y/m/d H:i:s")."]訂單編號：");
        $SQL  = "SELECT D.GROUPID,C.V1,A.BOID,A.BONUM,B.WBPAGES,A.BID,B.WBFLOW,A.YFPBOID ";
        $SQL .= " FROM PORDER A join WBOOK B on A.BID=B.WBID ";
        $SQL .= " join W2POP C on A.BOID=C.BOID ";
        $SQL .= " left join PORDERGROUP D on A.BOID=D.GBOID ";
        $SQL .= " where C.ITEM='PPID'  ";
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

        $SQL.=" order by B.WBPAGES desc,A.YFPBOID asc";

				//echo $SQL;

        ll_echo("[".date("Y/m/d H:i:s")."]共：".$count."筆");


    		$QUERYSQL    = mssql_query($SQL,$GB_dblk);
    		while($REC = mssql_fetch_array($QUERYSQL)) {
    				$BONUM   = trim($REC[BONUM]);   //數量
            $BOID    = trim($REC[BOID]);
            $GROUPID = trim($REC[GROUPID]);
            $WBID    = trim($REC[BID]);     // BID
            $WBPAGES = trim($REC[WBPAGES]); //頁數
            $WBFLOW  = trim($REC[WBFLOW]);  //WBFLOW
            $YFPBOID = trim($REC[YFPBOID]); //訂單編號
            
            $V1      = trim($REC[V1]);
            if ($V1 < '600') {
                $MAP[PPID]["$BOID"]=$V1;
                $T_ARRAY[]=$BOID;
            } 
						if ($V1=='743') {
								$MAP[GOLD]["$BOID"]=$V1;
						}
            $MAP[WBID]["$BOID"]=$WBID;
            $MAP[WBFLOW]["$BOID"]=$WBFLOW;
            $MAP[BONUM]["$BOID"]=$BONUM;
						//$MAP[BONUM]["$BOID"]=1;
            $MAP[GROUPID]["$BOID"]=$GROUPID;
            $MAP[YFPBOID]["$BOID"]=$YFPBOID;
            $MAP[WBPAGES]["$BOID"]=$WBPAGES;
            
    		}

        //===================================================================================================================
        //  把加工條件加入陣列
        //===================================================================================================================
        foreach ($T_ARRAY as $T_BOID ) {
            if ($MAP["WBPAGES"]["$T_BOID"]=='52' or $MAP["WBPAGES"]["$T_BOID"]=='26') {
                $DESK_ARY[$MAP["PPID"]["$T_BOID"]]["SINGLE"][]=$T_BOID;
            } else {
                $DESK_ARY[$MAP["PPID"]["$T_BOID"]]["DOUBLE"][]=$T_BOID;
            }
        }
        ll_echo("[".date("Y/m/d H:i:s")."]PDF檔案陣列整理");
        //===================================================================================================================
        //  整理實際處理的PDF清單陣列
        //===================================================================================================================
        $err_msg="";
        foreach ($DESK_ARY as $paper_id => $ary_page) {
            $k=0;
            foreach ($ary_page as $pages => $ary_boid) {
                $k=0;
                foreach ($ary_boid as $v_boid) {
                    $SAVE_FILE=array();
                    switch($MAP["WBPAGES"]["$v_boid"]) {
                        //===================================================================================================================
                        //  桌曆簡約版13張
                        //===================================================================================================================
                        case "26": case "52":
                            $f_loop=24;
                            switch ($MAP[WBFLOW]["$v_boid"]) {
                                //===========================================================================================================
                                //  FTP上傳的訂單
                                //===========================================================================================================
                                case "1":
                                    $opener=opendir($GB_BOOKPATH.$MAP["WBID"]["$v_boid"]."/pdf");
                                    while (false!==($file=readdir($opener))) {
                                        if (substr(strtolower($file),-3)=='pdf') {
                                            $SAVE_FILE[]=$file;
                                        }
                                    }
                                    for ($n=0;$n<$MAP["BONUM"]["$v_boid"];$n++) {
                                        $k++;
                                        foreach ($SAVE_FILE as $f_value) {
                                            $source=$GB_BOOKPATH.$MAP["WBID"]["$v_boid"]."/pdf/".$f_value;
																						$double_idx=1;
																						$single_idx=1;
                                            for ($t=0;$t<=$f_loop;$t+=2) {
                                                //簡約版
                                                if ($MAP["WBPAGES"]["$v_boid"]=='26') {
                                                    $TEMPLATE["$paper_id"]["single"][$k][$t]=$source.",".($single_idx);
                                                    $TEMPLATE["$paper_id"]["single"][$k][$t+1]="";
																										$single_idx++;
                                                } else {
                                                    $TEMPLATE["$paper_id"]["single"][$k][$t]=$source.",".($double_idx);
                                                    $TEMPLATE["$paper_id"]["single"][$k][$t+1]=$source.",".($double_idx+1);
																										$double_idx+=2;
                                                }
                                            }
                                        }
                                        $MAP["BOID"]["$paper_id"][single][$k]=$v_boid;
																				$MAP["COVER"]["$paper_id"]["$v_boid"][]=$source;
                                    }
                                    break;
                                //===========================================================================================================
                                //  編輯器的訂單
                                //===========================================================================================================
                                case "2":
                                    $source=$GB_BOOKPATH_PDF."spool/".$MAP["WBID"]["$v_boid"].".pdf";
																		if ($single_pdf and !in_array($v_boid,$make_already)) {
																				ll_echo("[".date("Y/m/d H:i:s")."]".$v_boid."單模不存在，重新製作");
																				fopen_exec($GB_W2PPDF."/album/makepdf.php?BID=".$MAP["WBID"]["$v_boid"]."&ORD=".$MAP["YFPBOID"]["$v_boid"].sprintf("%'02s",$MAP["BONUM"]["$v_boid"]));
																				$make_already[]=$v_boid;
																		}
                                    for ($n=0;$n<$MAP["BONUM"]["$v_boid"];$n++) {
                                        if (file_exists($source)) {
                                            $k++;
																						$double_idx=1;
																						$single_idx=1;
                                            for ($t=0;$t<=$f_loop;$t+=2) {
                                                if ($MAP["WBPAGES"]["$v_boid"]=='26') {
                                                    $TEMPLATE["$paper_id"]["single"][$k][$t]=$source.",".($single_idx);
                                                    $TEMPLATE["$paper_id"]["single"][$k][$t+1]="";
																										$single_idx++;
                                                } else {
                                                    $TEMPLATE["$paper_id"]["single"][$k][$t]=$source.",".($double_idx);
                                                    $TEMPLATE["$paper_id"]["single"][$k][$t+1]=$source.",".($double_idx+1);
																										$double_idx+=2;
                                                }
                                            }
                                            $MAP["BOID"]["$paper_id"][single][$k]=$v_boid;
                                        } else {
                                            ll_echo("[".date("Y/m/d H:i:s")."]".$v_boid.":".$source."不存在");
                                            $err_msg.=$v_boid."訂單".$source."不存在 \r\n";
                                        }
                                    }
                                    $last_boid1=$v_boid;
                                    break;
                            }
                            break;
                        //===================================================================================================================
                        //  桌曆豪華版16張
                        //===================================================================================================================
                        case "32": case "64":
                            $f_loop=30;
                            switch ($MAP[WBFLOW]["$v_boid"]) {
                                //===========================================================================================================
                                //  FTP上傳的訂單
                                //===========================================================================================================
                                case "1":
                                    $opener=opendir($GB_BOOKPATH.$MAP["WBID"]["$v_boid"]."/pdf");
                                    while (false!==($file=readdir($opener))) {
                                        if (substr(strtolower($file),-3)=='pdf') {
                                            $SAVE_FILE[]=$file;
                                        }
                                    }
                                    for ($n=0;$n<$MAP["BONUM"]["$v_boid"];$n++) {
                                        $k++;
                                        foreach ($SAVE_FILE as $f_value) {
                                            $source=$GB_BOOKPATH.$MAP["WBID"]["$v_boid"]."/pdf/".$f_value;
																						$double_idx=1;
																						$single_idx=1;
                                            for ($t=0;$t<=$f_loop;$t+=2) {
																								//簡約版
                                                if ($MAP["WBPAGES"]["$v_boid"]=='32') {
                                                    $TEMPLATE["$paper_id"]["double"][$k][$t]=$source.",".($single_idx);
                                                    $TEMPLATE["$paper_id"]["double"][$k][$t+1]="";
																										$single_idx++;
                                                } else {
                                                    $TEMPLATE["$paper_id"]["double"][$k][$t]=$source.",".($double_idx);
                                                    $TEMPLATE["$paper_id"]["double"][$k][$t+1]=$source.",".($double_idx+1);
																										$double_idx+=2;
                                                }
                                            }
                                        }
                                        $MAP["BOID"]["$paper_id"]["double"][$k]=$v_boid;
																				$MAP["COVER"]["$paper_id"]["$v_boid"][]=$source;
                                    }
                                    break;
                                //===========================================================================================================
                                //  編輯器的訂單
                                //===========================================================================================================
                                case "2":
                                    $source=$GB_BOOKPATH_PDF."spool/".$MAP["WBID"]["$v_boid"].".pdf";
																		if ($single_pdf and !in_array($v_boid,$make_already)) {
																				ll_echo("[".date("Y/m/d H:i:s")."]".$v_boid."單模不存在，重新製作");
																				fopen_exec($GB_W2PPDF."/album/makepdf.php?BID=".$MAP["WBID"]["$v_boid"]."&ORD=".$MAP["YFPBOID"]["$v_boid"].sprintf("%'02s",$MAP["BONUM"]["$v_boid"]));
																				$make_already[]=$v_boid;
																		}
                                    for ($n=0;$n<$MAP["BONUM"]["$v_boid"];$n++) {
                                        if (file_exists($source)) {
                                            $k++;
																					  $double_idx=1;
																						$single_idx=1;
                                            for ($t=0;$t<=$f_loop;$t+=2) {
																								//簡約版
                                                if ($MAP["WBPAGES"]["$v_boid"]=='32') {
                                                    $TEMPLATE["$paper_id"]["double"][$k][$t]=$source.",".($single_idx);
                                                    $TEMPLATE["$paper_id"]["double"][$k][$t+1]="";
																										$single_idx++;
                                                } else {
                                                    $TEMPLATE["$paper_id"]["double"][$k][$t]=$source.",".($double_idx);
                                                    $TEMPLATE["$paper_id"]["double"][$k][$t+1]=$source.",".($double_idx+1);
																										$double_idx+=2;
                                                }
                                            }
                                            $MAP["BOID"]["$paper_id"]["double"][$k]=$v_boid;
                                        } else {
                                            ll_echo("[".date("Y/m/d H:i:s")."]".$v_boid.":".$source."不存在");
                                            $err_msg.=$v_boid."訂單".$source."不存在 \r\n";
                                        }
                                    }
                                    $last_boid2=$v_boid;
                                    break;
                            }
                            break;
                    }
                }
            }
        }

        if ($err_msg!='') {
            send_mail("桌曆缺檔通知",$err_msg);
        }

        $real_loop=0;
        foreach ($TEMPLATE as $paper_id => $idx_ary) {
            foreach ($TEMPLATE[$paper_id] as $work_id => $idx_ary1 ) {
                $real_loop+=ceil(count($TEMPLATE[$paper_id][$work_id])/4);
            }
        }
        
        //資訊陣列echo 
        
        //print_r($DESK_ARY);
       
        //echo "template==============================================================";
        //print_r($TEMPLATE);
       
       // echo "map==============================================================";
        //print_r($MAP);
        
       
        
				$err_msg="";
        try {
            $COMBIN=0;
            $sub_html="";
            foreach ($TEMPLATE as $paper_id => $idx_ary) {
                foreach ($TEMPLATE[$paper_id] as $page_id => $idx_ary) {
                    $loop=ceil(count($TEMPLATE[$paper_id][$page_id])/4);
                    for ($p=0;$p<$loop;$p++) {
											
                        $COMBIN++;
                        $new_pdf =$GB_BOOKPATH_PDF."spool/".date("YmdHi",$DATE_NAME)."_DESK_".$COMBIN."(".$real_loop.").pdf";
                        ll_echo("[".date("Y/m/d H:i:s")."]大版：".basename($new_pdf));
                        $IN_BOID=array();
                        $O_FILE[]=basename($new_pdf);
                        //=====================================================================================================
                        //  落大版
                        //======================================================================================================
                        PDF_HEAD($pdfhw,$new_pdf);
                        $x=0;
                        $y=0;
                        $min1=($p*4)+1;
                        $max1=($p*4)+4;
                        $real_paper="";
                        for ($t1=$min1;$t1<=$max1;$t1++) {
                            if (empty($TEMPLATE[$paper_id][$page_id]["$t1"])) {
                                continue;
                            }
                            $c_pages=$MAP["WBPAGES"][$MAP["BOID"]["$paper_id"]["$page_id"]["$t1"]];
                            if ($c_pages > $real_paper or $real_paper=='') {
                                $real_paper=$c_pages;
                            }
                            $x++;
                            if ($x==2) {
                                $x=0;$y++;
                            }
                        }
                        //=====================================================================================================
                        //  計算實際要開幾頁PDF
                        //======================================================================================================
                        if ($real_paper=='64' or $real_paper=='32') {
                            $r_loop=32;
                        } else {
                            $r_loop=26;
                        }
                        //=====================================================================================================
                        //  實際印刷訂單內容
                        //======================================================================================================
                        for ($i=1;$i<=$r_loop;$i++) {
                            $x=0;
                            $y=0;
                            $pdfhw->begin_page_ext($pdf_width, $pdf_height, "topdown");		//開啟一個新PDF工作頁
                            $min=($p*4)+1;
                            $max=($p*4)+4;
                            for ($t=$min;$t<=$max;$t++) {
                                if (empty($TEMPLATE[$paper_id][$page_id]["$t"]) or empty($TEMPLATE[$paper_id][$page_id]["$t"][$i-1])) {
                                    continue;
                                }
                                $tmp_array=explode(",",$TEMPLATE[$paper_id][$page_id]["$t"][$i-1]);

                                $source=$tmp_array[0];
                                $filepage=$tmp_array[1];
                                $page = LL_NewPage($pdfhw, $doc, $source);

                                $pastX=$startX+($make_width+5)*$px*$x;
                                $pastY=$startY+$make_height*$px*$y;

                                $t_boid=$MAP["BOID"][$paper_id][$page_id][$t];
                                $YFPBOID=$MAP[YFPBOID]["$t_boid"];
                                $BONUM  =$MAP[BONUM]["$t_boid"];
                                //=====================================================================================================
                                //  正面處理
                                //=====================================================================================================
                                if ($i%2==1) {
                                    LL_FitPdiPage($pdfhw, $doc, $pastX, $pastY, $make_width*$px, $make_height*$px, $filepage);
                                    if ($x==0) {
                                        pdflib_font($pdfhw, '黑體', 12,$YFPBOID.sprintf("%'02s",$BONUM),$pastX-5, $pastY-200,1);
                                    } else {
                                        pdflib_font($pdfhw, '黑體', 12,$YFPBOID.sprintf("%'02s",$BONUM),$pastX+$make_width*$px+8, $pastY-200,1);
                                    }
                                } else {
                                //=====================================================================================================
                                //  背面處理
                                //=====================================================================================================
                                    if ($x==0) {
                                        if ($r_loop==16 or $r_loop==14) {
                                            $pastX=$startX+($make_width+5)*$x*$px;
                                            pdflib_font($pdfhw, '黑體', 12,$YFPBOID.sprintf("%'02s",$BONUM),$pastX-5, $pastY-200,1);
                                        } else {
                                            $pastX=$startX+($make_width+5)*($x+1)*$px;
                                            pdflib_font($pdfhw, '黑體', 12,$YFPBOID.sprintf("%'02s",$BONUM),$pastX+$make_width*$px+8, $pastY-200,1);
                                        }
                                    } else {
                                        if ($r_loop==16 or $r_loop==14) {
                                            $pastX=$startX+($make_width+5)*$x*$px;
                                            pdflib_font($pdfhw, '黑體', 12,$YFPBOID.sprintf("%'02s",$BONUM),$pastX+$make_width*$px+8, $pastY-200,1);
                                        } else {
                                            $pastX=$startX+($make_width+5)*($x-1)*$px;
                                            pdflib_font($pdfhw, '黑體', 12,$YFPBOID.sprintf("%'02s",$BONUM),$pastX-5, $pastY-200,1);
                                        }
                                    }
                                    LL_FitPdiPage($pdfhw, $doc, $pastX, $pastY, $make_width*$px, $make_height*$px, $filepage);
                                }
                                LL_ClosePage($pdfhw, $doc, $page);	//關閉pdi
                                $x++;
                                if ($x==2) {
                                    $x=0;$y++;
                                }
                            }
                            LL_CutLine($pdfhw,10);
                            $pdfhw->end_page_ext("");
                        }
												//=====================================================================================================
                        //  資訊頁
                        //=====================================================================================================
												$pdfhw->begin_page_ext($pdf_width, $pdf_height, "topdown");		//開啟一個新PDF工作頁
                        $pdfhw->end_page_ext("");
                        $pdfhw->begin_page_ext($pdf_width, $pdf_height, "topdown");		//開啟一個新PDF工作頁
                        $x=1;
                        $y=0;
                        $min1=($p*4)+1;
                        $max1=($p*4)+4;
                        $real_paper="";
                        for ($t1=$min1;$t1<=$max1;$t1++) {
                            if (empty($TEMPLATE[$paper_id][$page_id]["$t1"])) {
                                continue;
                            }
                            $pastX=$startX+($make_width+5)*$px*$x;
                            $pastY=$startY+$make_height*$px*$y;

                            $t_boid=$MAP["BOID"]["$paper_id"]["$page_id"]["$t1"];
                            $YFPBOID=$MAP[YFPBOID]["$t_boid"];
                            $BONUM  =$MAP[BONUM]["$t_boid"];
                            $GROUPID=$MAP[GROUPID]["$t_boid"];
                            if (!in_array($t_boid,$IN_BOID)) {
                                $IN_BOID[]=$t_boid;
                            }
                            if ($MAP[WBFLOW]["$t_boid"]==1) {
                                $source=$GB_BOOKPATH.$MAP["WBID"]["$t_boid"]."/pdf/body.pdf";
                                if (!file_exists($source)) {
    
                                    $opener=opendir($GB_BOOKPATH.$MAP["WBID"]["$t_boid"]."/pdf");
                                    while (false!==($file=readdir($opener))) {
                                        if (substr(strtolower($file),-3)=='pdf') {
                                            $SAVE_FILE[]=$file;
                                        }
                                    }
                                    for ($n=0;$n<$MAP["BONUM"]["$t_boid"];$n++) {
																				$k++;
                                        foreach ($SAVE_FILE as $f_value) {
																						$source=$GB_BOOKPATH.$MAP["WBID"]["$t_boid"]."/pdf/".$f_value;
                                        }
                                    }     
                              }
                              
                            } else {
                                $source=$GB_BOOKPATH_PDF."spool/".$MAP["WBID"]["$t_boid"].".pdf";
                            }
                            $WBPAGES=$MAP["WBPAGES"]["$t_boid"];

                            if ($MAP["WBPAGES"]["$t_boid"]=='52') {
                               $map_page=array("1"=>"1","2"=>"3","3"=>"5","4"=>"7","5"=>"9","6"=>"11","7"=>"13","8"=>"15","9"=>"17","10"=>"19","11"=>"21","12"=>"23","13"=>"25");
                            } elseif ($MAP["WBPAGES"]["$t_boid"]=='64') {
                               $map_page=array("1"=>"1","2"=>"5","3"=>"7","4"=>"9","5"=>"11","6"=>"13","7"=>"15","8"=>"17","9"=>"19","10"=>"21","11"=>"23","12"=>"25","13"=>"27");
                            } elseif ($MAP["WBPAGES"]["$t_boid"]=='26') {
                               $map_page=array("1"=>"1","2"=>"2","3"=>"3","4"=>"4","5"=>"5","6"=>"6","7"=>"7","8"=>"8","9"=>"9","10"=>"10","11"=>"11","12"=>"12","13"=>"13");
                            } elseif ($MAP["WBPAGES"]["$t_boid"]=='32') {
                               $map_page=array("1"=>"1","2"=>"3","3"=>"4","4"=>"5","5"=>"6","6"=>"7","7"=>"8","8"=>"9","9"=>"10","10"=>"11","11"=>"12","12"=>"13","13"=>"14");
                            }
                            $page = LL_NewPage($pdfhw, $doc, $source);

                            $pre_make_width =43.2*$px;
                            $pre_make_height=35.8*$px;

                            $gap_x=6;
                            $gap_y=2.5;
                            $pre_y=0;
                            $pre_x=0;
                            for ($p_t=1;$p_t<=13;$p_t++) {
                                switch ($p_t) {
                                    case "1":
                                        $pre_startX=16*$px;
                                        $pre_StartY=$pastY-$make_height*$px+60.8*$px;
                                        $pre_pastX=$pastX+$pre_startX;
                                        $pre_pastY=$pre_StartY;
                                        break;
                                    case "4":
                                    case "7":
                                    case "10":
                                        $pre_pastX=$pre_pastX+$gap_x*$px+$pre_x*35.2*$px;
                                        $pre_make_width =35.2*$px;
                                        $pre_make_height=26.7*$px;
                                        $pre_y++;
                                        $pre_x=0;
                                        break;
                                    case "3":
                                    case "6":
                                    case "9":
                                    case "12":
                                    case "13":
                                        $pre_pastX=$pre_pastX+$gap_x*$px+$pre_x*35.2*$px;
                                        $pre_make_width =35.2*$px;
                                        $pre_make_height=26.7*$px;
                                        break;
                                    case "2":
                                    case "5":
                                    case "8":
                                    case "11":
                                        $pre_make_width =43.2*$px; //封面寬度
                                        $pre_make_height=35.8*$px;
                                        $pre_pastX=$pastX+$pre_startX+$pre_make_width+4*$px;
                                        $pre_pastY=$pre_StartY-9.1*$px+(2.5+26.7)*$pre_y*$px;
                                        $pre_make_width =35.2*$px; //內頁寬度
                                        $pre_make_height=26.7*$px;
                                        $pre_x++;
                                        break;
                                }
                                $output_pic=$ROOT_DIR.'./transfer/desk_tmp/'.$YFPBOID."_".$map_page[$p_t].".jpg";
                                if (!file_exists($output_pic)) {
                                    EB_IMPDF2JPG( $source, $output_pic, intval($map_page[$p_t]));
                                }
                                $image = $pdfhw->load_image('auto', $output_pic, "passthrough=true");
                                $pdfhw->fit_image( $image, $pre_pastX, $pre_pastY, "boxsize={"."$pre_make_width $pre_make_height"."} fitmethod=entire");
                            }
                            LL_ClosePage($pdfhw, $doc, $page);	//關閉pdi
                            $pdfhw->setcolor("fillstroke", "rgb", 0, 0, 0, 0);
                            pdflib_font($pdfhw, 'code39', 26, '*'.$YFPBOID.'*', $pastX+$pre_startX+10, $pre_pastY-20);

                            $fontname = get_pdflib_fontname("黑體");
                            $font = $pdfhw->load_font($fontname, "unicode","embedding");
                            $pdfhw->setfont($font, 13);
                            if ($GROUPID!='') {
                                $pdfhw->fit_textline($YFPBOID."-".sprintf("%'02s",$BONUM)." *", $pastX+$pre_startX+13, $pre_pastY-5, "charspacing=10%");
                            } else {
                                $pdfhw->fit_textline($YFPBOID."-".sprintf("%'02s",$BONUM), $pastX+$pre_startX+13, $pre_pastY-5, "charspacing=10%");
                            }
                            $x--;
                            if ($x==-1) {
                                $x=1;$y++;
                            }
                        }
                        LL_CutLine($pdfhw,10);
                        $pdfhw->end_page_ext("");
                       
												
												
                        $pdfhw->end_document("");
                        $sub_html.=make_html($paper_id,$page_id,basename($new_pdf),$IN_BOID);
												//判斷是否跟上一個檔案包含的訂單一樣，如果一樣就不存檔案，降低傳檔數量
												if (!empty($last_in_boid)) {
														$check_same_boid=count(array_diff($last_in_boid,$IN_BOID));//比對兩個陣列是否相同
														$last_count=count($last_in_boid);
														$in_count=count($IN_BOID);
														//要另外判斷兩個陣列是否數量相同
														//因為array_diff只會判斷第二參數陣列是否有元素與第一參數元素有相同
														//EX: A=array(1),B=array(1,2)，array_diff會回傳空陣列，因為B中的1存在於A
														//check_same_boid=0代表兩個陣列相同且陣列數量也相同
														//就代表此檔案的內容與上ㄧ個印件相同所以要從傳檔陣列中移除
														if ($check_same_boid < 1 and $last_count==$in_count) {
																if (($key=array_search(basename($new_pdf),$O_FILE)) !==false) {
																		unset($O_FILE[$key]);
																}
														}
												}
												$last_in_boid=$IN_BOID;
                    }
                }
            }
            ll_echo("[".date("Y/m/d H:i:s")."]產生桌曆檔案清單");
            $html_mail ='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
        		$html_mail.='<html xmlns="http://www.w3.org/1999/xhtml">';
        		$html_mail.='<head>';
      	  	$html_mail.='<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
      		  $html_mail.='<title>W2P工作單'.date("Y-m-d H:i",$insert_time).'</title>';
      		  $html_mail.='</head>';
      		  $html_mail.='<body>';
            $html_mail.='<table width="700" border="1">';
            $html_mail.='<tr><th width="30%">檔案名稱</th>';
    				$html_mail.='<th width="15%">紙別</th>';
            $html_mail.='<th width="15%">加工</th>';
            $html_mail.='<th width="15%">落版方式</th>';
    				$html_mail.='<th width="30%">包含訂單</th>';
        		$html_mail.='</tr>';
            $html_mail.=$sub_html;
            $html_mail.='</table>';
    			  $html_mail.='</body>';
    			  $html_mail.='</html>';

            $s_html = $DATE_NAME."_DESK.html";
      	    $fileopen = fopen($ROOT_DIR.'./transfer/mail/combin/'.$MAIL_DIR.'/'.$s_html,"w+");
      	    fseek($fileopen,0);
      	    fwrite($fileopen,$html_mail);
      	    fclose($fileopen);


          	$title ='桌曆訂單處理明細'.date("Y-m-d H:i",$DATE_NAME);
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
            $emailBody .= 'Content-type: text/html; name='.$DATE_NAME.'_DESK.html'."\n";
            $emailBody .= 'Content-transfer-encoding: base64'."\n";
            $emailBody .= 'Content-disposition: inline; filename'.$DATE_NAME.'_DESK.html'."\n\n";

            $emailBody.= $read."\n";

            $emailBody.="--$boundary--";

    			  $result=mail($mail_addr, $subject, $emailBody, $headers);
           
						if ($err_msg!='') {
								send_mail("桌曆直落訂單重複",$err_msg);
						}
            //開關控制是否直接丟RIP且被份檔案
            if ($IS_RIP) {
                ll_echo("[".date("Y/m/d H:i:s")."]檔案丟enhance及壓縮落版檔案");
								
								if(!$ftp_conn_id = ftp_connect("192.168.50.242")) {
										send_mail("RIP主機FTP連線失敗[桌曆]","");
								} else {
										if (ftp_login($ftp_conn_id, "pmanager", "yfpnpcuser")) {
												ftp_chdir($ftp_conn_id,"W2PDESK");
												foreach ($O_FILE as $o_value) {
														$FILE     =$GB_BOOKPATH_PDF."spool\\$o_value ";
														if (!@ftp_put($ftp_conn_id, iconv("UTF-8","BIG5",$o_value), $FILE, FTP_BINARY)) {
                                send_mail("[桌曆]直落檔案上傳失敗",basename($FILE));
                            } 
												}
												ftp_close($ftp_conn_id);
												if (!is_dir($GB_BOOKPATH_PDF."spool\\deskbackup")) {
														 mkdir($GB_BOOKPATH_PDF."spool\\deskbackup");
												}
												foreach ($O_FILE as $o_value) {
														$path=$GB_BOOKPATH_PDF."spool\\deskbackup\\".date('Ymd',time());
														if (!is_dir($path)) {//若無目錄create 目錄
															 mkdir($path);
														}
														$BACKUP_PATH=$path."\\$o_value ";
														@copy($GB_BOOKPATH_PDF."spool\\".$o_value,$BACKUP_PATH);
														@unlink($GB_BOOKPATH_PDF."spool\\".$o_value);
												}
										}
								}
            }
            unset($TEMPLATE);
            unset($DESK_ARY);
            unset($MAP);
            unset($O_FILE);
            unset($IN_BOID);
            unset($TMP_ARRAY);
						unset($make_already);

            $del_cmd = "rmdir /s/q D:\\www\\transfer\\desk_tmp";
            exec($del_cmd);
            sleep(3);
          	if (!is_dir("D:\\www\\transfer\\desk_tmp")) {
            		mkdir("D:\\www\\transfer\\desk_tmp");
         		}
            ll_echo("[".date("Y/m/d H:i:s")."]作業完畢");
        } catch (PDFlibException $e) {
            $errnum = $e->get_errnum();
            $apiname = $e->get_apiname();
            $errmsg = $e->get_errmsg();
            echo "[".$errmsg."]";
        } catch (Exception $e) {
            die($e);
        }

    } else {
         ll_echo("[".date("Y/m/d H:i:s")."]無桌曆訂單!!");

         send_mail("無桌曆訂單".date("Y-m-d",$DATE_NAME),"");
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
    $obj->set_info("Title",   "Desk");
    $x=0;$y=0;

}

/****************************************************************************************
*  繪製四周裁切線標示, 10px
*****************************************************************************************/
function	LL_CutLine( $pobj)  {
    global $pdf_width,$pdf_height,$make_width,$make_height,$startX,$startY,$px;

    $cutX1=($pdf_width-(($make_width*2+5)*$px))/2+1*$px;
    $cutX2=$cutX1+($make_width-2)*$px;
    $cutX3=$cutX2+(5+2)*$px;
    $cutX4=$cutX3+($make_width-2)*$px;

    $y1=($pdf_height-(($make_height*2)*$px))/2+1*$px;
    $y2=$y1+($make_height-2)*$px;
    $y3=$y2+2*$px;
    $y4=$y3+($make_height-2)*$px;

    $cutY1=($pdf_height-(($make_height*2)*$px))/2+($make_height*2)*$px;
    $cutY2=($pdf_height-(($make_height*2)*$px))/2;
    $Left_x=($pdf_width-(($make_width*2+5)*$px))/2;
    $Right_X=$Left_x+($make_width*2+5)*$px;


		$pobj->setlinewidth(0.25);
		$pobj->moveto($cutX1,$cutY2);			 $pobj->lineto($cutX1,$cutY2-15);
    $pobj->moveto($cutX2,$cutY2);			 $pobj->lineto($cutX2,$cutY2-15);
    $pobj->moveto($cutX3,$cutY2);			 $pobj->lineto($cutX3,$cutY2-15);
    $pobj->moveto($cutX4,$cutY2);			 $pobj->lineto($cutX4,$cutY2-15);

    $pobj->moveto($cutX1,$cutY1);	 $pobj->lineto($cutX1,$cutY1+15);
    $pobj->moveto($cutX2,$cutY1);	 $pobj->lineto($cutX2,$cutY1+15);
    $pobj->moveto($cutX3,$cutY1);	 $pobj->lineto($cutX3,$cutY1+15);
    $pobj->moveto($cutX4,$cutY1);	 $pobj->lineto($cutX4,$cutY1+15);
    //橫線
    $pobj->moveto($Left_x,$y1);		 $pobj->lineto($Left_x-15,$y1);
    $pobj->moveto($Left_x,$y2);		 $pobj->lineto($Left_x-15,$y2);
    $pobj->moveto($Left_x,$y3);		 $pobj->lineto($Left_x-15,$y3);
    $pobj->moveto($Left_x,$y4);		 $pobj->lineto($Left_x-15,$y4);


    $pobj->moveto($Right_X,$y1);	 $pobj->lineto($Right_X+15,$y1);
    $pobj->moveto($Right_X,$y2);	 $pobj->lineto($Right_X+15,$y2);
    $pobj->moveto($Right_X,$y3);	 $pobj->lineto($Right_X+15,$y3);
    $pobj->moveto($Right_X,$y4);	 $pobj->lineto($Right_X+15,$y4);


		$pobj->stroke();
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
/*  函式名稱：make_html($_paper_id,$_work_id,$_pdf_name,$_YFPOBID,$_TYPE)
/*  函式參數： $_paper_id : 紙別代號
/*             $_work_id  : 加工代號
/*             $_pdf_name : 檔案名稱
/*             $_YFPBOID  : 訂單號碼
/*  回傳值  ：
/*  函式功能：產生直落工作單(告知哪個檔案要用哪種紙印)
/***********************************************************************************************/
function make_html($_paper_id="",$_work_id="",$_pdf_name="",$_BOID=array(),$_TYPE="") {
    global $GB_dblk,$MAP,$thislog,$DATE_NAME,$err_msg,$SAVE_LIST;
    $SQL="Select PPID,PPNAME from W2PP where PPID in ('$_paper_id','$_work_id')";
    $query=mssql_query($SQL);
    $WORK_NAME='';
    $PAPER_NAME='';
    while ($rs=mssql_fetch_array($query)) {
        $PPNAME = trim(iconv("BIG5","UTF-8",$rs["PPNAME"])); //紙別
        $PPID   = trim($rs["PPID"]);
        if ($PPID < 600) {
            $PAPER_NAME=$PPNAME;
        } else {
            $WORK_NAME =$PPNAME;
        }
    }
    $html ='<tr>';
    error_log("$_pdf_name ", 3, $thislog);
    $html.='<td>'.$_pdf_name.'</td>';
    $html.='<td>'.$PAPER_NAME.'</td>';
    $html.='<td>';
    $html.=$WORK_NAME;
    $html.='</td>';
    $html.='<td>';
    $idx=$_BOID[0];
    switch ($MAP[WBPAGES]["$idx"]) {
        case "26":case "52":
            $SHOW_PAGE="13頁";
            break;
        default:
            $SHOW_PAGE="16頁";
            break;
    }
    if ($_TYPE=='OLD') {
        $html.="原本拼板-".$SHOW_PAGE;
    } else {
        $html.="直落-".$SHOW_PAGE;
    }
    $html.='</td>';
		$html.='<td>';
    foreach ($_BOID as $value) {
				if ($MAP[GOLD]["$value"]!='') {
						$html.=$MAP[YFPBOID]["$value"]."(底版燙金)<br>";
				} else {
						$html.=$MAP[YFPBOID]["$value"]."<br>";
				}
        $PBOID=$MAP[YFPBOID]["$value"];
        error_log(" $PBOID \r\n", 3, $thislog);
				//記錄開關
				if ($SAVE_LIST) {
						$SQL="Select * from PORDER_ERP1 where BOID='$value' and TIME<>'".$DATE_NAME."' and TIME2 < '".date("Y-m-d H:i:s",$DATE_NAME)."' ";
						$QUERY    = mssql_query($SQL,$GB_dblk);
						$rows     = mssql_num_rows($QUERY);
						if ($rows < 1) {
								$INS_SQL ="Insert into PORDER_ERP1(BOID,TIME,PAPER_ID,WORK_ID,FILENAME,YFPBOID) ";
								if ($WORK_NAME!='') {
										$work_id=$_work_id;
								} else {
										$work_id="";
								}
								$INS_SQL.=" values('".$value."','".$DATE_NAME."','".$_paper_id."','".$work_id."','".basename($_pdf_name,".pdf")."','".$PBOID."')";
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
/*  函式名稱：send_mail($_title=,$_content)
/*  函式參數： $_title   : 信件標題
/*             $_content : 信件內容
/*  回傳值  ：
/*  函式功能：Email通知信寄發
/***********************************************************************************************/
function send_mail($_title="",$_content="") {
    global $mail_addr;
		
		$mail_title       = "『".$_title."』";

		$MailHeader = "MIME-Version: 1.0\r\n
		Content-type: text/html; charset=UTF-8\r\n
		From: WebMaster@cloudw2p.com\r\n
		Reply-To: WebMaster@cloudw2p.com\r\n
		X-Priority: 1\r\n
		X-MSMail-Priority: High\r\n
		X-Mailer: PHP/".phpversion()."\r\n";

	  $HeaderFirst = "=?UTF-8?B?" . base64_encode($mail_title) . "?=";

		$AX = mail($mail_addr,$HeaderFirst,$_content,$MailHeader);
    
}

/***********************************************************************************************/
/*  函式名稱：DelAryEmt($ary,$element)
/*  函式參數：$ary      : 陣列
/*            $element  : 要刪除的元素值
/*  回傳值  ：刪除後的陣列
/*  函式功能：刪除陣列中某個元素
/***********************************************************************************************/
if (!function_exists("DelAryEmt")) {
    Function DelAryEmt($ary,$element){
        $offset = array_search($element,$ary);
        if($offset!==false){
            $array_count=count($ary);
            $length=$offset-$array_count+1;
            if($length==0){
                array_splice($ary,$offset);
            }else{
                array_splice($ary,$offset,$length);
            }
            return $ary;
        }else{
            return $ary;
        }
    }
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
