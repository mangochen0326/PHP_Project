<?php
    /*=========================================================================================
     *  明信片直落拼板併版PDF功能
     *
     *  2011/11/4 Arvin 明信片直落
     *  2013/02/18 Arvin 新版資訊頁開始使用
     *  2013/02/22 Arvin 修正直落單本呼叫，並修正預覽圖製作及落版製作次數，降低loading~
     *  2012/02/25 Arvin 修改新版資訊頁FTP上傳的在條碼上方增加顯示 總份數 - 第幾份
     *  2013/04/29 Arvin 增加單本落組合拼版的BID抓取，避免造成makeallpdf77_ftp.php在抓來源PDF時因為BID沒有跟著改造成錯誤
     *  2013/10/31 Arvin 明信片全部改直落編排方式調整，以48模6版為基礎
		 *  2013/11/20 Arvin 丟RIP改用FTP上傳
		 *  2014/01/21 Arvin 直落清單改紀錄於PORDER_ERP1資料表內由後台查詢顯示
		 *  2014/05/27 Arvin 增加顯示是否上光代號並移動頁數顯示位置
		 *  2014/06/26 Arvin 調整RIP Hot Folder
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
   	//include_once($ROOT_DIR.'./album/makepdf_font.php');		          //設定字體資源
   	/*========================================================================================
  	*  Environment Variable Set
		*========================================================================================*/
    $DATE_NAME =time();
    $MAIL_DIR  =date("Ymd");
   

    $single_pdf=true;  //是否重做單模
    $RIP       =true;  //是否丟RIP
		$SAVE_LIST =true; //是否記錄於後台彙整資料表，測試的時候要關起來，不然會造成測試資料後台彙整查詢出來(false)
    $MAIL_ADDR ="arvin.chen@email.yfp.com.tw,herlonglong@email.yfp.com.tw,chu61@email.yfp.com.tw";
    //$MAIL_ADDR ="arvin.chen@email.yfp.com.tw";

    $px=2.8346456;
    //單位mm
    $make_width = (148+4)*$px;		//單張寬
    $make_height = (104.2+4)*$px;	//單張高

    $pdf_width =320*$px;      //pdf 文件寬
    $pdf_height=460*$px;      //pdf 文件高
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
    $thislog=$ROOT_DIR.'./transfer/log/'.$MAIL_DIR."/".$MAIL_DIR."_POSTCARD.log";

    //起點X座標
    $startX=($pdf_width-$make_width*2)/2;
    //起點Y座標
    $startY=($pdf_height-$make_height*4)/2+$make_height;

    $POST_CARD=array();
    $O_FILE   =array();
		$RIP_FILE =array();
		
    $this_day   =date("d");
    $this_month =date("m");
    $this_year  =date("Y");
    //$s_date=mktime(0,0,0,6,19,$this_year);
    //$e_date=mktime(23,59,59,6,19,$this_year);
    //$s_date=mktime(0,0,0,10,3,$this_year);
    //$e_date=mktime(23,59,59,10,3,$this_year);

		
    $s_date=mktime(0,0,0,$this_month,$this_day-1,$this_year);
    $e_date=mktime(23,59,59,$this_month,$this_day-1,$this_year);
		

    $SQL='Select A.BOID from pordereport A join PORDER B on A.BOID=B.BOID join WBOOK C on B.BID=C.WBID ';
    $SQL.=' where C.BTYPE=\'77\' and A.NOCOMBIN <>\'Y\' and B.BOSTATUS=\'12\' and A.TSNEW between \''.$s_date.'\' and \''.$e_date.'\' ';
    //$SQL.=' where C.BTYPE=\'77\' and A.BOID in (\'1408202213\') ';
    //$SQL.=' where C.BTYPE=\'77\' and B.YFPBOID in (\'VBE21221\',\'VBE21220\',\'VBE21253\')';
		//$SQL.=' where C.BTYPE=\'77\' and B.YFPBOID in (\'VBE21221\',\'VBE21220\')';
   

    
    //echo $SQL;
    //die;
    $QUERY    = mssql_query($SQL,$GB_dblk);
    $rows     = mssql_num_rows($QUERY);
  
    

    if ($rows > 0) {
        ll_echo("[".date("Y/m/d H:i:s")."]訂單編號：");
        $SQL  = "SELECT D.GROUPID,C.ITEM,C.V3,C.V1,A.BOID,A.BONUM,B.WBPAGES,A.BID,B.WBFLOW,A.YFPBOID,convert(varbinary(max),A.BORADDR) as BORADDR, ";
        $SQL .= " convert(varbinary(max),A.BORNAME) as BORNAME,convert(varbinary(max),A.BOMEMO) as BOMEMO ";
        $SQL .= " FROM PORDER A join WBOOK B on A.BID=B.WBID ";
        $SQL .= " join W2POP C on A.BOID=C.BOID ";
        $SQL .= " left join pordergroup D on A.BOID=D.GBOID ";
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


        $SQL.=" order by A.BONUM desc,A.YFPBOID";

        //echo $SQL;

        ll_echo("[".date("Y/m/d H:i:s")."]共：".$count."筆");


    		$QUERYSQL    = mssql_query($SQL,$GB_dblk);
    		while($REC = mssql_fetch_array($QUERYSQL)) {
    				$BONUM   = trim($REC[BONUM]);   //數量
            $BOID    = trim($REC[BOID]);
            $GROUPID = trim($REC[GROUPID]); //購物車ID
            $WBID    = trim($REC[BID]);     // BID
            $WBPAGES = trim($REC[WBPAGES]); //頁數
            $WBFLOW  = trim($REC[WBFLOW]);  //WBFLOW
            $YFPBOID = trim($REC[YFPBOID]); //訂單編號
            $ITEM    = trim($REC[ITEM]);
            $V1      = trim($REC[V1]);
            $V3      = trim($REC[V3]);
            //$BONUM=1;
            //PPID 小於 600的為紙別
            if ($V1 < '600' and $ITEM=='PPID') {
                $TMP_ARRAY["$BOID"]["PPID"]=$V1;
            //PPID 大於 600 且不為754 755包裝紙盒的為加工項目
            } elseif ($V1=='737' and $ITEM=='PPID') {
                $TMP_ARRAY["$BOID"]["WORK"]=$V1;
            //分別判斷每個PDF的訂購數量
            } elseif ($ITEM=='NAME') {
                $MAP[BONUM1]["$BOID"][$V3]=$V1;
								for ($d=0;$d<$V1;$d++) {
										$MAP[FILE]["$BOID"][]=$V3;
								}
								$MAP["FTP"]["$BOID"]=0;
            }
						
            $MAP[WBID]["$BOID"]=$WBID;
            $MAP[WBFLOW]["$BOID"]=$WBFLOW;
            $MAP[BONUM]["$BOID"]=$BONUM;
            $MAP[GROUPID]["$BOID"]=$GROUPID;
            $MAP[YFPBOID]["$BOID"]=$YFPBOID;
            $MAP[WBPAGES]["$BOID"]=$WBPAGES;
						
    		}
        //======================================================================
				// 計算每種紙別含加工的數量
				//======================================================================
				foreach ($TMP_ARRAY as $T_BOID => $T_VALUE) {
						if ($TMP_ARRAY["$T_BOID"][WORK]!='') {
								$COUNT[$TMP_ARRAY["$T_BOID"]["PPID"].$TMP_ARRAY["$T_BOID"]["WORK"]]+=$MAP["BONUM"][$T_BOID];
								for ($i=0;$i<$MAP["BONUM"][$T_BOID];$i++) {
										$NUM[$TMP_ARRAY["$T_BOID"]["PPID"].$TMP_ARRAY["$T_BOID"]["WORK"]][]=$T_BOID;
								}
						} else {
								$COUNT[$TMP_ARRAY["$T_BOID"]["PPID"]."NO"]+=$MAP["BONUM"][$T_BOID];
								for ($i=0;$i<$MAP["BONUM"][$T_BOID];$i++) {
										$NUM[$TMP_ARRAY["$T_BOID"]["PPID"]."NO"][]=$T_BOID;
								}
						}            
        }
				//print_r($NUM);
				//======================================================================
				// 將陣列中的訂單資料依據分別的紙別及加工條件逐筆倒出來排列
				//======================================================================
				foreach ($NUM as $paper_work =>$ary1) {
					
						$loop=floor(count($ary1)/48); //計算有幾個集合,每48模6版A3為一單位
						$other=count($ary1)-$loop*48; //剩下的數量
						
						//$p=0;
						$idx=0; //設定陣列起始index
						$big=0; //設定大版初始值
						for ($d=0;$d<$loop*48;$d++) {		
						
								$WORK=$TMP_ARRAY[$ary1[$d]]["WORK"]; //加工條件
								if ($WORK=='') {
										$WORK="NO";//如果沒有加工給予NO的key值辨別
								}
								//======================================================================
								// 計算每六直落大版後index才增加  
								// 每份大版的座標左上(0,0)開始到右下(3,0)總共8模
								// Ex:當有份明信片有六套，它的位置就是在六版的每一大版的(0,0)位置
								//    而不是先前的排在第1模的(0,0),(0,1) (1,0) (1,1) (2,0),(2,1)                               
								//======================================================================
								if ($d%6==0 and $d>1) {
										$big=0;
										$idx++;
								}
								$in_idx2=$big*8+$idx;
								
								$chk_status=$POST_CARD[$TMP_ARRAY[$ary1[$d]]["PPID"]][$WORK][$in_idx2];
								//判斷座標是否已經紀錄資料了，如果記錄了index加1後重新判斷，直到找到空的位置才存
								while ($chk_status!='') {
										$idx++;
										$in_idx2=$big*8+$idx;
										$chk_status=$POST_CARD[$TMP_ARRAY[$ary1[$d]]["PPID"]][$WORK][$in_idx2];
								}
								$POST_CARD[$TMP_ARRAY[$ary1[$d]]["PPID"]][$WORK][$in_idx2]=$ary1[$d];
								
								//$p++;
								$big++;
						}
						//======================================================================
						// 計算剩下來的模數可以湊成幾大版。
						// Ex:總共65/48餘17，17/8=2，所以剩下的排列順序依照2大版直落的方式排列
						//======================================================================
						$other_big=ceil($other/8);
						$p=0;
						$big=0;
						if ($other_big > 1) {
								//迴圈的起始為 48 x 前面的六版直落的數量到加上剩餘可湊的大版數
								for ($o=$loop*48;$o<($loop*48+$other_big*8);$o++) {
										//因為是8的倍數後面可能有沒資料的，所以要判斷是否有資料在排列
										if ($ary1[$o]!='') {
												$WORK=$TMP_ARRAY[$ary1[$o]]["WORK"]; //加工條件
												if ($WORK=='') {
														$WORK="NO"; //如果沒有加工給予NO的key值辨別
												}
												//======================================================================
												// 計算每剩下的直落大版後index才增加  
												// 每份大版的座標左上(0,0)開始到右下(3,0)總共8模
												// Ex:當有份明信片有六套，它的位置就是在六版的每一大版的(0,0)位置
												//    而不是先前的排在第1模的(0,0),(1,0),(0,1),(1,1) (0,2),(1,2)                               
												//======================================================================
												if ($p % $other_big==0 and $p>1) {
														$big=0;
														$idx++;
												}
												$in_idx2=$big*8+$idx;
												$chk_status=$POST_CARD[$TMP_ARRAY[$ary1[$o]]["PPID"]][$WORK][$in_idx2];
												//判斷座標是否已經紀錄資料了，如果記錄了index加1後重新判斷，直到找到空的位置才存
												while ($chk_status!='') {
														$idx++;
														$in_idx2=$big*8+$idx;
														$chk_status=$POST_CARD[$TMP_ARRAY[$ary1[$o]]["PPID"]][$WORK][$in_idx2];
												}
												$POST_CARD[$TMP_ARRAY[$ary1[$o]]["PPID"]][$WORK][$in_idx2]=$ary1[$o];
												
												$p++;
												$big++;
										}								
								}
						} else {
								//======================================================================
								// 剩下來的模數不超過8模則直接按照(0,0)~(3,0)排序資料
								// Ex:總共50/48餘2，2/8 < 1，所以剩下的排列依照順序排列 (0,0) (1,0)
								//======================================================================
								for ($kk=$loop*48;$kk<count($ary1);$kk++) {
										$WORK=$TMP_ARRAY[$ary1[$kk]]["WORK"];
										if ($WORK=='') {
												$WORK="NO";
										}
										$chk_status=$POST_CARD[$TMP_ARRAY[$ary1[$kk]]["PPID"]][$WORK][$idx];
										//判斷座標是否已經紀錄資料了，如果記錄了index加1後重新判斷，直到找到空的位置才存
										while ($chk_status!='') {
												$idx++;
												$chk_status=$POST_CARD[$TMP_ARRAY[$ary1[$d]]["PPID"]][$WORK][$idx];
										}
										$POST_CARD[$TMP_ARRAY[$ary1[$kk]]["PPID"]][$WORK][$idx]=$ary1[$kk];
								}
						}
				}
				
				//==================================================================
        //  把陣列重新依據索引重新依序排列
        //==================================================================
				foreach ($POST_CARD as $paper_id=>$ary1) {
						foreach ($ary1 as $work_id =>$ary2) {
								ksort($POST_CARD[$paper_id][$work_id]);
						}
				}
				$make_already=array();
        ll_echo("[".date("Y/m/d H:i:s")."]PDF檔案陣列整理");
        //===================================================================================================================
        //  整理實際處理的PDF清單陣列
        //===================================================================================================================
        $err_msg="";
        foreach ($POST_CARD as $paper_id => $ary_work) {
            $k=0;
            foreach ($ary_work as $work => $ary_boid) {
                $k=0;
                foreach ($ary_boid as $s_idx=> $v_boid) {
                    $SAVE_FILE=array();
                    switch($MAP["WBPAGES"]["$v_boid"]) {
                        //===================================================================================================================
                        //  明信片八張不同款
                        //===================================================================================================================
                        case "32":
                            switch ($MAP[WBFLOW]["$v_boid"]) {
                                //===========================================================================================================
                                //  FTP上傳的訂單
                                //===========================================================================================================
                                case "1":
																		//先刪除做預覽的PDF
																		$opener1=opendir($GB_BOOKPATH.$MAP["WBID"]["$v_boid"]);
                                    while (false!==($dfile=readdir($opener1))) {
                                        if (substr(strtolower($dfile),-3)=='pdf') {
                                            @unlink($GB_BOOKPATH.$MAP["WBID"]["$v_boid"]."/".$dfile);
                                        }
                                    }
																
                                    $opener=opendir($GB_BOOKPATH.$MAP["WBID"]["$v_boid"]."/pdf");
                                    while (false!==($file=readdir($opener))) {
                                        if (substr(strtolower($file),-3)=='pdf') {
                                            $SAVE_FILE[]=$file;
                                        }
                                    }
                                    $r_loop=$MAP["BONUM"]["$v_boid"]/(count($SAVE_FILE)/8);
                                    $MAP["TOTAL"]["$v_boid"]=$MAP["BONUM"]["$v_boid"];
                                    $t_i=0;
																		$k++; $j=0;
																		//當判斷八張不同款迴圈已經超過檔案數量時，就讓基底歸零避免抓不到檔案名稱
																		if (($MAP["FTP"]["$v_boid"]*8)>=count($SAVE_FILE)) {
																				$MAP["FTP"]["$v_boid"]=0;
																		}
																		for ($fi=$MAP["FTP"]["$v_boid"]*8;$fi<($MAP["FTP"]["$v_boid"]*8)+8;$fi++) {
																				$source=$GB_BOOKPATH.$MAP["WBID"]["$v_boid"]."/pdf/".$SAVE_FILE[$fi];
																				if ($j>15) {
																						 $j=0;$k++;
																				}
																				if ($j==0) {
																						$t_i++;
																						$MAP["COUNT"][$v_boid][$s_idx]=$t_i;
																				}
																				$TEMPLATE["$paper_id"]["$work"][$s_idx][$j]=$source.",1";
																				$TEMPLATE["$paper_id"]["$work"][$s_idx][$j+1]=$source.",2";
																				$j+=2;
																				$MAP["BOID"]["$paper_id"]["$work"][$s_idx]=$v_boid;
																		}
																		$MAP["FTP"]["$v_boid"]++;
                                    
                                    break;
                                //===========================================================================================================
                                //  編輯器的訂單
                                //===========================================================================================================
                                case "2":
                                    $source=$GB_BOOKPATH_PDF."spool/".$MAP["WBID"]["$v_boid"].".pdf";
																		if ($single_pdf and !in_array($v_boid,$make_already)) {
																				ll_echo("[".date("Y/m/d H:i:s")."]".$v_boid."單模重新製作");
																				fopen_exec($GB_W2PPDF."/album/makepdf.php?BID=".$MAP["WBID"]["$v_boid"]."&ORD=".$MAP["YFPBOID"]["$v_boid"].sprintf("%'02s",$MAP["BONUM"]["$v_boid"]));
																				$make_already[]=$v_boid;
																		}
																		if (file_exists($source)) {
																				//$k++;
																				for ($t=0;$t<=15;$t+=2) {
																						$TEMPLATE["$paper_id"]["$work"][$s_idx][$t]=$source.",".($t+1);
																						$TEMPLATE["$paper_id"]["$work"][$s_idx][$t+1]=$source.",".($t+2);
																				}
																				$MAP["BOID"]["$paper_id"]["$work"][$s_idx]=$v_boid;
																		} else {
																		    ll_echo("[".date("Y/m/d H:i:s")."]".$v_boid.":".$source."不存在");
																		    $err_msg.=$v_boid."訂單".$source."不存在 \r\n";
																		}
                                    break;
                            }
                            break;
                        //===================================================================================================================
                        //  明信片八張同款
                        //===================================================================================================================
                        case "4":
                            switch ($MAP[WBFLOW]["$v_boid"]) {
                                //===========================================================================================================
                                //  FTP上傳的訂單
                                //===========================================================================================================
                                case "1":
																		//先刪除做預覽的PDF
																		$opener1=opendir($GB_BOOKPATH.$MAP["WBID"]["$v_boid"]);
                                    while (false!==($dfile=readdir($opener1))) {
                                        if (substr(strtolower($dfile),-3)=='pdf') {
                                            @unlink($GB_BOOKPATH.$MAP["WBID"]["$v_boid"]."/".$dfile);
                                        }
                                    }
																
																		$source=$GB_BOOKPATH.$MAP["WBID"]["$v_boid"]."/pdf/".$MAP["FILE"]["$v_boid"][$MAP["FTP"]["$v_boid"]].".pdf";
																		$f_name=basename($f_value,".pdf");
																	
																		$k++;
																		$MAP["COUNT"][$v_boid]["$s_idx"]=($MAP["FTP"]["$v_boid"]+1);
																		for ($t=0;$t<=15;$t+=2) {
																				$TEMPLATE["$paper_id"]["$work"][$s_idx][$t]=$source.",1";
																				$TEMPLATE["$paper_id"]["$work"][$s_idx][$t+1]=$source.",2";
																		}
																		$MAP["BOID"]["$paper_id"]["$work"][$s_idx]=$v_boid;
																		
                                    $MAP["TOTAL"]["$v_boid"]=$MAP["BONUM"]["$v_boid"];
																		$MAP["FTP"]["$v_boid"]++;
                                    break;
                                //===========================================================================================================
                                //  編輯器的訂單
                                //===========================================================================================================
                                case "2":
                                    $source=$GB_BOOKPATH_PDF."spool/".$MAP["WBID"]["$v_boid"].".pdf";
																		if ($single_pdf and !in_array($v_boid,$make_already)) {
																				ll_echo("[".date("Y/m/d H:i:s")."]".$v_boid."單模重新製作");
																				fopen_exec($GB_W2PPDF."/album/makepdf.php?BID=".$MAP["WBID"]["$v_boid"]."&ORD=".$MAP["YFPBOID"]["$v_boid"].sprintf("%'02s",$MAP["BONUM"]["$v_boid"]));
																				$make_already[]=$v_boid;
																		}
																		if (file_exists($source)) {
																				$k++;
																				for ($t=0;$t<=15;$t+=2) {
																						$TEMPLATE["$paper_id"]["$work"][$s_idx][$t]=$source.",1";
																						$TEMPLATE["$paper_id"]["$work"][$s_idx][$t+1]=$source.",2";
																				}
																				$MAP["BOID"]["$paper_id"]["$work"][$s_idx]=$v_boid;
																		} else {
																		    ll_echo("[".date("Y/m/d H:i:s")."]".$v_boid.":".$source."不存在");
																		    $err_msg.=$v_boid."訂單".$source."不存在 \r\n";
																		}
                                    break;
                            }
                            break;
                    }
                }
            }
        }

        if ($err_msg!='') {
            send_mail("明信片缺檔通知",$err_msg);
        }

      
        $real_loop=0;
				$big_loop=0;
        foreach ($TEMPLATE as $paper_id => $idx_ary) {
            foreach ($TEMPLATE[$paper_id] as $work_id => $idx_ary1 ) {
                $real_loop+=ceil(count($TEMPLATE[$paper_id][$work_id])/8);
								$tmp_count=ceil(count($TEMPLATE[$paper_id][$work_id])/8);
								$big_loop+=ceil($tmp_count/6);
								
                //echo ceil(count($TEMPLATE[$paper_id][$work_id])/8)."<br>";
            }
        }
				
				
			  //echo "----------------------------------POSTCARD <br>";
        //print_r($POST_CARD);
        //echo "----------------------------------Template <br>";
        //print_r($TEMPLATE);
        //echo "----------------------------------Map <br>";
        //print_r($MAP);
        //die;
				
				$err_msg="";
        try {
            $COMBIN=0;
						$BIG=0;
            $sub_html="";
            foreach ($TEMPLATE as $paper_id => $idx_ary) {
                foreach ($TEMPLATE[$paper_id] as $work_id => $idx_ary) {
										
                    $loop=ceil(count($TEMPLATE[$paper_id][$work_id])/8);
										$SAVE_FILE=array();
                    for ($p=0;$p<$loop;$p++) {
                        $COMBIN++;
                        $new_pdf =$GB_BOOKPATH_PDF."spool/".date("YmdHi",$DATE_NAME)."_POSTCARD_".$COMBIN."(".$real_loop.").pdf";
                        ll_echo("[".date("Y/m/d H:i:s")."]大版：".basename($new_pdf));
                        //$IN_BOID=array();
                        $O_FILE[]=basename($new_pdf);
												$SAVE_FILE[]=$new_pdf;
												$BIG_FILE[basename($new_pdf)]=array();
                        PDF_HEAD($pdfhw,$new_pdf);
                        //=====================================================================================================
                        //  資訊頁
                        //=====================================================================================================
                        $pdfhw->begin_page_ext($pdf_width, $pdf_height, "topdown");		//開啟一個新PDF工作頁
                        $x=0;
                        $y=0;
                        $min1=($p*8);
                        $max1=($p*8)+7;

                        if (!is_dir("D:\\www\\transfer\\postcard_tmp")) {
                        		mkdir("D:\\www\\transfer\\postcard_tmp");
                     		}

                        for ($t1=$min1;$t1<=$max1;$t1++) {
                            if (empty($TEMPLATE[$paper_id][$work_id]["$t1"])) {
                                continue;
                            }
                            $pastX=$startX+$make_width*$x;
                            $pastY=$startY+$make_height*$y;

                            $t_boid=$MAP["BOID"][$paper_id][$work_id][$t1];
                            $YFPBOID=$MAP[YFPBOID]["$t_boid"];
                            $BONUM  =$MAP[BONUM]["$t_boid"];
                            $BORADDR=$MAP[BORADDR]["$t_boid"];
                            $BORNAME=$MAP[BORNAME]["$t_boid"];
                            $WBPAGES=$MAP[WBPAGES]["$t_boid"];
                            $BOMEMO =$MAP[BOMEMO]["$t_boid"];
                            $WBFLOW =$MAP[WBFLOW]["$t_boid"];
                            ll_echo("[".date("Y/m/d H:i:s")."]訂單：".$t_boid);
														if (!in_array($YFPBOID,$BIG_FILE[basename($new_pdf)])) {
                                $BIG_FILE[basename($new_pdf)][]=$t_boid;
                            }
                            $GROUPID=$MAP[GROUPID]["$t_boid"];
                            //八張八款
                            if ($WBPAGES=='32') {
                                $pre_x=0;
                                $pre_y=0;
                                $pre_make_width =$make_width*0.2; //內頁寬度
                                $pre_make_height=$make_height*0.2;
                                $fit_ftp=false;
                                $ftp_name="";
                                for ($k=0;$k<16;$k=$k+2) {
                                    $s_value=$TEMPLATE[$paper_id][$work_id]["$t1"][$k];
                                    $tmp_ary=explode(",",$s_value);
                                    $source=$tmp_ary[0];
                                    if ($WBFLOW=='1') {
                                        $fit_page=1;
                                    } else {
                                        $fit_page=$tmp_ary[1];
                                    }
                                    if ($pre_x==4) {
                                        $pre_x=0;
                                        $pre_y++;
                                    }
                                    $pre_startX=10*$px+$pre_x*($make_width*0.2+2*$px+1*$px);
                                    $pre_StartY=$pastY-70*$px+$pre_y*($make_height*0.2+5*$px);
                                    $pre_pastX=$pastX+$pre_startX;
                                    $pre_pastY=$pre_StartY;
                                    if ($MAP["COUNT"]["$t_boid"]["$t1"]!='') {
                                        $ftp_name=$t1;
                                        $fit_ftp=true;
                                    }
                                    $output_pic=$ROOT_DIR.'./transfer/postcard_tmp/'.$YFPBOID."_".basename($source,".pdf")."_".$fit_page.".jpg";
                                    if (!file_exists($output_pic)) {
                                        EB_IMPDF2JPG( $source, $output_pic, intval($fit_page));
                                    }
                                    $image = $pdfhw->load_image('auto', $output_pic, "passthrough=true");
                                    $pdfhw->fit_image( $image, $pre_pastX, $pre_pastY, "boxsize={"."$pre_make_width $pre_make_height"."} fitmethod=entire");
                                    $pre_x++;
                                }
                                $pdfhw->setcolor("fillstroke", "rgb", 0, 0, 0, 0);
                                if ($fit_ftp) {
                                    $show_count=$MAP["COUNT"]["$t_boid"]["$ftp_name"];
                                    $show_total=$MAP["TOTAL"]["$t_boid"];
                                    pdflib_font($pdfhw, '黑體', 10, $show_total."-".$show_count, $pastX+$pre_startX+30, $pre_pastY+60);
                                }
                                pdflib_font($pdfhw, 'code39', 26, '*'.$YFPBOID.'*', $pastX+$pre_startX-17, $pre_pastY+90);
                                $fontname = get_pdflib_fontname("黑體");

                                $font = $pdfhw->load_font($fontname, "unicode","embedding");
                                $pdfhw->setfont($font, 13);
                                //判斷購物車在後面加* 號
                                if ($GROUPID!='') {
                                    $pdfhw->fit_textline($YFPBOID."-".sprintf("%'02s",$BONUM)." *", $pastX+$pre_startX-15, $pre_pastY+103, "charspacing=10%");
                                } else {
                                    $pdfhw->fit_textline($YFPBOID."-".sprintf("%'02s",$BONUM), $pastX+$pre_startX-15, $pre_pastY+103, "charspacing=10%");
                                }
																if ($work_id=='737') {
																		pdflib_font($pdfhw, '黑體', 12,"N",$startX-6, 100,1);
																		pdflib_font($pdfhw, '黑體', 12,"N",$startX-6, 1000,1);
																		pdflib_font($pdfhw, '黑體', 12,"N",$startX+$make_width*2+6, 100,1);
																		pdflib_font($pdfhw, '黑體', 12,"N",$startX+$make_width*2+6, 1000,1);
																} else {
																		pdflib_font($pdfhw, '黑體', 12,"Y",$startX-6, 100,1);
																		pdflib_font($pdfhw, '黑體', 12,"Y",$startX-6, 1000,1);
																		pdflib_font($pdfhw, '黑體', 12,"Y",$startX+$make_width*2+6, 100,1);
																		pdflib_font($pdfhw, '黑體', 12,"Y",$startX+$make_width*2+6, 1000,1);
																}
																
                            //八張同款
                            } else {
                                $fit_ftp=false;
                                $ftp_name="";

                                $pre_make_width =$make_width*0.6; //內頁寬度
                                $pre_make_height=$make_height*0.6;

                                $s_value=$TEMPLATE[$paper_id][$work_id]["$t1"][0];
                                $tmp_ary=explode(",",$s_value);
                                $source=$tmp_ary[0];
                                
                                $pre_startX=25*$px;
                                $pre_StartY=$pastY-30*$px;
                                $pre_pastX=$pastX+$pre_startX;
                                $pre_pastY=$pre_StartY;

                                //$f_name=basename($source,".pdf");
                                if ($MAP["COUNT"]["$t_boid"]["$t1"]!='') {
                                    $ftp_name=$t1;
                                    $fit_ftp=true;
                                }

                                $output_pic=$ROOT_DIR.'./transfer/postcard_tmp/'.$YFPBOID.'_'.basename($source,".pdf").'_1.jpg';
                                if (!file_exists($output_pic)) {
                                    EB_IMPDF2JPG( $source, $output_pic, 1);
                                }
                                $image = $pdfhw->load_image('auto', $output_pic, "passthrough=true");
                                $pdfhw->fit_image( $image, $pre_pastX, $pre_pastY, "boxsize={"."$pre_make_width $pre_make_height"."} fitmethod=entire");

                                $pdfhw->setcolor("fillstroke", "rgb", 0, 0, 0, 0);
                                if ($fit_ftp) {
                                    $show_count=$MAP["COUNT"]["$t_boid"]["$ftp_name"];
                                    $show_total=$MAP["TOTAL"]["$t_boid"];
                                    pdflib_font($pdfhw, '黑體', 10, $show_total."-".$show_count, $pastX+$pre_startX+80*$px+45, $pre_pastY+20);
                                }

                                pdflib_font($pdfhw, 'code39', 26, '*'.$YFPBOID.'*', $pastX+$pre_startX+80*$px, $pre_pastY+50);
                                $fontname = get_pdflib_fontname("黑體");

                                $font = $pdfhw->load_font($fontname, "unicode","embedding");
                                $pdfhw->setfont($font, 13);
                                if ($GROUPID!='') {
                                    $pdfhw->fit_textline($YFPBOID."-".sprintf("%'02s",$BONUM)." *", $pastX+$pre_startX+81*$px, $pre_pastY+63, "charspacing=10%");
                                } else {
                                    $pdfhw->fit_textline($YFPBOID."-".sprintf("%'02s",$BONUM), $pastX+$pre_startX+81*$px, $pre_pastY+63, "charspacing=10%");
                                }
																
																if ($work_id=='737') {
																		pdflib_font($pdfhw, '黑體', 12,"N",$startX-6, 100,1);
																		pdflib_font($pdfhw, '黑體', 12,"N",$startX-6, 1000,1);
																		pdflib_font($pdfhw, '黑體', 12,"N",$startX+$make_width*2+6, 100,1);
																		pdflib_font($pdfhw, '黑體', 12,"N",$startX+$make_width*2+6, 1000,1);
																} else {
																		pdflib_font($pdfhw, '黑體', 12,"Y",$startX-6, 100,1);
																		pdflib_font($pdfhw, '黑體', 12,"Y",$startX-6, 1000,1);
																		pdflib_font($pdfhw, '黑體', 12,"Y",$startX+$make_width*2+6, 100,1);
																		pdflib_font($pdfhw, '黑體', 12,"Y",$startX+$make_width*2+6, 1000,1);
																}
                            }                            
                            $x++;
                            if ($x==2) {
                                $x=0;$y++;
                            }
                        }
                        LL_CutLine($pdfhw,10);
                        $pdfhw->end_page_ext("");
                        $pdfhw->begin_page_ext($pdf_width, $pdf_height, "topdown");		//開啟一個新PDF工作頁
                        $pdfhw->end_page_ext("");
                        //=====================================================================================================
                        //  實際印刷訂單內容
                        //=====================================================================================================
                        for ($i=1;$i<=16;$i++) {
                            $x=0;
                            $y=0;
                            $pdfhw->begin_page_ext($pdf_width, $pdf_height, "topdown");		//開啟一個新PDF工作頁
                            $min=($p*8);
                            $max=($p*8)+7;
                            for ($t=$min;$t<=$max;$t++) {
                                if (empty($TEMPLATE[$paper_id][$work_id]["$t"])) {
                                    continue;
                                }
                                $tmp_array=explode(",",$TEMPLATE[$paper_id][$work_id]["$t"][$i-1]);
                                $source=$tmp_array[0];
                                $filepage=$tmp_array[1];
                                $page = LL_NewPage($pdfhw, $doc, $source);
                                $pastX=$startX+$make_width*$x;
                                $pastY=$startY+$make_height*$y;

                                $t_boid=$MAP["BOID"][$paper_id][$work_id][$t];
                                $YFPBOID=$MAP[YFPBOID]["$t_boid"];
                                $BONUM  =$MAP[BONUM]["$t_boid"];
                                //=====================================================================================================
                                //  正面處理
                                //=====================================================================================================
                                if ($i%2==1) {
                                    LL_FitPdiPage($pdfhw, $doc, $pastX, $pastY, $make_width, $make_height, $filepage);
                                    if ($x==0) {
                                        pdflib_font($pdfhw, '黑體', 12,$YFPBOID.sprintf("%'02s",$BONUM),$pastX-5, $pastY-200,1);
                                    } else {
                                        pdflib_font($pdfhw, '黑體', 12,$YFPBOID.sprintf("%'02s",$BONUM),$pastX+$make_width+5, $pastY-200,1);
                                    }
                                } else {
                                //=====================================================================================================
                                //  背面處理
                                //=====================================================================================================
                                    if ($x==0) {
                                        $pastX=$startX+$make_width*($x+1);
                                         pdflib_font($pdfhw, '黑體', 12,$YFPBOID.sprintf("%'02s",$BONUM),$pastX+$make_width+5, $pastY-200,1);
                                    } else {
                                        $pastX=$startX+$make_width*($x-1);
                                         pdflib_font($pdfhw, '黑體', 12,$YFPBOID.sprintf("%'02s",$BONUM),$pastX-5, $pastY-200,1);
                                    }																		
                                    LL_FitPdiPage($pdfhw, $doc, $pastX, $pastY, $make_width, $make_height, $filepage);
                                }
																pdflib_font($pdfhw, '黑體', 12,$COMBIN."-".$i,$startX+$make_width-10*$px, $startY+$make_height*3+10);
                                LL_ClosePage($pdfhw, $doc, $page);	//關閉pdi
                                $x++;
                                if ($x==2) {
                                    $x=0;$y++;
                                }
                            }
                            LL_CutLine($pdfhw,10);
														
														if ($work_id=='737') {
																pdflib_font($pdfhw, '黑體', 12,"N",$startX-6, 100,1);
																pdflib_font($pdfhw, '黑體', 12,"N",$startX-6, 1000,1);
																pdflib_font($pdfhw, '黑體', 12,"N",$startX+$make_width*2+6, 100,1);
																pdflib_font($pdfhw, '黑體', 12,"N",$startX+$make_width*2+6, 1000,1);
														} else {
																pdflib_font($pdfhw, '黑體', 12,"Y",$startX-6, 100,1);
																pdflib_font($pdfhw, '黑體', 12,"Y",$startX-6, 1000,1);
																pdflib_font($pdfhw, '黑體', 12,"Y",$startX+$make_width*2+6, 100,1);
																pdflib_font($pdfhw, '黑體', 12,"Y",$startX+$make_width*2+6, 1000,1);
														}
																
                            $pdfhw->end_page_ext("");
                        }
                        $pdfhw->end_document("");
                        //$sub_html.=make_html($paper_id,$work_id,basename($new_pdf),$IN_BOID);
                    }
										$fi=0;
										$CMD='';
										//$FIRST_CMD = 'C:\APP\pdftk\bin\pdftk.exe ';
										$show_yfpboid=array();
										$BIG_ARY=array();
										foreach ($SAVE_FILE as $value) {
												$CMD.=' '.$value.' ';
												$fi++;
												$BIG_ARY[]=$value;
												foreach ($BIG_FILE[basename($value)] as $yfp_value) {
														if (!in_array($yfp_value,$show_yfpboid)) {
																$show_yfpboid[]=$yfp_value;
														}
												}
												if ($fi%6==0) {
														$BIG++;
														$out_bigcombin =$GB_BOOKPATH_PDF."spool/".date("YmdHi",$DATE_NAME)."_POSTCARD_BIG_".$BIG."(".$big_loop.").pdf";
														$END_CMD=' cat output '.$out_bigcombin.' ';
														ll_echo("[".date("Y/m/d H:i:s")."]合併大版：".basename($out_bigcombin)."檔案:".$CMD);
														//exec($FIRST_CMD.$CMD.$END_CMD);
														$CMD="";
														$sub_html.=make_html($paper_id,$work_id,basename($out_bigcombin),$show_yfpboid);
														$show_yfpboid=array();
														$RIP_FILE[]=basename($out_bigcombin);
														//=====================================================================================================
														//  PDFLib 拼大版
														//=====================================================================================================
														PDF_HEAD($pdfhw,$out_bigcombin);
														foreach ($BIG_ARY as $small_pdf) {
																$pdfdata=LL_GetPdfPages($small_pdf);
																$page = LL_NewPage($pdfhw, $doc, $small_pdf);
																for ($si=1;$si<=$pdfdata[totalpage];$si++) {
																		$pdfhw->begin_page_ext($pdf_width, $pdf_height, "topdown");		//開啟一個新PDF工作頁
																		LL_FitPdiPage($pdfhw, $doc, 0, $pdf_height,  $pdf_width, $pdf_height, $si);
																		$pdfhw->end_page_ext("");
																}
																LL_ClosePage($pdfhw, $doc, $page);	//關閉pdi
														}
														$pdfhw->end_document("");
														$BIG_ARY=array();
												}
										}
										if ($fi%6>0) {
												$BIG++;
												$out_bigcombin =$GB_BOOKPATH_PDF."spool/".date("YmdHi",$DATE_NAME)."_POSTCARD_BIG_".$BIG."(".$big_loop.").pdf";
												$END_CMD=' cat output '.$out_bigcombin.' ';
												ll_echo("[".date("Y/m/d H:i:s")."]合併大版：".basename($out_bigcombin)."檔案:".$CMD);
												//exec($FIRST_CMD.$CMD.$END_CMD);
												$sub_html.=make_html($paper_id,$work_id,basename($out_bigcombin),$show_yfpboid);
												$RIP_FILE[]=basename($out_bigcombin);
												//=====================================================================================================
												//  PDFLib 拼大版
												//=====================================================================================================
												PDF_HEAD($pdfhw,$out_bigcombin);
												foreach ($BIG_ARY as $small_pdf) {
														$pdfdata=LL_GetPdfPages($small_pdf);
														$page = LL_NewPage($pdfhw, $doc, $small_pdf);
														for ($si=1;$si<=$pdfdata[totalpage];$si++) {
																$pdfhw->begin_page_ext($pdf_width, $pdf_height, "topdown");		//開啟一個新PDF工作頁
																LL_FitPdiPage($pdfhw, $doc, 0, $pdf_height,  $pdf_width, $pdf_height, $si);
																$pdfhw->end_page_ext("");
														}
														LL_ClosePage($pdfhw, $doc, $page);	//關閉pdi
												}
												$pdfhw->end_document("");
												
										}
                }
            }
            ll_echo("[".date("Y/m/d H:i:s")."]產生明信片檔案清單");
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

            $s_html = $DATE_NAME."_POSTCARD.html";
      	    $fileopen = fopen($ROOT_DIR.'./transfer/mail/combin/'.$MAIL_DIR.'/'.$s_html,"w+");
      	    fseek($fileopen,0);
      	    fwrite($fileopen,$html_mail);
      	    fclose($fileopen);


          	$title ='明信片訂單處理明細'.date("Y-m-d H:i",$DATE_NAME);
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
            $emailBody .= 'Content-type: text/html; name='.$DATE_NAME.'_POSTCARD.html'."\n";
            $emailBody .= 'Content-transfer-encoding: base64'."\n";
            $emailBody .= 'Content-disposition: inline; filename'.$DATE_NAME.'_POSTCARD.html'."\n\n";

            $emailBody.= $read."\n";

            $emailBody.="--$boundary--";

    			  $result=mail($MAIL_ADDR, $subject, $emailBody, $headers);
            
						if ($err_msg!='') {
								send_mail("明信片直落訂單重複",$err_msg);
						}
						
						
						
            //RIP變數用來判斷是否丟檔案到RIP主機
            if ($RIP) {
                ll_echo("[".date("Y/m/d H:i:s")."]檔案丟Enhance及壓縮落版檔案");
								if(!@$ftp_conn_id = ftp_connect("192.168.50.242")) {
										send_mail("RIP主機FTP連線失敗[明信片]","");
								} else {
										if (@ftp_login($ftp_conn_id, "pmanager", "yfpnpcuser")) {
												@ftp_chdir($ftp_conn_id,"W2PCARD");
												foreach ($RIP_FILE as $o_value) {
														$FILE     =$GB_BOOKPATH_PDF."spool\\$o_value ";
														if(!@ftp_put($ftp_conn_id, iconv("UTF-8","BIG5",$o_value), $FILE, FTP_BINARY)) {
																send_mail("Enhance檔案上傳失敗",$FILE);
														}
												}
												foreach ($RIP_FILE as $o_value) {
														$BACKUP_PATH=$GB_BOOKPATH_PDF."spool\\postcardbackup\\$o_value";
														@copy($GB_BOOKPATH_PDF."spool\\".$o_value,$BACKUP_PATH);
														@unlink($GB_BOOKPATH_PDF."spool\\".$o_value);
												}
										}
										@ftp_close($ftp_conn_id);
								}
            }

            unset($TEMPLATE);
            unset($POST_CARD);
            unset($MAP);
            unset($O_FILE);
            unset($TMP_ARRAY);
						unset($NUM);
						unset($RIP_FILE);
            $del_cmd = "rmdir /s/q D:\\www\\transfer\\postcard_tmp";
            exec($del_cmd);
            sleep(3);
          	if (!is_dir("D:\\www\\transfer\\postcard_tmp")) {
            		mkdir("D:\\www\\transfer\\postcard_tmp");
         		}
            ll_echo("[".date("Y/m/d H:i:s")."]作業完畢");
        } catch (PDFlibException $e) {
            $errnum = $e->get_errnum();
            $apiname = $e->get_apiname();
            $errmsg = $e->get_errmsg();
        } catch (Exception $e) {
            die($e);
        }

    } else {
         ll_echo("[".date("Y/m/d H:i:s")."]無明信片訂單!!");

         send_mail("無明信片訂單".date("Y-m-d",$DATE_NAME),"");
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
    $obj->set_info("Title",   "PostCard");
    $x=0;$y=0;

}

