<?php
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
					*  2010/12/24 Arvin 移除 Preview檔案製做
					*  2011/03/22 Arvin 增加作品集拼板程式呼叫
					*  2011/04/14 Arvin 增加FTP上傳作品集拼板
					*  2011/05/04 Arvin 增加FTP 傳檔失敗Mail 通知相關人員
					*  2011/06/02 Arvin 增加明信片FTP上傳檔案處理
					*  2011/06/17 Arvin 增加加工條件資料於轉檔清冊備註顯示
					*  2011/07/20 Arvin 增加判斷BOINVTYPE 發票型態，若為21則在BOMEMO內加上二聯式捐贈，25為電子發票
					*  2011/07/26 Arvin 判斷優惠券產品折價後為0元時顯示兌換券，自取顯示在工作單的備註
					*  2011/08/04 Arvin 判斷裝訂方式代號顯示在作品集PDF檔案名稱內，作品集檔案命名改為 BTYPE + BODY(COVER)+ _ + 裝訂方式(B1~B5) + YFPBOID + 數量.pdf
					*  2011/09/20 Arvin FTP上傳桌、掛曆檔案處理
					*  2011/12/14 Arvin 移除轉檔時生產工廠判斷，直接抓PORDER.FACID出來分辨此筆訂單要轉哪個生產工廠
					*  2012/03/06 Arvin 增加筆記本轉檔相關設定
					*  2012/05/16 Arvin 無框畫轉檔處理
					*  2012/07/05 Arvin 增加拼圖處理
					*  2012/08/03 Arvin 增加便利貼處理
					*  2012/08/24 Arvin 增加GROUPID、FLOWNEW判斷購物車主訂單處理
					*  2012/10/16 Arvin 吸水杯墊轉回由永豐生產
					*  2012/11/29 Arvin 增加Iphone5處理
					*  2013/01/02 Arvin iphone5轉成直落排除
					*  2013/01/15 Arvin 增加自製Template名片處理
					*  2013/02/20 Arvin 名片自製獨立拼板，不用 YTD也不與VDP共用
					*  2013/04/15 Arvin 增加喜帖處理
					*  2013/04/25 Arvin 圓形鏡盒處理
					*  2013/05/06 Arvin A5、A6厚筆記本封面處理
          *  2013/06/19 Arvin 增加處理BTYPE=40的自製企業DM
          *  2013/07/02 Arvin 直噴機產品改為外包訂單
					*  2013/09/04 Arvin 隨身瓶處理
					*  2013/10/02 Arvin PLA隨身瓶處理
					*  2013/10/16 Arvin 增加新版桌曆、掛曆處理
					*  2013/10/30 Arvin 增加喜帖燙金黑板寄送
					*  2013/12/03 Arvin 喜帖燙金資訊增加寄送給依您印，燙金黑板檔案改用FTP方式傳送
					*  2014/01/03 Arvin 修正喜帖信封燙金透過FTP的方式沒抓到檔案
					*  2014/01/13 Arvin 桌曆底板燙金處理
					*  2014/07/14 Arvin 增加名信片喜帖處理
					*  2014/09/15 Arvin 增加桌曆、喜帖燙金檔案寄送通知生管
					*  2014/10/20 Arvin 增加直式掛勾掛曆處理
					*  2014/11/12 Arvin 厚版無框畫處理
					*  2014/11/13 Arvin VDP處理
					*  2015/04/30 Arvin 拼圖除了A3尺寸外其餘都改依您印製作
					*  2015/06/26 Arvin 調整轉單不給檔直接不做檔案處理，減少花費時間
					*  2015/08/31 Arvin 增加保溫瓶拼板
					***********************************************************************************************/
					$obj->ll_echo("[".$obj->show_time()."]自製類產品轉檔");
					foreach ($VOW_ARRAY as $key => $value1) {
								//成功與否開關
								$TRANS_STATUS=true;	

                if (!is_array($trans_list["VOW_ARRAY"])) {
                    continue;
                }
								//判斷訂單編號要在XML 傳檔成功的陣列才進行後續動作
								if (!in_array($key,$trans_list["VOW_ARRAY"])) {
										continue;
								}

								$WBFLOW     = $tmp_array[$key][WBFLOW];  //產品製作方式 (編輯器、FTP、Template)
								$BTYPE      = $tmp_array[$key][BTYPE];
								$BID        = $tmp_array[$key][BID];       //作品ID
                $FACID      = $tmp_array[$key][FACID];     //生產工廠ID
								$BOID       = $key;
								$BORNAME    = $tmp_array[$key][BORNAME];   //收件人
								$BOMID      = $tmp_array[$key][BOMID];     //郵遞區號
								$BMEMO      = $tmp_array[$key][BMEMO];     //品項
                $BOMEMO     = $tmp_array[$key][BOMEMO];    //備註
								if ($JUST_ORDER) {
										$BOMEMO = $BOMEMO."[轉單不給檔]";
								}
								$BORADDR    = $tmp_array[$key][BORADDR];   //送貨地址
                $BOSEND     = $tmp_array[$key][BOSEND];    //送貨方式
                //$BOGROUP    = $tmp_array[$key][BOGROUP];  //是否合併寄送(主訂單號碼)
                $GROUPID    = $tmp_array[$key][GROUPID];   //是否為購物車訂單
                $FLOWNEW    = $tmp_array[$key][FLOWNEW];   //購物車訂單運費(有值得代表主訂單)
								$BONUM      = $tmp_array[$key][BONUM];     //數量
                $BOPRICE    = $tmp_array[$key][BOPRICE];    //總金額
								$BOTIME     = $tmp_array[$key][BOTIME];     //訂單成立時間
                $BOSHIPTIME = $tmp_array[$key][BOSHIPTIME]; //訂單約交日
								$UNAME      = $tmp_array[$key][UNAME];      //註冊帳號
								$BOPAYTYPE  = $tmp_array[$key][BOPAYTYPE];  //付款方式
                $BOINVTYPE  = $tmp_array[$key][BOINVTYPE];  //發票形式：2:二聯 3:三聯 21:二聯捐贈 25:電子發票
								$BOPAYDATA  = $tmp_array[$key][BOPAYDATA];	//信用卡回復資訊
								$WBPAGES    = $tmp_array[$key][WBPAGES];    //頁數
								$BORPHONE   = $tmp_array[$key][BORPHONE];   //收件者電話
								$BOVATNO    = $tmp_array[$key][BOVATNO];    //統一編號
                $BOINVTITLE = $tmp_array[$key][BOINVTITLE]; //發票抬頭
								$TITLE	    = $tmp_array[$key][TITLE];      //作品名稱
                $CPBONUS    = $tmp_array[$key][CPBONUS];    //折扣數
                $BOCOUPON   = $tmp_array[$key][BOCOUPON];  //折扣碼
                $BIND       = $tmp_array[$key][BIND];       //裝訂方式
                $PPID       = $tmp_array[$key][PPID];       //紙別代號
                $PPNAME     = $tmp_array[$key][PPNAME];     //紙別名稱
                $PREORDER   = $tmp_array[$key][PREORDER];   //預收訂單
                $WORK_ARRAY = $tmp_array[$key][WORKNAME];  //加工陣列
                $YFPBOID    = $tmp_array[$key][YFPBOID];   //工廠要看訂單編號
                $VVBOID     = $tmp_array[$key][VVBOID];    //發票訂單編號
								$EXBOID     = $tmp_array[$key][EXBOID];    //拆帳訂單編號
                $TEXT       = $tmp_array[$key][TEXT];      //筆記本內頁格式
                $PREV       = $tmp_array[$key][PREVIEW];  //判斷是否有立體木盒看要不要抓prev檔案
                $S_FACE     = $tmp_array[$key][S_FACE];   //單面頁數
                $D_FACE     = $tmp_array[$key][D_FACE];   //雙面頁數
               	$PDF_ARRAY  = $tmp_array[$key][CFILE];
                $PDF_ARRAY1 = $tmp_array[$key][CNUM];     //各檔案 實際 訂購量陣列
                $DM_PTE_NO  = $tmp_array[$key][DM_PTE_NO]; //企業DM版號
								$GOLD       = $tmp_array[$key][GOLD]; //喜帖是否燙金
								$GOLDMSG    = $tmp_array[$key][GOLDMSG]; //燙金/銀訊息
								$VDP_COUNT  = $tmp_array[$key][VDP_COUNT]; //VDP變動資料筆數

								$print_array[$key][BID]        = $BID;
								$print_array[$key][BOID]       = $BOID;
                $print_array[$key][FACID]      = $FACID;
								$print_array[$key][YFPBOID]    = $YFPBOID;
                $print_array[$key][UNAME]      = $UNAME;
								$print_array[$key][BORNAME]    = $BORNAME;
								$print_array[$key][BOMID]      = $BOMID;
								$print_array[$key][BMEMO]      = $BMEMO;
								$print_array[$key][BORADDR]    = $BORADDR;
                $print_array[$key][BOSEND]     = $BOSEND;
								$print_array[$key][BOVATNO]    = $BOVATNO;
                $print_array[$key][GROUPID]    = $GROUPID;
                $print_array[$key][FLOWNEW]    = $FLOWNEW;
								$print_array[$key][BONUM]      = $BONUM;
                $print_array[$key][BOPRICE]    = $BOPRICE;
								$print_array[$key][BOTIME]     = $BOTIME;
                $print_array[$key][BOSHIPTIME] = $BOSHIPTIME;
								$print_array[$key][BORPHONE]   = $BORPHONE;
								$print_array[$key][BOPAYTYPE]  = $BOPAYTYPE;
                $print_array[$key][BOINVTYPE]  = $BOINVTYPE;
								$print_array[$key][BOPAYDATA]  = $BOPAYDATA;
								$print_array[$key][BTYPE]      = $BTYPE;
								$print_array[$key][WBFLOW]     = $WBFLOW;
								$print_array[$key][TITLE]      = $TITLE;
                $print_array[$key][BOMEMO]     = $BOMEMO;
                $print_array[$key][WBPAGES]    = $WBPAGES;
                $print_array[$key][BIND]       = $BIND;
                $print_array[$key][PPID]       = $PPID;
                $print_array[$key][PPNAME]     = $PPNAME;
                $print_array[$key][CPBONUS]    = $CPBONUS;  //折扣數
                $print_array[$key][BOCOUPON]   = $BOCOUPON; //折扣碼
                $print_array[$key][YFPBOID]    = $YFPBOID; //工廠要看訂單編號
                $print_array[$key][VVBOID]     = $VVBOID;  //發票訂單編號
								$print_array[$key][EXBOID]     = $EXBOID;  //拆帳訂單編號
								
                $print_array[$key][PREORDER]   = $PREORDER;
                $print_array[$key][TEXT]       = $TEXT;
                $print_array[$key][WORKNAME]   = $WORK_ARRAY; // 加工條件陣列
                $print_array[$key][S_FACE]     = $S_FACE;
                $print_array[$key][D_FACE]     = $D_FACE;
              	$print_array[$key][PDF]        = $PDF_ARRAY;  // 名片檔案
               	$print_array[$key][PDF2]       = $PDF_ARRAY1; // 名片檔案訂購盒數
								$print_array[$key][GOLD]       = $GOLD; //喜帖是否燙金
								$print_array[$key][VDP_COUNT]  = $VDP_COUNT; //VDP變動資料筆數
								
								$result_ftp=$obj->choose_ftp($FACID);

								//記錄生產工廠ID
								$print_array[$key][FACNAME]   = $result_ftp[FACNAME];

								//更新VOW PDF時間
								$SQL=' Update pordereg set TSPDF=\''.$obj->date_time().'\' where BOID=\''.$BOID.'\' and TSNEW=\''.$insert_time.'\'';
								$query=mssql_query($SQL,$GB_dblk);
                /***************************************************************************************
								* 直落產品跳過不處理
								****************************************************************************************/
                $jump=false;
                switch ($BTYPE) {
                    case '77'://明信片
                    case '31'://桌曆
										case '30'://新版桌曆
										case '212'://磁鐵
										case '213':
										case '214':
										case '114'://PLA隨身瓶
                    case '81'://icash
                        $jump=true;
                        break;
                    case '40':
                        //=====================================================================================
                        // 自製企業帳單、DM、空白報表
                        //=====================================================================================
                        $SQL="Select * from w2product40 where PRTPTEID='$DM_PTE_NO'";
                        $query=mssql_query($SQL,$GB_dblk);
                        while ($rs1=mssql_fetch_array($query)) {
                            $WBPAGES  =trim($rs1[BPAGE]);
                            $PPID     =trim($rs1[PPID]);
                            $MAP_BTYPE=trim($rs1[SIZETYPE]);
                            $print_array[$key][BMEMO]=str_replace($search, $replace, trim(iconv('BIG5','UTF-8',$rs1["PRTNAME"])));
                            $print_array[$key][FILENAME]=$DM_PTE_NO;
                        }
                        $jump=true;
                        break;
                }
								//判斷是否有燙金版
								switch ($BTYPE) {
										case "124": case "125": case "126": case "127": case "128": case "129":case "30": case "31":
												if ($BTYPE=="30" or $BTYPE=="31") {
														$Process_Name=$GOLDMSG;
												} else {
														$Process_Name="喜帖信封燙金";
												}
												if ($GOLD=='Y') {
														if (file_exists($GB_BOOKPATH.$BID."/PDF/BLACK.pdf")) {
																$obj->ll_echo("[".$obj->show_time()."]".$Process_Name."檔案處理[".$BOID."]");
																@copy($GB_BOOKPATH.$BID."/PDF/BLACK.pdf",$GB_BOOKPATH_PDF."spool/BLACK_".$YFPBOID.sprintf("%'02s",$BONUM).".pdf");
																//$obj->mail_black($YFPBOID,$GB_BOOKPATH_PDF."spool/BLACK_".$YFPBOID.sprintf("%'02s",$BONUM).".pdf","arvin.chen@email.yfp.com.tw,junior@email.yfp.com.tw,herlonglong@email.yfp.com.tw,chaochi@email.yfp.com.tw,yifen@e0in.com");
																$rs_ftp=$obj->choose_ftp(23);
																if ($INI_SET[FTP]=="Y") {
																		$obj->ll_echo("[".$obj->show_time()."]FTP檔案處理中[".$BOID."]");
																		if(!@$ftp_conn_id = ftp_connect($rs_ftp[FTPIP])) {
																				$obj->ll_echo("[".$obj->show_time()."]FTP連線失敗");
																		}
																		if (!@ftp_login($ftp_conn_id, $rs_ftp[FTPUSER], $rs_ftp[FTPPASS])) {
																				$obj->ll_echo("[".$obj->show_time()."]FTP登入失敗");
																		}
																		$remote_dir  =iconv('UTF-8','BIG5',$Process_Name);
																		@ftp_chdir($ftp_conn_id,"0946");
																		@ftp_chdir($ftp_conn_id,$remote_dir);
																		@ftp_pasv($ftp_conn_id, true);				
																		if (!@ftp_put($ftp_conn_id, "BLACK_".$YFPBOID."_".sprintf("%'02s",$BONUM).".pdf",$GB_BOOKPATH_PDF."spool/BLACK_".$YFPBOID.sprintf("%'02s",$BONUM).".pdf", FTP_BINARY)) {
																				$obj->send_mail($Process_Name."燙金銀檔案上傳失敗","平台編號:".$BOID);
																		}
																		@ftp_close($ftp_conn_id);
																}
														 } else {
																 $obj->send_mail($Process_Name."燙金銀檔案不存在","平台編號:".$BOID);
														 }
														 $title = $Process_Name;
														 $subject = "=?UTF-8?B?" . base64_encode($title) . "?=";
														 $boundary = uniqid("");
														 
														 $headers ="From: webmaster@cloudw2p.com"."\r\n";
														 $headers.="Reply-To: webmaster@cloudw2p.com"."\r\n";
														 $headers.="X-Priority: 1"."\r\n";
														 $headers.="X-MSMail-Priority: High"."\r\n";
														 $headers.="Content-type: multipart/mixed; boundary=\"$boundary\"";
														 $emailBody = '--'.$boundary."\n";
														 $emailBody.= 'Content-type: text/html; charset="utf-8"'."\n";
														 $emailBody.= 'Content-transfer-encoding: base64'."\n\n";
														 $emailBody.= base64_encode("平台編號:".$BOID."訂單編號：".$YFPBOID."數量：".sprintf("%'02s",$BONUM))."\n"; // 本文內容
														 $emailBody.= '--'.$boundary."\r\n";
														 $emailBody.= 'Content-Type: application/octet-stream; name=BLACK_'.$YFPBOID.'_'.sprintf("%'02s",$BONUM).'.pdf'."\r\n";
														 $emailBody.= 'Content-disposition: inline; attachment'."\r\n";
														 $emailBody.= 'Content-transfer-encoding: base64'."\r\n\r\n";
														 
														 $att_file = fopen($GB_BOOKPATH_PDF."spool/BLACK_".$YFPBOID.sprintf("%'02s",$BONUM).".pdf","r");
														 //把要夾的檔案讀出來 
														 $att_data = fread($att_file,filesize($GB_BOOKPATH_PDF."spool/BLACK_".$YFPBOID.sprintf("%'02s",$BONUM).".pdf"));
														 fclose($att_file);
														 //編碼後以固定長度斷行 
														 $read = chunk_split(base64_encode($att_data));
														 $emailBody.= $read."\r\n";
														 $emailBody.="--$boundary--";
														 $result=mail($INI_SET[BCC].',jennis@email.yfp.com.tw', $subject, $emailBody, $headers);
														 $obj->ll_echo("[".$obj->show_time()."]寄發桌曆燙金檔案通知");
												}
												break;
								}
                if ($jump or $JUST_ORDER) {
                    $obj->ll_echo("[".$obj->show_time()."]例外產品及轉單不給檔跳過不處理[".$BOID."]");
                    continue;
                }
                /***************************************************************************************
								* 判斷編輯器製做的需透過makepdf.php來產生單模
								****************************************************************************************/
                switch ($WBFLOW) {
                    //非作品集的 FTP上傳及名片Template自製產品
                    case "1":
                    case "3":
                        switch ($BTYPE) {
														 //=====================================================//
            								 // 判斷FTP上傳的作品集及紙袋需透過 makesplitpdf.php來產生落版檔案
        		    						 //=====================================================//
														case ($BTYPE<'30'):
														case ($BTYPE >='130' and $BTYPE <='139'):
                                $obj->ll_echo("[".$obj->show_time()."]FTP上傳檔案裁切[".$BOID."]");
                                $obj->fopen_exec($GB_W2PPDF."/album/makesplitpdf.php?BID=".$BID."&ORD=".$YFPBOID.sprintf("%'02s",$BONUM));
																break;
														//=====================================================//
        								    //Template 名片檔案處理
    		    						    //=====================================================//
                            case 82: //數位名片
                            case 83:
                            case 84:
                                $obj->ll_echo("[".$obj->show_time()."]自製名片處理[".$BOID."]");
                                @$obj->fopen_exec($GB_W2PPDF."/album/makenamecard.php?BID=".$BID."&ORD=".$YFPBOID.sprintf("%'02s",$BONUM));
                                break;
														default:
																$obj->ll_echo("[".$obj->show_time()."]非作品集FTP上傳檔案處理[".$BOID."]");
                                $PDF_PATH=$GB_BOOKPATH.$BID."/pdf/";
                                $WB_DIRHW = opendir($PDF_PATH);	//開啟目錄
                        				if($WB_DIRHW) {
                        				    while( $file = strtolower( readdir($WB_DIRHW)))	{
                    				            if (!is_dir($PDF_PATH.$file)) {
                    				                $filetype = strtolower(substr($file, strlen($file)-3)); //抓出副檔名並轉換成小
                    				    		  	    switch( $filetype)	{
                    				    						    case 'pdf':
                    				    							     $file = strtolower(trim($file));
                                                   @copy($PDF_PATH.$file,$GB_BOOKPATH_PDF."spool/".$BID.".pdf");
                           				    						 break;
                    				                }
																						break;
                    				            }
                        				    }
                        				    closedir($WB_DIRHW);
                        				}
																switch ($BTYPE) {
																		case ($BTYPE >='142' and $BTYPE <='163'):
																				@$obj->fopen_exec($GB_W2PPDF."/album/makeAllpdfpaint.php?BID=".$BID."&ORD=".$YFPBOID.sprintf("%'02s",$BONUM));
																				break;
																		case ($BTYPE >='232' and $BTYPE <='241'):
																				@$obj->fopen_exec($GB_W2PPDF."/album/makeallpdfpaintnew.php?BID=".$BID."&ORD=".$YFPBOID.sprintf("%'02s",$BONUM));
																				break;
																		case ($BTYPE >='206' and $BTYPE <='209'):
																				@$obj->fopen_exec($GB_W2PPDF."/album/makeAllpdfsticker.php?BID=".$BID."&ORD=".$YFPBOID.sprintf("%'02s",$BONUM));
																				break;
																		case 35:
																		case 55:
																				@$obj->fopen_exec($GB_W2PPDF."/album/makeAllpdf55.php?BID=".$BID."&ORD=".$YFPBOID.sprintf("%'02s",$BONUM));
																				break;
																		case 56:
																		case 36:
																				@$obj->fopen_exec($GB_W2PPDF."/album/makeAllpdf56.php?BID=".$BID."&ORD=".$YFPBOID.sprintf("%'02s",$BONUM));
																				break;
																		case 32:
																		case 33:
																				@$obj->fopen_exec($GB_W2PPDF."/album/makeAllpdf32.php?BID=".$BID."&ORD=".$YFPBOID.sprintf("%'02s",$BONUM));
																				break;
																		case 111:
																		case 219:
																		case 218:
																				@$obj->fopen_exec($GB_W2PPDF."/album/makeallpdfbottle.php?BID=".$BID."&ORD=".$YFPBOID.sprintf("%'02s",$BONUM));
																				break;
																		case 60:
																		case 61:
																				@copy($GB_BOOKPATH_PDF."spool/".$BID.".pdf",$GB_BOOKPATH_PDF."spool/COVER_".$BID.".pdf");
																				break;
																		default:
																				@$obj->fopen_exec($GB_W2PPDF."/album/makeAllpdf".$BTYPE.".php?BID=".$BID."&ORD=".$YFPBOID.sprintf("%'02s",$BONUM));
																				break;
																}																	
																break;
                        }
                        break;
                    default:
                        $obj->ll_echo("[".$obj->show_time()."]產生PDF[".$BOID."]");
        								$obj->fopen_exec($GB_W2PPDF."/album/makepdf.php?BID=".$BID."&ORD=".$YFPBOID.sprintf("%'02s",$BONUM));	  //做單模
                        if (!file_exists($GB_BOOKPATH_PDF."spool/".$BID.".pdf")) {
                            $obj->ll_echo("[".$obj->show_time()."]PDF單模產生失敗[".$BOID."]");
                            $obj->send_mail("PDF單模產生失敗","平台編號:".$BOID);
                        }
                        //判斷是否為蝴蝶頁裝訂
                        if (($BIND!='804' and $BIND!='805') or $BTYPE=='20' or $BTYPE=='21' or $BTYPE=='24') {
                            //內頁拼版
                            switch ($BTYPE) {
                                 //無框畫處理
                                case ($BTYPE>='142' and $BTYPE<='163'):
                                    @$obj->fopen_exec($GB_W2PPDF."/album/makeAllpdfpaint.php?BID=".$BID."&ORD=".$YFPBOID.sprintf("%'02s",$BONUM));
                                    break;
																 //新版厚無框畫處理
																case ($BTYPE>='232' and $BTYPE<='241'):
																		@$obj->fopen_exec($GB_W2PPDF."/album/makeallpdfpaintnew.php?BID=".$BID."&ORD=".$YFPBOID.sprintf("%'02s",$BONUM));
																		break;
                                //便利貼處理
                                case ($BTYPE>='206' and $BTYPE<='209'):
                                    @$obj->fopen_exec($GB_W2PPDF."/album/makeAllpdfsticker.php?BID=".$BID."&ORD=".$YFPBOID.sprintf("%'02s",$BONUM));
                                    break;
                                //不需要拼版的產品
                                case '60': // HTC ONE X
                                case '61':
                                    @copy($GB_BOOKPATH_PDF."spool/".$BID.".pdf",$GB_BOOKPATH_PDF."spool/COVER_".$BID.".pdf");
                                    break;
                                //自製名片
                                case '82':
                                case '83':
                                case '84':
                                    @$obj->fopen_exec($GB_W2PPDF."/album/makenamecard.php?BID=".$BID."&ORD=".$YFPBOID.sprintf("%'02s",$BONUM));
                                    break;
                                case '35':
                                case '55':
                                    @$obj->fopen_exec($GB_W2PPDF."/album/makeAllpdf55.php?BID=".$BID."&ORD=".$YFPBOID.sprintf("%'02s",$BONUM));
                                    break;
                                case '36':
                                case '56':
                                    @$obj->fopen_exec($GB_W2PPDF."/album/makeAllpdf56.php?BID=".$BID."&ORD=".$YFPBOID.sprintf("%'02s",$BONUM));
                                    break;
																case '32':
																case '33':
																		@$obj->fopen_exec($GB_W2PPDF."/album/makeAllpdf32.php?BID=".$BID."&ORD=".$YFPBOID.sprintf("%'02s",$BONUM));
																		break;
																case '111':
																case '219':
																case '218':
																		@$obj->fopen_exec($GB_W2PPDF."/album/makeallpdfbottle.php?BID=".$BID."&ORD=".$YFPBOID.sprintf("%'02s",$BONUM));
																		//@copy($GB_BOOKPATH_PDF."spool\\".$BID.".pdf",$GB_BOOKPATH_PDF."spool\\COVER_".$BID.".pdf");
																		break;
                                default:
                                    @$obj->fopen_exec($GB_W2PPDF."/album/makeAllpdf".$BTYPE.".php?BID=".$BID."&ORD=".$YFPBOID.sprintf("%'02s",$BONUM));
                                    break;
                            }
                        } else {
                            //蝴蝶頁內頁拼版
                            @$obj->fopen_exec($GB_W2PPDF."/album/makeAllpdf".$BTYPE."a.php?BID=".$BID."&ORD=".$YFPBOID.sprintf("%'02s",$BONUM));
                        }
     								    @$obj->fopen_exec($GB_W2PPDF."/album/makeAllpdf".$BTYPE."c.php?BID=".$BID."&ORD=".$YFPBOID.sprintf("%'02s",$BONUM));   //封面拼版
                        break;
                }
                //自製名片處理完跳掉
                if ($BTYPE <= '84' and $BTYPE >= '82') {
                    $n_rar_file=$GB_BOOKPATH_PDF."\\spool\\".$YFPBOID.sprintf("%'02s",$BONUM).".rar";
                    $n_remote_file=$YFPBOID.sprintf("%'02s",$BONUM).".rar";
                    if ($INI_SET[FTP]=="Y") {
                        $obj->ll_echo("[".$obj->show_time()."]自製名片FTP檔案處理中[".$BOID."]");
    										// 上傳檔案
    										if(!@$ftp_conn_id = ftp_connect($result_ftp[FTPIP])) {
    												$obj->ll_echo("[".$obj->show_time()."]自製名片FTP連線失敗");
    										}
    										if (!@ftp_login($ftp_conn_id, $result_ftp[FTPUSER], $result_ftp[FTPPASS])) {
    										   	$obj->ll_echo("[".$obj->show_time()."]自製名片FTP登入失敗");
    										}
                        //永豐FTP 才移到 W2P目錄
                        if ($FACID=='10') {
        										ftp_chdir($ftp_conn_id,"W2P");
        										if (!@ftp_chdir($ftp_conn_id,$DIR_NAME)) {
        												@ftp_mkdir($ftp_conn_id,$DIR_NAME);
        										}
                            @ftp_chdir($ftp_conn_id,$DIR_NAME);
                        }
    										@ftp_pasv($ftp_conn_id, true);
    										if (@ftp_put($ftp_conn_id, iconv("UTF-8","BIG5",$n_remote_file), $n_rar_file, FTP_BINARY)) {
    												$SQL=' Update pordereg set TSFTP=\''.$obj->date_time().'\' where BOID=\''.$BOID.'\' and TSNEW=\''.$insert_time.'\' ';
    												$query=mssql_query($SQL,$GB_dblk);
                            unlink($n_rar_file);
    										} else {
    												$obj->ll_echo("自製名片檔案上傳失敗!!原始檔案:".$n_rar_file."遠端檔案:".$n_remote_file);
                            $obj->send_mail("W2P自製名片檔案上傳失敗","原始檔案:".$n_rar_file."遠端檔案:".$n_remote_file);
    										}
												$print_array[$key]["BODY"]=$BTYPE."BODY_".$YFPBOID.sprintf("%'02s",$BONUM).".pdf";
												
    										@ftp_close($ftp_conn_id);
                        $obj->ll_echo("[".$obj->show_time()."]自製名片FTP傳檔結束");
    								}
                    $obj->ll_echo("[".$obj->show_time()."]自製名片處理結束[".$BOID."]");
                    continue;
                }
                /***************************************************************************************
								* 拼圖檔案處理
								****************************************************************************************/
                if ($BTYPE >='115' and $BTYPE <='122') {
                    $obj->ll_echo("[".$obj->show_time()."]拼圖檔案處理[".$BOID."]");
                    $obj->fopen_exec($GB_W2PPDF."/album/makeallpdfpuzzle.php?BID=".$BID."&ORD=".$YFPBOID.sprintf("%'02s",$BONUM));	//貼紙拼板
                    switch ($BTYPE) {
                        //A6 拼圖
                        case '115':
                            $TYPE='A6_W';
                            break;
                        case '116':
                            $TYPE='A6_V';
                            break;
                        //A5 拼圖
                        case '117':
                            $TYPE='A5_W';
                            break;
                        case '118':
                            $TYPE='A5_V';
                            break;
                        //A4 拼圖
                        case '119':
                            $TYPE='A4_W';
                            break;
                        case '120':
                            $TYPE='A4_V';
                            break;
                        //A3 拼圖
                        case '121':
                            $TYPE='A3_W';
                            break;
                        case '122':
                            $TYPE='A3_V';
                            break;
                    }
                    $remote_file=$YFPBOID.'_'.$TYPE.'_'.$BONUM;
                    $print_array[$BOID][FILENAME]=$remote_file;
										if ($BTYPE=='121' or $BTYPE=='122') {
												$result_ftp=$obj->choose_ftp(19);
										} else {
												$result_ftp=$obj->choose_ftp(23);
										}
										if ($INI_SET[FTP]=="Y") {
												$obj->ll_echo("[".$obj->show_time()."]FTP檔案處理中[".$BOID."]");
												if(!@$ftp_conn_id = ftp_connect($result_ftp[FTPIP])) {
														$obj->ll_echo("[".$obj->show_time()."]FTP連線失敗");
												}
												if (!@ftp_login($ftp_conn_id, $result_ftp[FTPUSER], $result_ftp[FTPPASS])) {
														$obj->ll_echo("[".$obj->show_time()."]FTP登入失敗");
												}
												//拼圖A3給英傑特印，其餘改依您印
												if ($BTYPE=='121' or $BTYPE=='122') { 
														@ftp_chdir($ftp_conn_id,"home");
														if (!@ftp_chdir($ftp_conn_id,$DIR_NAME)) {
																ftp_mkdir($ftp_conn_id,$DIR_NAME);
																ftp_chdir($ftp_conn_id,$DIR_NAME);
														}
												} else {
														@ftp_chdir($ftp_conn_id,"0946");
                            @ftp_chdir($ftp_conn_id,"mimaki");
														$remote_dir  =iconv('UTF-8','BIG5','拼圖');
														@ftp_chdir($ftp_conn_id,$remote_dir);
														//if (!@ftp_chdir($ftp_conn_id,$DIR_NAME)) {
														//		ftp_mkdir($ftp_conn_id,$DIR_NAME);
														//		ftp_chdir($ftp_conn_id,$DIR_NAME);
														//}
												}
												
												@ftp_pasv($ftp_conn_id, true);
												if ($PREV=='Y') {
														$PREV_FILE=$GB_BOOKPATH_PDF."spool\\BODY_".$BID."_prev.pdf";
														if (!@ftp_put($ftp_conn_id, iconv("UTF-8","BIG5",$remote_file."_prev.pdf"),$PREV_FILE, FTP_BINARY)) {
																$obj->ll_echo("拼圖預覽檔上傳失敗!!原始檔案:".$PREV_FILE."遠端檔案:".$remote_file."_prev.pdf");
																$obj->send_mail("拼圖預覽檔上傳失敗","原始檔案:".$PREV_FILE."遠端檔案:".$remote_file."_prev.pdf");
														} else {
																@unlink($PREV_FILE);
																$print_array[$key]["COVER"]=$remote_file."_prev.pdf";
														}
												}
												$BODY_FILE=$GB_BOOKPATH_PDF."spool\\BODY_".$BID.".pdf";
												if (@ftp_put($ftp_conn_id, iconv("UTF-8","BIG5",$remote_file.".pdf"), $BODY_FILE, FTP_BINARY)) {
														//更新FTP時間
														$SQL=' Update pordereg set TSFTP=\''.$obj->date_time().'\' where BOID=\''.$BOID.'\' and TSNEW=\''.$insert_time.'\' ';
														$query=mssql_query($SQL,$GB_dblk);
														$print_array[$key]["BODY"]=$remote_file.".pdf";
														 @unlink($BODY_FILE);
												} else {
														$obj->ll_echo("PDF檔案上傳失敗!!原始檔案:".$BODY_FILE."遠端檔案:".$remote_file.".pdf");
														$obj->send_mail("拼圖檔案上傳失敗","原始檔案:".$BODY_FILE."遠端檔案:".$remote_file.".pdf");
												}
										}
                    $obj->ll_echo("[".$obj->show_time()."]拼圖處理完畢跳過[".$BOID."]");
                    continue;
                }
                /***************************************************************************************
						    * 判斷裝訂方式代號顯示在作品集PDF檔案名稱內，作品集檔案命名改為 BTYPE + BODY(COVER)+ _ + 裝訂方式(B1~B5) + YFPBOID + 數量.pdf
						    ****************************************************************************************/
                $SHOW_BIND="";
                switch ($BIND) {
                    case "801":
                        $SHOW_BIND='B1';
                        break;
                    case "802":
                        $SHOW_BIND="B2";
                        break;
                    case "803":
                        $SHOW_BIND="B3";
                        break;
                    case "804":
                        $SHOW_BIND="B4";
                        break;
                    case "805":
                        $SHOW_BIND="B5";
                        break;
                }
								$rar_file=$GB_BOOKPATH_PDF."\\spool\\".$YFPBOID.sprintf("%'02s",$BONUM).".rar";
								$remote_file=$YFPBOID.sprintf("%'02s",$BONUM).".rar";
								$WORKDIR="";
								switch ($BTYPE) {
                    //DM
										case ($BTYPE >=85 and $BTYPE<=92):
                        $WORKDIR = $GB_BOOKPATH_PDF."spool\\".$BTYPE."BODY_".$YFPBOID.sprintf("%'02s",$BONUM).".pdf ";
                        break;
										//喜帖邀請卡
										case ($BTYPE >=124 and $BTYPE<=129):
                        $WORKDIR = $GB_BOOKPATH_PDF."spool\\".$BTYPE."BODY_".$YFPBOID.sprintf("%'02s",$BONUM).".pdf ";
												$WORKDIR.= $GB_BOOKPATH_PDF."spool\\BLACK_".$YFPBOID.sprintf("%'02s",$BONUM).".pdf";
                        break;
										//作品集
									  case 20:case 21:case 24:case 25:
										    $WORKDIR =$GB_BOOKPATH_PDF."spool\\".$BTYPE."BODY_".$SHOW_BIND."_".$YFPBOID.sprintf("%'02s",$BONUM).".pdf ";
										    $WORKDIR.=$GB_BOOKPATH_PDF."spool\\".$BTYPE."COVER_".$SHOW_BIND."_".$YFPBOID.sprintf("%'02s",$BONUM).".pdf ";
										    break;
                    //21正方、B5直、B5橫、A4直、A4橫
                    case 17:case 22:case 23:case 28:case 29:
                        $WORKDIR =$GB_BOOKPATH_PDF."spool\\".$BTYPE."BODY_".$SHOW_BIND."_".$YFPBOID.sprintf("%'02s",$BONUM).".pdf ";
                        //大於兩本直落，所以會有X2檔案
                        if ($BONUM >=2) {
                            $WORKDIR.=$GB_BOOKPATH_PDF."spool\\".$BTYPE."BODY_".$SHOW_BIND."_".$YFPBOID.sprintf("%'02s",$BONUM)."_X2.pdf ";
                            $check_file=$GB_BOOKPATH_PDF."spool\\BODY_".$BID."_X2.pdf";
														if (file_exists($check_file)) {
																$des_file=$GB_BOOKPATH_PDF."spool\\".$BTYPE."BODY_".$SHOW_BIND."_".$YFPBOID.sprintf("%'02s",$BONUM)."_X2.pdf";
																if(copy($check_file,$des_file)) {
                                    unlink($check_file);
                                }
														}
                        }
										    $WORKDIR.=$GB_BOOKPATH_PDF."spool\\".$BTYPE."COVER_".$SHOW_BIND."_".$YFPBOID.sprintf("%'02s",$BONUM).".pdf ";
                        break;
                    //A5直、A5橫
                    case 26:case 27:
                        $WORKDIR =$GB_BOOKPATH_PDF."spool\\".$BTYPE."BODY_".$SHOW_BIND."_".$YFPBOID.sprintf("%'02s",$BONUM).".pdf ";
                        //大於兩本直落，所以會有X4檔案
                        if ($BONUM >=4) {
                            $WORKDIR.=$GB_BOOKPATH_PDF."spool\\".$BTYPE."BODY_".$SHOW_BIND."_".$YFPBOID.sprintf("%'02s",$BONUM)."_X4.pdf ";
                            $check_file=$GB_BOOKPATH_PDF."spool\\BODY_".$BID."_X4.pdf";
														if (file_exists($check_file)) {
																$des_file=$GB_BOOKPATH_PDF."spool\\".$BTYPE."BODY_".$SHOW_BIND."_".$YFPBOID.sprintf("%'02s",$BONUM)."_X4.pdf";
																copy($check_file,$des_file);
														}
                        }
										    $WORKDIR.=$GB_BOOKPATH_PDF."spool\\".$BTYPE."COVER_".$SHOW_BIND."_".$YFPBOID.sprintf("%'02s",$BONUM).".pdf ";
                        break;
										//桌曆、月曆、貼紙、明信片
										case 32:case 72:case 33:case 38:case 39:
										    if ($BTYPE=="30") {
														if ($BONUM>3) {
																$WORKDIR = $GB_BOOKPATH_PDF."spool\\".$BTYPE."BODY_".$YFPBOID.sprintf("%'02s",$BONUM).".pdf ";
																$WORKDIR.= $GB_BOOKPATH_PDF."spool\\".$BTYPE."BODY_".$YFPBOID.sprintf("%'02s",$BONUM)."_X4.pdf";
																$check_file=$GB_BOOKPATH_PDF."spool\\BODY_".$BID."_X4.pdf";
																if (file_exists($check_file)) {
																		$des_file=$GB_BOOKPATH_PDF."spool\\".$BTYPE."BODY_".$YFPBOID.sprintf("%'02s",$BONUM)."_X4.pdf";
																		if(copy($check_file,$des_file)) {
                                        unlink($check_file);
                                    }
																}
														} else {
																$WORKDIR = $GB_BOOKPATH_PDF."spool\\".$BTYPE."BODY_".$YFPBOID.sprintf("%'02s",$BONUM).".pdf ";
														}
                        } else {
												    $WORKDIR =$GB_BOOKPATH_PDF."spool\\".$BTYPE."BODY_".$YFPBOID.sprintf("%'02s",$BONUM).".pdf ";
											  }
										    break;
										case ($BTYPE >=250 and $BTYPE <=263):
												@copy($GB_BOOKPATH_PDF."spool\\VDP_".$BID.".pdf",$GB_BOOKPATH_PDF."spool\\VDP_".$YFPBOID."_".sprintf("%'02s",$BONUM).".pdf");
												@copy($GB_BOOKPATH_PDF."spool\\VDPDATA_".$BID.".pdf",$GB_BOOKPATH_PDF."spool\\VDPDATA_".$YFPBOID."_".sprintf("%'02s",$BONUM).".pdf");
												@copy($GB_BOOKPATH_PDF."spool\\".$BID.".pdf",$GB_BOOKPATH_PDF."spool\\".$YFPBOID."_".sprintf("%'02s",$BONUM).".pdf");
										
												$WORKDIR = $GB_BOOKPATH_PDF."spool\\VDP_".$YFPBOID."_".sprintf("%'02s",$BONUM).".pdf ";
												$WORKDIR.= $GB_BOOKPATH_PDF."spool\\VDPDATA_".$YFPBOID."_".sprintf("%'02s",$BONUM).".pdf ";
												$WORKDIR.= $GB_BOOKPATH_PDF."spool\\".$YFPBOID."_".sprintf("%'02s",$BONUM).".pdf ";
												break;
                    //筆記本、無框畫、便利貼、htc、iphone5
										case ($BTYPE >=142 and $BTYPE<=163):
										case ($BTYPE >=232 and $BTYPE<=241):
                    case 35: case 36: case 54:case 55:case 56:case 57:case 112:case 113:case 114:
                    case 206:case 207:case 208:case 209:case 60:case 61:case 111:case 219:case 218:
                        $WORKDIR = $GB_BOOKPATH_PDF."spool\\".$BTYPE."COVER_".$YFPBOID.sprintf("%'02s",$BONUM).".pdf ";
                        break;
								}
								foreach ($RENAME_ARRAY as $re_value) {
										$is_file=$GB_BOOKPATH_PDF."spool\\".$re_value."_".$BID.".pdf";
										if (file_exists($is_file)) {
                        if ($SHOW_BIND!='') {
												    $new_file=$GB_BOOKPATH_PDF."spool\\".$BTYPE.$re_value."_".$SHOW_BIND."_".$YFPBOID.sprintf("%'02s",$BONUM).".pdf";
                            $print_array[$key][$re_value]=$BTYPE.$re_value."_".$SHOW_BIND."_".$YFPBOID.sprintf("%'02s",$BONUM).".pdf";
                        } else {
                            $new_file=$GB_BOOKPATH_PDF."spool\\".$BTYPE.$re_value."_".$YFPBOID.sprintf("%'02s",$BONUM).".pdf";
    												$print_array[$key][$re_value]=$BTYPE.$re_value."_".$YFPBOID.sprintf("%'02s",$BONUM).".pdf";
                        }
												if (copy($is_file,$new_file)) {
                            unlink($is_file);
                        }
										}
								}	
								if ($WORKDIR!='') {
										$obj->ll_echo("[".$obj->show_time()."]產生壓縮檔[".$BOID."]");
										$CMD_RAR = "c:/app/winrar/rar.exe a -ep -m0 $rar_file  $WORKDIR";
										//更新VOW RAR時間
										$SQL=' Update pordereg set TSRAR=\''.$obj->date_time().'\' where BOID=\''.$BOID.'\' and TSNEW=\''.$insert_time.'\'';
										$query=mssql_query($SQL,$GB_dblk);
										exec($CMD_RAR);
                    if (!file_exists($rar_file)) {
                        $obj->ll_echo("壓縮檔製作失敗!!檔案:".basename($rar_file)."語法:".$CMD_RAR);
                        $obj->send_mail("壓縮檔製作失敗","檔案:".basename($rar_file)."語法:".$CMD_RAR);
                    }
								} 
								/***************************************************************************************
								* FTP 上傳
								****************************************************************************************/
								if ($INI_SET[FTP]=="Y") {
                    $obj->ll_echo("[".$obj->show_time()."]FTP檔案處理中[".$BOID."]");
										// 上傳檔案
										if(!@$ftp_conn_id = ftp_connect($result_ftp[FTPIP])) {
												$obj->ll_echo("[".$obj->show_time()."]FTP連線失敗");
										} 
										if (!@ftp_login($ftp_conn_id, $result_ftp[FTPUSER], $result_ftp[FTPPASS])) {
										   	$obj->ll_echo("[".$obj->show_time()."]FTP登入失敗");
										}
                    //永豐FTP 才移到 W2P目錄
                    if ($FACID=='10') {
    										ftp_chdir($ftp_conn_id,"W2P");
    										if (!@ftp_chdir($ftp_conn_id,$DIR_NAME)) {
    												@ftp_mkdir($ftp_conn_id,$DIR_NAME);
    										}
                        @ftp_chdir($ftp_conn_id,$DIR_NAME);
                    }
										@ftp_pasv($ftp_conn_id, true);	
										if (@ftp_put($ftp_conn_id, iconv("UTF-8","BIG5",$remote_file), $rar_file, FTP_BINARY)) {
												$SQL=' Update pordereg set TSFTP=\''.$obj->date_time().'\' where BOID=\''.$BOID.'\' and TSNEW=\''.$insert_time.'\' ';
												$query=mssql_query($SQL,$GB_dblk);
                        unlink($rar_file);
										} else {
												$obj->ll_echo("PDF檔案上傳失敗!!原始檔案:".$rar_file."遠端檔案:".$remote_file);
                        $obj->send_mail("W2P檔案上傳失敗","原始檔案:".$rar_file."遠端檔案:".$remote_file);
										}
										@ftp_close($ftp_conn_id);
                    $obj->ll_echo("[".$obj->show_time()."]FTP傳檔結束");
								}
						}

?>
