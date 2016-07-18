<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "DTD/xhtml1-strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<?
		/*********************************************************************************************** 
		*  WTP 批次轉檔主程式 w2p_trans.php
		*  設定檔：       w2p_trans.ini.php
		*  合板產品分支： w2p_array.php
		*  photo產品分支：vow_array.php
		*  XML 樣版：     xml_template.php
		*  轉檔清冊信件： w2p_mail.php
		*  物件         : class.php
		*  2010/07/05 Code by Arvin
		*  2010/11/18 Arvin 修改T-shrit轉檔清冊
		*  2010/12/8  Arvin BOPTION欄位從PORDERDATA移至 PORDER 內
		*  2010/12/23 Arvin 配合非合板產品的拼版需用 ERP 訂單編號，修改相關程式(改成一開始就傳 XML 要VB訂單號碼)
		*  2011/01/03 Arvin 增加零售業轉檔(MOMO)
		*  2011/05/02 Arvin 增加無訂單通知信
		*  2012/10/02 Arvin 增加後台控制單筆轉檔處理
		*  2013/02/19 Arvin 增加名片新紙別 691(F) 雙霧雙局光
		*  2013/04/18 Arvin 增加婚紗展判斷不轉XML
		*  2013/09/09 Arvin 作品集產品作業移至87主機處理
		***********************************************************************************************/
    ignore_user_abort(true);
    set_time_limit(0);

    if ($_GET[BOID]!='') {
        echo "<script>";
        echo " alert('轉檔處理中');";
        echo " window.open('','_parent',''); window.close();";
        echo "</script>";
    }
		$JUST_ORDER=false;
		if ($_GET[ORDER]!='') {
				$JUST_ORDER=true;
		}
		require_once('class.php');
	
		$obj=new search_data();
		/********************************************************************
		 *  連結資料庫
		 ********************************************************************/
		$obj->connect();
		/********************************************************************
		 *  讀取 INI 設定 
		 ********************************************************************/
		$INI_SET=$obj->ini_set();
	
    $book_array=array();
		$trans_list =array();
		$error_array=array();	
		$SEND_TOTAL=false;
		$RENAME_ARRAY=array("BODY","COVER","WING"); //作品集產品名稱對應陣列

		/********************************************************************
		 * 紙別代號對應設定
		 ********************************************************************/
     //白紗紙別代號轉換
   	 $paper_map[white]=array("100" => "P","101" => "C","102" => "N","103" => "L","104" => "H","105" =>"Q","106" => "R","107" => "S",
                            "108" => "Z","109" => "100P","114"=>"100P道林","110"=>"150P","111" => "200P","112" => "250P","113" => "80P","687"=>"K",
                            "688"=>"K","689"=>"W","690"=>"W","200"=>"230P","128"=>"150P雪銅");

     //貼紙紙別代號轉換
     $paper_map[kuo]  =array("190" => "9A","191" => "SY19B","192" => "POLY","193" => "POLY","194" => "DLW","195" => "Silver","196" => "Removable");

		try {
				//指定特定訂單的陣列
			
        //$ord_array=array ('1602262123');
				$mode=true; //用來解開class裡面的京城判斷，這樣就不用每次要另外轉檔要進去改SQL語法
        if ($_GET[BOID]!='') {
            $ord_array=explode(",",$_GET[BOID]);
						$mode=false;
        }
        //判斷主機鎖住後台單獨轉檔用
        if ($INI_SET[tran_type]=='IN') {
            $UPDATE_TYPE="'batchlock101'"; //自製主機
            $MAIL_SERVER="自製處理主機";
        } elseif ($INI_SET[tran_type]=='OUT') {
            $UPDATE_TYPE="'batchlock87'";  //外包主機
            $MAIL_SERVER="外包及作品集處理主機";
				} elseif ($INI_SET[tran_type]=='ALL') {
						$UPDATE_TYPE="'batchlock87','batchlock101'";  //外包主機 //自製主機
						$MAIL_SERVER="W2P處理主機";
        } else {
            $MAIL_SERVER="測試主機";
            $UPDATE_TYPE="'XXX'";        //測試主機
        }
				$SQL =' Update PVARS set value=\'1\' where item=\'order\' and name in('.$UPDATE_TYPE.') ';
  	    $query=mssql_query($SQL,$GB_dblk);
				$master=$obj->query($ord_array,$mode);
				if ($master[NUM_ROW] < 1) {
						$obj->ll_echo("[".$obj->show_time()."]無轉檔資料");
            $mail_content = "無訂單資料";
						$obj->send_mail("[".$MAIL_SERVER."]W2P無訂單通知",$mail_content,3);
            //解開後台轉檔按鈕
						$SQL =' Update PVARS set value=\'0\' where item=\'order\' and name in('.$UPDATE_TYPE.') ';
            $query=mssql_query($SQL,$GB_dblk);
						die;
				} else {
						$obj->ll_echo("[".$obj->show_time()."]轉檔作業開始");
						//登記轉檔資料
						$insert_time=$obj->date_time();
						foreach ($master[BOID] as $value) {
								$SQL =' Insert into pordereg (BOID,TSNEW,FLAGPO) ';
								$SQL.=' Values (\''.$value.'\',\''.$insert_time.'\',\'N\')';
								$query=mssql_query($SQL,$GB_dblk);
						}	
						$obj->ll_echo($master[mail_content]);
						$obj->ll_echo("共計：".$master[NUM_ROW]."筆");
				}
				/***************************************************************************************
				* 預計轉檔清單寄送
				****************************************************************************************/
				if ($INI_SET[SEND_MAIL]=='Y' and $master[NUM_ROW] > 0 ) {
						$mail_content = $master[mail_content]."共".$master[NUM_ROW]. "筆";
						$obj->send_mail("[".$MAIL_SERVER."]正式機-W2P預計轉檔清單",$mail_content,5);
				}
				/***************************************************************************************
				* 先傳 XML 要訂單號碼
				****************************************************************************************/
        //透過INI設定判斷這是要轉外包訂單還是自製，外包XML流水號500開始
				if ($INI_SET[tran_type]=='IN') {
				    $step=1;
        } else {
            $step=500;
        }
				if (is_array($Final)) {
						foreach ($Final as $f_key => $final_value) {
								foreach ($final_value as $f_key2) {
										$tax_check=true; //用來判斷未稅金額與含稅金額是否正常
										//$show_array=$$f_key;
										//生成XML檔案
										$obj->make_xml(${$f_key},$f_key2,$step);
										$rs_ftp=$obj->choose_ftp(${$f_key}[$f_key2][FACID]);
										$step++;
										/***************************************************************************************
										* XML 傳送 biz talk
										****************************************************************************************/
										if ($INI_SET[TRANS_XML]=='Y' and ${$f_key}[$f_key2][PDT_GP]!='W001' and $rs_ftp[FACYFP]=='1' and $tax_check) {  //XML 傳送開關
												$result=$obj->trans_xml ($f_key2,$f_key,'M');
												if (trim($result[trans_list])!='') {
														$trans_list[$f_key][]=$result[trans_list]; //訂單成功開立資料
												} else {
														$error_array[]=$result[error_array];//訂單開立失敗資料
												}
                        //額外XML檔案處理
                        if (${$f_key}["$f_key2"][EXTRA]!='' and ${$f_key}["$f_key2"][CPBONUS]!='' and ${$f_key}["$f_key2"][CPBONUS]==0 and $tax_check) {
                            $result=$obj->trans_xml ($f_key2,$f_key,'E');
                          	if (trim($result[trans_list])!='') {
    														$trans_list[$f_key][]=$result[trans_list]; //訂單成功開立資料
    												} else {
    														$error_array[]=$result[error_array];//訂單開立失敗資料
    												}
                        }
										} else {
												// XML 傳送開關未打開且稅額判斷沒有問題，檔案處理完即算成功
												if ($tax_check) {
														$trans_list[$f_key][]=$f_key2;
														
														${$f_key}["$f_key2"]["YFPBOID"]=$f_key2;  
														${$f_key}["$f_key2"]["VVBOID"] =$f_key2;
														
														//更新轉檔時間
														$query_string='Update PORDER set BOERPTIME=\''.$insert_time.'\' where BOID=\''.$f_key2.'\'';
														$query = mssql_query($query_string,$GB_dblk);
														
														//更新完成時間
														$SQL=' Update pordereg set BOERP=\''.$obj->date_time().'\',FLAGPO=\'F\' where BOID=\''.$f_key2.'\' and TSNEW=\''.$insert_time.'\'';
														$query=mssql_query($SQL,$GB_dblk);
												} else {
														$error_array[]=$f_key2."ERP訂單開立失敗，原因：訂單稅額有誤，請檢查訂單資訊";
														 //失敗更新狀態
														$query_string='Update PORDEREG set FLAGPO=\'E\' where BOID=\''.$f_key2.'\' and TSNEW=\''.$insert_time.'\'';
														$query = mssql_query($query_string,$GB_dblk);
												}												
										}
								}
				  	}
            //解開後台轉檔按鈕
						$SQL =' Update PVARS set value=\'0\' where item=\'order\' and name in('.$UPDATE_TYPE.') ';
            $query=mssql_query($SQL,$GB_dblk);
				}
				/***************************************************************************************
				* 執行 W2P 及 VOW 的操作
				****************************************************************************************/
				if ($master[NUM_ROW] > 0) {
						$loop=array("WTP_ARRAY","VOW_ARRAY");
					  foreach ($loop as $value) {
								$tmp_array=$$value;
								if (!is_array($tmp_array)) continue;
								if ($value=='WTP_ARRAY') {
          				//處理外包及合版資料
									include($ROOT_FOLDER."./transfer/w2p_array.php");
                  if (count($trans_list[WTP_ARRAY]) > 0) {
                      $LOOP_ARRAY=$trans_list[WTP_ARRAY];
                      $B_MAIL_TITLE="外包產品-";
                      $MAIL_N="out";
          						include($ROOT_FOLDER."./transfer/w2p_mail.php");
          				}
								} elseif ($value=='VOW_ARRAY') {
                    //處理自製資料
                    include($ROOT_FOLDER."./transfer/vow_array.php");
                    if (count($trans_list[VOW_ARRAY]) > 0) {
                        $LOOP_ARRAY=$trans_list[VOW_ARRAY];
                        $B_MAIL_TITLE="自製產品-";
                        $MAIL_N="in";
                        include($ROOT_FOLDER."./transfer/w2p_mail.php");
                    }
								}
						}
				}
		}	catch (Exception $e) {
			  if($makelog)	error_log("[$session_key],die( $e)\n", 3, $thislog);
			  die($e);
		}
		/***************************************************************************************
		* 實際轉檔清單寄送
		****************************************************************************************/
		if ($INI_SET[SEND_MAIL]=='Y' and (count($trans_list["WTP_ARRAY"]) > 0 or count($trans_list["VOW_ARRAY"]) > 0 or count($error_array) > 0)) {
				$j=0;
				$k=0;
				$mail_content='';
				$mail_content = "轉檔成功：\r\n";
				if (is_array($trans_list)) {
						foreach ($trans_list as $t_key => $value) {
                foreach ($trans_list[$t_key] as $vlaue1 ) {
    								$mail_content = $mail_content.$vlaue1."\r\n";
    								$j++;
                }
						}
				}
				$mail_content = $mail_content."共$j 筆"."\r\n";
				$mail_content = $mail_content."\r\n轉檔失敗：\r\n";
        $err_mail_content = "轉檔失敗：\r\n";
				if (is_array($error_array)) {
						foreach ($error_array as $key => $value) {
								$mail_content     = $mail_content.$value."\r\n";
                $err_mail_content = $err_mail_content.$value."\r\n";
								$k++;
						}
				}
				$mail_content = $mail_content."共$k 筆";
        $err_mail_content = $err_mail_content."共$k 筆";
				$obj->send_mail("[".$MAIL_SERVER."]正式機-W2P實際轉檔清單",$mail_content);
        if ($k >0) { 
            $obj->send_mail("[".$MAIL_SERVER."]異常訂單通知 $k 筆",$err_mail_content,4);
        }
		}
		unset($WTP_ARRAY);
		unset($VOW_ARRAY);
		unset($Final);
		unset($print_array);
		unset($error_array);
		unset($tmp_array);
		unset($trans_list);
		unset($FAC_SEND_ARRAY);
		unset($MAP_ARRAY);
		unset($totl_send_array);
		unset($rs_ftp);
		$obj->ll_echo("[".$obj->show_time()."]轉檔作業結束");
		$obj=null;
?>
</body>
</html>
