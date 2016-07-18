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
			*  2010/11/18 修改T-shrit轉檔清冊
			*  2010/12/08 Arvin BOPTION欄位從PORDERDATA移至 PORDER 內
			*  2011/01/03 Arvin 增加銷售業(MOMO) 轉檔判斷
			*  2011/01/18 Arvin 名片轉檔增加各PDF數量表，以利工廠核對盒數
			*  2011/05/04 Arvin 增加FTP 傳檔失敗Mail 通知相關人員
			*  2011/05/06 Arvin 增加廣告面紙廠商通知Mail需要欄位
			*  2011/05/23 Arvin 增加DM 自製上傳方式用網路磁碟機方式丟到 192.168.50.242 RIP HotFolder
			*  2011/05/25 Arvin 修改各PDF檔案訂購量及最低訂購量改抓 W2POP，並增加抓名片人名資訊
			*  2011/05/26 Arvin 增加難字判斷，將檔案上傳到指定的路徑
			*  2011/07/05 Arvin 難字判斷改抓W2PWORDFIND資料表，若GGID內無難字登記就直接轉檔傳送
			*  2011/07/20 Arvin 增加判斷BOINVTYPE 發票型態，若為21則在BOMEMO內加上二聯式捐贈，25為電子發票
			*  2011/07/21 Arvin 若名片有難字則在轉檔清冊內的備註註明。
			*  2011/11/07 Arvin iphone殼轉檔程式更新
			*  2011/11/21 Arvin 廣告面紙檔名增加包及抽的中文表示
			*  2011/12/05 Arvin 變更白紗檔案命名 A1永豐 => S1永豐
			*  2011/12/14 Arvin 移除轉檔時生產工廠判斷，直接抓PORDER.FACID出來分辨此筆訂單要轉哪個生產工廠
			*  2011/12/20 Arvin 增加寫入外包轉檔清單
			*  2012/02/09 Arvin 判斷檔案不存跳過，避免PDFLib Exception後續的都無法處理
			*  2012/04/11 Arvin 貼紙單模壓縮檔案處理上傳
			*  2012/04/30 Arvin 吸水杯墊轉檔處理
			*  2012/05/04 Arvin 配合企業DM轉白紗修改轉檔
			*  2012/05/15 Arvin 貼紙白墨代號由 W 改成 white
			*  2012/05/22 Arvin 廣告面紙轉廠商改為300 dpi JPG圖檔
			*  2012/07/05 Arvin 拼圖直接從前台就變更為 FACID=10 自製，移除拼圖外包動作
			*  2012/07/17 Arvin 紙袋FTP檔案處理
			*  2012/08/24 Arvin 增加GROUPID、FLOWNEW判斷購物車主訂單處理
			*  2012/08/29 Arvin 增加桌曆轉依您印的處理判斷
			*  2012/08/29 Arvin 吸水杯墊增加傳送預覽圖
			*  2012/10/11 Arvin 複寫聯單處理上正式機
			*  2012/10/16 Arvin 吸水杯墊轉回由永豐生產
			*  2012/10/16 Arvin 信封套處理
			*  2012/11/22 Arvin 增加掛曆轉依您印處理流程
			*  2012/11/29 Arvin 貼紙落版檔案位置改到BID下面不覆蓋001.pdf
			*  2013/01/08 Arvin 判斷頁數大於2等於多模，檔名加上MULTI
			*  2013/03/18 Arvin 調整原先的檔案處理流程，增加複寫聯單的流水號位置座標及起號；自製名片轉版號給ERP;依您印自製名片處理
			*  2013/04/15 Arvin 增加壁掛貼處理
			*  2013/05/22 Arvin 修改壁掛貼的檔案命名：訂單編號 + (CR or SQ) + 數量.pdf
			*  2013/06/07 Arvin 增加FACID=25(伍泰)零售外包商處理
			*  2013/06/20 Arvin 名片增加對色資訊，在class.php裡面判斷需要對色的模板
			*  2013/07/02 Arvin 直噴機產品改為外包訂單
			*  2013/07/18 Arvin 增加iPad mini處理
			*  2013/07/24 Arvin 御牧FTP上傳資料夾變更
			*  2013/07/30 Arvin 新竹物流上線並增加向前興業(FACID=26)
			*  2013/09/09 Arvin 原本FTP連線、登入失敗僅記錄LOG，現增加mail通知
			*  2013/09/11 Arvin 判斷需要上傳檔案的檔名若有包含難字，將難字替換成空白。避免外包FTP檔名無法接受難字造成檔案上傳正反面覆蓋
			*  2013/09/16 Arvin 增加note2轉檔設定
			*  2013/12/06 Arvin 增加天威外包商處理
			*  2013/12/17 Arvin 依您印1900後的訂單檔案傳入隔日資料夾
			*  2014/01/23 Arvin 修改WBOPTION資料在前面抓，存入WBOPTION_ARRAY陣列
			*  2014/02/13 Arvin 判斷是否沒有可處理的檔案資訊，寄發通知。
			*  2014/02/24 Arvin 複寫聯單改直接抓前台操作所產生的預覽寄送給廠商
			*  2014/04/24 Arvin 增加喜帖主體發外的處理流程
			*  2014/05/23 Arvin 增加3D公仔處理
			*  2014/06/06 Arvin 增加給白紗的DM成品尺寸標示
			*  2014/06/24 Arvin 修改名片難字通知方式，改為直接夾帶檔案
			*  2014/07/21 Arvin 白紗名片命名X01 => X001 變更為3碼
			*  2014/08/29 Arvin 調整御牧FTP資料夾位置
			*  2014/09/09 Arvin 調整國田給檔方式，全部都再給單模PDF檔案讓國田自己去拼
			*  2014/09/09 Arvin 增加彩色TEE處理
			*  2014/09/15 Arvin 增加桌曆、喜帖燙金檔案寄送通知生管
			*  2014/09/22 Arvin 增加巨茂復航名片判斷，正背PDF互換
      *  2014/12/02 Arvin 增加制式工商日誌處理
      *  2015/03/13 Arvin Iphone4保護殼改由依您印生產，檔案改直落方式處理
			*  2015/04/08 Arvin 紙膠帶編輯器產品跟L夾產品處理
			*  2015/05/05 Arvin 外商商轉檔清冊單位抓w2product40內的PRTUNIT來顯示
			*  2015/06/26 Arvin 調整轉單不給檔直接不做檔案處理，減少花費時間
			*  2015/09/04 Arvin 塑料面紙包處理
			***********************************************************************************************/
			$obj->ll_echo("[".$obj->show_time()."]合版類產品轉檔");
			$pdfhw = new PDFlib();

			$search = array ("'","\"","<",">","&");
			$replace = array ("’","＂","＜","＞","＆");
			$search_filename=array("/","\\",":","*",":","|","<",">","\"");
			foreach ($tmp_array as $key => $value1) {
					//成功與否開關
					$TRANS_STATUS=true;

					if (!is_array($trans_list["WTP_ARRAY"])) {
							continue;
					}
					//判斷訂單編號要在XML 傳檔成功的陣列才進行後續動作
					if (!in_array($key,$trans_list["WTP_ARRAY"])) {
							continue;
					}
					$WBFLOW     = $tmp_array[$key][WBFLOW];
					$BTYPE      = $tmp_array[$key][BTYPE];
					$BID        = $tmp_array[$key][BID];
					$FACID      = $tmp_array[$key][FACID];
					$BOID       = $key;
					$BORNAME    = $tmp_array[$key][BORNAME];  //收件人
					$UDNAME     = $tmp_array[$key][UDNAME];   //訂購人
					$BOMID      = $tmp_array[$key][BOMID];    //郵遞區號
					$BMEMO      = $tmp_array[$key][BMEMO];    //品項
					$BOMEMO     = $tmp_array[$key][BOMEMO];   //備註
					if ($JUST_ORDER) {
							$BOMEMO = $BOMEMO."[轉單不給檔]";
					}
					$BORADDR    = $tmp_array[$key][BORADDR];  //送貨地址
					$BOSEND     = $tmp_array[$key][BOSEND];   //送貨方式
					//$BOGROUP    = $tmp_array[$key][BOGROUP];  //是否合併寄送(主訂單號碼)
					$GROUPID    = $tmp_array[$key][GROUPID];   //是否為購物車訂單
					$FLOWNEW    = $tmp_array[$key][FLOWNEW];   //購物車訂單運費(有值得代表主訂單)
					$BONUM      = $tmp_array[$key][BONUM];    //數量
					$BOPRICE    = $tmp_array[$key][BOPRICE];    //總金額
					$BOTIME     = $tmp_array[$key][BOTIME];   //訂單成立時間
					$UNAME      = $tmp_array[$key][UNAME];    //註冊帳號
					$BOSHIPTIME = $tmp_array[$key][BOSHIPTIME]; //訂單約交日
					$BOPAYTYPE  = $tmp_array[$key][BOPAYTYPE];//付款方式
					$BOINVTYPE  = $tmp_array[$key][BOINVTYPE];//發票形式：2:二聯 3:三聯 21:二聯捐贈 25:電子發票
					$WBPAGES    = $tmp_array[$key][WBPAGES];  // PDF 頁數
					$BORPHONE   = $tmp_array[$key][BORPHONE]; //收件人連絡電話
					$BOVATNO    = $tmp_array[$key][BOVATNO];  //統編
					$BOINVTITLE = $tmp_array[$key][BOINVTITLE]; //發票抬頭
					$WBOPTION   = $tmp_array[$key][WBOPTION];
					$BOPTION    = $tmp_array[$key][BOPTION];
					$PDT_NAME   = $tmp_array[$key][PDT_NAME];  //零售業品名
					$UNIT       = $tmp_array[$key][UNIT];      //零售業產品單位
					$PDT_GP     = $tmp_array[$key][PDT_GP];    //零售業產品分類
					$PPNAME     = $tmp_array[$key][PPNAME];    //紙別名稱
					$PPID       = $tmp_array[$key][PPID];      //紙別代號
					$CPBONUS    = $tmp_array[$key][CPBONUS];   //折扣數
					$BOCOUPON   = $tmp_array[$key][BOCOUPON];  //折扣碼
					$YFPBOID    = $tmp_array[$key][YFPBOID];   //工廠要看訂單編號
					$VVBOID     = $tmp_array[$key][VVBOID];    //發票訂單編號
					$EXBOID     = $tmp_array[$key][EXBOID];    //拆帳訂單編號
					$PREORDER   = $tmp_array[$key][PREORDER];  //預收訂單
					$WORK_ARRAY = $tmp_array[$key][WORKNAME];  //加工陣列
					$WORK_ARRAY1= $tmp_array[$key][WORKNAME1]; //加工陣列 => 貼紙專用
					$SIZE       = $tmp_array[$key][SIZE];      //貼紙尺寸
					$OUT        = $tmp_array[$key][OUT];       //出紙方向
					$PDF_ARRAY  = $tmp_array[$key][CFILE];
					$PDF_ARRAY1 = $tmp_array[$key][CNUM];     //各檔案 實際 訂購量陣列
					$PDF_ARRAY2 = $tmp_array[$key][CMINNUM];  //各檔案 最低 訂購量陣列
					$NAME_ARRAY = $tmp_array[$key][CNAME];    //名片姓名陣列
					$DM_PTE_NO  = $tmp_array[$key][DM_PTE_NO]; //企業DM版號
					$COLOR      = $tmp_array[$key][COLOR];
					$CK_COLOR   = $tmp_array[$key][CK_COLOR]; //名片是否對色
					$CHANGEFTOB = $tmp_array[$key][CHANGEFTOB]; //復航名片正背對調
					$GOLD       = $tmp_array[$key][GOLD]; //喜帖是否燙金/銀
					$GOLDMSG    = $tmp_array[$key][GOLDMSG]; //燙金/銀訊息
					$OBJID      = $tmp_array[$key][OBJID]; //3D公仔印製物件ID
					$COVER_KIND = $tmp_array[$key][COVER_KIND]; //3C保護殼細項-iphone6 or iphone6_plus
					$PRODUCT_UNIT = $tmp_array[$key][PRODUCT_UNIT]; //企業產品單位
					$hole       = $tmp_array[$key][hole];
					$gold_position = $tmp_array[$key][gold_position];
					$gold_kind     = $tmp_array[$key][gold_kind];
					$DBTITLE       = $tmp_array[$key][DBTITLE];
					$PAINTBOX    = $tmp_array[$key][PAINTBOX];
					
					
					$print_array[$key][BTYPE]      = $BTYPE;
					$print_array[$key][WBFLOW]     = $WBFLOW;
					$print_array[$key][BID]        = $BID;
					$print_array[$key][PPID]       = $PPID;
					$print_array[$key][FACID]      = $FACID;
					$print_array[$key][BOID]       = $BOID;
					$print_array[$key][BORNAME]    = $BORNAME;
					$print_array[$key][UDNAME]     = $UDNAME;
					$print_array[$key][BOVATNO]    = $BOVATNO;
					$print_array[$key][UNAME]      = $UNAME;
					$print_array[$key][BOMID]      = $BOMID;
					$print_array[$key][BMEMO]      = $BMEMO;
					$print_array[$key][BOMEMO]     = $BOMEMO;
					$print_array[$key][BORADDR]    = $BORADDR;
					$print_array[$key][BOSEND]     = $BOSEND;
					$print_array[$key][GROUPID]    = $GROUPID;
					$print_array[$key][FLOWNEW]    = $FLOWNEW;
					$print_array[$key][BONUM]      = $BONUM;
					$print_array[$key][BOPRICE]    = $BOPRICE;    //總金額
					$print_array[$key][BOTIME]     = $BOTIME;
					$print_array[$key][BOSHIPTIME] = $BOSHIPTIME;
					$print_array[$key][BORPHONE]   = $BORPHONE;
					$print_array[$key][BOPAYTYPE]  = $BOPAYTYPE;
					$print_array[$key][BOINVTYPE]  = $BOINVTYPE;
					$print_array[$key][PDT_NAME]   = $PDT_NAME;
					$print_array[$key][PDT_GP]     = $PDT_GP;
					$print_array[$key][UNIT]       = $UNIT;
					$print_array[$key][PDF]        = $PDF_ARRAY;  // T-shirt檔名、名片檔案數量用
					$print_array[$key][YFPBOID]    = $YFPBOID; //工廠要看訂單編號
					$print_array[$key][VVBOID]     = $VVBOID;  //發票訂單編號
				  $print_array[$key][EXBOID]     = $EXBOID;  //拆帳訂單編號
					$print_array[$key][CPBONUS]    = $CPBONUS;  //折扣數
					$print_array[$key][BOCOUPON]   = $BOCOUPON; //折扣碼
					$print_array[$key][PREORDER]   = $PREORDER; //預收訂單
					$print_array[$key][PDF2]       = $PDF_ARRAY1; // 名片檔案數量 2011/01/18 Arvin 名片轉檔增加各PDF數量表，以利工廠核對盒數
					$print_array[$key][PPNAME]     = $PPNAME;       //2011/05/06 廣告面紙廠商通知信需要資訊
					$print_array[$key][WORKNAME]   = $WORK_ARRAY; // 加工條件陣列
					$print_array[$key][SIZE]       = $SIZE;
					$print_array[$key][OUT]        = $OUT;
					$print_array[$key][COLOR]      = $COLOR; //紙袋繩子顏色
					$print_array[$key][FILENAME]   = $YFPBOID;
					$print_array[$key][BOINVTITLE] = $BOINVTITLE;
					$print_array[$key][DM_PTE_NO]  = $DM_PTE_NO;
					$print_array[$key][GOLD]       = $GOLD; //是否燙金
					$print_array[$key][OBJID]      = $OBJID; //3D公仔印製物件ID
					$print_array[$key][COVER_KIND] = $COVER_KIND;
					$print_array[$key][PRODUCT_UNIT] = $PRODUCT_UNIT;//企業產品單位
					$print_array[$key][DBTITLE]            = $DBTITLE;
					$print_array[$key][PAINTBOX]         = $PAINTBOX;
					
					//2013/09/11 Arvin 判斷需要上傳檔案的檔名若有包含難字，將難字替換成空白。
					//                 避免外包FTP檔名無法接受難字造成檔案上傳正反面覆蓋
					
					$tmp_ary1 = explode(',', $WBOPTION);
					foreach((array)$tmp_ary1 as $t_ary)	{
							$tmp_ary2		= explode('=', $t_ary);
							$opkey		= trim(strtolower($tmp_ary2[0]));
							$opvalue	= trim($tmp_ary2[1]);
							if($opkey!='')	$WBOPTION_ARRAY[$opkey]= $opvalue;
					}
					
					
					//計算名字的長度
					$str_len=mb_strlen($BORNAME,'utf-8');
					$w_i=0;
					$tmp_borname='';
					while ($w_i < $str_len) {
							//抓出每個字來做難字比對
							$f_word=mb_substr($BORNAME, $w_i, 1, 'utf-8');
							$SQL='Select convert(varbinary(max),word) as word from W2PWORD where word='.$obj->EB_sqluniencode($f_word);
							$query=mssql_query($SQL);
							$num_row = mssql_num_rows($query);
							if ($num_row > 0 or in_array($f_word,$search_filename)) {
									//有難字替換掉準備上傳的檔名
									$tmp_borname=str_replace($f_word," ",$BORNAME);
							}
							$w_i++;
					}
					if ($tmp_borname!='') {
							$BORNAME=$tmp_borname;
					}

					$UPLOAD_PATH = $GB_BOOKPATH.$BID;	//上傳路徑

					/***************************************************************************************
					* 抓該訂單的紙別是否含有加工條件
					****************************************************************************************/
					$paper_array=$obj->getpaperppid($BTYPE,$PPID);

					$sub_process='';
					$process='';
					$process1='';
					if ($paper_array) {
							$print_array[$key][SUBWORKNAME]=$paper_array;
							foreach ($paper_array as $p_key => $value) {
									$sub_process.="+".$value;
									if (($BTYPE=='74' or $BTYPE=='75') and $p_key=='193') {
											$process1="+white";
									}
							}
					}
					/***************************************************************************************
					* 抓該訂單的加工條件及紙別
					****************************************************************************************/
					if (!empty($WORK_ARRAY)) {
							foreach ($WORK_ARRAY as $w_key => $w_value) {
									$process .="+".$w_value;
									if ($WORK_ARRAY1[$w_key]!='') {
											$process1.="+".$WORK_ARRAY1[$w_key];
									}
							}
					}
					$s_array=explode(",",$WBOPTION);
					$tshirt_kind= strtolower($s_array[1]);
					$tshirt_maping=array("221"=>"40x50,40x50","222"=>"40x50,30x20","223"=>"40x50,15x15","224"=>"40x50,","225"=>"30x20,30x20",
															 "226"=>"30x20,15x15","227"=>"30x20,","228"=>"15x15,15x15","229"=>"15x15,","222c"=>"30x20,40x50",
															 "223c"=>"15x15,40x50","224c"=>",40x50","226c"=>"15x15,30x20","229c"=>",15x15");

					$result_ftp=$obj->choose_ftp($FACID);

					//記錄生產工廠ID
					$print_array[$key]["FACNAME"]   = $result_ftp["FACNAME"];

					//建立PDF目錄用來存放編輯器的檔案 (讓編輯器產生的檔案也存放同FTP上傳的目錄，後面處理檔案方便)
					if (substr($BOID,0,1)!='S') {
							if (!is_dir($UPLOAD_PATH.'\\pdf')) {
									@mkdir($UPLOAD_PATH.'\\pdf');
							}
					}
					//=====================================================================================
					// 企業帳單、DM
					//=====================================================================================
					if ($BTYPE=='40' or $BTYPE=='42') {
							$SQL="Select * from w2product40 where PRTPTEID='$DM_PTE_NO'";
							$query=mssql_query($SQL,$GB_dblk);
							while ($rs1=mssql_fetch_array($query)) {
									$WBPAGES  =trim($rs1[BPAGE]);
									$PPID     =trim($rs1[PPID]);
									$MAP_BTYPE=trim($rs1[SIZETYPE]);
									$print_array[$key][BMEMO]=str_replace($search, $replace, trim(iconv('BIG5','UTF-8',$rs1["PRTNAME"])));
									$print_array[$key][BMEMO2]=str_replace($search, $replace, trim(iconv('BIG5','UTF-8',$rs1["PRTNAME2"])));
							}
					}
					$jump=false;
					switch ($BTYPE) {
							case '40'://企業訂單
							case '44'://吸水杯墊
							case '43':
							case '68'://iphone5
							case '69':
							case '45'://鏡盒(方形)
							case '46'://圓形鏡盒
							case '47'://鏡盒(LED)
							case '66'://note2
							case '67'://samsung S3
							case '72'://iPad mini
							case '73':
							case '61'://行動電源
							case '62':
							//case '220'://3D公仔
              case '70': //iphone4
              case '71':
							case '396':
							case '397':
									$jump=true;
									break;
							default:
									if (substr($BOID,0,1)=='S') {
											$jump=true;
									}
									break;
					}
					switch ($FACID) {
							case 30:
							case 34:
							case 99://測試工廠
									$jump=true;
									break;
							
					}
					//判斷是否有燙金版	
					if ($GOLD=='Y') {
							$CH_DIR="";
							$remote_dir="";
							switch ($BTYPE) {
									case 270:case 271:case 272:
											$FTP_HOST=$FACID;
											$Process_Name=$GOLDMSG;
                      $CH_DIR  =iconv('UTF-8','BIG5',"永豐");
											$remote_dir  =iconv('UTF-8','BIG5',$Process_Name);
											$jump=true;
											switch (strtolower($hole)) {
													case "b":
															$print_array[$key]["hole"]="打孔類型：方孔(排孔)";
															break;
													case "r":
															$print_array[$key]["hole"]="打孔類型：圓孔(6孔)";
															break;
											}
											switch (strtolower($gold_position)) {
													case "u":
															$print_array[$key]["gold_position"]="燙金位置：上";
															break;
													case "r":
															$print_array[$key]["gold_position"]="燙金位置：右";
															break;
													case "d":
															$print_array[$key]["gold_position"]="燙金位置：下";
															break;
											}
											switch (strtolower($gold_kind)) {
													case "s":
															$print_array[$key]["gold_kind"]="燙金類型：銀";
															break;
													case "g":
															$print_array[$key]["gold_kind"]="燙金類型：金";
															break;
											}
											break;
									default:
											$FTP_HOST="23";
											$CH_DIR="0946";
											$Process_Name=$GOLDMSG;
											$remote_dir  =iconv('UTF-8','BIG5',$Process_Name);
											break;
							}
							
							if (file_exists($GB_BOOKPATH.$BID."/PDF/BLACK.pdf")) {
									$obj->ll_echo("[".$obj->show_time()."]".$Process_Name."檔案處理[".$BOID."]");
									@copy($GB_BOOKPATH.$BID."/PDF/BLACK.pdf",$GB_BOOKPATH_PDF."spool/BLACK_".$YFPBOID."_".sprintf("%'02s",$BONUM).".pdf");													
									$rs_ftp=$obj->choose_ftp($FTP_HOST);
									if (!$JUST_ORDER) {
											$obj->ll_echo("[".$obj->show_time()."]FTP處理中[".$BOID."]");
											if(!@$ftp_conn_id = ftp_connect($rs_ftp[FTPIP])) {
													$obj->ll_echo("[".$obj->show_time()."]FTP連線失敗");
											}
											if (!@ftp_login($ftp_conn_id, $rs_ftp[FTPUSER], $rs_ftp[FTPPASS])) {
													$obj->ll_echo("[".$obj->show_time()."]FTP登入失敗");
											}
											if (!@ftp_chdir($ftp_conn_id,$CH_DIR)) {
													ftp_mkdir($ftp_conn_id,$CH_DIR);
													ftp_chdir($ftp_conn_id,$CH_DIR);
											}
											
											if ($remote_dir!="") {
													ftp_chdir($ftp_conn_id,$remote_dir);
											}
											@ftp_pasv($ftp_conn_id, true);				
											if (!@ftp_put($ftp_conn_id, "BLACK_".$YFPBOID."_".sprintf("%'02s",$BONUM).".pdf",$GB_BOOKPATH_PDF."spool/BLACK_".$YFPBOID."_".sprintf("%'02s",$BONUM).".pdf", FTP_BINARY)) {
													$obj->send_mail($MAIL_SERVER."-".$Process_Name."黑版檔案上傳失敗","平台編號:".$BOID);
													$obj->save_ftp_file($FTP_HOST,$GB_BOOKPATH_PDF."spool/BLACK_".$YFPBOID."_".sprintf("%'02s",$BONUM).".pdf","BLACK_".$YFPBOID."_".sprintf("%'02s",$BONUM).".pdf");
											}
											@ftp_close($ftp_conn_id);
									}
							 } else {
									 $obj->send_mail($MAIL_SERVER."-".$Process_Name."黑版檔案不存在","平台編號:".$BOID);
							 }
							 $title = $MAIL_SERVER."-".$Process_Name;
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
							 
							 $att_file = fopen($GB_BOOKPATH_PDF."spool/BLACK_".$YFPBOID."_".sprintf("%'02s",$BONUM).".pdf","r");
							 //把要夾的檔案讀出來 
							 $att_data = fread($att_file,filesize($GB_BOOKPATH_PDF."spool/BLACK_".$YFPBOID."_".sprintf("%'02s",$BONUM).".pdf"));
							 fclose($att_file);
							 //編碼後以固定長度斷行 
							 $read = chunk_split(base64_encode($att_data));
							 $emailBody.= $read."\r\n";
							 $emailBody.="--$boundary--";
							 if (!$JUST_ORDER) {
									$result=mail($INI_SET[BCC].',jennis@email.yfp.com.tw', $subject, $emailBody, $headers);
							 }
							 $obj->ll_echo("[".$obj->show_time()."]寄發燙金銀黑版檔案通知");
					}
					
					//因為企業DM有可能是白紗生產，所以不設定BTYPE=40而是設定FACID=25、26
					if ($jump or $JUST_ORDER) {
							$obj->ll_echo("[".$obj->show_time()."]例外產品及轉單不給檔跳過不處理[".$BOID."]");
							continue;
					}
	
					/***************************************************************************************
					* 單雙面
					****************************************************************************************/
					($WBPAGES=='2') ? $Single_Double='A' : $Single_Double='B';

					//$remote_dir ='';//遠端 FTP 目錄 
					/***************************************************************************************
					* 判斷編輯器製做的需透過makepdf.php來產生單模
					****************************************************************************************/
					if ($WBFLOW=='2') {
							$obj->ll_echo("[".$obj->show_time()."][".$BOID."]自由編輯-產生單模PDF");
							$status=$obj->fopen_exec($GB_W2PPDF."/album/makepdf.php?BID=".$BID."&ORD=".$YFPBOID.sprintf("%'02s",$BONUM));	//做單模
							//紙袋拼板
							if ($BTYPE>='130' and $BTYPE <='139') {
									@$obj->fopen_exec($GB_W2PPDF."/album/makeallpdfbag.php?BID=".$BID."&ORD=".$YFPBOID.sprintf("%'02s",$BONUM)); 
							//畫鐘
							} elseif ($BTYPE>='274' and $BTYPE <='283') {
									@$obj->fopen_exec($GB_W2PPDF."/album/makeAllpdfclock.php?BID=".$BID."&ORD=".$YFPBOID.sprintf("%'02s",$BONUM));
							} else {
									@$obj->fopen_exec($GB_W2PPDF."/album/makeAllpdf".$BTYPE.".php?BID=".$BID."&ORD=".$YFPBOID.sprintf("%'02s",$BONUM)); //內頁拼
							}
							//廣告面紙把PDF轉成300 dpi 的JPG
							if ($BTYPE=='109') {
									$obj->ll_echo("[".$obj->show_time()."][".$BOID."]廣告面紙PDF轉JPG");
									$source_pdf=$GB_BOOKPATH_PDF."spool\\BODY_".$BID.".pdf";
									$output_jpg=$GB_BOOKPATH_PDF."spool\\BODY_".$BID.".jpg";
									$CMD ="c:\\app\\Imagemagick\\convert -density 500 ";
									$CMD.=$source_pdf." ".$output_jpg;
									exec($CMD);
									sleep(1);
							}
					} elseif ($WBFLOW=='1') {
							if ($BTYPE>='130' and $BTYPE<='139') {
									$obj->ll_echo("[".$obj->show_time()."]紙袋FTP上傳檔案裁切[".$BOID."]");
									@$obj->fopen_exec($GB_W2PPDF."/album/makesplitpdf.php?BID=".$BID."&ORD=".$YFPBOID.sprintf("%'02s",$BONUM));
									$obj->ll_echo("[".$obj->show_time()."]紙袋FTP上傳檔案拼版[".$BOID."]");
									@$obj->fopen_exec($GB_W2PPDF."/album/makeallpdfbag.php?BID=".$BID."&ORD=".$YFPBOID.sprintf("%'02s",$BONUM)); //內頁拼
							} else {
									@$obj->fopen_exec($GB_W2PPDF."/album/makeAllpdf".$BTYPE.".php?BID=".$BID."&ORD=".$YFPBOID.sprintf("%'02s",$BONUM)); //內頁拼
							}
					}
					//因為74、75都是要呼叫makeallpdf74，所以這邊只要額外特別判斷當75的時候去呼叫就好，74依循上面的正常程序
					if ($BTYPE=="75") {
							@$obj->fopen_exec($GB_W2PPDF."/album/makeAllpdf74.php?BID=".$BID."&ORD=".$YFPBOID.sprintf("%'02s",$BONUM)); //內頁拼							
					}
					/***************************************************************************************
					* 檔案處理
					****************************************************************************************/
					//判斷是否沒有可處理的檔案資訊，寄發通知。
					if (count($PDF_ARRAY) < 1) {
							$obj->send_mail($BOID."(".$YFPBOID.")外包訂單無檔案資訊","訂單:".$BOID."(".$YFPBOID.")");
					}
					for($jj=0;$jj<count($PDF_ARRAY);$jj++) {
							$word_jump='N'; //難字處理開關
							$rename_file=array();
							//廣告面紙轉給廠商是 JPG
							if ($BTYPE=='109') {
									$file      = $UPLOAD_PATH.'\\pdf\\'.$PDF_ARRAY[$jj].".jpg";
									$old_file  = $UPLOAD_PATH.'\\pdf\\'.$PDF_ARRAY[$jj].".pdf";
									$new_file  = $file;
									if ($WBFLOW=='1') {
											$obj->ll_echo("[".$obj->show_time()."][".$BOID."]廣告面紙PDF轉JPG");
											$CMD ="convert -density 500 ";
											$CMD.=$old_file." ".$file;
											exec($CMD);
											sleep(1);
									}
							} elseif ($BTYPE=='74' or $BTYPE=='75') {
									$file      =$UPLOAD_PATH.'\\'.$PDF_ARRAY[$jj].".pdf";
							} else {
									$file      =$UPLOAD_PATH.'\\pdf\\'.$PDF_ARRAY[$jj].".pdf";
							}
							$file_black=$UPLOAD_PATH.'\\pdf\\'.$PDF_ARRAY[$jj]."_black.pdf";
							/***************************************************************************************
							* 編輯器製做的檔案要搬到PDF資料夾內
							****************************************************************************************/
							$r_file='';
							$pdi_dir="PDF"; //surcepdf 子目錄
							switch ($BTYPE) {
									case 48: //壁掛貼
									case 50: //靜電貼
									case 108://木質面紙盒
											$r_file=$GB_BOOKPATH_PDF."spool\\BODY_".$BID.".pdf";
											break;
									case 74: //貼紙
									case 75:
											$r_file=$GB_BOOKPATH_PDF."spool\\BODY_".$BID.".pdf";
											$pdi_dir=""; //surcepdf 子目錄
											break;
									case 76: //紙膠帶
									case 124://喜帖
									case 125:
									case 126:
									case 127:
									case 128:
									case 129:
									case 105:
											if ($WBFLOW=='2') {
													$r_file=$GB_BOOKPATH_PDF."spool\\BODY_".$BID.".pdf";
											}
											break;
									case 109://廣告面紙
											if ($WBFLOW=='2') {
													$r_file=$GB_BOOKPATH_PDF."spool\\BODY_".$BID.".jpg";
											}
											break;
									case 130:case 131:case 132:case 133:case 134:case 135:case 136:case 137:case 138:case 139:
									case ($BTYPE>=274 and $BTYPE<=283):
											//if ($WBFLOW=='2') {
													$r_file=$GB_BOOKPATH_PDF."spool\\COVER_".$BID.".pdf";
											//}
											break;
									default:
											if ($WBFLOW=='2') {
													$r_file=$GB_BOOKPATH_PDF."spool\\".$BID.".pdf";
											}
											break;
							}
							$l_file=$file;
							if (file_exists($r_file)) {
									copy($r_file, $l_file);
							}
							/***************************************************************************************
							* 判斷檔案是否存在，避免PDFLib Exception後續的都無法處理
							****************************************************************************************/
							if (!file_exists($file)) {
									$obj->send_mail($BOID."外包PDF檔案不存在","檔案:".$file);
									continue;
							}
							//更新PDF時間
							$SQL=' Update pordereg set TSPDF=\''.$obj->date_time().'\' where BOID=\''.$BOID.'\' and TSNEW=\''.$insert_time.'\'';
							$query=mssql_query($SQL,$GB_dblk);
							/***************************************************************************************
							*  生產工廠判斷
							****************************************************************************************/
							switch ($FACID) {
									//廣告面紙
									case 13:
									case 23:
									case 38: //紙膠帶
											//前面處理過了，這邊只是不讓他進default
											break;
									case 17://巨茂
											if ($CHANGEFTOB=='1') {
													$pdf_data=$obj->get_pdfdata($file);
													if (intval($pdf_data[totalpage]) >1) {
															$workpdfile = $UPLOAD_PATH."\\pdf\\tmp_".basename($file);
															$pdfhw->begin_document($workpdfile, "optimize=true compatibility=".$pdf_data[version]);
															$pdfhw->set_parameter("errorpolicy", "return");
															$pdfhw->set_parameter("textformat", "utf8");
															$pdfhw->set_info("Creator", "YFP");
															$pdfhw->set_info("Author",  "Arvin");
															$pdfhw->set_info("Title",   "single");

															$pdfhw->begin_page_ext($pdf_data[width],$pdf_data[height], "topdown");
															$doc = $pdfhw->open_pdi_document($file, "");
															$pagehw = $pdfhw->open_pdi_page($doc, 2, "");
															$pdfhw->fit_pdi_page($pagehw,0, $pdf_data[height], "boxsize={".$pdf_data[width]." ".$pdf_data[height]."} fitmethod=entire");
															$pdfhw->close_pdi_page( $pagehw);
															$pdfhw->end_page_ext("");
															
															$pdfhw->begin_page_ext($pdf_data[width],$pdf_data[height], "topdown");
															$pagehw = $pdfhw->open_pdi_page($doc, 1, "");
															$pdfhw->fit_pdi_page($pagehw,0, $pdf_data[height], "boxsize={".$pdf_data[width]." ".$pdf_data[height]."} fitmethod=entire");
															$pdfhw->close_pdi_page( $pagehw);
															$pdfhw->end_page_ext("");
															
															$pdfhw->close_pdi_document( $doc);
															$pdfhw->end_document("");
															
															if (@copy($workpdfile,$file)) {
																	unlink($workpdfile);
															}
													}
											}
											break;
									default:
											switch ($BTYPE) {
													case 74:
													case 75:
													case 48:
													case 50:
															//貼紙類產品要傳送每一模的檔案包一包壓縮檔給廠商
															//編輯器
															if ($WBFLOW==2) {
																	$D=array();
																	$single_pdf=$GB_BOOKPATH_PDF."spool\\".$BID.".pdf";
																	$pdf_data=$obj->get_pdfdata($single_pdf);
																	$WORKDIR='';
																	for ($j=1;$j<=intval($pdf_data[totalpage]);$j++) {
																			//抓取每頁的長寬
																			$each_page_data=$obj->get_pdfdata($single_pdf,($j-1));

																			$workpdfile = $UPLOAD_PATH."\\pdf\\".$j.".pdf ";
																			$pdfhw->begin_document($workpdfile, "optimize=true compatibility=".$each_page_data[version]);
																			$pdfhw->set_parameter("errorpolicy", "return");
																			$pdfhw->set_parameter("textformat", "utf8");
																			$pdfhw->set_info("Creator", "YFP");
																			$pdfhw->set_info("Author",  "Arvin");
																			$pdfhw->set_info("Title",   "single");

																			$pdfhw->begin_page_ext($each_page_data[width],$each_page_data[height], "topdown");
																			$doc = $pdfhw->open_pdi_document($single_pdf, "");
																			$pagehw = $pdfhw->open_pdi_page($doc, $j, "");
																			$pdfhw->fit_pdi_page($pagehw,0, $each_page_data[height], "boxsize={".$each_page_data[width]." ".$each_page_data[height]."} fitmethod=entire");
																			$pdfhw->close_pdi_page( $pagehw);
																			$pdfhw->end_page_ext("");

																			$pdfhw->close_pdi_document( $doc);
																			$pdfhw->end_document("");
																			$WORKDIR .=$workpdfile;
																			$D[]=$workpdfile;
																	}
															//FTP
															} else {
																	$WORKDIR='';
																	$WB_DIRHW = opendir($UPLOAD_PATH."\\pdf\\");	//開啟目錄
																	if($WB_DIRHW)		{
																			while( $t_file = strtolower( readdir($WB_DIRHW)))		{
																					$filetype = strtolower(substr($t_file, strlen($t_file)-3));//抓出副檔名
																					if ($filetype=='pdf' and !stristr($t_file,"hash")) {
																							$WORKDIR.=$UPLOAD_PATH."\\pdf\\".$t_file." ";
																					}
																			}
																			closedir($WB_DIRHW);
																	}
															}
															$obj->ll_echo("[".$obj->show_time()."][".$BOID."]單模壓縮檔上傳");
															$rar_file=$UPLOAD_PATH."\\pdf\\".$YFPBOID.".rar";
															$CMD_RAR = "c:/app/winrar/rar.exe a -ep -m0 $rar_file  $WORKDIR";
															exec($CMD_RAR);
															//編輯器產生的要刪除暫存檔
															if ($WBFLOW=='2') {
																	foreach ($D as $d_value) {
																			@unlink($d_value);
																	}
															}
															break;
													default:
															/***************************************************************************************
															* 讀取要拆開的PDF檔案資訊
															****************************************************************************************/
															$pdf_data=$obj->get_pdfdata($file);
															for ($j=1;$j<=intval($pdf_data[totalpage]);$j++) {
																	if ($j%2==0) {
																			//背面檔案
																			$pdf_name="TMP_".$PDF_ARRAY[$jj]."B";
																	} else {
																			$pdf_name="TMP_".$PDF_ARRAY[$jj];
																	}
																	//抓取每頁的長寬
																	$each_page_data=$obj->get_pdfdata($file,($j-1));

																	$workpdfile = $UPLOAD_PATH."\\pdf\\".$pdf_name.".pdf";
																	$pdfhw->begin_document($workpdfile, "optimize=true compatibility=".$each_page_data[version]);
																	$pdfhw->set_parameter("errorpolicy", "return");
																	$pdfhw->set_parameter("textformat", "utf8");
																	$pdfhw->set_info("Creator", "YFP");
																	$pdfhw->set_info("Author",  "Arvin");
																	$pdfhw->set_info("Title",   "split_pdf");
																	$pdfhw->begin_page_ext($each_page_data[width],$each_page_data[height], "topdown");
																	if ($pdi_dir!='') {
																			$doc = $pdfhw->open_pdi_document($UPLOAD_PATH."\\pdf\\".$PDF_ARRAY[$jj].".pdf", "");
																	} else {
																			$doc = $pdfhw->open_pdi_document($UPLOAD_PATH."\\".$PDF_ARRAY[$jj].".pdf", "");
																	}
																	$pagehw = $pdfhw->open_pdi_page($doc, $j, "");
																	$pdfhw->fit_pdi_page($pagehw,0, $each_page_data[height], "boxsize={".$each_page_data[width]." ".$each_page_data[height]."} fitmethod=entire");
																	$pdfhw->close_pdi_page( $pagehw);
																	/***************************************************************************************
																	* 名片類才顯示外框線
																	****************************************************************************************/
																	if ($BTYPE < 85 and $BTYPE > 81) {
																			$pdfhw->setcolor("fillstroke", "rgb", 1, 0, 0, 0);
																			$pdfhw->setlinewidth(0.3);
																			$pdfhw->moveto(0,0);			  								$pdfhw->lineto(0,$each_page_data[height]);
																			$pdfhw->moveto($each_page_data[width],0);		$pdfhw->lineto($each_page_data[width],$each_page_data[height]);
																			$pdfhw->moveto(0,0);			  								$pdfhw->lineto($each_page_data[width],0);
																			$pdfhw->moveto(0,$each_page_data[height]);	$pdfhw->lineto($each_page_data[width],$each_page_data[height]);
																			$pdfhw->stroke();
																	}
																	$pdfhw->end_page_ext("");

																	$pdfhw->close_pdi_document( $doc);
																	$pdfhw->end_document("");
																	if (file_exists($workpdfile)) {
																			$rename_file[]=$pdf_name.".pdf";
																	}
															}
															/***************************************************************************************
															* T-shirt要增加預覽PDF檔案
															****************************************************************************************/
															if ($FACID=='14' or $FACID=='88') {
																	$front=$UPLOAD_PATH."\\preview\\".$PDF_ARRAY[$jj].".jpg";
																	$back =$UPLOAD_PATH."\\preview\\".$PDF_ARRAY[$jj]."B.jpg";
																	$image = imagecreatefromjpeg($front);
																	$pic_width_front =imagesx($image)*0.3;
																	$pic_height_front=imagesy($image)*0.3;
																	$make_height=$pic_height_front;
																	$workpdfile = $UPLOAD_PATH."\\pdf\\preview.pdf";
																	$pdfhw->begin_document($workpdfile, "optimize=true compatibility=1.5");
																	$pdfhw->set_parameter("errorpolicy", "return");
																	$pdfhw->set_parameter("textformat", "utf8");
																	$pdfhw->set_info("Creator", "YFP");
																	$pdfhw->set_info("Author",  "Arvin");
																	$pdfhw->set_info("Title",   "Tshirt");

																	$pdfhw->begin_page_ext(595.3,841.9, "topdown");
																	$image = $pdfhw->load_image('auto', $front, "");
																	$pdfhw->fit_image($image, 110, $make_height, "boxsize={"."$pic_width_front $pic_height_front"."} fitmethod=entire");
																	$pdfhw->close_image($image);
																	//T-shirt如有背面檔案
																	$image = imagecreatefromjpeg($back);
																	$pic_width_back =imagesx($image)*0.3; //將預覽圖長寬縮小 30%
																	$pic_height_back=imagesy($image)*0.3;
																	$make_height=$pic_height_front+$pic_height_back;
																	$image = $pdfhw->load_image('auto', $back, "");
																	$pdfhw->fit_image($image, 110, $make_height, "boxsize={"."$pic_width_back $pic_height_back"."} fitmethod=entire");
																	$pdfhw->close_image($image);

																	$pdfhw->set_parameter("FontOutline", "font_arial=c:\\windows\\fonts\\arialuni.ttf");		// 內建中黑體
																	$font = $pdfhw->load_font("font_arial", "unicode", "embedding");
																	$pdfhw->setfont($font, 10);
																	$pdfhw->setcolor("fillstroke", "cmyk", 1, 1, 1, 1);
																	$pdf_y=$make_height+20;
																	$pdfhw->set_text_pos(60, $pdf_y);
																	$pdfhw->show("訂單號碼：".$YFPBOID);

																	$t_size_array=explode(",",$tshirt_maping[$tshirt_kind]);

																	$pdfhw->set_text_pos(60, $pdf_y+15);
																	if ($t_size_array[0]!='') {  //T-shirt如有背面才顯示
																			$front_name=$YFPBOID."_front+".trim($t_size_array[0]);
																	} else {
																			$front_name="";
																	}
																	$pdfhw->show("正面檔案：".$front_name);
																	$pdfhw->set_text_pos(60, $pdf_y+30);
																	if ($t_size_array[1]!='') {  //T-shirt如有背面才顯示
																			$back_name =$YFPBOID."_behind+".trim($t_size_array[1]);
																	} else {
																			$back_name="";
																	}
																	$pdfhw->show("背面檔案：".$back_name);
																	$pdfhw->set_text_pos(60, $pdf_y+45);
																	$pdfhw->show("合計：".$BONUM." 件");

																	$tt_array = explode(",",$BOPTION);
																	/***************************************************************************************/
																	/* 在T-shirt 上面畫 各尺寸及數量 Table
																	/***************************************************************************************/
																	$cell_optlist = "fittextline={position={left center} font=$font fontsize=10} margin=2";
																	$tbl1 = 0;
																	$tbl1 = $pdfhw->add_table_cell($tbl1, 1, 1,"女　版", $cell_optlist);
																	$tbl1 = $pdfhw->add_table_cell($tbl1, 1, 2,"中性版", $cell_optlist);

																	$col1=2;
																	$col2=2;
																	foreach ($tt_array as $t_key => $t_value) {
																			if ($t_key > 1) {
																					$tt_array1=explode("=",$t_value);
																					foreach ($tt_array1 as $t_key1 => $t_value1) {
																							$t_type=substr($tt_array1[0],4,1);
																							$t_size=strtoupper(substr($tt_array1[0],5))." 號 ";
																							$t_num =$tt_array1[1]." 件 ";
																					}
																					$show_string = $t_size.$t_num;
																					switch (strtolower($t_type)) {
																							case "w":
																									$tbl1 = $pdfhw->add_table_cell($tbl1, $col1, 1, $show_string, $cell_optlist);
																									$col1++;
																									break;
																							case "f":
																									$tbl1 = $pdfhw->add_table_cell($tbl1, $col2, 2, $show_string, $cell_optlist);
																									$col2++;
																									break;
																					}
																			}
																	}
																	$table_optlist = "stroke={{line=frame linewidth=0.3} {line=other linewidth=0.3}}";
																	$llx=60; $lly=$pdf_y+60; $urx=500; $ury=$pdf_y+110;
																	$result = $pdfhw->fit_table($tbl1, $llx, $lly, $urx, $ury, $table_optlist);
																	$pdfhw->end_page_ext("");
																	$pdfhw->delete_table($tbl1,"");
																	$pdfhw->end_document("");
																	$rename_file[]="preview.pdf";
															}
															break;
											}
											break;
							}
							/***************************************************************************************
							* 判斷名片 Template 是否有上光黑版
							****************************************************************************************/
							if (file_exists($file_black) and $WBFLOW=='3' and $FACID!='23' and $FACID!='17') {
									$pdf_data_black=$obj->get_pdfdata($file_black);
									for ($j=1;$j<=intval($pdf_data_black[totalpage]);$j++) {
											if ($j%2==0) {
													//背面檔案
													$pdf_name="TMP_black_".$PDF_ARRAY[$jj]."B";
											} else {
													$pdf_name="TMP_black_".$PDF_ARRAY[$jj];
											}
											$workpdfile = $UPLOAD_PATH."\\pdf\\".$pdf_name.".pdf";
											$pdfhw->begin_document($workpdfile, "optimize=true compatibility=".$pdf_data_black[version]);
											$pdfhw->set_parameter("errorpolicy", "return");
											$pdfhw->set_parameter("textformat", "utf8");
											$pdfhw->set_info("Creator", "YFP");
											$pdfhw->set_info("Author",  "Arvin");
											$pdfhw->set_info("Title",   "NameCard");
											$pdfhw->begin_page_ext($pdf_data_black[width],$pdf_data_black[height], "topdown");
											$doc = $pdfhw->open_pdi_document($UPLOAD_PATH."\\pdf\\".$PDF_ARRAY[$jj]."_black.pdf", "");
											$pagehw = $pdfhw->open_pdi_page($doc, $j, "");
											$pdfhw->fit_pdi_page($pagehw,0, $pdf_data_black[height], "boxsize={".$pdf_data_black[width]." ".$pdf_data_black[height]."} fitmethod=entire");
											$pdfhw->close_pdi_page( $pagehw);
											/***************************************************************************************
											* 名片類才顯示外框線
											****************************************************************************************/
											if ($BTYPE < 85 and $BTYPE > 81) {
													$pdfhw->setcolor("fillstroke", "rgb", 1, 0, 0, 0);
													$pdfhw->setlinewidth(0.3);
													$pdfhw->moveto(0,0);			  					      $pdfhw->lineto(0,$pdf_data_black[height]);
													$pdfhw->moveto($pdf_data_black[width],0);		$pdfhw->lineto($pdf_data_black[width],$pdf_data_black[height]);
													$pdfhw->moveto(0,0);			  					      $pdfhw->lineto($pdf_data_black[width],0);
													$pdfhw->moveto(0,$pdf_data_black[height]);	$pdfhw->lineto($pdf_data_black[width],$pdf_data_black[height]);
													$pdfhw->stroke();
											}
											$pdfhw->end_page_ext("");
											$pdfhw->close_pdi_document( $doc);
											$pdfhw->end_document("");
											if (file_exists($workpdfile)) {
													$rename_file[]=$pdf_name.".pdf";
											}
									}
							}
							/***************************************************************************************
							* 轉換檔案命名
							****************************************************************************************/
							switch ($BTYPE) {
									case ($BTYPE < 85 and $BTYPE > 81):
											//白紗名片
											if ($FACID=='12') {
													//名片 公司名 – 紙別代號、單雙面、盒數 – 組數 – 檔案名稱 + 後加工及其它
													// S1永豐 – PA5 – 1P1M –1081005240104 + 燙金 + 沖圓角
													if ($PDF_ARRAY1[$jj] < $PDF_ARRAY2[$jj]) {  // 判斷下訂量若小於最低訂購量，則將最低訂購量當成要轉給工廠的數量
															$COUNT = $PDF_ARRAY2[$jj];
													} else {
															$COUNT = $PDF_ARRAY1[$jj];
													}
													$remote_file  = 'S1永豐-'.trim($paper_map['white'][$PPID]).$Single_Double.$COUNT;
													//$remote_file .= '-1P-'.$YFPBOID.'X'.substr($PDF_ARRAY[$jj],1,2).$process.$sub_process."+".$BOMID;
													$remote_file .= '-1P-'.$YFPBOID.'X'.$PDF_ARRAY[$jj].$process.$sub_process."+".$BOMID;
													$remote_file .= $BORNAME;
													//對色訊息，在class.php裡面判斷相關需要對色的模板。
													if ($CK_COLOR!='') {
															$remote_file.="+".$CK_COLOR;
													}
											//依您印名片、巨茂
											} else {
													$remote_file=$YFPBOID.'X'.$PDF_ARRAY[$jj]."_".$PDF_ARRAY1[$jj];
													//$remote_dir  ='名片';
													$remote_dir  =iconv('UTF-8','BIG5','名片');
											}
											break;
									//無痕壁掛貼
									case 48:
											if ($WBOPTION_ARRAY["style"]=='B') {
													$remote_file = $YFPBOID."+HOOK+SQ+";
											} else {
													$remote_file = $YFPBOID."+HOOK+CR+";
											}
											$remote_file.=$BONUM;
											$remote_dir  =iconv('UTF-8','BIG5','永豐');
											break;
									//停車證靜電貼
									case 50:
											if ($WBOPTION_ARRAY["style"]=='B') {
													$remote_file = $YFPBOID."+PET+MIRROR+SQ+";
											} else {
													$remote_file = $YFPBOID."+PET+MIRROR+CR+";
											}
											$remote_file.=$BONUM;
											$remote_dir  =iconv('UTF-8','BIG5','永豐');
											break;
									//平張、捲筒貼紙
									case 74:
									case 75:
											$remote_file  = $YFPBOID."+".trim($paper_map['kuo'][$PPID]).$process1."#";
											$remote_file .= $SIZE."#".$PDF_ARRAY1[$jj]."PCS".$OUT."+".$tmp_array[$key][PICS];
											//2013/01/08 判斷頁數大於2等於多模，檔名加上MULTI
											if ($WBPAGES > 2) {
													$remote_file.='+MULTI';
											}
											$remote_dir  =iconv('UTF-8','BIG5','永豐');
											break;
									//DM、企業DM
									case ($BTYPE >= 85 and $BTYPE < 103):
									case 40:
									case 42:
											if ($BTYPE==40 or $BTYPE==42) {
													$SW_BTYPE=$MAP_BTYPE;
											} else {
													$SW_BTYPE=$BTYPE;
											}
											switch ($SW_BTYPE) {
													case 85:case 86:
															$product_kind='A4-成品297x210mm';
															break;
													case 87:case 88:
															$product_kind='A5-成品210x148mm';
															break;
													case 89:case 90:
															$product_kind='A3-成品420x297mm';
															break;
													case 91:case 92:
															$product_kind='B4-成品353x250mm';
															break;
													case 101:case 102:
															$product_kind='A2-成品594x420mm';
															break;
													default:
															$product_kind='其它';
															break;
											}
											//非名片類  公司名 – 尺寸 + 磅數 + 數量、單雙面 – 檔案名稱 + 分區分群 + 後加工及其它
											// S1永豐 – A4 + 100P + 1仟B – 1081005240104 + 不裁修
											$remote_file  = 'S1永豐-'.$product_kind.'+'.trim($paper_map['white'][$PPID])."+".$obj->getChineseNumber($PDF_ARRAY1[$jj]);
											$remote_file .= $Single_Double."-".$YFPBOID.'X'.substr($PDF_ARRAY[$jj],-2)."+".$BOMID.$BORNAME;
											
											$remote_file .= $process.$sub_process."+特急件";
											if ($BTYPE=='40') {
													$print_array[$BOID][FILENAME]=basename($file);  //2012/05/30 企業DM
											}
											break;
									//木質面紙盒
									case 108:
											$remote_file=$YFPBOID;
											$remote_dir  ='BOX';
											break;
									case 105:
									case 106://名片面紙包
											$kind_array=array("652"=>"8","653"=>"10");
											if ($BTYPE=='106') {
													$remote_dir  =iconv('UTF-8','BIG5','名片面紙');
													$remote_file = $YFPBOID."_".$kind_array[$PPID]."抽_".$PDF_ARRAY1[$jj];
											} else {
													$remote_dir  =iconv('UTF-8','BIG5','塑料面紙包');
													$remote_file = $YFPBOID."_".$print_array[$key][PPNAME]."_".$PDF_ARRAY1[$jj];
											}
											$print_array[$BOID][FILENAME]=$remote_file;
											break;
									case 109:
											$kind_array=array("650"=>"5","651"=>"7");
											$remote_file = $BOID.substr($PDF_ARRAY[$jj],-2)."+".$PDF_ARRAY1[$jj]."包+".$kind_array[$PPID]."抽";
											$remote_dir  ='yfp';
											break;
									//桌、掛曆
									case 30:
									case 31:
									case 32:
									case 33:
											/* 2012/08/29 Arvin 桌曆250本以上改轉依您印 */
											$remote_file=$YFPBOID;
											if ($BTYPE=='30' or $BTYPE=='31') {
													$remote_dir  =iconv('UTF-8','BIG5','桌曆');
											} else {
													$remote_dir  =iconv('UTF-8','BIG5','掛曆');
											}
											break;
									//喜帖
									case 124:
									case 125:
									case 126:
									case 127:
									case 128:
									case 129:
											$remote_file=$YFPBOID."_".$BONUM;
											$remote_dir  =iconv('UTF-8','BIG5','喜帖');
											break;
									//T恤
									case 110:
									case 107:
											$remote_file = $YFPBOID;
											foreach ($tt_array as $t_key => $t_value) {
													if ($t_key > 1) {
															$tt_array1=explode("=",$t_value);
															foreach ($tt_array1 as $t_key1 => $t_value1) {
																	$T_SHIRT_FILE[$BOID][$PDF_ARRAY[$jj]][SIZE][substr($tt_array1[0],4,1)][substr($tt_array1[0],5)]=$tt_array1[1];
															}
													}
											}
											if ($BTYPE=='110') {
													$remote_dir  ="100451B";
											} else {
													$remote_dir  =iconv('UTF-8','BIG5','永豐');
											}
											break;
									//信封套
									case ($BTYPE>='180' and $BTYPE<='203'):
											$remote_file=$YFPBOID;
											$remote_dir  =iconv('UTF-8','BIG5','信封套');
											break;
									case 70:
									case 71:
											switch ($BTYPE) {
													case '70':
															$TYPE='V';
															break;
													case '71':
															$TYPE='H';
															break;
											}
											switch ($PPID) {
													case '236':
													case '126':
															$COLOR='B';
															break;
													case '237':
													case '127':
															$COLOR='W';
															break;
											}
											$remote_file=$YFPBOID.'_'.$TYPE.$COLOR.'_'.$BONUM;
											break;
									//紙袋
									case ($BTYPE>='130' and $BTYPE<='139'):
											$remote_file=$YFPBOID.'_'.$BMEMO.'_'.$BONUM;
											$remote_dir  =iconv('UTF-8','BIG5','紙袋');
											break;
									//複寫聯單
									case ($BTYPE >='170' and $BTYPE <='177'):
											$remote_file=$YFPBOID;
											$remote_dir  =iconv('UTF-8','BIG5','複寫聯單');
											break;
									//帆布袋
									case 167:
									case 168:
											$remote_file=$YFPBOID;
											$remote_dir  =iconv('UTF-8','BIG5','帆布袋');
											break;
									//塑料面紙包
									//L夾
									case 166:
											$remote_file=$YFPBOID;
											$remote_dir  =iconv('UTF-8','BIG5','L夾');
											break;
									default:
											$remote_file=$YFPBOID;
											break;
							}
							/***************************************************************************************
							* 名片是否有難字處理
							****************************************************************************************/
							if ($BTYPE < 85 and $BTYPE > 81 and $WBFLOW=='3' and $FACID!='23') {
									//計算名字的長度
									$str_len=mb_strlen($NAME_ARRAY[$jj],'utf-8');
									$w_i=0;
									while ($w_i < $str_len) {
											//抓出每個字來做難字比對
											$f_word=mb_substr($NAME_ARRAY[$jj], $w_i, 1, 'utf-8');
											$tmp_result=$obj->search_cus_no($BOID);
											$WGGID=$tmp_result[ggid];
											$SQL='Select convert(varbinary(max),word) as word from W2PWORDFIND where WGGID=\''.$WGGID.'\' and word='.$obj->EB_sqluniencode($f_word);
											$query=mssql_query($SQL);
											$num_row = mssql_num_rows($query);
											if ($num_row > 0) {
													$bad_namecard_ary=array();
													//有難字特別處理
													$obj->ll_echo("[".$obj->show_time()."][".$BOID."]".basename($file)."難字處理");
													$m_file_name='';
													
													foreach ($rename_file as $real_file) {
															$real_remote_file=$remote_file;
															$upfile=$UPLOAD_PATH.'\\pdf\\'.$real_file;
															//判斷背面的檔案檔名要加上#
															if (strstr($real_file,"B")) {
																	$real_remote_file.="#";
															}
															//判斷上光黑板檔名要加上K
															if (strstr($real_file,"black")) {
																	$real_remote_file.="K";
															}
															$real_remote_file .=".pdf";
															$m_file_name .=$real_remote_file." <br>";
															if (!copy($upfile,"D:\\www\\transfer\\badnamecard\\".iconv("UTF-8","BIG5",$real_remote_file))) {
																	$obj->ll_echo("難字PDF檔案複製失敗!!原始檔案:".$upfile);
															}
															$bad_namecard_ary["file"][]=$upfile;
															$bad_namecard_ary["name"][]=$real_remote_file;
													}
													$mail_text ="[".$YFPBOID."]名片訂單內有難字($NAME_ARRAY[$jj])，請協助處理。<br>";
													$mail_text.="請修改內容後直接傳檔至白紗主機，下載檔案切勿修改【檔名】<br>";
													$mail_text.="檔案名稱：<br>".$m_file_name;
													
													
													
													$title="名片難字通知";
													
													$subject = "=?UTF-8?B?" . base64_encode($title) . "?=";								
													$boundary = uniqid("");
															
													$headers ="From: webmaster@cloudw2p.com"."\r\n";
													$headers.="Reply-To: webmaster@cloudw2p.com"."\r\n";
													$headers.="X-Priority: 1"."\r\n";
													$headers.="X-MSMail-Priority: High"."\r\n";
													$headers.="Content-type: multipart/mixed; boundary=\"$boundary\"";
													
													$read = base64_encode($mail_text);
													$read = chunk_split($read);
													
													$emailBody = '--'.$boundary."\n";
													$emailBody.= 'Content-type: text/html; charset="utf-8"'."\n";
													$emailBody.= 'Content-transfer-encoding: base64'."\n\n";
													
													$emailBody.= $read."\n"; // 本文內容
													
													foreach ($bad_namecard_ary["file"] as $bad_key => $bad_value) {	
															$att_file = fopen($bad_value,"r");
															//把要夾的檔案讀出來 
															$att_data = fread($att_file,filesize($bad_value));
															fclose($att_file);
															//編碼後以固定長度斷行 
															$read1 = chunk_split(base64_encode($att_data));
															$emailBody.= "\r\n";
															$emailBody.= '--'.$boundary."\r\n";
															$emailBody.= 'Content-Type: application/octet-stream; name='.$bad_namecard_ary["name"][$bad_key]."\r\n";
															$emailBody.= 'Content-disposition: inline; attachment'."\r\n";
															$emailBody.= 'Content-transfer-encoding: base64'."\r\n\r\n";
															$emailBody.= $read1."\r\n";
													} 
													$emailBody.="--$boundary--";
													$result=mail($INI_SET[namecard], $subject, $emailBody, $headers);
													if ($result) { 
															$obj->ll_echo("[".$obj->show_time()."][".$BOID."]名片難字寄送成功");
													} else {
															$obj->ll_echo("[".$obj->show_time()."][".$BOID."]名片難字寄送失敗");
													}
													$word_jump='Y';//跳掉一般程序
													break;
											}
											$w_i++;
									}
									if ($word_jump=='Y') {
											//2011/07/21 Arvin 若名片有難字則在轉檔清冊內的備註註明。
											$print_array[$key][WORD] = "難字處理";
											continue;
									}
							}
							/***************************************************************************************
							* FTP 上傳
							****************************************************************************************/
							if ($INI_SET[FTP]=="Y") {
									//木質面紙盒傳直噴機不上依您印FTP  2015/03/20 Arvin 直噴機依您印搬回去了所以要改FTP傳檔到直噴機目錄下
									//if ($BTYPE=='108') {
									//		 $d_file="\\\\192.168.50.242\\jobs\\en_out\\box\\".$remote_file.".pdf";
									//		 copy($file, $d_file);
									//		 continue;
									//}

									// 上傳檔案
									if(!@$ftp_conn_id = ftp_connect($result_ftp[FTPIP])) {
											$obj->ll_echo("[".$obj->show_time()."]FTP連線失敗");
											$obj->send_mail("FTP連線失敗","廠商：".$result_ftp["FACNAME"]);

									}
									if (!@ftp_login($ftp_conn_id, $result_ftp[FTPUSER], $result_ftp[FTPPASS])) {
											$obj->ll_echo("[".$obj->show_time()."]FTP登入失敗");
									}
									if ($FACID=='23') {
											@ftp_chdir($ftp_conn_id,"0946");
											if ($BTYPE=='108' or $BTYPE=='167' or $BTYPE=='168') { //面紙盒要轉到直噴機的資料夾
													@ftp_chdir($ftp_conn_id,"mimaki");
											} 
									}
									//切換到遠端的目錄
									if ($remote_dir!='') {
											@ftp_chdir($ftp_conn_id,$remote_dir);
									}
									//被動模式
									@ftp_pasv($ftp_conn_id, true);
									//iphone殼、拼圖、吸水杯墊、紙袋、信封套
									switch ($BTYPE) {
											case 70:
											case 71:
											case 60:
											//case 61:
											//case ($BTYPE >='115' and $BTYPE <='122'):
											case ($BTYPE >='130' and $BTYPE <='139'):
											case ($BTYPE >='180' and $BTYPE <='203'):
													if (!@ftp_chdir($ftp_conn_id,$DIR_NAME)) {
															ftp_mkdir($ftp_conn_id,$DIR_NAME);
															ftp_chdir($ftp_conn_id,$DIR_NAME);
													}
													break;
											case ($BTYPE >='82'  and $BTYPE <='84'):
													//依您印名片要多開資料夾
													if ($FACID=='23') {
															$check_hour=date("H",strtotime($DATETIME));
															if ($check_hour > 19) {
																	$update_dir_name=date("Ymd",mktime(0,0,0,date("m",strtotime($DATETIME)),date("d",strtotime($DATETIME))+1,date("Y",strtotime($DATETIME))));
																	$NEW_DIR_NAME=$update_dir_name;
															} else {
																	$NEW_DIR_NAME=$DIR_NAME;
															}
															if (!@ftp_chdir($ftp_conn_id,$NEW_DIR_NAME)) {
																	ftp_mkdir($ftp_conn_id,$NEW_DIR_NAME);
																	ftp_chdir($ftp_conn_id,$NEW_DIR_NAME);
															}
													} elseif ($FACID=='17') {
															if (!@ftp_chdir($ftp_conn_id,$DIR_NAME)) {
																	ftp_mkdir($ftp_conn_id,$DIR_NAME);
																	ftp_chdir($ftp_conn_id,$DIR_NAME);
															}
													}
													break;
											case 110:
													@ftp_mkdir($ftp_conn_id,$BOID);
													@ftp_chdir($ftp_conn_id,$BOID);
													break;
									}
									/***************************************************************************************
									* 貼紙壓縮檔上傳
									****************************************************************************************/
									switch ($BTYPE) {
											case 74:
											case 75:
											case 48:
											case 50:
													if (!@ftp_put($ftp_conn_id, iconv("UTF-8","BIG5",basename($rar_file)), $rar_file, FTP_BINARY)) {
															$obj->ll_echo("國田訂單壓縮檔上傳失敗!!原始檔案:".$rar_file."遠端檔案:".basename($rar_file));
															$obj->send_mail("國田訂單壓縮檔上傳失敗!!原始檔案:".$rar_file."遠端檔案:".basename($rar_file));
															$obj->save_ftp_file($FACID,$rar_file,basename($rar_file));
													} else {
															@unlink($rar_file);
													}
													break;
									}
									if (!empty($rename_file) and count($rename_file) > 0) {
											foreach ($rename_file as $real_file) {
													$skip="N"; //T-shrit上傳檔案開關
													$real_remote_file=$remote_file;
													$upfile=$UPLOAD_PATH.'\\pdf\\'.$real_file;
													//判斷背面的檔案檔名要加上#
													if ($BTYPE!='110' and $BTYPE!='107') {
															if ($BTYPE!='74' and $BTYPE!='75') {
																	if (strstr($real_file,"B")) {
																			$real_remote_file.="#";
																	}
																	//判斷上光黑板檔名要加上K
																	if (strstr($real_file,"black")) {
																			$real_remote_file.="K";
																	}
															} else {
																	if (strstr($real_file,"B")) {
																			$real_remote_file.="+K";
																	}
															}
													//T-shirt檔名命名規則
													} else {
															$tt_array=explode(",",$tshirt_maping[$tshirt_kind]);
															$front=trim($tt_array[0]);
															$back =trim($tt_array[1]);
															if (strstr($real_file,"B")) {
																	$real_remote_file.="_behind+".$back;
																	if ($back!='') {
																			$T_SHIRT_FILE[$BOID][$PDF_ARRAY[$jj]][BACK]=$real_remote_file;
																	} else {
																			$T_SHIRT_FILE[$BOID][$PDF_ARRAY[$jj]][BACK]="無";
																			$skip="Y";
																	}
															} elseif (strstr($real_file,"preview")) {
																	$real_remote_file.="_Preview";
															} else {
																	$real_remote_file.="_front+".$front;
																	if ($front!='') {
																			$T_SHIRT_FILE[$BOID][$PDF_ARRAY[$jj]][FRONT]=$real_remote_file;
																	} else {
																			$T_SHIRT_FILE[$BOID][$PDF_ARRAY[$jj]][FRONT]="無";
																			$skip="Y";
																	}
															}
													}
													$real_remote_file .=".pdf";
													//T-shirt用，如果前後無圖檔則不上傳檔案
													if ($skip!="Y") {
															if (@ftp_put($ftp_conn_id, iconv("UTF-8","BIG5",$real_remote_file), $upfile, FTP_BINARY)) {
																	//上傳完畢刪除拆開的PDF檔案
																	if (($WBPAGES=='4' and ($BTYPE!='109' or $BTYPE!='110' or $BTYPE!='107')) or $BTYPE=='74' or $BTYPE=='75') {
																			unlink($upfile);
																	}
																	//更新FTP時間
																	$SQL=' Update pordereg set TSFTP=\''.$obj->date_time().'\' where BOID=\''.$BOID.'\' and TSNEW=\''.$insert_time.'\' ';
																	$query=mssql_query($SQL,$GB_dblk);
															} else {
																	$obj->ll_echo("PDF檔案上傳失敗!!原始檔案:".$upfile."遠端檔案:".$real_remote_file);
																	$obj->send_mail("W2P檔案上傳失敗","原始檔案:".$upfile."遠端檔案:".$real_remote_file);
																	$obj->save_ftp_file($FACID,$upfile,$real_remote_file);
															}
													}
											}
									} else {
											//廣告面紙轉給廠商是JPG檔案
											if ($BTYPE=='109') {
													$remote_file.=".jpg";
											} else {
													$remote_file.=".pdf";
											}
											//複寫聯單  2012/10/16 Arvin 吸水杯墊轉回由永豐生產
											if ($BTYPE>='170' and $BTYPE<='177') {
													//2014/02/24 Arvin 因為預覽直接抓前台所製作的，所以增加判斷沒有預覽圖寄發通知
													if (!file_exists($UPLOAD_PATH."\\preview\\preview001.jpg")) {
															$obj->ll_echo("複寫聯單預覽不存在!訂單編號:".$YFPBOID);
															$obj->send_mail("複寫聯單預覽不存在!訂單編號:".$YFPBOID);
													}
											
													copy($file,$UPLOAD_PATH."\\".$remote_file);
													$WORKDIR =$UPLOAD_PATH."\\preview\\preview001.jpg " ;
													$WORKDIR.=$UPLOAD_PATH."\\".$remote_file;
													$rar_file=$UPLOAD_PATH."\\".$YFPBOID.".rar";
													$CMD_RAR = "c:/app/winrar/rar.exe a -ep -m0 $rar_file  $WORKDIR";
													exec($CMD_RAR);
													unlink($UPLOAD_PATH."\\".$remote_file);
													$file=$rar_file;
													$remote_file=basename($rar_file);
													$print_array[$BOID][FILENAME]=$remote_file;
											}
											if (!file_exists($file)) {
													$obj->ll_echo("PDF檔案不存在!!原始檔案:".$file."遠端檔案:".$remote_file);
													$obj->send_mail("PDF檔案不存在!!","原始檔案:".$file."遠端檔案:".$remote_file);
													$obj->save_ftp_file($FACID,$file,$real_remote_file);
											}

											if (@ftp_put($ftp_conn_id, iconv("UTF-8","BIG5",$remote_file), $file, FTP_BINARY)) {
													//更新FTP時間
													$SQL=' Update pordereg set TSFTP=\''.$obj->date_time().'\' where BOID=\''.$BOID.'\' and TSNEW=\''.$insert_time.'\' ';
													$query=mssql_query($SQL,$GB_dblk);
													if ($BTYPE>='170' and $BTYPE<='177') {
															unlink($rar_file);
													}
											} else {
													$obj->ll_echo("PDF檔案上傳失敗!!原始檔案:".$file."遠端檔案:".$remote_file);
													$obj->send_mail("W2P檔案上傳失敗","原始檔案:".$file."遠端檔案:".$remote_file);
													$obj->save_ftp_file($FACID,$file,$real_remote_file);
											}
									}
									@ftp_close($ftp_conn_id);
									$obj->ll_echo("[".$obj->show_time()."][$BOID]FTP傳檔結束");
							}
					}
			}

?>