// -------------------------------------------------
//繪製四周裁切線標示, 10px
function	LL_CutLine( $pobj, $cutline)  {
    global $startX,$startY,$make_width,$make_height,$pdf_width,$pdf_height,$px;
		// 直
		$pobj->moveto($startX+2*$px,$startY-$make_height);
		$pobj->lineto($startX+2*$px, $startY-$make_height-$cutline);

    $pobj->moveto($startX+$make_width-2*$px,$startY-$make_height);
		$pobj->lineto($startX+$make_width-2*$px, $startY-$make_height-$cutline);

    $pobj->moveto($startX+$make_width+2*$px,$startY-$make_height);
		$pobj->lineto($startX+$make_width+2*$px, $startY-$make_height-$cutline);

    $pobj->moveto(($startX+$make_width*2)-2*$px,$startY-$make_height);
		$pobj->lineto(($startX+$make_width*2)-2*$px, $startY-$make_height-$cutline);

    $pobj->moveto($startX+2*$px,$startY+$make_height*3);
		$pobj->lineto($startX+2*$px, $startY+$make_height*3+$cutline);

    $pobj->moveto($startX+$make_width-2*$px,$startY+$make_height*3);
		$pobj->lineto($startX+$make_width-2*$px, $startY+$make_height*3+$cutline);

    $pobj->moveto($startX+$make_width+2*$px,$startY+$make_height*3);
		$pobj->lineto($startX+$make_width+2*$px, $startY+$make_height*3+$cutline);

    $pobj->moveto(($startX+$make_width*2)-2*$px,$startY+$make_height*3);
		$pobj->lineto(($startX+$make_width*2)-2*$px, $startY+$make_height*3+$cutline);

    // 橫 (左)
    $pobj->moveto($startX,$startY-$make_height+2*$px);
		$pobj->lineto($startX-$cutline,$startY-$make_height+2*$px);

    $pobj->moveto($startX,$startY-2*$px);
		$pobj->lineto($startX-$cutline,$startY-2*$px);

    $pobj->moveto($startX,$startY+2*$px);
		$pobj->lineto($startX-$cutline,$startY+2*$px);

    $pobj->moveto($startX,$startY+$make_height-2*$px);
		$pobj->lineto($startX-$cutline,$startY+$make_height-2*$px);

    $pobj->moveto($startX,$startY+$make_height+2*$px);
		$pobj->lineto($startX-$cutline,$startY+$make_height+2*$px);

    $pobj->moveto($startX,$startY+$make_height*2-2*$px);
		$pobj->lineto($startX-$cutline,$startY+$make_height*2-2*$px);

    $pobj->moveto($startX,$startY+$make_height*2+2*$px);
		$pobj->lineto($startX-$cutline,$startY+$make_height*2+2*$px);

    $pobj->moveto($startX,$startY+$make_height*3-2*$px);
		$pobj->lineto($startX-$cutline,$startY+$make_height*3-2*$px);

    //橫(右)

    $pobj->moveto($startX+$make_width*2,$startY-$make_height+2*$px);
		$pobj->lineto($startX+$make_width*2+$cutline,$startY-$make_height+2*$px);

    $pobj->moveto($startX+$make_width*2,$startY-2*$px);
		$pobj->lineto($startX+$make_width*2+$cutline,$startY-2*$px);

    $pobj->moveto($startX+$make_width*2,$startY+2*$px);
		$pobj->lineto($startX+$make_width*2+$cutline,$startY+2*$px);

    $pobj->moveto($startX+$make_width*2,$startY+$make_height-2*$px);
		$pobj->lineto($startX+$make_width*2+$cutline,$startY+$make_height-2*$px);

    $pobj->moveto($startX+$make_width*2,$startY+$make_height+2*$px);
		$pobj->lineto($startX+$make_width*2+$cutline,$startY+$make_height+2*$px);

    $pobj->moveto($startX+$make_width*2,$startY+$make_height*2-2*$px);
		$pobj->lineto($startX+$make_width*2+$cutline,$startY+$make_height*2-2*$px);

    $pobj->moveto($startX+$make_width*2,$startY+$make_height*2+2*$px);
		$pobj->lineto($startX+$make_width*2+$cutline,$startY+$make_height*2+2*$px);

    $pobj->moveto($startX+$make_width*2,$startY+$make_height*3-2*$px);
		$pobj->lineto($startX+$make_width*2+$cutline,$startY+$make_height*3-2*$px);


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
/*  函式功能：產生明信片直落工作單(告知哪個檔案要用哪種紙印)
/***********************************************************************************************/
function make_html($_paper_id="",$_work_id="",$_pdf_name="",$_YFPBOID=array(),$_TYPE="") {
		global $GB_dblk,$MAP,$DATE_NAME,$err_msg,$SAVE_LIST;;
    $SQL="Select PPID,PPNAME from W2PP where PPID in ('$_paper_id','$_work_id')";
    $query=mssql_query($SQL);
    $WORK_NAME='';
    $PAPER_NAME='';
    while ($rs=mssql_fetch_array($query)) {
        $PPNAME = trim(iconv("BIG5","UTF-8",$rs["PPNAME"])); //紙別
        $PPID   = trim($rs["PPID"]);
        if ($PPID < 600) {
            if ($PPID=='150') {
                $PAPER_NAME="禾風原卡280P";
            } else {
                $PAPER_NAME=$PPNAME;
            }
        } else {
            $WORK_NAME =$PPNAME;
        }
    }

    $html ='<tr>';
    $html.='<td>'.$_pdf_name.'</td>';
    $html.='<td>'.$PAPER_NAME.'</td>';
    $html.='<td>';
    $html.=$WORK_NAME;
    $html.='</td>';
    $html.='<td>';
    if ($_TYPE=='OLD') {
        $html.="原本拼板";
    } else {
        $html.="直落";
    }
    $html.='</td>';
		$html.='<td>';
    //foreach ($_YFPBOID as $value) {
    //    $html.=$value."<br>";
    //}
		foreach ($_YFPBOID as $value) {
				$html.=$MAP[YFPBOID]["$value"]."<br>";
					//記錄開關
				if ($SAVE_LIST) {
						$SQL="Select * from PORDER_ERP1 where BOID='".$value."' and TIME<>'".$DATE_NAME."' and TIME2 < '".date("Y-m-d H:i:s",$DATE_NAME)."' ";
						$QUERY    = mssql_query($SQL,$GB_dblk);
						$rows     = mssql_num_rows($QUERY);
						if ($rows < 1) {
								$INS_SQL ="Insert into PORDER_ERP1(BOID,TIME,PAPER_ID,WORK_ID,FILENAME,YFPBOID) ";
								if ($WORK_NAME!='') {
										$work_id=$_work_id;
								} else {
										$work_id="";
								}
								$INS_SQL.=" values('".$value."','".$DATE_NAME."','".$_paper_id."','".$work_id."','".basename($_pdf_name,".pdf")."','".$MAP[YFPBOID]["$value"]."')";
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
