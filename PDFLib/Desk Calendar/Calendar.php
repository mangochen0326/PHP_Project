<?php 
/*
 * 農曆 節氣 節日
 */
class Calendar { 
	
	public  $Document_root    = null;
	public  $PDF_OUTPUT_PATH  = null;
	public  $PRE_OUTPUT_PATH  = null;
	public  $THU_OUTPUT_PATH  = null;
	public  $SOURCE_PATH      = null;
	public  $INI_FILE         = null;
	private $data = array();
	private $data_map =array();
	public  $px = 2.8346456;
	
	
	var $MIN_YEAR = 1891; 
	var $MAX_YEAR = 2100; 
	var $lunarInfo = array( 
	array(0,2,9,21936),array(6,1,30,9656),array(0,2,17,9584),array(0,2,6,21168),array(5,1,26,43344),array(0,2,13,59728), 
	array(0,2,2,27296),array(3,1,22,44368),array(0,2,10,43856),array(8,1,30,19304),array(0,2,19,19168),array(0,2,8,42352), 
	array(5,1,29,21096),array(0,2,16,53856),array(0,2,4,55632),array(4,1,25,27304),array(0,2,13,22176),array(0,2,2,39632), 
	array(2,1,22,19176),array(0,2,10,19168),array(6,1,30,42200),array(0,2,18,42192),array(0,2,6,53840),array(5,1,26,54568), 
	array(0,2,14,46400),array(0,2,3,54944),array(2,1,23,38608),array(0,2,11,38320),array(7,2,1,18872),array(0,2,20,18800), 
	array(0,2,8,42160),array(5,1,28,45656),array(0,2,16,27216),array(0,2,5,27968),array(4,1,24,44456),array(0,2,13,11104), 
	array(0,2,2,38256),array(2,1,23,18808),array(0,2,10,18800),array(6,1,30,25776),array(0,2,17,54432),array(0,2,6,59984), 
	array(5,1,26,27976),array(0,2,14,23248),array(0,2,4,11104),array(3,1,24,37744),array(0,2,11,37600),array(7,1,31,51560), 
	array(0,2,19,51536),array(0,2,8,54432),array(6,1,27,55888),array(0,2,15,46416),array(0,2,5,22176),array(4,1,25,43736), 
	array(0,2,13,9680),array(0,2,2,37584),array(2,1,22,51544),array(0,2,10,43344),array(7,1,29,46248),array(0,2,17,27808), 
	array(0,2,6,46416),array(5,1,27,21928),array(0,2,14,19872),array(0,2,3,42416),array(3,1,24,21176),array(0,2,12,21168), 
	array(8,1,31,43344),array(0,2,18,59728),array(0,2,8,27296),array(6,1,28,44368),array(0,2,15,43856),array(0,2,5,19296), 
	array(4,1,25,42352),array(0,2,13,42352),array(0,2,2,21088),array(3,1,21,59696),array(0,2,9,55632),array(7,1,30,23208), 
	array(0,2,17,22176),array(0,2,6,38608),array(5,1,27,19176),array(0,2,15,19152),array(0,2,3,42192),array(4,1,23,53864), 
	array(0,2,11,53840),array(8,1,31,54568),array(0,2,18,46400),array(0,2,7,46752),array(6,1,28,38608),array(0,2,16,38320), 
	array(0,2,5,18864),array(4,1,25,42168),array(0,2,13,42160),array(10,2,2,45656),array(0,2,20,27216),array(0,2,9,27968), 
	array(6,1,29,44448),array(0,2,17,43872),array(0,2,6,38256),array(5,1,27,18808),array(0,2,15,18800),array(0,2,4,25776), 
	array(3,1,23,27216),array(0,2,10,59984),array(8,1,31,27432),array(0,2,19,23232),array(0,2,7,43872),array(5,1,28,37736), 
	array(0,2,16,37600),array(0,2,5,51552),array(4,1,24,54440),array(0,2,12,54432),array(0,2,1,55888),array(2,1,22,23208), 
	array(0,2,9,22176),array(7,1,29,43736),array(0,2,18,9680),array(0,2,7,37584),array(5,1,26,51544),array(0,2,14,43344), 
	array(0,2,3,46240),array(4,1,23,46416),array(0,2,10,44368),array(9,1,31,21928),array(0,2,19,19360),array(0,2,8,42416), 
	array(6,1,28,21176),array(0,2,16,21168),array(0,2,5,43312),array(4,1,25,29864),array(0,2,12,27296),array(0,2,1,44368), 
	array(2,1,22,19880),array(0,2,10,19296),array(6,1,29,42352),array(0,2,17,42208),array(0,2,6,53856),array(5,1,26,59696), 
	array(0,2,13,54576),array(0,2,3,23200),array(3,1,23,27472),array(0,2,11,38608),array(11,1,31,19176),array(0,2,19,19152), 
	array(0,2,8,42192),array(6,1,28,53848),array(0,2,15,53840),array(0,2,4,54560),array(5,1,24,55968),array(0,2,12,46496), 
	array(0,2,1,22224),array(2,1,22,19160),array(0,2,10,18864),array(7,1,30,42168),array(0,2,17,42160),array(0,2,6,43600), 
	array(5,1,26,46376),array(0,2,14,27936),array(0,2,2,44448),array(3,1,23,21936),array(0,2,11,37744),array(8,2,1,18808), 
	array(0,2,19,18800),array(0,2,8,25776),array(6,1,28,27216),array(0,2,15,59984),array(0,2,4,27424),array(4,1,24,43872), 
	array(0,2,12,43744),array(0,2,2,37600),array(3,1,21,51568),array(0,2,9,51552),array(7,1,29,54440),array(0,2,17,54432), 
	array(0,2,5,55888),array(5,1,26,23208),array(0,2,14,22176),array(0,2,3,42704),array(4,1,23,21224),array(0,2,11,21200), 
	array(8,1,31,43352),array(0,2,19,43344),array(0,2,7,46240),array(6,1,27,46416),array(0,2,15,44368),array(0,2,5,21920), 
	array(4,1,24,42448),array(0,2,12,42416),array(0,2,2,21168),array(3,1,22,43320),array(0,2,9,26928),array(7,1,29,29336), 
	array(0,2,17,27296),array(0,2,6,44368),array(5,1,26,19880),array(0,2,14,19296),array(0,2,3,42352),array(4,1,24,21104), 
	array(0,2,10,53856),array(8,1,30,59696),array(0,2,18,54560),array(0,2,7,55968),array(6,1,27,27472),array(0,2,15,22224), 
	array(0,2,5,19168),array(4,1,25,42216),array(0,2,12,42192),array(0,2,1,53584),array(2,1,21,55592),array(0,2,9,54560) 
	); 
	
	private static $instance = false;
	
	function __construct($DB) {
			$this->DB=$DB;
  }
	
	static function getInstance($DB) {
        if (!self::$instance) {
          self::$instance = new Calendar($DB);
        }
        return self::$instance;
    }
	
	
	/** 
	* 將陽曆轉換為陰曆 
	* @param year 西曆-年 
	* @param month 西曆-月 
	* @param date 西曆-日 
	*/ 
	function convertSolarToLunar($year,$month,$date){ 
		//debugger; 
		$yearData = $this->lunarInfo[$year-$this->MIN_YEAR]; 
		if($year==$this->MIN_YEAR&&$month<=2&&$date<=9){ 
			return array(1891,'正月','初一','辛卯',1,1,'兔'); 
		} 
		return $this->getLunarByBetween($year,$this->getDaysBetweenSolar($year,$month,$date,$yearData[1],$yearData[2])); 
	} 
	
	function convertSolarMonthToLunar($year,$month) { 
		$yearData = $this->lunarInfo[$year-$this->MIN_YEAR]; 
		if($year==$this->MIN_YEAR&&$month<=2&&$date<=9){ 
			return array(1891,'正月','初一','辛卯',1,1,'兔'); 
		} 
		$month_days_ary = array("1"=>31, "2"=>28, "3"=>31, "4"=>30, "5"=>31, "6"=>30, "7"=>31, "8"=>31, "9"=>30, "10"=>31, "11"=>30, "12"=>31); 
		$dd = $month_days_ary[$month]; 
		if($this->isLeapYear($year) && $month == 2) $dd++; 
		$lunar_ary = array(); 
		for ($i = 1; $i <=$dd; $i++) { 
			$array = $this->getLunarByBetween($year,$this->getDaysBetweenSolar($year, $month, $i, $yearData[1], $yearData[2])); 
			$array[] = $year . '-' . $month . '-' . $i; 
			$lunar_ary[$i] = $array; 
		}
		$luntotal_day=$month_days_ary[$month]; 
		
		
		return $luntotal_day; 
		//return $lunar_ary; 
	} 
	/** 
	* 將陰曆轉換為陽曆 
	* @param year 陰曆-年 
	* @param month 陰曆-月，閏月處理：例如如果當年閏五月，那麼第二個五月就傳六月，相當於陰曆有13個月，只是有的時候第13個月的天數為0 
	* @param date 陰曆-日 
	*/ 
	function convertLunarToSolar($year,$month,$date){ 
		$yearData = $this->lunarInfo[$year-$this->MIN_YEAR]; 
		$between = $this->getDaysBetweenLunar($year,$month,$date); 
		$res = mktime(0,0,0,$yearData[1],$yearData[2],$year); 
		$res = date('Y-m-d', $res+$between*24*60*60); 
		$day = explode('-', $res); 
		$year = $day[0]; 
		$month= $day[1]; 
		$day = $day[2]; 
		return array($year, $month, $day); 
	} 
	/** 
	* 判斷是否是閏年 
	* @param year 
	*/ 
	function isLeapYear($year){ 
		return (($year%4==0 && $year%100 !=0) || ($year%400==0)); 
	} 
	/** 
	* 獲取干支紀年 
	* @param year 
	*/ 
	function getLunarYearName($year){ 
		$sky = array('庚','辛','壬','癸','甲','乙','丙','丁','戊','己'); 
		$earth = array('申','酉','戌','亥','子','醜','寅','卯','辰','巳','午','未'); 
		$year = $year.''; 
		return $sky[$year{3}].$earth[$year%12]; 
	} 
	/** 
	* 根據陰曆年獲取生肖 
	* @param year 陰曆年 
	*/ 
	function getYearZodiac($year){ 
		$zodiac = array('猴','雞','狗','豬','鼠','牛','虎','兔','龍','蛇','馬','羊'); 
		return $zodiac[$year%12]; 
	} 
	/** 
	* 獲取陽曆月份的天數 
	* @param year 陽曆-年 
	* @param month 陽曆-月 
	*/ 
	function getSolarMonthDays($year,$month){ 
		$monthHash = array('1'=>31,'2'=>$this->isLeapYear($year)?29:28,'3'=>31,'4'=>30,'5'=>31,'6'=>30,'7'=>31,'8'=>31,'9'=>30,'10'=>31,'11'=>30,'12'=>31); 
		return $monthHash["$month"]; 
	} 
	/** 
	* 獲取陰曆月份的天數 
	* @param year 陰曆-年 
	* @param month 陰曆-月，從一月開始 
	*/ 
	function getLunarMonthDays($year,$month){ 
		$monthData = $this->getLunarMonths($year); 
		return $monthData[$month-1]; 
	} 
	/** 
	* 獲取陰曆每月的天數的陣列 
	* @param year 
	*/ 
	function getLunarMonths($year){ 
		$yearData = $this->lunarInfo[$year - $this->MIN_YEAR]; 
		$leapMonth = $yearData[0]; 
		$bit = decbin($yearData[3]); 
		for ($i = 0; $i < strlen($bit);$i ++) { 
			$bitArray[$i] = substr($bit, $i, 1); 
		} 
		for($k=0,$klen=16-count($bitArray);$k<$klen;$k++){ 
			array_unshift($bitArray, '0'); 
		} 
		$bitArray = array_slice($bitArray,0,($leapMonth==0?12:13)); 
		for($i=0; $i<count($bitArray); $i++){ 
			$bitArray[$i] = $bitArray[$i] + 29; 
		} 
		return $bitArray; 
	} 
	/** 
	* 獲取農曆每年的天數 
	* @param year 農曆年份 
	*/ 
	function getLunarYearDays($year){ 
		$yearData = $this->lunarInfo[$year-$this->MIN_YEAR]; 
		$monthArray = $this->getLunarYearMonths($year); 
		$len = count($monthArray); 
		return ($monthArray[$len-1]==0?$monthArray[$len-2]:$monthArray[$len-1]); 
	} 
	function getLunarYearMonths($year){ 
		//debugger; 
		$monthData = $this->getLunarMonths($year); 
		$res=array(); 
		$temp=0; 
		$yearData = $this->lunarInfo[$year-$this->MIN_YEAR]; 
		$len = ($yearData[0]==0?12:13); 
		for($i=0;$i<$len;$i++){ 
			$temp=0; 
			for($j=0;$j<=$i;$j++){ 
				$temp+=$monthData[$j]; 
			} 
			array_push($res, $temp); 
		} 
		return $res; 
	} 
	/** 
	* 獲取閏月 
	* @param year 陰曆年份 
	*/ 
	function getLeapMonth($year){ 
		$yearData = $this->lunarInfo[$year-$this->MIN_YEAR]; 
		return $yearData[0]; 
	} 
	/** 
	* 計算陰曆日期與正月初一相隔的天數 
	* @param year 
	* @param month 
	* @param date 
	*/ 
	function getDaysBetweenLunar($year,$month,$date){ 
		$yearMonth = $this->getLunarMonths($year); 
		$res=0; 
		for($i=1;$i<$month;$i++){ 
			$res +=$yearMonth[$i-1]; 
		} 
		$res+=$date-1; 
		return $res; 
	} 
	/** 
	* 計算2個陽曆日期之間的天數 
	* @param year 陽曆年 
	* @param cmonth 
	* @param cdate 
	* @param dmonth 陰曆正月對應的陽曆月份 
	* @param ddate 陰曆初一對應的陽曆天數 
	*/ 
	function getDaysBetweenSolar($year,$cmonth,$cdate,$dmonth,$ddate){ 
		$a = mktime(0,0,0,$cmonth,$cdate,$year); 
		$b = mktime(0,0,0,$dmonth,$ddate,$year); 
		return ceil(($a-$b)/24/3600); 
	} 
	/** 
	* 根據距離正月初一的天數計算陰曆日期 
	* @param year 陽曆年 
	* @param between 天數 
	*/ 
	function getLunarByBetween($year,$between){ 
		//debugger; 
		$lunarArray = array(); 
		$yearMonth=array(); 
		$t=0; 
		$e=0; 
		$leapMonth=0; 
		$m=''; 
		if($between==0){ 
			array_push($lunarArray, $year,'正月','初一'); 
			$t = 1; 
			$e = 1; 
		}else{ 
			$year = $between>0? $year : ($year-1); 
			$yearMonth = $this->getLunarYearMonths($year); 
			$leapMonth = $this->getLeapMonth($year); 
			$between = $between>0?$between : ($this->getLunarYearDays($year)+$between); 
			for($i=0;$i<13;$i++){ 
				if($between==$yearMonth[$i]){ 
					$t=$i+2; 
					$e=1; 
					break; 
				}else if($between<$yearMonth[$i]){ 
					$t=$i+1; 
					$e=$between-(empty($yearMonth[$i-1])?0:$yearMonth[$i-1])+1; 
					break; 
				} 
			} 
			//$m = ($leapMonth!=0&&$t==$leapMonth+1)?('閏'.$this->getCapitalNum($t- 1,true)):$this->getCapitalNum(($leapMonth!=0&&$leapMonth+1<$t?($t-1):$t),true); 

			if ($leapMonth!=0&&$t==$leapMonth+1) {
					$m='閏'.$this->getCapitalNum($t-1,true);
			} else {
					//$this->getCapitalNum(($leapMonth!=0&&$leapMonth+1<$t?($t-1):$t),true);
					if ($leapMonth!=0&&$leapMonth+1<$t) {
							$tt=$t-1;
					} else {
							$tt=$t;
					}
					$m=$this->getCapitalNum($tt,true);
			}
			
			
			array_push($lunarArray,$year,$m,$this->getCapitalNum($e,false)); 
		} 
		array_push($lunarArray,$this->getLunarYearName($year));// 天干地支 
		
		//echo "[".$this->getLunarYearName($year)."]";
		array_push($lunarArray,$t,$e); 
		//array_push($lunarArray,($leapMonth!=0&&$leapMonth+1<=$t?($t-1):$t),$e);
		array_push($lunarArray,$this->getYearZodiac($year));// 12生肖 
		array_push($lunarArray,$leapMonth);// 閏幾月 
		
		return $lunarArray; 
	} 
	/** 
	* 獲取數字的陰曆叫法 
	* @param num 數字 
	* @param isMonth 是否是月份的數字 
	*/ 
	function getCapitalNum($num,$isMonth){ 
		$isMonth = $isMonth || false; 
		$dateHash=array('0'=>'','1'=>'一','2'=>'二','3'=>'三','4'=>'四','5'=>'五','6'=>'六','7'=>'七','8'=>'八','9'=>'九','10'=>'十'); 
		$monthHash=array('0'=>'','1'=>'正月','2'=>'二月','3'=>'三月','4'=>'四月','5'=>'五月','6'=>'六月','7'=>'七月','8'=>'八月','9'=>'九月','10'=>'十月','11'=>'十一月','12'=>'十二月'); 
		$res=''; 
		if($isMonth){ 
			$res = $monthHash[$num]; 
		}else{ 
			if($num<=10){ 
				$res = '初'.$dateHash[$num]; 
			}else if($num>10&&$num<20){ 
				$res = '十'.$dateHash[$num-10]; 
			}else if($num==20){ 
				$res = "二十"; 
			}else if($num>20&&$num<30){ 
				$res = "廿".$dateHash[$num-20]; 
			}else if($num==30){ 
				$res = "三十"; 
			} 
		} 
		return $res; 
	}

	/*
	 * 節氣通用演算法
	 */	
	function getJieQi($_year,$month,$day)
	{
		$year = substr($_year,-2)+0;
		$coefficient = array(
			array(5.4055,2019,-1),//小寒
			array(20.12,2082,1),//大寒
			array(3.87),//立春
			array(18.74,2026,-1),//雨水
			array(5.63),//驚蟄
			array(20.646,2084,1),//春分
			array(4.81),//清明
			array(20.1),//穀雨
			array(5.52,1911,1),//立夏
			array(21.04,2008,1),//小滿
			array(5.678,1902,1),//芒種
			array(21.37,1928,1),//夏至
			array(7.108,2016,1),//小暑
			array(22.83,1922,1),//大暑
			array(7.5,2002,1),//立秋
			array(23.13),//處暑
			array(7.646,1927,1),//白露
			array(23.042,1942,1),//秋分
			array(8.318),//寒露
			array(23.438,2089,1),//霜降
			array(7.438,2089,1),//立冬
			array(22.36,1978,1),//小雪
			array(7.18,1954,1),//大雪
			array(21.94,2021,-1)//冬至
		);
		$term_name = array(   
		"小寒","大寒","立春","雨水","驚蟄","春分","清明節","穀雨",   
		"立夏","小滿","芒種","夏至","小暑","大暑","立秋","處暑",   
		"白露","秋分","寒露","霜降","立冬","小雪","大雪","冬至");
		
		$idx1 = ($month-1)*2;
		$_leap_value = floor(($year-1)/4);
		
		$day1 = floor($year*0.2422+$coefficient[$idx1][0])-$_leap_value;
		if(isset($coefficient[$idx1][1])&&$coefficient[$idx1][1]==$_year)
		{
			$day1 += $coefficient[$idx1][2];
		}
		$day2 = floor($year*0.2422+$coefficient[$idx1+1][0])-$_leap_value;
		if(isset($coefficient[$idx1+1][1])&&$coefficient[$idx1+1][1]==$_year)
		{
			$day1 += $coefficient[$idx1+1][2];
		}
		//echo __FILE__.'->'.__LINE__.' $day1='.$day1,',$day2='.$day2.'<br/>'.chr(10);
		if($day==$day1) return $term_name[$idx1];
		if($day==$day2) return $term_name[$idx1+1];
		return '';
	}
	
	
	/*
	 * 獲取節日：特殊的節日只能修改此函數來計算
	 */
	function getFestival($yy,$mm,$dd, $nl_info = false,$config = 1)
	{
		if($config == 1)
		{
			$arr_lunar=array('0101'=>'春節','0102'=>'回娘家','0103'=>'祭祖','0104'=>'迎神','0105'=>'開市','0115'=>'元宵節','0505'=>'端午節','0707'=>'七夕','0715'=>'中元節','0815'=>'中秋節','0909'=>'重陽節','1208'=>'臘八節','1216'=>'尾牙','1229'=>'小年夜','1230'=>'除夕');	
			$arr_solar=array('0101'=>'元旦','0228'=>'二二八','0214'=>'情人節','0308'=>'婦女節','0312'=>'植樹節','0329'=>'青年節','0401'=>'愚人節','0404'=>'兒童節','0501'=>'勞動節','0531'=>'無菸日','0808'=>'父親節','0903'=>'軍人節','0928'=>'教師節','1010'=>'國慶日','1025'=>'光復節','1031'=>'蔣公誕辰','1112'=>'國父誕辰','1224'=>'平安夜','1225'=>'耶誕節');
		}//需要不同節日的，用不同的$config,然後配置$arr_lunar和$arr_solar

		$festivals = array();
		
	
		if(!$nl_info) $nl_info = $this->convertSolarToLunar($yy,intval($mm),intval($dd));
	
		
	
		if($nl_info[7]>0&&$nl_info[7]<$nl_info[4]) $nl_info[4]-=1;
		
		$md_lunar = sprintf("%02d",$nl_info[4]).sprintf("%02d",$nl_info[5]);
		$md_solar=sprintf("%02d",$mm).sprintf("%02d",$dd);
		
	
		isset($arr_lunar[$md_lunar])?array_push($festivals, $arr_lunar[$md_lunar]):'';
		isset($arr_solar[$md_solar])?array_push($festivals, $arr_solar[$md_solar]):'';
		
		
		$glweek  = date("w",mktime(0,0,0,$mm,$dd,$yy));    //0-6 
		if($mm==5&&($dd>7)&&($dd<15)&&($glweek==0))array_push($festivals, "母親節"); 
		

		$jieqi = $this->getJieQi($yy,$mm,$dd);
		if($jieqi)array_push($festivals,$jieqi); 
		return implode('/',$festivals);
	}
	
	//產生萬年歷資料
		function Make_calendar_date($START_YEAR,$END_YEAR) {
				//$SQL="Delete from DESK_CHDAY";
				//mssql_query($SQL,$this->DB);
				for ($y=intval($START_YEAR);$y<=intval($END_YEAR);$y++) {
						//$y=$s_y;
						$SQL="Delete DESK_MASTER where left(YEAR,4)='$y'";
						mssql_query($SQL,$this->DB);
						//$d_s_year=$this->convertSolarToLunar($y,1,1);
						//$d_e_year=$this->convertSolarToLunar($y,12,31);
						for ($m=1;$m<=12;$m++) {
								$ii=1;
								for ($d=1;$d<=42;$d++) {
										if(checkdate($m,$d,$y)=="1"){
												$b = date ("w",mktime(0,0,0,$m,$d,$y));
												if($d==1) {
														$ii+=$b;
												} 
												$ch_date=$this->convertSolarToLunar($y, $m, $d);
												$season=$this->getFestival($y, $m, $d);
												//判斷如果沒有12/30的話12/29就是除夕 2015/10/14 Arvin
												if ($ch_date[4]==12 and $ch_date[5]==29) {
														if ($d>=31) {
																$chk_m=$m+1;
																$chk_d=1;
														}  else {
																$chk_m=$m;
																$chk_d=$d+1;
														}
														$cc_date=$this->convertSolarToLunar($y, $chk_m, $chk_d);
														if ($cc_date[4]==1 and $cc_date[1]) {
																$season="除夕";
														}
												}
												//$season=   $lunar->getJieQi($y,$m,$d);
												//$tmp_days=$lunar->getLunarMonthDays($y,$m);
												if ($ii>35) {
														$ii=$ii-7;
												}
												$SQL=" insert into DESK_MASTER (YEAR,MONTH,DAY,CHYEAR,CHMONTH,CHDAY,WEEK,POSITION) values('".$y."','".sprintf("%02d",$m)."','".sprintf("%02d",$d)."','".$ch_date[0]."','".sprintf("%02d",$ch_date[4])."','".sprintf("%02d",$ch_date[5])."','".$b."','".$ii."')";
												$QUERY    = mssql_query($SQL,$this->DB);
												if ($season!='') {
														$SQL ="Select * from DESK_CHDAY where CHMD='".$ch_date[0].sprintf("%02d",$ch_date[4]).sprintf("%02d",$ch_date[5])."' ";
														$query=mssql_query($SQL,$this->DB);
														$count=mssql_num_rows($query);
														if ($count < 1) {
																$SQL="Insert into DESK_CHDAY (CHMD,NAME) values('".$ch_date[0].sprintf("%02d",$ch_date[4]).sprintf("%02d",$ch_date[5])."','". iconv('UTF-8', 'BIG5', $season)."')";
																$QUERY    = mssql_query($SQL,$this->DB);
														} else {
																$SQL="update DESK_CHDAY set NAME='".iconv('UTF-8', 'BIG5', $season)."' where CHMD='".$ch_date[0].sprintf("%02d",$ch_date[4]).sprintf("%02d",$ch_date[5])."'";
																$QUERY    = mssql_query($SQL,$this->DB);
														}
												} 
												$ii++;
										} else{
												break;
										}
								}
						}
				}
				$ch_date= null;
		}
		
		function search_pdf() {
				$WB_DIRHW = opendir($this->SOURCE_PATH);	//開啟目錄
				if ($WB_DIRHW) {
						while( $file = strtolower(readdir($WB_DIRHW))) {
								if (is_file($this->SOURCE_PATH.$file)) {
										$info=pathinfo($file);
										switch ($info["extension"]) {
											 case "pdf":
														$mode=strtoupper(substr($info[filename],0,1));
														$kind=substr($info[filename],1,2);
														$mon=substr($info[filename],3,2);
														$type=substr($info[filename],-1);
														
														switch ($type) {
																case "y":
																		$rs[YEAR][$mode][$kind][]=$this->SOURCE_PATH.$info[basename];
																		break;
																default:
																		$rs[MON][$mode][$kind][$mon][$type][]=$this->SOURCE_PATH.$info[basename];
																		break;
														}
														break;
										}
								}
						}
						closedir($WB_DIRHW);
						$this->data=$rs;
						return $this->data;
				}
		
		}
		
		function search_data() {
				$sql ='Select YEAR,MONTH,DAY,CHYEAR,CHMONTH,CHDAY,WEEK,POSITION,STATUS,B.NAME,C.NAME as CNAME ';
				$sql.=' from desk_master A left join desk_holiday B on A.YEAR+A.MONTH+A.DAY=B.YMD ';
				$sql.=' left join DESK_CHDAY C on A.CHYEAR+A.CHMONTH+A.CHDAY=C.CHMD ';
				$sql.=' order by YEAR,MONTH,DAY,POSITION ';
				//echo $sql;
			
				$query=mssql_query($sql,$this->DB);
				
				while($rs=mssql_fetch_array($query)) {
						$year=trim($rs[YEAR]);
						$mon =intval(trim($rs[MONTH]));
						$position=trim($rs[POSITION]);
						$day=intval(trim($rs[DAY]));
						$week=trim($rs[WEEK]);
						$status=trim($rs[STATUS]);
						$chname=trim($rs[CNAME]);
						
						$result[$year][$mon][$position][]=$day;
						$result[$year.sprintf("%02d",$mon).$position][WEEK][$day]=$week;
						$result[$year.sprintf("%02d",$mon).$position][STATUS][$day]=$status;
						$result["PRINT"][$year][$mon][$position][$day]=$week;
						$result["CHMAP"][$year.sprintf("%02d",$mon).sprintf("%02d",$day)]=$chname;
				}
				return $this->data_map=$result;
		}
		
		function make_day_background ($pobj,$s_year,$_type) {
				//讀取ini設定相關參數
				$ini_array=$this->ini_set();
				
				$GOTHIC = $pobj->load_font("GOTHIC", "unicode", "embedding");
				$GOTHICB = $pobj->load_font("GOTHICB", "unicode", "embedding");
				$BHEI01M = $pobj->load_font("BHEI01M", "unicode", "embedding");
				$CALIBRI =$pobj->load_font("CALIBRI", "unicode", "embedding");
				$CHAPA   =$pobj->load_font("CHAPA", "unicode", "embedding");
				$DFT     =$pobj->load_font("DFT:0", "unicode", "embedding");
				$BKANT   =$pobj->load_font("BKANT", "unicode", "embedding");
				$CAN     =$pobj->load_font("CAN", "unicode", "embedding");
				$font_arial     =$pobj->load_font("font_arial", "unicode", "embedding");
			
				$week_array =array("0"=>"日","1"=>"一","2"=>"二","3"=>"三","4"=>"四","5"=>"五","6"=>"六");
				$week_array_en=array("0"=>"S","1"=>"M","2"=>"T","3"=>"W","4"=>"T","5"=>"F","6"=>"S");
				//die;
				if (!empty($this->data[MON])) {
						//所有需要處理的模式
						foreach ($this->data[MON] as $mode => $tmp_ary1) {
								//所有需要處理的背景款式
								foreach ($tmp_ary1 as $kind => $tmp_ary2) {
										//所有需要處理的月份
										foreach ($tmp_ary2 as $mon =>$tmp_ary3) {
												//所有需要處理的檔案
												foreach ($tmp_ary3 as $type => $tmp_ary4) {
														foreach ($tmp_ary4 as $source) {
																switch (strtoupper($type)) {
																		//**************************************************************
																		//*  簡約版
																		//**************************************************************
																		case "S":	
																				$table_width  =$ini_array[$mode][SINGLE_TABLE_WIDTH];
																				$table_height =$ini_array[$mode][SINGLE_TABLE_HEIGHT];
																				$table_sub_y  =$ini_array[$mode][SINGLE_TABLE_SUB_Y];
																				
																				//套印資料的檔案位置及檔名
																				$new_pdf =$this->PDF_OUTPUT_PATH.$mode.$kind.$s_year.sprintf("%02d",$mon).strtoupper($type).".pdf"; 
																				//PDF表頭設定
																				pdf_head($pobj,$new_pdf);
																				$page = LL_NewPage($pobj, $doc, $source);	
																				//開起PDF頁面
																				switch (strtoupper($mode)) {
																						case "F":
																								$pobj->begin_page_ext($ini_array[vertical_width]*$this->px, $ini_array[vertical_height]*$this->px, "topdown");		//開啟一個新PDF工作頁
																								LL_FitPdiPage($pobj, $doc,0*$this->px, $ini_array[vertical_height]*$this->px, $ini_array[vertical_width]*$this->px, $ini_array[vertical_height]*$this->px, 1);
																								break;
																						default:
																								$pobj->begin_page_ext($ini_array[width]*$this->px, $ini_array[height]*$this->px, "topdown");		//開啟一個新PDF工作頁
																								LL_FitPdiPage($pobj, $doc,0*$this->px, $ini_array[height]*$this->px, $ini_array[width]*$this->px, $ini_array[height]*$this->px, 1);
																								break;
																				}
																				
																				// $pobj->begin_page_ext($ini_array[width]*$this->px, $ini_array[height]*$this->px, "topdown");		//開啟一個新PDF工作頁
																				// LL_FitPdiPage($pobj, $doc,0*$this->px, $ini_array[height]*$this->px, $ini_array[width]*$this->px, $ini_array[height]*$this->px, 1);
																				
																				
																				switch (strtoupper($mode)) {
																						case "A":
																								//計算處理的月份有幾天，來產生年份的colspan數字
																								$m_days=$this->getSolarMonthDays($s_year,intval($mon));
																								$colspan="colspan=".($m_days);
																								
																								$year_cell_optlist = "fittextline={position={right} font=$GOTHIC fontsize=13} rowheight=9 $colspan";
																								$tbl1 = $pobj->add_table_cell($tbl1, 1, 1,$s_year." ", $year_cell_optlist);
																								
																								$col=1;
																								foreach ($this->data_map["PRINT"][$s_year][intval($mon)] as $position => $day_ary) {
																										$day=array_keys($day_ary);
																										$week=array_values($day_ary);
																										$cname="";
																									
																										//$tf="S_WEEK".$mon.$position;
																										//$tf1="S_DAY".$mon.$position;
																										$tf2="S_CDAY".$mon.$position;
																										$tf3="S_CDAY1".$mon.$position;
																										$chk_status=$this->data_map[$s_year.$mon.$position][STATUS][$day[0]]; //判斷是否假日(非六日)
																										
																										//判斷星期六日及假日顯示紅字
																										if ($week[0]==0 or $week[0]==6) {
																												$week_color="fillcolor={ rgb 1 0 0}";
																												$day_color="fillcolor={ rgb 1 0 0}";
																										} else {
																												$week_color="fillcolor={ rgb 0 0 0}";
																												$day_color="fillcolor={ rgb 0 0 0}";
																										}
																										if ($chk_status==2) {
																												$day_color="fillcolor={ rgb 0 0 0}";
																										} elseif ($chk_status==1) {
																												$week_color="fillcolor={ rgb 1 0 0}";
																												$day_color="fillcolor={ rgb 1 0 0}";
																										}
																										
																										
																										//$$tf = $pobj->create_textflow($week_array[$week[0]], "fontname=BHEI01M fontsize=5 alignment=center encoding=unicode $week_color");
																										//$optlist = " colwidth=24 rowheight=10 textflow=".$$tf;
																										
																										$optlist="fittextline={position={center} font=$BHEI01M fontsize=5 $week_color} colwidth=24 rowheight=10 ";
																										
																										
																										//$$tf1 = $pobj->create_textflow($day[0], "fontname=GOTHIC fontsize=10 alignment=center encoding=unicode $day_color");
																										//$optlist1 = " colwidth=24 rowheight=10 textflow=".$$tf1;
																										
																										$optlist1="fittextline={position={center} font=$GOTHIC fontsize=10 $day_color} colwidth=24 rowheight=10 ";
																										
																										
																										//農曆節氣、特殊名稱轉換
																										$cname=$this->data_map["CHMAP"][$s_year.$mon.sprintf("%02d",$day[0])];
																										//執行國曆轉農曆
																										$ch_date=$this->convertSolarToLunar($s_year, intval($mon),intval($day[0]));
																										//計算處理月份農曆天數
																										$ch_countday=$this->getLunarMonthDays($ch_date[0],$ch_date[4]);
																										//每個農曆1號不顯示初一，改顯示月份大小。ex:八月大、閏九月小
																										if ($ch_date[5]==1) {
																												if ($ch_countday==29)  {
																														$bs="小";
																												} else {
																														$bs="大";
																												}
																												$show_cname=$ch_date[1].$bs;
																											//農曆非1號並且有特殊名稱時則顯示特殊名稱。ex:臘八，大寒
																										} else {
																												$show_cname=$ch_date[2];
																										}
																										if ($cname!='') {
																												$show_cname=iconv('BIG5','UTF-8',$cname);
																										}
																										
																										$tmp=explode("/",$show_cname);
																										if (count($tmp)> 2) {
																												$show_cname=$tmp[2];
																										} elseif (count($tmp)> 1) {
																												$show_cname=$tmp[1];
																										} else {
																												$show_cname=$tmp[0];
																										}
																										
																										//判斷當顯示的農曆名稱大於3個字則利用Margin來做到兩個字換下一行
																										if (mb_strlen($show_cname) > 9) {
																												$margin="margin=2";
																										} else {
																												$margin="";
																										}
																										
																										//產生農曆日期的文字
																										$$tf2 = $pobj->create_textflow($show_cname, "fontname=BHEI01M fontsize=5 alignment=center  encoding=unicode $day_color");
																										$optlist2 = " colwidth=24 $margin rowheight=10 textflow=".$$tf2;
																										
																										//$optlist2="fittextline={position={center top} font=$BHEI01M fontsize=5 $day_color} colwidth=24 rowheight=18 $margin";
																										
																										$tbl1 = $pobj->add_table_cell($tbl1,$col,2,$week_array[$week[0]], $optlist);
																										$tbl2 = $pobj->add_table_cell($tbl2,$col,1,$day[0], $optlist1);
																										//$tbl1 = $pobj->add_table_cell($tbl1,$col,2,"", $optlist);
																										//$tbl2 = $pobj->add_table_cell($tbl2,$col,1,"", $optlist1);
																										$tbl3 = $pobj->add_table_cell($tbl3,$col,1,"", $optlist2);
																										
																										
																										if (count($this->data_map[$s_year][intval($mon)][$position]) > 1) {
																												//農曆節氣、特殊名稱轉換
																												$cname=$this->data_map["CHMAP"][$s_year.$mon.sprintf("%02d",$day[1])];
																												//執行國曆轉農曆
																												$ch_date=$this->convertSolarToLunar($s_year, intval($mon),intval($day[1]));
																												//計算處理月份農曆天數
																												$ch_countday=$this->getLunarMonthDays($ch_date[0],$ch_date[4]);
																												//每個農曆1號不顯示初一，改顯示月份大小。ex:八月大、閏九月小
																												if ($ch_date[5]==1) {
																														if ($ch_countday==29)  {
																																$bs="小";
																														} else {
																																$bs="大";
																														}
																														$show_cname=$ch_date[1].$bs;
																													//農曆非1號並且有特殊名稱時則顯示特殊名稱。ex:臘八，大寒
																												} else {
																														$show_cname=$ch_date[2];
																												}
																												if ($cname!='') {
																														$show_cname=iconv('BIG5','UTF-8',$cname);
																												}
																												
																												$tmp=explode("/",$show_cname);
																												if (count($tmp)> 2) {
																														$show_cname=$tmp[2];
																												} elseif (count($tmp)> 1) {
																														$show_cname=$tmp[1];
																												} else {
																														$show_cname=$tmp[0];
																												}
																												
																												
																												//判斷當顯示的農曆名稱大於3個字則利用Margin來做到兩個字換下一行
																												if (mb_strlen($show_cname) > 9) {
																														$margin="margin=2";
																												} else {
																														$margin="";
																												}
																												
																												$$tf3 = $pobj->create_textflow($show_cname, "fontname=BHEI01M fontsize=5 alignment=center encoding=unicode $day_color");
																												$optlist2 = " colwidth=24 $margin rowheight=10 textflow=".$$tf3;
																												
																										
																												$tbl1 = $pobj->add_table_cell($tbl1,($col+7),2,$week_array[$week[0]], $optlist);
																												$tbl2 = $pobj->add_table_cell($tbl2,($col+7),1,$day[1], $optlist1);
																												$tbl3 = $pobj->add_table_cell($tbl3,($col+7),1,"", $optlist2);
																												
																										}
																										
																										$col++;
																										
																										
																										
																								}
																								//$table_optlist = "stroke={{line=frame linewidth=0.3} {line=other linewidth=0.3}} ";
																								$table_optlist = "";
																			
																								$Tsx=(($ini_array[width]-$table_width)*$this->px)/2; //表格起始X
																								$Tsy=($ini_array[height]-$table_sub_y)*$this->px;   //表格起始Y
																								$Tex=$Tsx+$table_width*$this->px;
																								$Tey=$Tsy+$table_height*$this->px;
																								$result = $pobj->fit_table($tbl1, $Tsx, $Tsy-10 , $Tex, $Tey-10, $table_optlist);	
																								$result = $pobj->fit_table($tbl2, $Tsx, $Tsy+10 , $Tex, $Tey+10, $table_optlist);													
																								$result = $pobj->fit_table($tbl3, $Tsx, $Tsy+20 , $Tex, $Tey+20, $table_optlist);			
																								
																								break;
																						//**************************************************************
																						//*  編排方式 B
																						//**************************************************************
																						case "B":
																								$year_optlist="fittextline={position={center top} font=$CALIBRI fontsize=18 fillcolor={ cmyk 1 0.92 0.1 0.47}} colwidth=20 rowheight=37.5 colspan=2 ";
																								$tbl1 = $pobj->add_table_cell($tbl1,1,1," ".$s_year, $year_optlist);
																								$col=2;
																								
																								$m_days=$this->getSolarMonthDays($s_year,intval($mon));
																								
																								$rows=$m_days-15; //判斷到底要開幾行的表格，抓最長
																								foreach ($this->data_map["PRINT"][$s_year][intval($mon)] as $position => $day_ary) {
																										$day=array_keys($day_ary);
																										$week=array_values($day_ary);
																										
																										$chk_status=$this->data_map[$s_year.$mon.$position][STATUS][$day[0]]; //判斷是否假日(非六日)
																										
																										//判斷星期六日及假日顯示紅字
																										if ($week[0]==0 or $week[0]==6) {
																												$day_color="fillcolor={ cmyk 0 0.83 0.17 0}";
																										} else {
																												$day_color="fillcolor={ cmyk 1 0.92 0.1 0.47}";
																										}
																										if ($chk_status==2) {
																												$day_color="fillcolor={ cmyk 1 0.92 0.1 0.47}";
																										} elseif ($chk_status==1) {
																												$day_color="fillcolor={ cmyk 0 0.83 0.17 0}";
																												$day_color="fillcolor={ cmyk 0 0.83 0.17 0}";
																										}
																										
																										$optlist="fittextline={position={center} font=$GOTHIC fontsize=8 $day_color} colwidth=22 rowheight=15.7 ";
																										
																										if ($col > 16) {
																												$tbl1 = $pobj->add_table_cell($tbl1,2,intval($col-15),$day[0], $optlist);
																												//判斷相同position有兩個日期，要額外處理
																												if (count($this->data_map[$s_year][intval($mon)][$position]) > 1) {
																														$tbl1 = $pobj->add_table_cell($tbl1,2,intval($col-15+7),$day[1], $optlist);
																												}
																										} else {
																												$tbl1 = $pobj->add_table_cell($tbl1,1,$col,$day[0], $optlist);
																												if ($rows==16 and $col==16) {
																														$tbl1 = $pobj->add_table_cell($tbl1,1,($col+1),"", $optlist);
																												}
																										}
																										
																										$col++;
																								}
																								
																								$Tsx=($ini_array[width]-$table_width)*$this->px; //表格起始X
																								$Tsy=($ini_array[height]-$table_sub_y)*$this->px;   //表格起始Y
																								$Tex=$Tsx+$table_width*$this->px;
																								$Tey=$Tsy+$table_height*$this->px;
																								
																								//$table_optlist = "stroke={{line=frame linewidth=0.3} {line=other linewidth=0.3}} ";
																								$table_optlist = "";
																								
																								$result = $pobj->fit_table($tbl1, $Tsx, $Tsy , $Tex, $Tey, $table_optlist);	
																								
																								break;		
																						//**************************************************************
																						//*  編排方式 C
																						//**************************************************************
																						case "C":
																								$cell_small="fittextline={position={center} font=$BHEI01M fontsize=9 fillcolor={ cmyk 0.6 0.1 0.2 0}} colwidth=11 rowheight=12 ";
																								$cell_small_holiday="fittextline={position={center} font=$BHEI01M fontsize=9 fillcolor={ cmyk 0 0.5 0.9 0}} colwidth=11 rowheight=12 ";
																										
																								$mon_cell_optlist = "fittextline={position={left center} font=$CHAPA fontsize=10.5 fillcolor={ cmyk 0 0 0 1}} rowheight=12 colspan=7";
																								$tbl2 = $pobj->add_table_cell($tbl2, 1, 1," ".$s_year, $mon_cell_optlist);
																								$tbl2 = $pobj->add_table_cell($tbl2, 1, 2,"日", $cell_small_holiday);
																								$tbl2 = $pobj->add_table_cell($tbl2, 2, 2,"一", $cell_small);
																								$tbl2 = $pobj->add_table_cell($tbl2, 3, 2,"二", $cell_small);
																								$tbl2 = $pobj->add_table_cell($tbl2, 4, 2,"三", $cell_small);
																								$tbl2 = $pobj->add_table_cell($tbl2, 5, 2,"四", $cell_small);
																								$tbl2 = $pobj->add_table_cell($tbl2, 6, 2,"五", $cell_small);
																								$tbl2 = $pobj->add_table_cell($tbl2, 7, 2,"六", $cell_small_holiday);
																								$position=1;
																								$extra_array=array();
																								for ($row=3;$row<=7;$row++) {
																										for ($col=1;$col<8;$col++) {
																												if (!empty($this->data_map[$s_year][intval($mon)][$position])) {
																														$day=$this->data_map[$s_year][intval($mon)][$position][0];
																														$day1=$this->data_map[$s_year][intval($mon)][$position][1];
																														$chk_day   =$this->data_map[$s_year.$mon.$position][WEEK][$day]; //判斷是否假日 (六日)
																														$chk_status=$this->data_map[$s_year.$mon.$position][STATUS][$day]; //判斷是否假日(非六日)
																														
																														
																														if ($chk_day==0 or $chk_day==6) {
																																$font_color="fillcolor={ cmyk 0 0.5 0.9 0}";
																																$red=1;
																														} else {
																																$font_color="fillcolor={ cmyk 0 0 0 1}";
																																$red=0;
																														}
																														if ($chk_status==2) {
																																$font_color="fillcolor={ cmyk 0 0 0 1}";
																																$red=0;
																														} elseif ($chk_status==1) {
																																$font_color="fillcolor={ cmyk 0 0.5 0.9 0}";
																																$red=1;
																														}
																														if (count($this->data_map[$s_year][intval($mon)][$position]) > 1) {
																																$pp=$position % 7;
																																$extra_array[$pp]["show"]=$day1;
																																$extra_array[$pp]["red"]=$red;
																														}
																														$show_txt=$day;
																														$content_optlist = "fittextline={position={center} font=$CHAPA fontsize=9 $font_color} rowheight=24 ";
																										
																														$tbl2 = $pobj->add_table_cell($tbl2, $col, $row,$show_txt, $content_optlist);
																												} else {
																														$tbl2 = $pobj->add_table_cell($tbl2, $col, $row,"", "");
																												}
																												$position++;
																										}
																								}
																								
																								if (!empty($extra_array)) {
																										for($jj=1;$jj<8;$jj++) {
																												if ($extra_array[$jj]["red"]==1) {
																														$content_optlist = "fittextline={position={center} font=$CHAPA fontsize=9 fillcolor={ cmyk 0 0.5 0.9 0}} rowheight=24 ";
																												} else {
																														$content_optlist = "fittextline={position={center} font=$CHAPA fontsize=9 fillcolor={ cmyk 0 0 0 1}} rowheight=24 ";
																												}
																												$tbl2 = $pobj->add_table_cell($tbl2, $jj, 8,$extra_array[$jj]["show"], $content_optlist);
																										}				
																								}
																								
																								//$table_optlist = "stroke={{line=frame linewidth=0.3} {line=other linewidth=0.3}} ";
																								$table_optlist = "";
																								
																								
																								$Tsx=($ini_array[width]-$table_width)*$this->px; //表格起始X
																								$Tsy=($ini_array[height]-$table_sub_y)*$this->px;   //表格起始Y
																								$Tex=$Tsx+$table_width*$this->px;
																								$Tey=$Tsy+$table_height*$this->px;
																								
																								$result = $pobj->fit_table($tbl2, $Tsx, $Tsy, $Tex, $Tey, $table_optlist);	
																							
																								$tbl2=null;	
																								break;
																						//**************************************************************
																						//*  編排方式 D
																						//**************************************************************
																						case "D":
																								$DFT1     =$pobj->load_font("DFT:0", "unicode", "embedding vertical=true");
																								$col=1;
																								foreach ($this->data_map["PRINT"][$s_year][intval($mon)] as $position => $day_ary) {
																										$day=array_keys($day_ary);
																										$week=array_values($day_ary);
																										$cname="";
																									
																										$tf2="S_CDAY".$mon.$position;
																										$chk_status=$this->data_map[$s_year.$mon.$position][STATUS][$day[0]]; //判斷是否假日(非六日)
																										
																										//判斷星期六日及假日顯示紅字
																										if ($week[0]==0 or $week[0]==6) {
																												$week_color="fillcolor={ cmyk 0.1 1 1 0}";
																												$day_color="fillcolor={ cmyk 0.1 1 1 0}";
																										} else {
																												$week_color="fillcolor={ cmyk 0 0 0 1}";
																												$day_color="fillcolor={ cmyk 0 0 0 1}";
																										}
																										if ($chk_status==2) {
																												$day_color="fillcolor={  cmyk 0 0 0 1}";
																										} elseif ($chk_status==1) {
																												$week_color="fillcolor={ cmyk 0.1 1 1 0}";
																												$day_color="fillcolor={ cmyk 0.1 1 1 0}";
																										}
																										
																										
																										//農曆節氣、特殊名稱轉換
																										$cname=$this->data_map["CHMAP"][$s_year.$mon.sprintf("%02d",$day[0])];
																										//執行國曆轉農曆
																										$ch_date=$this->convertSolarToLunar($s_year, intval($mon),intval($day[0]));
																										//計算處理月份農曆天數
																										$ch_countday=$this->getLunarMonthDays($ch_date[0],$ch_date[4]);
																										//每個農曆1號不顯示初一，改顯示月份大小。ex:八月大、閏九月小
																										if ($ch_date[5]==1) {
																												if ($ch_countday==29)  {
																														$bs="小";
																												} else {
																														$bs="大";
																												}
																												$show_cname=$ch_date[1].$bs;
																											//農曆非1號並且有特殊名稱時則顯示特殊名稱。ex:臘八，大寒
																										} else {
																												$show_cname=$ch_date[2];
																										}
																										if ($cname!='') {
																												$show_cname=iconv('BIG5','UTF-8',$cname);
																										}
																										
																										$tmp=explode("/",$show_cname);
																										if (count($tmp)> 2) {
																												$show_cname=$tmp[2];
																										} elseif (count($tmp)> 1) {
																												$show_cname=$tmp[1];
																										} else {
																												$show_cname=$tmp[0];
																										}
																										
																										
																										$optlist="fittextline={position={center} font=$DFT fontsize=8 $week_color} colwidth=14.5 rowheight=13.5 ";
																										$optlist1="fittextline={position={center} font=$BKANT fontsize=10 $day_color} colwidth=14.5 rowheight=17 ";
																										
																										//產生農曆日期的文字
																										$optlist2="fittextline={position={center top} font=$DFT1 vertical=true fontsize=7 $day_color } colwidth=14.5 rowheight=26.5";
																										
																										$tbl1 = $pobj->add_table_cell($tbl1,$col,1,$week_array[$week[0]], $optlist);
																										$tbl2 = $pobj->add_table_cell($tbl1,$col,2,$day[0], $optlist1);
																										$tbl3 = $pobj->add_table_cell($tbl1,$col,3,$show_cname, $optlist2);
																										
																										//判斷相同position有兩個日期就要額外處理
																										if (count($this->data_map[$s_year][intval($mon)][$position]) > 1) {
																												//農曆節氣、特殊名稱轉換
																												$cname=$this->data_map["CHMAP"][$s_year.$mon.sprintf("%02d",$day[1])];
																												//執行國曆轉農曆
																												$ch_date=$this->convertSolarToLunar($s_year, intval($mon),intval($day[1]));
																												//計算處理月份農曆天數
																												$ch_countday=$this->getLunarMonthDays($ch_date[0],$ch_date[4]);
																												//每個農曆1號不顯示初一，改顯示月份大小。ex:八月大、閏九月小
																												
																												if ($ch_date[5]==1) {
																														if ($ch_countday==29)  {
																																$bs="小";
																														} else {
																																$bs="大";
																														}
																														$show_cname=$ch_date[1].$bs;
																													//農曆非1號並且有特殊名稱時則顯示特殊名稱。ex:臘八，大寒
																												} else {
																														$show_cname=$ch_date[2];
																												}
																												if ($cname!='') {
																														$show_cname=iconv('BIG5','UTF-8',$cname);
																												}
																												
																												$tmp=explode("/",$show_cname);
																												if (count($tmp)> 2) {
																														$show_cname=$tmp[2];
																												} elseif (count($tmp)> 1) {
																														$show_cname=$tmp[1];
																												} else {
																														$show_cname=$tmp[0];
																												}
																												
																												$tbl1 = $pobj->add_table_cell($tbl1,($col+7),1,$week_array[$week[0]], $optlist);
																												$tbl2 = $pobj->add_table_cell($tbl1,($col+7),2,$day[1], $optlist1);
																												$tbl3 = $pobj->add_table_cell($tbl1,($col+7),3,$show_cname, $optlist2);
																												
																										
																										}
																										
																										
																										$col++;
																								}
																								//$table_optlist = "stroke={{line=frame linewidth=0.3} {line=other linewidth=0.3}} ";
																								$table_optlist = "";
																			
																								$Tsx=(($ini_array[width]-$table_width)*$this->px)/2+32; //表格起始X
																								$Tsy=($ini_array[height]-$table_sub_y)*$this->px;   //表格起始Y
																								$Tex=$Tsx+$table_width*$this->px;
																								$Tey=$Tsy+$table_height*$this->px;
																								$result = $pobj->fit_table($tbl1, $Tsx, $Tsy-10 , $Tex, $Tey-10, $table_optlist);	
																							
																								
																								break;
																						//**************************************************************
																						//*  編排方式 E
																						//**************************************************************
																						case "E":
																								$col=1;
																								$extra=0;//用來判斷天數，因為position會有重複用count計算會少
																								foreach ($this->data_map["PRINT"][$s_year][intval($mon)] as $position => $day_ary) {
																										$day=array_keys($day_ary);
																										$week=array_values($day_ary);
																										$cname="";
																									
																										$chk_status=$this->data_map[$s_year.$mon.$position][STATUS][$day[0]]; //判斷是否假日(非六日)
																										
																										//判斷星期六日及假日顯示紅字
																										if ($week[0]==0 or $week[0]==6) {
																												$week_color="fillcolor={ cmyk 0.15 1 0.9 0.1}";
																												$day_color="fillcolor={ cmyk 0.15 1 0.9 0.1}";
																										} else {
																												$week_color="fillcolor={ cmyk 0 0 0 1}";
																												$day_color="fillcolor={ cmyk 0 0 0 1}";
																										}
																										if ($chk_status==2) {
																												$day_color="fillcolor={ cmyk 0 0 0 1}";
																										} elseif ($chk_status==1) {
																												$week_color="fillcolor={ cmyk 0.15 1 0.9 0.1}";
																												$day_color="fillcolor={ cmyk 0.15 1 0.9 0.1}";
																										}
																										
																										$optlist="fittextline={position={center bottom} font=$CAN fontsize=8 $week_color} colwidth=16 rowheight=10 ";
																										
																										$optlist1="fittextline={position={center bottom} font=$font_arial fontsize=7.5 $day_color} colwidth=16 rowheight=13 ";
																										
																										
																										if ($col==17) {
																												$tbl1 = $pobj->add_table_cell($tbl1,17,1,"", " colwidth=43 rowheight=10");
																												$tbl1 = $pobj->add_table_cell($tbl1,17,2,"", " colwidth=43 rowheight=13");
																										}
																										
																										if ($col < 17) {
																												$tbl1 = $pobj->add_table_cell($tbl1,$col,1,$week_array_en[$week[0]], $optlist);
																												$tbl1 = $pobj->add_table_cell($tbl1,$col,2,$day[0], $optlist1);
																										} else {
																												$tbl1 = $pobj->add_table_cell($tbl1,($col+1),1,$week_array_en[$week[0]], $optlist);
																												$tbl1 = $pobj->add_table_cell($tbl1,($col+1),2,$day[0], $optlist1);
																												
																												//判斷相同position有兩個日期就要額外處理
																												if (count($this->data_map[$s_year][intval($mon)][$position]) > 1) {
																														$tbl1 = $pobj->add_table_cell($tbl1,($col+1+7),1,$week_array_en[$week[0]], $optlist);
																														$tbl1 = $pobj->add_table_cell($tbl1,($col+1+7),2,$day[1], $optlist1);
																														$extra++;//有兩個位置相同的，需要額外加1
																												}
																										}
																										$col++;
																								}
																								
																								//補滿31格
																								$sub_num=count($this->data_map["PRINT"][$s_year][intval($mon)])+$extra;
																								//echo $sub_num;
																								if ($sub_num < 31) {
																										for ($bo=1;$bo<=intval(31-$sub_num);$bo++) {
																												$tbl1 = $pobj->add_table_cell($tbl1,($col+$bo+$extra),1,"", $optlist);
																												$tbl1 = $pobj->add_table_cell($tbl1,($col+$bo+$extra),2,"", $optlist1);
																										}
																								}
																								
																								//$table_optlist = "stroke={{line=frame linewidth=0.3} {line=other linewidth=0.3}} ";
																								$table_optlist = "";
																			
																								$Tsx=(($ini_array[width]-$table_width)*$this->px)/2+26; //表格起始X
																								$Tsy=($ini_array[height]-$table_sub_y)*$this->px;   //表格起始Y
																								$Tex=$Tsx+$table_width*$this->px;
																								$Tey=$Tsy+$table_height*$this->px;
																								$result = $pobj->fit_table($tbl1, $Tsx, $Tsy-10 , $Tex, $Tey-10, $table_optlist);										
																								break;
																						//**************************************************************
																						//*  直式桌曆 F
																						//**************************************************************
																						case "F":
																								$day_rowheight="17";
																								$cname_rowheight="13.2";
																								
																								$rowspan="13";
																								
																								$cell_small="fittextline={position={center} font=$GOTHICB fontsize=10 fillcolor={ cmyk 0 0 0 1}} colwidth=43 rowheight=$day_rowheight ";
																								$cell_small_holiday="fittextline={position={center} font=$GOTHICB fontsize=10 fillcolor={ cmyk 0.15 1 0.9 0.1}} colwidth=43 rowheight=$day_rowheight ";
																										
																								$mon_cell_optlist = "fittextline={position={left bottom} font=$GOTHICB fontsize=14 fillcolor={ cmyk 0 0 0 1}}  marginbottom=8 colwidth=55 rowspan=13";
																								$tbl2 = $pobj->add_table_cell($tbl2, 1, 1," ".$s_year, $mon_cell_optlist);
																								$tbl2 = $pobj->add_table_cell($tbl2, 2, 1,"SUN", $cell_small_holiday);
																								$tbl2 = $pobj->add_table_cell($tbl2, 3, 1,"MON", $cell_small);
																								$tbl2 = $pobj->add_table_cell($tbl2, 4, 1,"TUE", $cell_small);
																								$tbl2 = $pobj->add_table_cell($tbl2, 5, 1,"WED", $cell_small);
																								$tbl2 = $pobj->add_table_cell($tbl2, 6, 1,"THU", $cell_small);
																								$tbl2 = $pobj->add_table_cell($tbl2, 7, 1,"FRI", $cell_small);
																								$tbl2 = $pobj->add_table_cell($tbl2, 8, 1,"SAT", $cell_small_holiday);
																								$position=1;
																								
																								$already_row="";
																								for ($row=0;$row<6;$row++) {
																										for ($col=2;$col<=8;$col++) {
																												if (!empty($this->data_map[$s_year][intval($mon)][$position])) {
																														$day=$this->data_map[$s_year][intval($mon)][$position][0];
																														$day1=$this->data_map[$s_year][intval($mon)][$position][1];
																														$chk_day   =$this->data_map[$s_year.$mon.$position][WEEK][$day]; //判斷是否假日 (六日)
																														$chk_status=$this->data_map[$s_year.$mon.$position][STATUS][$day]; //判斷是否假日(非六日)
																														
																														if ($chk_day==0 or $chk_day==6) {
																																$font_color="fillcolor={ cmyk 0.15 1 0.9 0.1}";
																																$red=1;
																														} else {
																																$font_color="fillcolor={ cmyk 0 0 0 1}";
																																$red=0;
																														}
																														if ($chk_status==2) {
																																$font_color="fillcolor={ cmyk 0 0 0 1}";
																																$red=0;
																														} elseif ($chk_status==1) {
																																$font_color="fillcolor={ cmyk 0.15 1 0.9 0.1}";
																																$red=1;
																														}
																														
																														//農曆節氣、特殊名稱轉換
																														$cname=$this->data_map["CHMAP"][$s_year.$mon.sprintf("%02d",$day)];
																														//執行國曆轉農曆
																														$ch_date=$this->convertSolarToLunar($s_year, intval($mon),$day);
																														//計算處理月份農曆天數
																														$ch_countday=$this->getLunarMonthDays($ch_date[0],$ch_date[4]);
																														//每個農曆1號不顯示初一，改顯示月份大小。ex:八月大、閏九月小
																														if ($ch_date[5]==1) {
																																if ($ch_countday==29)  {
																																		$bs="小";
																																} else {
																																		$bs="大";
																																}
																																$show_cname=$ch_date[1].$bs;
																															//農曆非1號並且有特殊名稱時則顯示特殊名稱。ex:臘八，大寒
																														} else {
																																$show_cname=$ch_date[2];
																														}
																														if ($cname!='') {
																																$show_cname=iconv('BIG5','UTF-8',$cname);
																														}
																														$show_txt=$day;
																														
																														$cell_optlist1="fittextline={position={center} font=$GOTHICB fontsize=12 $font_color} rowheight=$day_rowheight ";
																														$tbl2 = $pobj->add_table_cell($tbl2, $col, ($row)*2+2,$show_txt, $cell_optlist1);
																														
																														$cell_optlist2 = "fittextline={position={center top} font=$BHEI01M fontsize=6 $font_color} rowheight=$cname_rowheight ";
																														$tbl2 = $pobj->add_table_cell($tbl2, $col, ($row)*2+3,"  ".$show_cname."  ", $cell_optlist2);
																														
																															//判斷如果是1格顯示2天
																														if (count($this->data_map[$s_year][intval($mon)][$position]) > 1) {
																																$show_txt=$day1;
																																//農曆節氣、特殊名稱轉換
																																$cname1=$this->data_map["CHMAP"][$s_year.$mon.sprintf("%02d",$day1)];
																																//執行國曆轉農曆
																																$ch_date1=$this->convertSolarToLunar($s_year, intval($mon),$day1);
																																//計算處理月份農曆天數
																																$ch_countday1=$this->getLunarMonthDays($ch_date1[0],$ch_date1[4]);
																																//每個農曆1號不顯示初一，改顯示月份大小。ex:八月大、閏九月小
																																if ($ch_date1[5]==1) {
																																		if ($ch_countday1==29)  {
																																				$bs="小";
																																		} else {
																																				$bs="大";
																																		}
																																		$show_cname1=$ch_date1[1].$bs;
																																//農曆非1號並且有特殊名稱時則顯示特殊名稱。ex:臘八，大寒
																																} else {
																																		$show_cname1=$ch_date1[2];
																																}																																
																																if ($cname1!='') {
																																		$show_cname1=iconv('BIG5','UTF-8',$cname1);
																																} 
																																$show_cname=$show_cname1;
																																
																																$cell_optlist1="fittextline={position={center} font=$GOTHICB fontsize=12 $font_color} rowheight=$day_rowheight ";
																																$tbl2 = $pobj->add_table_cell($tbl2, $col, ($row+1)*2+2,$show_txt, $cell_optlist1);
																																
																																$cell_optlist2 = "fittextline={position={center top} font=$BHEI01M fontsize=6 $font_color} rowheight=$cname_rowheight ";
																																$tbl2 = $pobj->add_table_cell($tbl2, $col, ($row+1)*2+3,"  ".$show_cname."  ", $cell_optlist2);
																														
																																$already_row=($row+1);
																														}
																												} else {
																														if ($already_row!=$row) {
																															echo (($row)*2+2)."<br>";
																															$tbl2 = $pobj->add_table_cell($tbl2, $col, ($row)*2+2,"","rowheight=$day_rowheight");
																															$tbl2 = $pobj->add_table_cell($tbl2, $col, ($row)*2+3,"","rowheight=$cname_rowheight");
																																
																														}
																														//$tbl2 = $pobj->add_table_cell($tbl2, $col, ($row)*2+2,"","");
																														//$tbl2 = $pobj->add_table_cell($tbl2, $col, ($row)*2+3,"","");
																												}
																												$position++;
																										}
																								}
																								
																								//$table_optlist = "stroke={{line=frame linewidth=0.3} {line=other linewidth=0.3}} ";
																								$table_optlist = "";
																								
																								$Tsx=$ini_array[$mode][SINGLE_TABLE_START_X]*$this->px; //表格起始X
																								$Tsy=$ini_array[$mode][SINGLE_TABLE_START_Y]*$this->px;   //表格起始Y
																								$Tex=$Tsx+$table_width*$this->px;
																								$Tey=$Tsy+$table_height*$this->px;
																							
																							
																								$result = $pobj->fit_table($tbl2, $Tsx, $Tsy, $Tex, $Tey, $table_optlist);	
																							
																								$tbl2=null;	
																								break;
																				}
																				//結束PDF
																				LL_PdfClosePage($pobj, $doc, $page);
																				
																				$tbl1 = null;
																				$tbl2 = null;
																				$tbl3 = null;
																				break;
																		//**************************************************************
																		//*  豪華版
																		//**************************************************************
																		case "D":
																				$table_width  =$ini_array[$mode][DOUBLE_TABLE_WIDTH];
																				$table_height =$ini_array[$mode][DOUBLE_TABLE_HEIGHT];
																				$table_sub_y  =$ini_array[$mode][DOUBLE_TABLE_SUB_Y];
																				
																				
																				foreach ($this->data_map["PRINT"][$s_year][intval($mon)] as $position => $day_ary) {
																						$day=array_keys($day_ary);
																						$week=array_values($day_ary);
																				}
																				
																				$before_mon=intval($mon-1);
																				if ($before_mon < 1) {
																						$before_year=$s_year-1;
																						$before_mon =12;
																				} else {
																						$before_year=$s_year;
																				}
																				
																				$after_mon=intval($mon+1);
																				if ($after_mon > 12) {
																						$after_year=$s_year+1;
																						$after_mon =1;
																				} else {
																						$after_year=$s_year;
																				}
																				//套印資料的檔案位置及檔名
																			
																				$new_pdf =$this->PDF_OUTPUT_PATH.$mode.$kind.$s_year.sprintf("%02d",$mon).strtoupper($type).".pdf";
																			
																				//PDF表頭設定
																				pdf_head($pobj,$new_pdf);
																				$page = LL_NewPage($pobj, $doc, $source);
																				
																				//開起PDF頁面
																				switch (strtoupper($mode)) {
																						case "F":
																								$pobj->begin_page_ext($ini_array[vertical_width]*$this->px, $ini_array[vertical_height]*$this->px, "topdown");		//開啟一個新PDF工作頁
																								LL_FitPdiPage($pobj, $doc,0*$this->px, $ini_array[vertical_height]*$this->px, $ini_array[vertical_width]*$this->px, $ini_array[vertical_height]*$this->px, 1);
																								break;
																						default:
																								$pobj->begin_page_ext($ini_array[width]*$this->px, $ini_array[height]*$this->px, "topdown");		//開啟一個新PDF工作頁
																								LL_FitPdiPage($pobj, $doc,0*$this->px, $ini_array[height]*$this->px, $ini_array[width]*$this->px, $ini_array[height]*$this->px, 1);
																								break;
																				}
																				switch (strtoupper($mode)) {
																						//**************************************************************
																						//*  編排方式 A
																						//**************************************************************
																						case "A":
																								$this->make_small_calendar($pobj,$before_year,$before_mon,391,42,67,60,$mode);
																								$this->make_small_calendar($pobj,$after_year,$after_mon,471,42,67,60,$mode);
																								
																								$cell_optlist3 = "colwidth=76 rowheight=24.8 ";
																								$position=1;
																								for ($row=0;$row<5;$row++) {
																										for ($col=1;$col<8;$col++) {
																												$show_content="show_txt".$s_year.intval($mon).$position;
																												if (!empty($this->data_map["PRINT"][$s_year][intval($mon)][$position])) {
																														$day=$this->data_map[$s_year][intval($mon)][$position][0];
																														$day1=$this->data_map[$s_year][intval($mon)][$position][1];
																														$chk_day   =$this->data_map[$s_year.$mon.$position][WEEK][$day]; //判斷是否假日 (六日)
																														$chk_status=$this->data_map[$s_year.$mon.$position][STATUS][$day]; //判斷是否假日(非六日)
																														
																													
																														if ($chk_day==0 or $chk_day==6) {
																																$font_color=" fillcolor={ rgb 1 0 0}";
																														} else {
																																$font_color=" fillcolor={ rgb 0 0 0}";
																														}
																														if ($chk_status==2) {
																																$font_color=" fillcolor={ rgb 0 0 0}";
																														} elseif ($chk_status==1) {
																																$font_color=" fillcolor={ rgb 1 0 0}";
																														}
																														//農曆節氣、特殊名稱轉換
																														
																														$cname=$this->data_map["CHMAP"][$s_year.$mon.sprintf("%02d",$day)];
																														//執行國曆轉農曆
																														$ch_date=$this->convertSolarToLunar($s_year, intval($mon),$day);
																														
																														//計算處理月份農曆天數
																														$ch_countday=$this->getLunarMonthDays($ch_date[0],$ch_date[4]);
																														//每個農曆1號不顯示初一，改顯示月份大小。ex:八月大、閏九月小
																														if ($ch_date[5]==1) {
																																if ($ch_countday==29)  {
																																		$bs="小";
																																} else {
																																		$bs="大";
																																}
																																$show_cname=$ch_date[1].$bs;
																															//農曆非1號並且有特殊名稱時則顯示特殊名稱。ex:臘八，大寒
																														} else {
																																$show_cname=$ch_date[2];
																														}
																														if ($cname!='') {
																																$show_cname=iconv('BIG5','UTF-8',$cname);
																														}
																														
																														//判斷如果是1格顯示2天
																														if (count($this->data_map[$s_year][intval($mon)][$position]) > 1) {
																																$show_txt=$day.'/'.$day1;
																																//農曆節氣、特殊名稱轉換
																																$cname1=$this->data_map["CHMAP"][$s_year.$mon.sprintf("%02d",$day1)];
																																//執行國曆轉農曆
																																$ch_date1=$this->convertSolarToLunar($s_year, intval($mon),$day1);
																																//計算處理月份農曆天數
																																$ch_countday1=$this->getLunarMonthDays($ch_date1[0],$ch_date1[4]);
																																//每個農曆1號不顯示初一，改顯示月份大小。ex:八月大、閏九月小
																																if ($ch_date1[5]==1) {
																																		if ($ch_countday1==29)  {
																																				$bs="小";
																																		} else {
																																				$bs="大";
																																		}
																																		$show_cname1=$ch_date1[1].$bs;
																																//農曆非1號並且有特殊名稱時則顯示特殊名稱。ex:臘八，大寒
																																} else {
																																		$show_cname1=$ch_date1[2];
																																}																																
																																if ($cname1!='') {
																																		$show_cname1=iconv('BIG5','UTF-8',$cname1);
																																} 
																																$show_cname=$show_cname.'/'.$show_cname1;
																														} else {
																																$show_txt=$day;
																														}
																														
																														
																														
																														$cell_optlist1="fittextline={position={right center} font=$GOTHIC fontsize=14 $font_color} colwidth=73 rowheight=19 ";
																														$tbl1 = $pobj->add_table_cell($tbl1, $col, ($row)*3+1,$show_txt."  ", $cell_optlist1);
																														
																														$cell_optlist2 = "fittextline={position={right bottom} font=$BHEI01M fontsize=6 $font_color} colwidth=76 rowheight=8 ";
																														$tbl1 = $pobj->add_table_cell($tbl1, $col, ($row)*3+2,$show_cname."   ", $cell_optlist2);
																														
																														$tbl1 = $pobj->add_table_cell($tbl1,$col, ($row)*3+3,"", $cell_optlist3);
																													
																												} else {
																														$cell_optlist1=" colwidth=76 rowheight=19 ";
																														$tbl1 = $pobj->add_table_cell($tbl1, $col, ($row)*3+1,"", $cell_optlist1);
																														$cell_optlist2 = " colwidth=76 rowheight=8 ";
																														$tbl1 = $pobj->add_table_cell($tbl1, $col, ($row)*3+2,"", $cell_optlist2);
																														
																														$tbl1 = $pobj->add_table_cell($tbl1,$col, ($row)*3+3,"", $cell_optlist3);
																														
																												}
																												$position++;
																										}
																								}
																								//$table_optlist = "stroke={{line=frame linewidth=0.3} {line=other linewidth=0.3}} ";
																								$table_optlist = "";
																							
																								$Tsx=$ini_array[$mode][DOUBLE_TABLE_START_X]; //表格起始X
																								$Tsy=$ini_array[$mode][DOUBLE_TABLE_START_Y];   //表格起始Y
																								$Tex=$Tsx+$table_width;
																								$Tey=$Tsy+$table_height;
																								$result = $pobj->fit_table($tbl1, $Tsx, $Tsy, $Tex, $Tey, $table_optlist);		
																								
																								break;
																						//**************************************************************
																						//*  編排方式 B
																						//**************************************************************
																						case "B":
																								$this->make_small_calendar($pobj,$before_year,$before_mon,402,51,67,60,$mode);
																								$this->make_small_calendar($pobj,$after_year,$after_mon,480,51,67,60,$mode);
																								
																								$colwidth="50.5";
																								
																								$cell_optlist3 = "colwidth=$colwidth rowheight=20 ";
																								$position=1;
																								for ($row=0;$row<5;$row++) {
																										for ($col=1;$col<8;$col++) {
																												$show_content="show_txt".$s_year.intval($mon).$position;
																												if (!empty($this->data_map["PRINT"][$s_year][intval($mon)][$position])) {
																														$day=$this->data_map[$s_year][intval($mon)][$position][0];
																														$day1=$this->data_map[$s_year][intval($mon)][$position][1];
																														$chk_day   =$this->data_map[$s_year.$mon.$position][WEEK][$day]; //判斷是否假日 (六日)
																														$chk_status=$this->data_map[$s_year.$mon.$position][STATUS][$day]; //判斷是否假日(非六日)
																														
																														if ($chk_day==0 or $chk_day==6) {
																																$font_color=" fillcolor={ cmyk 0 0.83 0.17 0}";
																														} else {
																																$font_color=" fillcolor={ cmyk 1 0.92 0.1 0.47}";
																														}
																														if ($chk_status==2) {
																																$font_color=" fillcolor={ cmyk 1 0.92 0.1 0.47}";
																														} elseif ($chk_status==1) {
																																$font_color=" fillcolor={ cmyk 0 0.83 0.17 0}";
																														}
																														//農曆節氣、特殊名稱轉換
																														$cname=$this->data_map["CHMAP"][$s_year.$mon.sprintf("%02d",$day)];
																														//執行國曆轉農曆
																														$ch_date=$this->convertSolarToLunar($s_year, intval($mon),$day);
																														//計算處理月份農曆天數
																														$ch_countday=$this->getLunarMonthDays($ch_date[0],$ch_date[4]);
																														//每個農曆1號不顯示初一，改顯示月份大小。ex:八月大、閏九月小
																														if ($ch_date[5]==1) {
																																if ($ch_countday==29)  {
																																		$bs="小";
																																} else {
																																		$bs="大";
																																}
																																$show_cname=$ch_date[1].$bs;
																															//農曆非1號並且有特殊名稱時則顯示特殊名稱。ex:臘八，大寒
																														} else {
																																$show_cname=$ch_date[2];
																														}
																														if ($cname!='') {
																																$show_cname=iconv('BIG5','UTF-8',$cname);
																														}
																														
																														//判斷如果是1格顯示2天
																														if (count($this->data_map[$s_year][intval($mon)][$position]) > 1) {
																																$show_txt=$day.'/'.$day1;
																																//農曆節氣、特殊名稱轉換
																																$cname1=$this->data_map["CHMAP"][$s_year.$mon.sprintf("%02d",$day1)];
																																//執行國曆轉農曆
																																$ch_date1=$this->convertSolarToLunar($s_year, intval($mon),$day1);
																																//計算處理月份農曆天數
																																$ch_countday1=$this->getLunarMonthDays($ch_date1[0],$ch_date1[4]);
																																//每個農曆1號不顯示初一，改顯示月份大小。ex:八月大、閏九月小
																																if ($ch_date1[5]==1) {
																																		if ($ch_countday1==29)  {
																																				$bs="小";
																																		} else {
																																				$bs="大";
																																		}
																																		$show_cname1=$ch_date1[1].$bs;
																																//農曆非1號並且有特殊名稱時則顯示特殊名稱。ex:臘八，大寒
																																} else {
																																		$show_cname1=$ch_date1[2];
																																}																																
																																if ($cname1!='') {
																																		$show_cname1=iconv('BIG5','UTF-8',$cname1);
																																} 
																																$show_cname=$show_cname.'/'.$show_cname1;
																														} else {
																																$show_txt=$day;
																														}
																														
																														$cell_optlist1="fittextline={position={right center} font=$GOTHIC fontsize=12 $font_color} colwidth=$colwidth rowheight=17 ";
																														$tbl1 = $pobj->add_table_cell($tbl1, $col, ($row)*3+1,$show_txt."  ", $cell_optlist1);
																														
																														$cell_optlist2 = "fittextline={position={right top} font=$BHEI01M fontsize=5 $font_color} colwidth=$colwidth rowheight=10 ";
																														$tbl1 = $pobj->add_table_cell($tbl1, $col, ($row)*3+2,$show_cname."   ", $cell_optlist2);
																														
																														$tbl1 = $pobj->add_table_cell($tbl1,$col, ($row)*3+3,"", $cell_optlist3);
																													
																												} else {
																														$cell_optlist1=" colwidth=$colwidth rowheight=17 ";
																														$tbl1 = $pobj->add_table_cell($tbl1, $col, ($row)*3+1,"", $cell_optlist1);
																														$cell_optlist2 = " colwidth=$colwidth rowheight=10 ";
																														$tbl1 = $pobj->add_table_cell($tbl1, $col, ($row)*3+2,"", $cell_optlist2);
																														
																														$tbl1 = $pobj->add_table_cell($tbl1,$col, ($row)*3+3,"", $cell_optlist3);
																														
																												}
																												$position++;
																										}
																								}
																								//$table_optlist = "stroke={{line=frame linewidth=0.3} {line=other linewidth=0.3}} ";
																								$table_optlist = "";
																							
																								$Tsx=$ini_array[$mode][DOUBLE_TABLE_START_X]; //表格起始X
																								$Tsy=$ini_array[$mode][DOUBLE_TABLE_START_Y];   //表格起始Y
																								$Tex=$Tsx+$table_width;
																								$Tey=$Tsy+$table_height;
																								$result = $pobj->fit_table($tbl1, $Tsx, $Tsy, $Tex, $Tey, $table_optlist);	
																								
																								
																								break;
																						//**************************************************************
																						//*  編排方式 C
																						//**************************************************************
																						case "C":
																								$this->make_small_calendar($pobj,$before_year,$before_mon,470,230,78,66,$mode);
																								$this->make_small_calendar($pobj,$after_year,$after_mon,470,310,78,66,$mode);
																								
																								$colwidth="61";
																								
																								$cell_optlist3 = "colwidth=$colwidth rowheight=37.5 ";
																								$position=1;
																								for ($row=0;$row<5;$row++) {
																										for ($col=1;$col<8;$col++) {
																												//$show_content="show_txt".$s_year.intval($mon).$position;
																												if (!empty($this->data_map["PRINT"][$s_year][intval($mon)][$position])) {
																														$day=$this->data_map[$s_year][intval($mon)][$position][0];
																														$day1=$this->data_map[$s_year][intval($mon)][$position][1];
																														$chk_day   =$this->data_map[$s_year.$mon.$position][WEEK][$day]; //判斷是否假日 (六日)
																														$chk_status=$this->data_map[$s_year.$mon.$position][STATUS][$day]; //判斷是否假日(非六日)
																														
																														if ($chk_day==0 or $chk_day==6) {
																																$font_color=" fillcolor={ cmyk 0 0.5 0.9 0}";
																														} else {
																																$font_color=" fillcolor={ cmyk 0 0 0 1}";
																														}
																														if ($chk_status==2) {
																																$font_color=" fillcolor={ cmyk 0 0 0 1}";
																														} elseif ($chk_status==1) {
																																$font_color=" fillcolor={ cmyk 0 0.5 0.9 0}";
																														}
																														//農曆節氣、特殊名稱轉換
																														$cname=$this->data_map["CHMAP"][$s_year.$mon.sprintf("%02d",$day)];
																														//執行國曆轉農曆
																														$ch_date=$this->convertSolarToLunar($s_year, intval($mon),$day);
																														//計算處理月份農曆天數
																														$ch_countday=$this->getLunarMonthDays($ch_date[0],$ch_date[4]);
																														//每個農曆1號不顯示初一，改顯示月份大小。ex:八月大、閏九月小
																														if ($ch_date[5]==1) {
																																if ($ch_countday==29)  {
																																		$bs="小";
																																} else {
																																		$bs="大";
																																}
																																$show_cname=$ch_date[1].$bs;
																															//農曆非1號並且有特殊名稱時則顯示特殊名稱。ex:臘八，大寒
																														} else {
																																$show_cname=$ch_date[2];
																														}
																														if ($cname!='') {
																																$show_cname=iconv('BIG5','UTF-8',$cname);
																														}
																														
																														//判斷如果是1格顯示2天
																														if (count($this->data_map[$s_year][intval($mon)][$position]) > 1) {
																																$show_txt=$day.'/'.$day1;
																																//農曆節氣、特殊名稱轉換
																																$cname1=$this->data_map["CHMAP"][$s_year.$mon.sprintf("%02d",$day1)];
																																//執行國曆轉農曆
																																$ch_date1=$this->convertSolarToLunar($s_year, intval($mon),$day1);
																																//計算處理月份農曆天數
																																$ch_countday1=$this->getLunarMonthDays($ch_date1[0],$ch_date1[4]);
																																//每個農曆1號不顯示初一，改顯示月份大小。ex:八月大、閏九月小
																																if ($ch_date1[5]==1) {
																																		if ($ch_countday1==29)  {
																																				$bs="小";
																																		} else {
																																				$bs="大";
																																		}
																																		$show_cname1=$ch_date1[1].$bs;
																																//農曆非1號並且有特殊名稱時則顯示特殊名稱。ex:臘八，大寒
																																} else {
																																		$show_cname1=$ch_date1[2];
																																}																																
																																if ($cname1!='') {
																																		$show_cname1=iconv('BIG5','UTF-8',$cname1);
																																} 
																																$show_cname=$show_cname.'/'.$show_cname1;
																														} else {
																																$show_txt=$day;
																														}
																														
																														
																														
																														$cell_optlist1="fittextline={position={center} font=$CHAPA fontsize=16 $font_color} colwidth=$colwidth rowheight=17 ";
																														$tbl1 = $pobj->add_table_cell($tbl1, $col, ($row)*3+1,$show_txt."  ", $cell_optlist1);
																														
																														$cell_optlist2 = "fittextline={position={center} font=$BHEI01M fontsize=7 $font_color} colwidth=$colwidth rowheight=11 ";
																														$tbl1 = $pobj->add_table_cell($tbl1, $col, ($row)*3+2,$show_cname."  ", $cell_optlist2);
																														
																														$tbl1 = $pobj->add_table_cell($tbl1,$col, ($row)*3+3,"", $cell_optlist3);
																													
																												} else {
																														$cell_optlist1=" colwidth=$colwidth rowheight=17 ";
																														$tbl1 = $pobj->add_table_cell($tbl1, $col, ($row)*3+1,"", $cell_optlist1);
																														$cell_optlist2 = " colwidth=$colwidth rowheight=11 ";
																														$tbl1 = $pobj->add_table_cell($tbl1, $col, ($row)*3+2,"", $cell_optlist2);
																														
																														$tbl1 = $pobj->add_table_cell($tbl1,$col, ($row)*3+3,"", $cell_optlist3);
																														
																												}
																												$position++;
																										}
																								}
																								//$table_optlist = "stroke={{line=frame linewidth=0.3} {line=other linewidth=0.3}} ";
																								$table_optlist = "";
																							
																								$Tsx=$ini_array[$mode][DOUBLE_TABLE_START_X]; //表格起始X
																								
																								$Tsy=$ini_array[$mode][DOUBLE_TABLE_START_Y];   //表格起始Y
																								$Tex=$Tsx+$table_width;
																								$Tey=$Tsy+$table_height;
																								$result = $pobj->fit_table($tbl1, $Tsx, $Tsy, $Tex, $Tey, $table_optlist);	
																								
																								break;
																						//**************************************************************
																						//*  編排方式 D
																						//**************************************************************
																						case "D":
																								$this->make_small_calendar($pobj,$before_year,$before_mon,89,234,85,80,$mode);
																								$this->make_small_calendar($pobj,$after_year,$after_mon,89,323,85,80,$mode);
																								
																								$colwidth="52.2";
																								
																								$cell_optlist3 = "colwidth=$colwidth rowheight=28 ";
																								$position=1;
																								for ($row=0;$row<5;$row++) {
																										for ($col=1;$col<8;$col++) {
																												$show_content="show_txt".$s_year.intval($mon).$position;
																												if (!empty($this->data_map["PRINT"][$s_year][intval($mon)][$position])) {
																														$day=$this->data_map[$s_year][intval($mon)][$position][0];
																														$day1=$this->data_map[$s_year][intval($mon)][$position][1];
																														$chk_day   =$this->data_map[$s_year.$mon.$position][WEEK][$day]; //判斷是否假日 (六日)
																														$chk_status=$this->data_map[$s_year.$mon.$position][STATUS][$day]; //判斷是否假日(非六日)
																														
																													
																														if ($chk_day==0 or $chk_day==6) {
																																$font_color=" fillcolor={ cmyk 0.1 1 1 0}";
																														} else {
																																$font_color=" fillcolor={ cmyk 0 0 0 1}";
																														}
																														if ($chk_status==2) {
																																$font_color=" fillcolor={ cmyk 0 0 0 1}";
																														} elseif ($chk_status==1) {
																																$font_color=" fillcolor={ cmyk 0.1 1 1 0}";
																														}
																														
																														//農曆節氣、特殊名稱轉換
																														$cname=$this->data_map["CHMAP"][$s_year.$mon.sprintf("%02d",$day)];
																														//執行國曆轉農曆
																														$ch_date=$this->convertSolarToLunar($s_year, intval($mon),$day);
																														//計算處理月份農曆天數
																														$ch_countday=$this->getLunarMonthDays($ch_date[0],$ch_date[4]);
																														//每個農曆1號不顯示初一，改顯示月份大小。ex:八月大、閏九月小
																														if ($ch_date[5]==1) {
																																if ($ch_countday==29)  {
																																		$bs="小";
																																} else {
																																		$bs="大";
																																}
																																$show_cname=$ch_date[1].$bs;
																															//農曆非1號並且有特殊名稱時則顯示特殊名稱。ex:臘八，大寒
																														} else {
																																$show_cname=$ch_date[2];
																														}
																														if ($cname!='') {
																																$show_cname=iconv('BIG5','UTF-8',$cname);
																														}
																														
																														//判斷如果是1格顯示2天
																														if (count($this->data_map[$s_year][intval($mon)][$position]) > 1) {
																																$show_txt=$day.'/'.$day1;
																																//農曆節氣、特殊名稱轉換
																																$cname1=$this->data_map["CHMAP"][$s_year.$mon.sprintf("%02d",$day1)];
																																//執行國曆轉農曆
																																$ch_date1=$this->convertSolarToLunar($s_year, intval($mon),$day1);
																																//計算處理月份農曆天數
																																$ch_countday1=$this->getLunarMonthDays($ch_date1[0],$ch_date1[4]);
																																//每個農曆1號不顯示初一，改顯示月份大小。ex:八月大、閏九月小
																																if ($ch_date1[5]==1) {
																																		if ($ch_countday1==29)  {
																																				$bs="小";
																																		} else {
																																				$bs="大";
																																		}
																																		$show_cname1=$ch_date1[1].$bs;
																																//農曆非1號並且有特殊名稱時則顯示特殊名稱。ex:臘八，大寒
																																} else {
																																		$show_cname1=$ch_date1[2];
																																}																																
																																if ($cname1!='') {
																																		$show_cname1=iconv('BIG5','UTF-8',$cname1);
																																} 
																																$show_cname=$show_cname.'/'.$show_cname1;
																														} else {
																																$show_txt=$day;
																														}
																														
																														
																														
																														$cell_optlist1="fittextline={position={left center} font=$BKANT fontsize=14 $font_color} colwidth=$colwidth rowheight=19 ";
																														$tbl1 = $pobj->add_table_cell($tbl1, $col, ($row)*3+1," ".$show_txt, $cell_optlist1);
																														
																														$cell_optlist2 = "fittextline={position={right center} font=$DFT fontsize=7 $font_color} colwidth=$colwidth rowheight=15.2 ";
																														$tbl1 = $pobj->add_table_cell($tbl1, $col, ($row)*3+3,$show_cname." ", $cell_optlist2);
																														
																														$tbl1 = $pobj->add_table_cell($tbl1,$col, ($row)*3+2,"", $cell_optlist3);
																													
																												} else {
																														$cell_optlist1=" colwidth=$colwidth rowheight=19 ";
																														$tbl1 = $pobj->add_table_cell($tbl1, $col, ($row)*3+1,"", $cell_optlist1);
																														$cell_optlist2 = " colwidth=$colwidth rowheight=15.2 ";
																														$tbl1 = $pobj->add_table_cell($tbl1, $col, ($row)*3+3,"", $cell_optlist2);
																														
																														$tbl1 = $pobj->add_table_cell($tbl1,$col, ($row)*3+2,"", $cell_optlist3);
																														
																												}
																												$position++;
																										}
																								}
																								//$table_optlist = "stroke={{line=frame linewidth=0.3} {line=other linewidth=0.3}} ";
																								$table_optlist = "";
																							
																								$Tsx=$ini_array[$mode][DOUBLE_TABLE_START_X]; //表格起始X
																								$Tsy=$ini_array[$mode][DOUBLE_TABLE_START_Y];   //表格起始Y
																								$Tex=$Tsx+$table_width;
																								$Tey=$Tsy+$table_height;
																								$result = $pobj->fit_table($tbl1, $Tsx, $Tsy, $Tex, $Tey, $table_optlist);		
																								break;
																						//**************************************************************
																						//*  編排方式 E
																						//**************************************************************
																						case "E":
																								$this->make_small_calendar($pobj,$before_year,$before_mon,373,51,68,80,$mode);
																								$this->make_small_calendar($pobj,$after_year,$after_mon,463,51,68,80,$mode);
																								
																								$colwidth="68.7";
																								
																								$cell_optlist3 = "colwidth=$colwidth rowheight=14.8 ";
																								$position=1;
																								for ($row=0;$row<5;$row++) {
																										for ($col=1;$col<8;$col++) {
																												$show_content="show_txt".$s_year.intval($mon).$position;
																												if (!empty($this->data_map["PRINT"][$s_year][intval($mon)][$position])) {
																														$day=$this->data_map[$s_year][intval($mon)][$position][0];
																														$day1=$this->data_map[$s_year][intval($mon)][$position][1];
																														$chk_day   =$this->data_map[$s_year.$mon.$position][WEEK][$day]; //判斷是否假日 (六日)
																														$chk_status=$this->data_map[$s_year.$mon.$position][STATUS][$day]; //判斷是否假日(非六日)
																														
																													
																														if ($chk_day==0 or $chk_day==6) {
																																$font_color=" fillcolor={ cmyk 0.15 1 0.9 0.1}";
																														} else {
																																$font_color=" fillcolor={ cmyk 0 0 0 1}";
																														}
																														if ($chk_status==2) {
																																$font_color=" fillcolor={ cmyk 0 0 0 1}";
																														} elseif ($chk_status==1) {
																																$font_color=" fillcolor={ cmyk 0.15 1 0.9 0.1}";
																														}
																														
																														//農曆節氣、特殊名稱轉換
																														$cname=$this->data_map["CHMAP"][$s_year.$mon.sprintf("%02d",$day)];
																														//執行國曆轉農曆
																														$ch_date=$this->convertSolarToLunar($s_year, intval($mon),$day);
																														//計算處理月份農曆天數
																														$ch_countday=$this->getLunarMonthDays($ch_date[0],$ch_date[4]);
																														//每個農曆1號不顯示初一，改顯示月份大小。ex:八月大、閏九月小
																														if ($ch_date[5]==1) {
																																if ($ch_countday==29)  {
																																		$bs="小";
																																} else {
																																		$bs="大";
																																}
																																$show_cname=$ch_date[1].$bs;
																															//農曆非1號並且有特殊名稱時則顯示特殊名稱。ex:臘八，大寒
																														} else {
																																$show_cname=$ch_date[2];
																														}
																														if ($cname!='') {
																																$show_cname=iconv('BIG5','UTF-8',$cname);
																														}
																														
																														//判斷如果是1格顯示2天
																														if (count($this->data_map[$s_year][intval($mon)][$position]) > 1) {
																																$show_txt=$day.'/'.$day1;
																																//農曆節氣、特殊名稱轉換
																																$cname1=$this->data_map["CHMAP"][$s_year.$mon.sprintf("%02d",$day1)];
																																//執行國曆轉農曆
																																$ch_date1=$this->convertSolarToLunar($s_year, intval($mon),$day1);
																																//計算處理月份農曆天數
																																$ch_countday1=$this->getLunarMonthDays($ch_date1[0],$ch_date1[4]);
																																//每個農曆1號不顯示初一，改顯示月份大小。ex:八月大、閏九月小
																																if ($ch_date1[5]==1) {
																																		if ($ch_countday1==29)  {
																																				$bs="小";
																																		} else {
																																				$bs="大";
																																		}
																																		$show_cname1=$ch_date1[1].$bs;
																																//農曆非1號並且有特殊名稱時則顯示特殊名稱。ex:臘八，大寒
																																} else {
																																		$show_cname1=$ch_date1[2];
																																}																																
																																if ($cname1!='') {
																																		$show_cname1=iconv('BIG5','UTF-8',$cname1);
																																} 
																																$show_cname=$show_cname.'/'.$show_cname1;
																														} else {
																																$show_txt=$day;
																														}
																														
																														
																														
																														$cell_optlist1="fittextline={position={left center} font=$font_arial fontsize=19 $font_color} colwidth=$colwidth rowheight=20 ";
																														$tbl1 = $pobj->add_table_cell($tbl1, $col, ($row)*3+1," ".$show_txt, $cell_optlist1);
																														
																														$cell_optlist2 = "fittextline={position={left top} font=$DFT fontsize=6 $font_color} colwidth=$colwidth rowheight=14.2 ";
																														$tbl1 = $pobj->add_table_cell($tbl1, $col, ($row)*3+2,"  ".$show_cname, $cell_optlist2);
																														
																														$tbl1 = $pobj->add_table_cell($tbl1,$col, ($row)*3+3,"", $cell_optlist3);
																													
																												} else {
																														$cell_optlist1=" colwidth=$colwidth rowheight=19 ";
																														$tbl1 = $pobj->add_table_cell($tbl1, $col, ($row)*3+1,"", $cell_optlist1);
																														$cell_optlist2 = " colwidth=$colwidth rowheight=15.2 ";
																														$tbl1 = $pobj->add_table_cell($tbl1, $col, ($row)*3+2,"", $cell_optlist2);
																														
																														$tbl1 = $pobj->add_table_cell($tbl1,$col, ($row)*3+3,"", $cell_optlist3);
																														
																												}
																												$position++;
																										}
																								}
																								//$table_optlist = "stroke={{line=frame linewidth=0.3} {line=other linewidth=0.3}} ";
																								$table_optlist = "";
																							
																								$Tsx=$ini_array[$mode][DOUBLE_TABLE_START_X]; //表格起始X
																								$Tsy=$ini_array[$mode][DOUBLE_TABLE_START_Y];   //表格起始Y
																								$Tex=$Tsx+$table_width;
																								$Tey=$Tsy+$table_height;
																								$result = $pobj->fit_table($tbl1, $Tsx, $Tsy, $Tex, $Tey, $table_optlist);	
																								
																								break;
																						//**************************************************************
																						//*  直式桌曆 F
																						//**************************************************************
																						case "F":
																								$colwidth="60";
																								
																								$cell_optlist3 = "colwidth=$colwidth rowheight=33.6 ";
																								$position=1;
																								$already_row="";
																								for ($row=0;$row<6;$row++) {
																										for ($col=1;$col<8;$col++) {
																												//$show_content="show_txt".$s_year.intval($mon).$position;
																												if (!empty($this->data_map["PRINT"][$s_year][intval($mon)][$position])) {
																														$day=$this->data_map[$s_year][intval($mon)][$position][0];
																														$day1=$this->data_map[$s_year][intval($mon)][$position][1];
																														$chk_day   =$this->data_map[$s_year.$mon.$position][WEEK][$day]; //判斷是否假日 (六日)
																														$chk_status=$this->data_map[$s_year.$mon.$position][STATUS][$day]; //判斷是否假日(非六日)
																														
																													
																														if ($chk_day==0 or $chk_day==6) {
																																$font_color=" fillcolor={ cmyk 0.15 1 0.9 0.1}";
																														} else {
																																$font_color=" fillcolor={ cmyk 0 0 0 1}";
																														}
																														if ($chk_status==2) {
																																$font_color=" fillcolor={ cmyk 0 0 0 1}";
																														} elseif ($chk_status==1) {
																																$font_color=" fillcolor={ cmyk 0.15 1 0.9 0.1}";
																														}
																														
																														//農曆節氣、特殊名稱轉換
																														$cname=$this->data_map["CHMAP"][$s_year.$mon.sprintf("%02d",$day)];
																														//執行國曆轉農曆
																														$ch_date=$this->convertSolarToLunar($s_year, intval($mon),$day);
																														//計算處理月份農曆天數
																														$ch_countday=$this->getLunarMonthDays($ch_date[0],$ch_date[4]);
																														//每個農曆1號不顯示初一，改顯示月份大小。ex:八月大、閏九月小
																														if ($ch_date[5]==1) {
																																if ($ch_countday==29)  {
																																		$bs="小";
																																} else {
																																		$bs="大";
																																}
																																$show_cname=$ch_date[1].$bs;
																															//農曆非1號並且有特殊名稱時則顯示特殊名稱。ex:臘八，大寒
																														} else {
																																$show_cname=$ch_date[2];
																														}
																														if ($cname!='') {
																																$show_cname=iconv('BIG5','UTF-8',$cname);
																														}
																														$show_txt=$day;
																													
																														$cell_optlist1="fittextline={position={right center} font=$GOTHICB fontsize=14 $font_color} colwidth=$colwidth rowheight=25 ";
																														$tbl1 = $pobj->add_table_cell($tbl1, $col, ($row)*3+1," ".$show_txt." ", $cell_optlist1);
																														
																														$cell_optlist2 = "fittextline={position={right top} font=$BHEI01M fontsize=6 $font_color} colwidth=$colwidth rowheight=12.2 ";
																														$tbl1 = $pobj->add_table_cell($tbl1, $col, ($row)*3+2," ".$show_cname."  ", $cell_optlist2);
																														
																														$tbl1 = $pobj->add_table_cell($tbl1,$col, ($row)*3+3,"", $cell_optlist3);
																														
																														
																														//判斷如果是1格顯示2天
																														if (count($this->data_map[$s_year][intval($mon)][$position]) > 1) {
																																$show_txt=$day1;
																																//農曆節氣、特殊名稱轉換
																																$cname1=$this->data_map["CHMAP"][$s_year.$mon.sprintf("%02d",$day1)];
																																//執行國曆轉農曆
																																$ch_date1=$this->convertSolarToLunar($s_year, intval($mon),$day1);
																																//計算處理月份農曆天數
																																$ch_countday1=$this->getLunarMonthDays($ch_date1[0],$ch_date1[4]);
																																//每個農曆1號不顯示初一，改顯示月份大小。ex:八月大、閏九月小
																																if ($ch_date1[5]==1) {
																																		if ($ch_countday1==29)  {
																																				$bs="小";
																																		} else {
																																				$bs="大";
																																		}
																																		$show_cname1=$ch_date1[1].$bs;
																																//農曆非1號並且有特殊名稱時則顯示特殊名稱。ex:臘八，大寒
																																} else {
																																		$show_cname1=$ch_date1[2];
																																}																																
																																if ($cname1!='') {
																																		$show_cname1=iconv('BIG5','UTF-8',$cname1);
																																} 
																																$show_cname=$show_cname1;
																																
																																$cell_optlist1="fittextline={position={right center} font=$GOTHICB fontsize=14 $font_color} colwidth=$colwidth rowheight=25 ";
																																
																																$tbl1 = $pobj->add_table_cell($tbl1, $col, ($row+1)*3+1," ".$show_txt." ", $cell_optlist1);
																																
																																$cell_optlist2 = "fittextline={position={right top} font=$BHEI01M fontsize=6 $font_color} colwidth=$colwidth rowheight=12.2 ";
																																$tbl1 = $pobj->add_table_cell($tbl1, $col, ($row+1)*3+2," ".$show_cname."  ", $cell_optlist2);
																																
																																$tbl1 = $pobj->add_table_cell($tbl1,$col, ($row+1)*3+3,"", $cell_optlist3);
																																$already_row=($row+1);
																														}
																													
																												} else {
																														if ($already_row!=$row) {
																																$cell_optlist1=" colwidth=$colwidth rowheight=25 ";
																																$tbl1 = $pobj->add_table_cell($tbl1, $col, ($row)*3+1,"", $cell_optlist1);
																																$cell_optlist2 = " colwidth=$colwidth rowheight=12.2 ";
																																$tbl1 = $pobj->add_table_cell($tbl1, $col, ($row)*3+2,"", $cell_optlist2);
																																
																																$tbl1 = $pobj->add_table_cell($tbl1,$col, ($row)*3+3,"", $cell_optlist3);
																														}
																												}
																												$position++;
																										}
																								}
																							  
																								//最下面的年月顯示
																								$cell_optlist_year = "fittextline={position={center} font=$GOTHICB fontsize=14 fillcolor={ cmyk 0 0 0 1}} colwidth=$colwidth rowheight=37 colspan=7 ";
																								$tbl1 = $pobj->add_table_cell($tbl1, 1, 19,sprintf("%02d",$mon)."/".$s_year, $cell_optlist_year);
																								
																								//$table_optlist = "stroke={{line=frame linewidth=0.3} {line=other linewidth=0.3}} ";
																								$table_optlist = "";
																							
																								$Tsx=$ini_array[$mode][DOUBLE_TABLE_START_X]; //表格起始X
																								$Tsy=$ini_array[$mode][DOUBLE_TABLE_START_Y];   //表格起始Y
																								$Tex=$Tsx+$table_width;
																								$Tey=$Tsy+$table_height;
																								$result = $pobj->fit_table($tbl1, $Tsx, $Tsy, $Tex, $Tey, $table_optlist);	
																								
																								//$imgsrc="d:\\www\\transfer\\desk\\02.png";
																								//$image = $pobj->load_image('auto', $imgsrc, "passthrough=true");
																								//$pobj->fit_image( $image, 0, $ini_array[vertical_height]*$this->px, "boxsize={"."$make_width $make_height"."} fitmethod=entire");
																								//$pobj->fit_image( $image, 0, $ini_array[vertical_height]*$this->px, "boxsize={".(152*$this->px)." ".(214*$this->px)."} fitmethod=meet");
																								//$pobj->close_image( $image);
																								
																								break;
																								
																				}
																				$tbl1=null;
																				$tbl2=null;
																			
																				//結束PDF
																				LL_PdfClosePage($pobj, $doc, $page);
																				
																				break;
																}
                                //判斷製作掛勾掛曆背景時簡約版的背版不處理，避免產生的背版JPG檔案互蓋，造成掛勾掛曆的背景便成簡約版的
                                if (strtoupper($type)=='S' and strtoupper($_type)=='B') {
                                    continue;
                                }
																if (strtoupper($_type)=='B') {
																		//$pre_pic =$this->PRE_OUTPUT_PATH.$mode.$kind.$s_year.sprintf("%02d",$mon).strtoupper($type)."M.jpg";
																		//$thu_pic =$this->THU_OUTPUT_PATH.$mode.$kind.$s_year.sprintf("%02d",$mon).strtoupper($type)."M.jpg";
																		
																		//$resize_output=$this->PDF_OUTPUT_PATH.$mode.$kind.$s_year.sprintf("%02d",$mon).strtoupper($type)."M.pdf";
																		$pre_pic =$this->PRE_OUTPUT_PATH.$mode.$kind.$s_year.sprintf("%02d",$mon)."S.jpg";
																		$thu_pic =$this->THU_OUTPUT_PATH.$mode.$kind.$s_year.sprintf("%02d",$mon)."S.jpg";
																		
																		$resize_output=$this->PDF_OUTPUT_PATH.$mode.$kind.$s_year.sprintf("%02d",$mon)."K.pdf";
																		
																		//PDF表頭設定
																		pdf_head($pobj,$resize_output);
																		$page = LL_NewPage($pobj, $doc, $new_pdf);
																			
																		//開起PDF頁面
																		$pobj->begin_page_ext($ini_array[b_width]*$this->px, $ini_array[b_height]*$this->px, "topdown");		//開啟一個新PDF工作頁
																		LL_FitPdiPage($pobj, $doc,0*$this->px, $ini_array[b_height]*$this->px, $ini_array[fit_width]*$this->px, $ini_array[fit_height]*$this->px, 1);
																		LL_PdfClosePage($pobj, $doc, $page);
																		if (file_exists($resize_output)) {	
																				$cmd=" convert -colorspace sRGB -quality 97 -density 300 -strip  ".$resize_output." -resize 710x ".$pre_pic ;
																				//$this->PDF2JPG($new_pdf,$pre_pic);
																				exec($cmd);
																				$cmd="convert ".$pre_pic." -resize 170x239 ".$thu_pic ;
																				exec($cmd);
																				//echo $cmd;
																				@unlink($new_pdf);
																				@unlink($resize_output);
																		}
																} else {
																		$pre_pic =$this->PRE_OUTPUT_PATH.$mode.$kind.$s_year.sprintf("%02d",$mon).strtoupper($type).".jpg";
																		$thu_pic =$this->THU_OUTPUT_PATH.$mode.$kind.$s_year.sprintf("%02d",$mon).strtoupper($type).".jpg";
																		
																		//EB_IMPDF2JPG_A($new_pdf, $pre_pic, 0,710,358);	
																		if (file_exists($new_pdf)) {	
																				$cmd=" convert -colorspace sRGB -quality 97 -density 300 -strip  ".$new_pdf." -resize 710x ".$pre_pic ;
																				//$this->PDF2JPG($new_pdf,$pre_pic);
																				exec($cmd);
																				$cmd="convert ".$pre_pic." -resize 208x157 ".$thu_pic ;
																				exec($cmd);
																				//echo $cmd;
																		}
																}
														}
												}
										}
								}
						}
				}
		}
		
		
		
		function make_year_background($pobj,$s_year) {
				
				$ini_array=$this->ini_set();
				
			
				$GOTHIC = $pobj->load_font("GOTHIC", "unicode", "embedding");
				$GOTHICB = $pobj->load_font("GOTHICB", "unicode", "embedding");
				$CALIBRI =$pobj->load_font("CALIBRI", "unicode", "embedding");
				$CHAPA   =$pobj->load_font("CHAPA", "unicode", "embedding");
				$DFT   =$pobj->load_font("DFT:0", "unicode", "embedding");
				$BKANT   =$pobj->load_font("BKANT", "unicode", "embedding");
				$font_arial     =$pobj->load_font("font_arial", "unicode", "embedding");
				
				$week_array=array("1"=>"S","2"=>"M","3"=>"T","4"=>"W","5"=>"T","6"=>"F","0"=>"S");
				
				for ($ii=1;$ii<13;$ii++) {
				//for ($ii=1;$ii<2;$ii++) {
						if (empty($this->data[YEAR])) {
								break;
						}
						foreach ($this->data[YEAR] as $mode => $tmp_ary1) {
								foreach ($tmp_ary1 as $kind => $tmp_ary2) {
										foreach ($tmp_ary2 as $source) {
												
												$new_pdf =$this->PDF_OUTPUT_PATH.$mode.$kind.$s_year.sprintf("%02d",$ii)."Y.pdf";
												$sourcepdf = $source;
												
												pdf_head($pobj,$new_pdf);
												$page = LL_NewPage($pobj, $doc, $sourcepdf);
												
												
												//開起PDF頁面
												switch (strtoupper($mode)) {
														case "F":
																$pobj->begin_page_ext($ini_array[vertical_width]*$this->px, $ini_array[vertical_height]*$this->px, "topdown");		//開啟一個新PDF工作頁
																LL_FitPdiPage($pobj, $doc,0*$this->px, $ini_array[vertical_height]*$this->px, $ini_array[vertical_width]*$this->px, $ini_array[vertical_height]*$this->px, 1);
																break;
														default:
																$pobj->begin_page_ext($ini_array[width]*$this->px, $ini_array[height]*$this->px, "topdown");		//開啟一個新PDF工作頁
																LL_FitPdiPage($pobj, $doc,0*$this->px, $ini_array[height]*$this->px, $ini_array[width]*$this->px, $ini_array[height]*$this->px, 1);
																break;
												}
												
												//$pobj->begin_page_ext($ini_array[width]*$this->px, $ini_array[height]*$this->px, "topdown");		//開啟一個新PDF工作頁
												//LL_FitPdiPage($pobj, $doc,0*$this->px, $ini_array[height]*$this->px, $ini_array[width]*$this->px, $ini_array[height]*$this->px, 1);
												
												switch (strtoupper($mode)) {
														//**************************************************************
														//*  編排方式A
														//**************************************************************
														case "A":
																//判斷年表示要顯示兩年還是一年
																if (intval($ii) > 1) {
																		$show_year=$s_year."~".intval($s_year+1);
																} else {
																		$show_year=$s_year;
																}
																
																$textflow = $pobj->create_textflow($show_year, "fontname=GOTHIC fontsize=14 alignment=center encoding=unicode fillcolor={ rgb 0 0 0} hyphenchar=none textlen=all");
																$pobj->fit_textflow( $textflow, 0, 19*$this->px,$ini_array[width]*$this->px , 25*$this->px, "");
																	 
																
																$cell_optlist = "fittextline={position={center} font=$GOTHICB fontsize=7} colwidth=16 rowheight=10 ";
																$holiday_optlist = "fittextline={position={center} font=$GOTHICB fontsize=7 fillcolor={ rgb 1 0 0}} colwidth=16 rowheight=10 ";
																$mon_cell_optlist = "fittextline={position={right} font=$GOTHIC fontsize=9} rowheight=15 colspan=7";
																$num=0;
																$table_startX=($ini_array[width]-($ini_array[$mode][YEAR_FITW]*4)-$ini_array[$mode][YEAR_X_GAP]*3)/2;
																$table_startY=38;
																$start_mon=$ii;
																$year=$s_year;
																	
																for ($y=0;$y<3;$y++) {
																		for ($x=0;$x<4;$x++) {
																			
																			if ($start_mon > 12) {
																					$year=$s_year+1;
																					$start_mon=1;
																			}
																			
																			$tbl1 = 0;
																			$tbl1 = $pobj->add_table_cell($tbl1, 1, 1,$start_mon."  ", $mon_cell_optlist);
																			$tbl1 = $pobj->add_table_cell($tbl1, 1, 2,"S", $holiday_optlist);
																			$tbl1 = $pobj->add_table_cell($tbl1, 2, 2,"M", $cell_optlist);
																			$tbl1 = $pobj->add_table_cell($tbl1, 3, 2,"T", $cell_optlist);
																			$tbl1 = $pobj->add_table_cell($tbl1, 4, 2,"W", $cell_optlist);
																			$tbl1 = $pobj->add_table_cell($tbl1, 5, 2,"T", $cell_optlist);
																			$tbl1 = $pobj->add_table_cell($tbl1, 6, 2,"F", $cell_optlist);
																			$tbl1 = $pobj->add_table_cell($tbl1, 7, 2,"S", $holiday_optlist);
																
																			$pastX1=$table_startX+($ini_array[$mode][YEAR_FITW]+$ini_array[$mode][YEAR_X_GAP])*$x;
																			$pastX2=$pastX1+$ini_array[$mode][YEAR_FITW];
																			$pastY1=$table_startY+($y*24.5)+$ini_array[$mode][YEAR_Y_GAP]*$y;
																			$pastY2=$pastY1+$ini_array[$mode][YEAR_FITH];
																			
																			
																			$llx=$pastX1*$this->px; $lly=$pastY1*$this->px; $urx=$pastX2*$this->px; $ury=$pastY2*$this->px;
																			$position=1;
																			for ($row=3;$row<=7;$row++) {
																					for ($col=1;$col<8;$col++) {
																							//$show_content="show_txt".$year.$start_mon.$position;
																							if (!empty($this->data_map[$year][$start_mon][$position])) {
																									$day=$this->data_map[$year][$start_mon][$position][0];
																									$day1=$this->data_map[$year][$start_mon][$position][1];
																									$chk_day   =$this->data_map[$year.sprintf("%02d",$start_mon).$position][WEEK][$day]; //判斷是否假日 (六日)
																									$chk_status=$this->data_map[$year.sprintf("%02d",$start_mon).$position][STATUS][$day]; //判斷是否假日(非六日)
																									
																									if ($chk_day==0 or $chk_day==6) {
																											$font_color=" fillcolor={ rgb 1 0 0}";
																											$red=1;
																									} else {
																											$font_color=" fillcolor={ rgb 0 0 0}";
																											$red=0;
																									}
																									if ($chk_status==2) {
																											$font_color=" fillcolor={ rgb 0 0 0}";
																											$red=0;
																									} elseif ($chk_status==1) {
																											$font_color=" fillcolor={ rgb 1 0 0}";
																											$red=1;
																									}

																									if (count($this->data_map[$year][$start_mon][$position]) > 1) {
																											$pp=$position % 7;
																											$extra_array[$pp]["show"]=$day1;
																											$extra_array[$pp]["red"]=$red;
																									}
																									$show_txt=$day;
																									
																									$content_optlist = "fittextline={position={center} font=$GOTHIC fontsize=7 $font_color} rowheight=10.5 ";
																									$tbl1 = $pobj->add_table_cell($tbl1, $col, $row,$show_txt, $content_optlist);
																							} else {
																									$tbl1 = $pobj->add_table_cell($tbl1, $col, $row,"", $cell_optlist);
																							}
																							$position++;
																					}
																			}
																			if (!empty($extra_array)) {
																					for($jj=1;$jj<8;$jj++) {
																							if ($extra_array[$jj]["red"]==1) {
																									$content_optlist = "fittextline={position={center} font=$GOTHIC fontsize=7 fillcolor={ rgb 1 0 0}}  rowheight=10.5 ";
																							} else {
																									$content_optlist = "fittextline={position={center} font=$GOTHIC fontsize=7 fillcolor={ rgb 0 0 0}}  rowheight=10.5 ";
																							}
																							$tbl1 = $pobj->add_table_cell($tbl1, $jj, 8,$extra_array[$jj]["show"], $content_optlist);
																					}				
																			}
																			unset($extra_array);
																			
																			
																			//$table_optlist = "stroke={{line=frame linewidth=0.3} {line=other linewidth=0.3}} ";
																			$table_optlist = "";
																			$result = $pobj->fit_table($tbl1, $llx, $lly, $urx, $ury, $table_optlist);		
																			$start_mon++;
																		}
																}
																$tbl1 = null;
																break;
														//**************************************************************
														//*  編排方式 B
														//**************************************************************
														case 'B':
																if (intval($ii) > 1) {
																		$show_year=$s_year."-".intval($s_year+1);
																} else {
																		$show_year=$s_year;
																}
																$textflow = $pobj->create_textflow($show_year, "fontname=CALIBRI fontsize=24 alignment=center encoding=unicode fillcolor={cmyk 1 0.92 0.1 0.47} hyphenchar=none textlen=all");
																$pobj->fit_textflow( $textflow, 0, 31*$this->px,$ini_array[width]*$this->px , 49*$this->px, "");
																
																
																$holiday_optlist = "fittextline={position={center top} font=$CALIBRI fontsize=9 fillcolor={ cmyk 0 0.83 0.17 0}} colwidth=11.7 rowheight=22 ";
																$cell_optlist = "fittextline={position={center top } font=$CALIBRI fontsize=9 fillcolor={cmyk 1 0.92 0.1 0.47}} colwidth=11.7 rowheight=22 ";
																$mon_optlist  = "fittextline={position={left center} font=$GOTHIC fontsize=9 fillcolor={cmyk 1 0.92 0.1 0.47}} colwidth=55 rowheight=22 ";
																$tbl1 = $pobj->add_table_cell($tbl1, 1, 1,"", $mon_optlist);
																$num=1;
																for ($w_i=2;$w_i<39;$w_i++) {
																		$idx=$num%7;
																		if ($idx==1 or $idx==0) {
																				$tbl1 = $pobj->add_table_cell($tbl1, $w_i, 1,$week_array[$idx], $holiday_optlist);
																		} else {
																				$tbl1 = $pobj->add_table_cell($tbl1, $w_i, 1,$week_array[$idx], $cell_optlist);
																		}
																		$num++;
																}
																
																$start_mon=$ii;
																$year=$s_year;
																
																
																for ($m=2;$m<=13;$m++) {
																		$position=1;
																		
																		if ($start_mon > 12) {
																					$year=$s_year+1;
																					$start_mon=1;
																		}
																		$mm=date("F",mktime(0,0,0,$start_mon,1,$year));
																		
																		$tbl1 = $pobj->add_table_cell($tbl1, 1, $m,$mm, $mon_optlist);
																		for ($w_i=2;$w_i<39;$w_i++) {
																				if (!empty($this->data_map[$year][$start_mon][$position]) or !empty($extra_array[$year][$start_mon][$position])) {
																						
																						if (!empty($extra_array[$year][$start_mon][$position])) {
																								$day=$extra_array[$year][$start_mon][$position]["show"];
																						} else {
																								$day=$this->data_map[$year][$start_mon][$position][0];
																								$day1=$this->data_map[$year][$start_mon][$position][1];
																						}
																					
																						$chk_day   =$this->data_map[$year.sprintf("%02d",$start_mon).$position][WEEK][$day]; //判斷是否假日 (六日)
																						$chk_status=$this->data_map[$year.sprintf("%02d",$start_mon).$position][STATUS][$day]; //判斷是否假日(非六日)
																						
																						if ($chk_day==0 or $chk_day==6) {
																								$font_color=" fillcolor={ cmyk 0 0.83 0.17 0}";
																								$red=1;
																						} else {
																								$font_color=" fillcolor={ cmyk 1 0.92 0.1 0.47}";
																								$red=0;
																						}
																						if ($chk_status==2) {
																								$font_color=" fillcolor={ cmyk 1 0.92 0.1 0.47}";
																								$red=0;
																						} elseif ($chk_status==1) {
																								$font_color=" fillcolor={ cmyk 0 0.83 0.17 0}";
																								$red=1;
																						}
																						
																						
																						if (count($this->data_map[$year][$start_mon][$position]) > 1) {
																								$pp=$position + 7;
																								$extra_array[$year][$start_mon][$pp]["show"]=$day1;
																								$extra_array[$year][$start_mon][$pp]["red"]=$red;
																						}
																						
																						$content_optlist = "fittextline={position={center} font=$CALIBRI fontsize=8 $font_color} rowheight=17 ";
																				
																						$tbl1 = $pobj->add_table_cell($tbl1, $w_i, $m,$day, $content_optlist);
																				} else {
																						$content_optlist = "rowheight=17 ";
																						$tbl1 = $pobj->add_table_cell($tbl1, $w_i, $m,"", $content_optlist);
																				}
																				$position++;
																		}
																		$start_mon++;
																}
																
																//$table_optlist = "stroke={{line=frame linewidth=0.3} {line=other linewidth=0.3}} ";
																$table_optlist = "";
																
																$table_startX=($ini_array[width]-$ini_array[$mode][YEAR_FITW])*$this->px/2+5;
																$table_startY=146;
																$pastX1=$table_startX;
																$pastX2=$pastX1+$ini_array[$mode][YEAR_FITW]*$this->px;
																$pastY1=$table_startY;
																$pastY2=$pastY1+$ini_array[$mode][YEAR_FITH]*$this->px;
																
																$result = $pobj->fit_table($tbl1, $pastX1, $pastY1, $pastX2,$pastY2, $table_optlist);
																
																$tbl1= null;
													
																break;
														//**************************************************************
														//*  編排方式 C
														//**************************************************************
														case 'C':
																//判斷年表示要顯示兩年還是一年
																if (intval($ii) > 1) {
																		$show_year=$s_year."~".intval($s_year+1);
																} else {
																		$show_year=$s_year;
																}
																$textflow = $pobj->create_textflow($show_year, "fontname=CHAPA fontsize=22 alignment=center encoding=unicode fillcolor={ rgb 0 0 0} hyphenchar=none textlen=all");
																$pobj->fit_textflow( $textflow, 11.8*$this->px, 13*$this->px,140 , 27*$this->px, "");
																	 
																
																$cell_optlist = "fittextline={position={center} font=$CHAPA fontsize=7} colwidth=16 rowheight=10 ";
																$holiday_optlist = "fittextline={position={center} font=$CHAPA fontsize=8 fillcolor={ cmyk 0 0.5 0.9 0}} colwidth=16 rowheight=10 ";
																$mon_cell_optlist = "fittextline={position={center top} font=$CHAPA fontsize=12.6 fillcolor={ cmyk 0.6 0.1 0.2 0}} rowheight=15 colspan=7";
																$table_startX=($ini_array[width]-($ini_array[$mode][YEAR_FITW]*3)-$ini_array[$mode][YEAR_X_GAP]*2)-10.5;
																$table_startY=16;
																$start_mon=$ii;
																$year=$s_year;
																	
																for ($y=0;$y<4;$y++) {
																		for ($x=0;$x<3;$x++) {
																			
																			if ($start_mon > 12) {
																					$year=$s_year+1;
																					$start_mon=1;
																			}
																			
																			$mm=date("F",mktime(0,0,0,$start_mon,1,$year));
																			
																			//$tbl1 = 0;
																			$tbl1 = $pobj->add_table_cell($tbl1, 1, 1,$mm, $mon_cell_optlist);
																			$tbl1 = $pobj->add_table_cell($tbl1, 1, 2,"S", $holiday_optlist);
																			$tbl1 = $pobj->add_table_cell($tbl1, 2, 2,"M", $cell_optlist);
																			$tbl1 = $pobj->add_table_cell($tbl1, 3, 2,"T", $cell_optlist);
																			$tbl1 = $pobj->add_table_cell($tbl1, 4, 2,"W", $cell_optlist);
																			$tbl1 = $pobj->add_table_cell($tbl1, 5, 2,"T", $cell_optlist);
																			$tbl1 = $pobj->add_table_cell($tbl1, 6, 2,"F", $cell_optlist);
																			$tbl1 = $pobj->add_table_cell($tbl1, 7, 2,"S", $holiday_optlist);
																
																			$pastX1=$table_startX+($ini_array[$mode][YEAR_FITW]+$ini_array[$mode][YEAR_X_GAP])*$x;
																			$pastX2=$pastX1+$ini_array[$mode][YEAR_FITW];
																			$pastY1=$table_startY+($y*$ini_array[$mode][YEAR_FITH])+$ini_array[$mode][YEAR_Y_GAP]*$y;
																			$pastY2=$pastY1+$ini_array[$mode][YEAR_FITH];
																			
																		
																		
																			$llx=$pastX1*$this->px; $lly=$pastY1*$this->px; $urx=$pastX2*$this->px; $ury=$pastY2*$this->px+20;
																			$position=1;
																			for ($row=3;$row<=7;$row++) {
																					for ($col=1;$col<8;$col++) {
																							//$show_content="show_txt".$year.$start_mon.$position;
																							if (!empty($this->data_map[$year][$start_mon][$position])) {
																									$day=$this->data_map[$year][$start_mon][$position][0];
																									$day1=$this->data_map[$year][$start_mon][$position][1];
																									$chk_day   =$this->data_map[$year.sprintf("%02d",$start_mon).$position][WEEK][$day]; //判斷是否假日 (六日)
																									$chk_status=$this->data_map[$year.sprintf("%02d",$start_mon).$position][STATUS][$day]; //判斷是否假日(非六日)
																									
																									if ($chk_day==0 or $chk_day==6) {
																											$font_color=" fillcolor={ cmyk 0 0.5 0.9 0}";
																											$red=1;
																									} else {
																											$font_color=" fillcolor={ rgb 0 0 0}";
																											$red=0;
																									}
																									if ($chk_status==2) {
																											$font_color=" fillcolor={ rgb 0 0 0}";
																											$red=0;
																									} elseif ($chk_status==1) {
																											$font_color=" fillcolor={ cmyk 0 0.5 0.9 0}";
																											$red=1;
																									}

																									if (count($this->data_map[$year][$start_mon][$position]) > 1) {
																											$pp=$position % 7;
																											$extra_array[$pp]["show"]=$day1;
																											$extra_array[$pp]["red"]=$red;
																									}
																									$show_txt=$day;
																									
																									$content_optlist = "fittextline={position={center} font=$CHAPA fontsize=7 $font_color} rowheight=10.5 ";
																									$tbl1 = $pobj->add_table_cell($tbl1, $col, $row,$show_txt, $content_optlist);
																							} else {
																									$tbl1 = $pobj->add_table_cell($tbl1, $col, $row,"", $cell_optlist);
																							}
																							$position++;
																					}
																			}
																			if (!empty($extra_array)) {
																					for($jj=1;$jj<8;$jj++) {
																							if ($extra_array[$jj]["red"]==1) {
																									$content_optlist = "fittextline={position={center} font=$CHAPA fontsize=7 fillcolor={ cmyk 0 0.5 0.9 0}}  rowheight=10.5 ";
																							} else {
																									$content_optlist = "fittextline={position={center} font=$CHAPA fontsize=7 fillcolor={ cmyk 1 1 1 1}}  rowheight=10.5 ";
																							}
																							$tbl1 = $pobj->add_table_cell($tbl1, $jj, 8,$extra_array[$jj]["show"], $content_optlist);
																					}				
																			}
																			unset($extra_array);
																			
																			
																			//$table_optlist = "stroke={{line=frame linewidth=0.3} {line=other linewidth=0.3}} ";
																			$table_optlist = "";
																			$result = $pobj->fit_table($tbl1, $llx, $lly, $urx, $ury, $table_optlist);		
																			$start_mon++;
																			$tbl1= null;
																		}
																}
																break;
														//**************************************************************
														//*  編排方式 D
														//**************************************************************
														case 'D':
																if (intval($ii) > 1) {
																		$show_year=$s_year."/".$ii." ～ ".intval($s_year+1)."/".(intval($ii+11)-12);
																} else {
																		$show_year=$s_year."/".$ii." ～ ".intval($s_year)."/".intval($ii+11);
																}
																$textflow = $pobj->create_textflow($show_year, "fontname=BKANT fontsize=13 alignment=center encoding=unicode fillcolor={ rgb 0 0 0} hyphenchar=none textlen=all");
																$pobj->fit_textflow( $textflow, 115, 19*$this->px,250 , 27*$this->px, "");
																	 
																
																$cell_optlist = "fittextline={position={left center} font=$DFT fontsize=8.5} colwidth=13.5 rowheight=10.5 ";
																$holiday_optlist = "fittextline={position={left center} font=$DFT fontsize=8.5 fillcolor={ cmyk 0.1 1 1 0}} colwidth=13.5 rowheight=10.5 ";
																$mon_cell_optlist = "fittextline={position={left center} font=$DFT fontsize=10} rowheight=14 colspan=7";
																$num=0;
																$table_startX=46.5;
																$table_startY=25;
																$start_mon=$ii;
																$year=$s_year;
																	
																for ($y=0;$y<3;$y++) {
																		for ($x=0;$x<4;$x++) {
																			if ($start_mon > 12) {
																					$year=$s_year+1;
																					$start_mon=1;
																			}
																			$tbl1 = 0;
																			$tbl1 = $pobj->add_table_cell($tbl1, 1, 1,$start_mon, $mon_cell_optlist);
																			$tbl1 = $pobj->add_table_cell($tbl1, 1, 2,"日", $holiday_optlist);
																			$tbl1 = $pobj->add_table_cell($tbl1, 2, 2,"一", $cell_optlist);
																			$tbl1 = $pobj->add_table_cell($tbl1, 3, 2,"二", $cell_optlist);
																			$tbl1 = $pobj->add_table_cell($tbl1, 4, 2,"三", $cell_optlist);
																			$tbl1 = $pobj->add_table_cell($tbl1, 5, 2,"四", $cell_optlist);
																			$tbl1 = $pobj->add_table_cell($tbl1, 6, 2,"五", $cell_optlist);
																			$tbl1 = $pobj->add_table_cell($tbl1, 7, 2,"六", $holiday_optlist);
																
																			$pastX1=$table_startX+($ini_array[$mode][YEAR_FITW]+$ini_array[$mode][YEAR_X_GAP])*$x;
																			$pastX2=$pastX1+$ini_array[$mode][YEAR_FITW];
																			$pastY1=$table_startY+($y*$ini_array[$mode][YEAR_FITH])+$ini_array[$mode][YEAR_Y_GAP]*$y;
																			$pastY2=$pastY1+$ini_array[$mode][YEAR_FITH];
																			
																			
																			$llx=$pastX1*$this->px; $lly=$pastY1*$this->px; $urx=$pastX2*$this->px; $ury=$pastY2*$this->px+40;
																			$position=1;
																			for ($row=3;$row<=7;$row++) {
																					for ($col=1;$col<8;$col++) {
																							//$show_content="show_txt".$year.$start_mon.$position;
																							if (!empty($this->data_map[$year][$start_mon][$position])) {
																									$day=$this->data_map[$year][$start_mon][$position][0];
																									$day1=$this->data_map[$year][$start_mon][$position][1];
																									$chk_day   =$this->data_map[$year.sprintf("%02d",$start_mon).$position][WEEK][$day]; //判斷是否假日 (六日)
																									$chk_status=$this->data_map[$year.sprintf("%02d",$start_mon).$position][STATUS][$day]; //判斷是否假日(非六日)
																									
																									if ($chk_day==0 or $chk_day==6) {
																											$font_color=" fillcolor={ cmyk 0.1 1 1 0}";
																											$red=1;
																									} else {
																											$font_color=" fillcolor={ cmyk 0 0 0 1}";
																											$red=0;
																									}
																									if ($chk_status==2) {
																											$font_color=" fillcolor={ cmyk 0 0 0 1}";
																											$red=0;
																									} elseif ($chk_status==1) {
																											$font_color=" fillcolor={ cmyk 0.1 1 1 0}";
																											$red=1;
																									}

																									if (count($this->data_map[$year][$start_mon][$position]) > 1) {
																											$pp=$position % 7;
																											$extra_array[$pp]["show"]=$day1;
																											$extra_array[$pp]["red"]=$red;
																									}
																									$show_txt=$day;
																									
																									$content_optlist = "fittextline={position={center} font=$BKANT fontsize=8.5 $font_color} rowheight=12 ";
																									$tbl1 = $pobj->add_table_cell($tbl1, $col, $row,$show_txt."  ", $content_optlist);
																							} else {
																									$tbl1 = $pobj->add_table_cell($tbl1, $col, $row,"", $cell_optlist);
																							}
																							$position++;
																					}
																			}
																			if (!empty($extra_array)) {
																					for($jj=1;$jj<8;$jj++) {
																							if ($extra_array[$jj]["red"]==1) {
																									$content_optlist = "fittextline={position={center} font=$BKANT fontsize=8.5 fillcolor={ cmyk 0.1 1 1 0}}  rowheight=12 ";
																							} else {
																									$content_optlist = "fittextline={position={center} font=$BKANT fontsize=8.5 fillcolor={ cmyk 0 0 0 1}}  rowheight=12 ";
																							}
																							$tbl1 = $pobj->add_table_cell($tbl1, $jj, 8,$extra_array[$jj]["show"]."  ", $content_optlist);
																					}				
																			}
																			unset($extra_array);
																			//$table_optlist = "stroke={{line=frame linewidth=0.3} {line=other linewidth=0.3}} ";
																			$table_optlist = "";
																			$result = $pobj->fit_table($tbl1, $llx, $lly, $urx, $ury, $table_optlist);		
																			$start_mon++;
																		}
																}
																$tbl1 = null;
																break;
														//**************************************************************
														//*  編排方式 E
														//**************************************************************
														case 'E':
																$start_mon=$ii;
																$year=$s_year;
																
																if (intval($ii) > 1) {
																		$show_year=$s_year."/".$ii." ～ ".intval($s_year+1)."/".(intval($ii+11)-12);
																} else {
																		$show_year=$s_year."/".$ii." ～ ".intval($s_year)."/".intval($ii+11);
																	
																}
																
																$textflow = $pobj->create_textflow($show_year, "fontname=CAN fontsize=21 alignment=center encoding=unicode fillcolor={ cmyk 0 0 0 0.6} hyphenchar=none textlen=all");
																$pobj->fit_textflow( $textflow, 195, 76*$this->px,380 , 86*$this->px, "");
																
																$positon_array=array("1"=>"40.5,47","2"=>"178,47","3"=>"315.5,47","4"=>"453,47",
																							"5"=>"453,144","6"=>"453,240.5","7"=>"453,338","8"=>"315.5,338",
																							"9"=>"178,338","10"=>"40.5,338","11"=>"40.5,240.5","12"=>"40.5,144");
																
																for ($loop=1;$loop<=12;$loop++) {
																		
																		if ($start_mon > 12) {
																				$year=$s_year+1;
																				$start_mon=1;
																		}
																		
																		if (!empty($positon_array[$loop])) {
																				$tmp_ary=explode(",",$positon_array[$loop]);
																				$x=$tmp_ary[0];
																				$y=$tmp_ary[1];
																				$this->make_small_calendar($pobj,$year,$start_mon,$x,$y,78,90,"YEAR");
																		}
																		$start_mon++;
																}			
																break;
														//**************************************************************
														//*  編排方式 F  直式桌曆年表
														//**************************************************************
														case 'F':
																	//判斷年表示要顯示兩年還是一年
																if (intval($ii) > 1) {
																		$show_year=$s_year."~".intval($s_year+1);
																} else {
																		$show_year=$s_year;
																}
																
																$textflow = $pobj->create_textflow($show_year, "fontname=GOTHICB fontsize=15 alignment=center encoding=unicode fillcolor={ rgb 0 0 0} hyphenchar=none textlen=all");
																$pobj->fit_textflow( $textflow, 0, 19*$this->px,$ini_array[vertical_width]*$this->px , 25*$this->px, "");
																	 
																
																$cell_optlist = "fittextline={position={center} font=$GOTHICB fontsize=8} colwidth=18 rowheight=10 ";
																$holiday_optlist = "fittextline={position={center} font=$GOTHICB fontsize=8 fillcolor={ cmyk 0.15 1 0.9 0.1}} colwidth=18 rowheight=10 ";
																$mon_cell_optlist = "fittextline={position={right center} font=$GOTHICB fontsize=10 fillcolor={ cmyk 0 0 0 1}} rowheight=19 colspan=7";
																$num=0;
																$table_startX=($ini_array[vertical_width]-($ini_array[$mode][YEAR_FITW]*3)-$ini_array[$mode][YEAR_X_GAP]*2)/2;
																$table_startY=40;
																$start_mon=$ii;
																$year=$s_year;
																	
																for ($y=0;$y<4;$y++) {
																		for ($x=0;$x<3;$x++) {
																			
																			if ($start_mon > 12) {
																					$year=$s_year+1;
																					$start_mon=1;
																			}
																			
																			$tbl1 = 0;
																			$tbl1 = $pobj->add_table_cell($tbl1, 1, 1,$start_mon." ", $mon_cell_optlist);
																			$tbl1 = $pobj->add_table_cell($tbl1, 1, 2,"S", $holiday_optlist);
																			$tbl1 = $pobj->add_table_cell($tbl1, 2, 2,"M", $cell_optlist);
																			$tbl1 = $pobj->add_table_cell($tbl1, 3, 2,"T", $cell_optlist);
																			$tbl1 = $pobj->add_table_cell($tbl1, 4, 2,"W", $cell_optlist);
																			$tbl1 = $pobj->add_table_cell($tbl1, 5, 2,"T", $cell_optlist);
																			$tbl1 = $pobj->add_table_cell($tbl1, 6, 2,"F", $cell_optlist);
																			$tbl1 = $pobj->add_table_cell($tbl1, 7, 2,"S", $holiday_optlist);
																
																			$pastX1=$table_startX+($ini_array[$mode][YEAR_FITW]+$ini_array[$mode][YEAR_X_GAP])*$x;
																			$pastX2=$pastX1+$ini_array[$mode][YEAR_FITW];
																			$pastY1=$table_startY+($y*24.5)+$ini_array[$mode][YEAR_Y_GAP]*$y;
																			$pastY2=$pastY1+$ini_array[$mode][YEAR_FITH];
																			
																			
																			$llx=$pastX1*$this->px; $lly=$pastY1*$this->px; $urx=$pastX2*$this->px; $ury=$pastY2*$this->px;
																			$position=1;
																			for ($row=3;$row<=7;$row++) {
																					for ($col=1;$col<8;$col++) {
																							//$show_content="show_txt".$year.$start_mon.$position;
																							if (!empty($this->data_map[$year][$start_mon][$position])) {
																									$day=$this->data_map[$year][$start_mon][$position][0];
																									$day1=$this->data_map[$year][$start_mon][$position][1];
																									$chk_day   =$this->data_map[$year.sprintf("%02d",$start_mon).$position][WEEK][$day]; //判斷是否假日 (六日)
																									$chk_status=$this->data_map[$year.sprintf("%02d",$start_mon).$position][STATUS][$day]; //判斷是否假日(非六日)
																									
																									if ($chk_day==0 or $chk_day==6) {
																											$font_color=" fillcolor={ cmyk 0.15 1 0.9 0.1}";
																											$red=1;
																									} else {
																											$font_color=" fillcolor={ rgb 0 0 0}";
																											$red=0;
																									}
																									if ($chk_status==2) {
																											$font_color=" fillcolor={ rgb 0 0 0}";
																											$red=0;
																									} elseif ($chk_status==1) {
																											$font_color=" fillcolor={ cmyk 0.15 1 0.9 0.1}";
																											$red=1;
																									}

																									if (count($this->data_map[$year][$start_mon][$position]) > 1) {
																											$pp=$position % 7;
																											$extra_array[$pp]["show"]=$day1;
																											$extra_array[$pp]["red"]=$red;
																									}
																									$show_txt=$day;
																									
																									$content_optlist = "fittextline={position={center} font=$GOTHIC fontsize=8 $font_color} rowheight=12 ";
																									$tbl1 = $pobj->add_table_cell($tbl1, $col, $row,$show_txt, $content_optlist);
																							} else {
																									$tbl1 = $pobj->add_table_cell($tbl1, $col, $row,"", $cell_optlist);
																							}
																							$position++;
																					}
																			}
																			if (!empty($extra_array)) {
																					for($jj=1;$jj<8;$jj++) {
																							if ($extra_array[$jj]["red"]==1) {
																									$content_optlist = "fittextline={position={center} font=$GOTHIC fontsize=8 fillcolor={ cmyk 0.15 1 0.9 0.1}}  rowheight=10.5 ";
																							} else {
																									$content_optlist = "fittextline={position={center} font=$GOTHIC fontsize=8 fillcolor={ rgb 0 0 0}}  rowheight=10.5 ";
																							}
																							$tbl1 = $pobj->add_table_cell($tbl1, $jj, 8,$extra_array[$jj]["show"], $content_optlist);
																					}				
																			}
																			unset($extra_array);
																			
																			
																			//$table_optlist = "stroke={{line=frame linewidth=0.3} {line=other linewidth=0.3}} ";
																			$table_optlist = "";
																			$result = $pobj->fit_table($tbl1, $llx, $lly, $urx, $ury, $table_optlist);		
																			$start_mon++;
																		}
																}
																$tbl1 = null;
																break;
												}
												
												
												
												LL_PdfClosePage($pobj, $doc, $page);
												//製作年表編輯器用底圖
												//$tmp_pre_pic =$this->PRE_OUTPUT_PATH."tmp_".$mode.$kind.$s_year.sprintf("%02d",$ii).".jpg";
												$pre_pic =$this->PRE_OUTPUT_PATH.$mode.$kind.$s_year.sprintf("%02d",$ii)."Y.jpg";
												$thu_pic =$this->THU_OUTPUT_PATH.$mode.$kind.$s_year.sprintf("%02d",$ii)."Y.jpg";
												//EB_IMPDF2JPG_A($new_pdf, $pre_pic, 0,710,358);	
												if (file_exists($new_pdf)) {	
														$cmd=" convert -colorspace sRGB -quality 97 -density 300 -strip  ".$new_pdf." -resize 710x ".$pre_pic ;
														//$this->PDF2JPG($new_pdf,$pre_pic);
														exec($cmd);
														$cmd="convert ".$pre_pic." -resize 208x157 ".$thu_pic ;
														exec($cmd);
														
														//echo $cmd;
												}
										}
								}
						}
				}
    }
		function make_small_calendar($obj,$_year,$_mon,$x,$y,$w,$h,$mode) {
				$GOTHIC = $obj->load_font("GOTHIC", "unicode", "embedding");
				$CALIBRI =$obj->load_font("CALIBRI", "unicode", "embedding");
				$CHAPA   =$obj->load_font("CHAPA", "unicode", "embedding");
				$DFT     =$obj->load_font("DFT:0", "unicode", "embedding");
				$BKANT   =$obj->load_font("BKANT", "unicode", "embedding");
				$CAN     =$obj->load_font("CAN", "unicode", "embedding");
				$font_arial     =$obj->load_font("font_arial", "unicode", "embedding");
				$extra_array=array();
				
				
				switch (strtoupper($mode)) {
						case "A":
								$holiday_color=" fillcolor={ rgb 1 0 0}";
								$normal_color=" fillcolor={ rgb 0 0 0}";
								$fontname=$GOTHIC;
								$week_fontname=$GOTHIC;
								$year_fontname=$GOTHIC;
								$colwidth="9.5";
								$rowheight="6";
								$year_height="11";
								$year_fontsize="8";
								$week_fontsize="5";
								$fontsize="5";
								break;
						case "B":
								$holiday_color=" fillcolor={ cmyk 0 0.83 0.17 0}";
								$normal_color=" fillcolor={ cmyk 1 0.92 0.1 0.47}";
								$fontname=$CALIBRI;
								$week_fontname=$CALIBRI;
								$year_fontname=$CALIBRI;
								$colwidth="9.5";
								$rowheight="6";
								$year_height="11";
								$year_fontsize="8";
								$week_fontsize="5";
								$fontsize="5";
								break;
						case "C":
								$holiday_color=" fillcolor={ cmyk 0 0 0 1}";
								$normal_color=" fillcolor={ cmyk 0 0 0 1}";
								$fontname=$CHAPA;
								$week_fontname=$CHAPA;
								$year_fontname=$CHAPA;
								$colwidth="11";
								$rowheight="7.7";
								$year_height="11";
								$year_fontsize="8";
								$week_fontsize="6";
								$fontsize="6";
								break;
						case "D":
								$holiday_color=" fillcolor={ cmyk 0 0 0 1}";
								$normal_color=" fillcolor={ cmyk 0 0 0 1}";
								$fontname=$BKANT;
								$week_fontname=$DFT;
								$year_fontname=$BKANT;
								$colwidth="13";
								$rowheight="8";
								$year_height="14";
								$year_fontsize="10";
								$week_fontsize="6";
								$fontsize="6";
								break;
						case "E":
								$holiday_color=" fillcolor={ cmyk 0.15 1 0.9 0.1}";
								$normal_color=" fillcolor={ cmyk 0 0 0 0.6}";
								$fontname=$font_arial;
								$week_fontname=$CAN;
								$year_fontname=$font_arial;
								$colwidth="10";
								$rowheight="8";
								$year_height="14";
								$year_fontsize="8.5";
								$week_fontsize="7";
								$fontsize="6";
								if (intval($_mon) > 9) {
									$space="  ";
								} else {
									$space="";
								}
								break;
						case "YEAR":
								$holiday_color=" fillcolor={ cmyk 0.15 1 0.9 0.1}";
								$normal_color=" fillcolor={ cmyk 0 0 0 0.6}";
								$fontname=$font_arial;
								$week_fontname=$CAN;
								$year_fontname=$font_arial;
								$colwidth="13";
								$rowheight="10";
								$year_height="14";
								$year_fontsize="16";
								$week_fontsize="7.7";
								$fontsize="6";
								break;
				}
			
				
				$cell_small="fittextline={position={center} font=$week_fontname fontsize=$week_fontsize $normal_color} colwidth=$colwidth rowheight=$rowheight ";
				$cell_small_holiday="fittextline={position={center} font=$week_fontname fontsize=$week_fontsize $holiday_color} colwidth=$colwidth rowheight=$rowheight ";
				
				if (strtoupper($mode)=='E') {
						$mon_cell_optlist = "fittextline={position={center} font=$year_fontname fontsize=$year_fontsize $normal_color} rowheight=$year_height colspan=7";
				} elseif (strtoupper($mode)=='YEAR') {
						$mon_cell_optlist = "fittextline={position={left center} font=$year_fontname fontsize=$year_fontsize $normal_color} colwidth=5 rowheight=$year_height colspan=8";
				} else {
						$mon_cell_optlist = "fittextline={position={left center} font=$year_fontname fontsize=$year_fontsize $normal_color} rowheight=$year_height colspan=7";
				}
				$max_col=8;
				$str_col=1;
				if (strtoupper($mode)=='D') {
						$tbl2 = $obj->add_table_cell($tbl2, 1, 1," ".$_year."/".$_mon, $mon_cell_optlist);
						$tbl2 = $obj->add_table_cell($tbl2, 1, 2,"日", $cell_small_holiday);
						$tbl2 = $obj->add_table_cell($tbl2, 2, 2,"一", $cell_small);
						$tbl2 = $obj->add_table_cell($tbl2, 3, 2,"二", $cell_small);
						$tbl2 = $obj->add_table_cell($tbl2, 4, 2,"三", $cell_small);
						$tbl2 = $obj->add_table_cell($tbl2, 5, 2,"四", $cell_small);
						$tbl2 = $obj->add_table_cell($tbl2, 6, 2,"五", $cell_small);
						$tbl2 = $obj->add_table_cell($tbl2, 7, 2,"六", $cell_small_holiday);
				} elseif (strtoupper($mode)=='YEAR') {
						if (intval($_mon) <10) {
								$space=" ";
						} else {
								$space="";
						}
						$tbl2 = $obj->add_table_cell($tbl2, 1, 1,$space.$_mon, $mon_cell_optlist);
						$tbl2 = $obj->add_table_cell($tbl2, 2, 2,"S", $cell_small_holiday);
						$tbl2 = $obj->add_table_cell($tbl2, 3, 2,"M", $cell_small);
						$tbl2 = $obj->add_table_cell($tbl2, 4, 2,"T", $cell_small);
						$tbl2 = $obj->add_table_cell($tbl2, 5, 2,"W", $cell_small);
						$tbl2 = $obj->add_table_cell($tbl2, 6, 2,"T", $cell_small);
						$tbl2 = $obj->add_table_cell($tbl2, 7, 2,"F", $cell_small);
						$tbl2 = $obj->add_table_cell($tbl2, 8, 2,"S", $cell_small_holiday);
						$max_col=9;
						$str_col=2;
				} else {
						$tbl2 = $obj->add_table_cell($tbl2, 1, 1,$space." ".$_year."/".$_mon, $mon_cell_optlist);
						$tbl2 = $obj->add_table_cell($tbl2, 1, 2,"S", $cell_small_holiday);
						$tbl2 = $obj->add_table_cell($tbl2, 2, 2,"M", $cell_small);
						$tbl2 = $obj->add_table_cell($tbl2, 3, 2,"T", $cell_small);
						$tbl2 = $obj->add_table_cell($tbl2, 4, 2,"W", $cell_small);
						$tbl2 = $obj->add_table_cell($tbl2, 5, 2,"T", $cell_small);
						$tbl2 = $obj->add_table_cell($tbl2, 6, 2,"F", $cell_small);
						$tbl2 = $obj->add_table_cell($tbl2, 7, 2,"S", $cell_small_holiday);
				}
				
				$position=1;
				for ($row=3;$row<=7;$row++) {
						for ($col=$str_col;$col<$max_col;$col++) {
								if (!empty($this->data_map[$_year][$_mon][$position])) {
										$day=$this->data_map[$_year][$_mon][$position][0];
										$day1=$this->data_map[$_year][$_mon][$position][1];
										$chk_day   =$this->data_map[$_year.sprintf("%02d",$_mon).$position][WEEK][$day]; //判斷是否假日 (六日)
										$chk_status=$this->data_map[$_year.sprintf("%02d",$_mon).$position][STATUS][$day]; //判斷是否假日(非六日)
										
										
										if ($chk_day==0 or $chk_day==6) {
												$font_color=$holiday_color;
												$red=1;
										} else {
												$font_color=$normal_color;
												$red=0;
										}
										if ($chk_status==2) {
												$font_color=$normal_color;
												$red=0;
										} elseif ($chk_status==1) {
												$font_color=$holiday_color;
												$red=1;	
										}
										if (count($this->data_map[$_year][$_mon][$position]) > 1) {
												if (strtoupper($mode)=='YEAR') {
														$pp=($position % 7)+1;
												} else {
														$pp=$position % 7;
												}
												$extra_array[$pp]["show"]=$day1;
												$extra_array[$pp]["red"]=$red;
										}
										$show_txt=$day;
										$content_optlist = "fittextline={position={center} font=$fontname fontsize=$fontsize $font_color} rowheight=$rowheight ";
						
										$tbl2 = $obj->add_table_cell($tbl2, $col, $row,$show_txt, $content_optlist);
								} else {
										$tbl2 = $obj->add_table_cell($tbl2, $col, $row,"", "");
								}
								$position++;
						}
				}
				if (!empty($extra_array)) {
						for($jj=$str_col;$jj<$max_col;$jj++) {
								if ($extra_array[$jj]["red"]==1) {
										$content_optlist = "fittextline={position={center} font=$fontname fontsize=$fontsize $holiday_color} rowheight=$rowheight ";
								} else {
										$content_optlist = "fittextline={position={center} font=$fontname fontsize=$fontsize $normal_color} rowheight=$rowheight ";
								}
								$tbl2 = $obj->add_table_cell($tbl2, $jj, 8,$extra_array[$jj]["show"], $content_optlist);
						}				
				}
				//$table_optlist = "stroke={{line=frame linewidth=0.3} {line=other linewidth=0.3}} ";
				$table_optlist = "";
				$x1=$x+$w;
				$y1=$y+$h;
				$result = $obj->fit_table($tbl2, $x, $y, $x1, $y1, $table_optlist);	
				$tbl2=null;
		
		}
		function PDF2JPG($in_pdf,$output_pic) {
				$im = new Imagick();
				$im->readImage($in_pdf);
				$im->setCompression(Imagick::COMPRESSION_JPEG);
				$im->setImageCompressionQuality(0);
				$im->setResolution(600,600);
				$im->setImageFormat('jpeg');
				$profiles=$im->getImageProfiles("icc",true);
				$im->stripImage();
				if (!empty($profiles)) {
						$im->profileImage("icc",$proflies['icc']);
				}
				$im->resizeImage(710,0,imagick::FILTER_LANCZOS,0.9);
				$im->writeImage($output_pic);
				$im->clear();
				$im->destroy();
				
		}
		
		
		
		function ini_set() {
				
				if(file_exists($this->INI_FILE)){
						$ini_array = parse_ini_file($this->INI_FILE, true);
				} else{
						echo  "找不到系統參數檔！ -> ".$this->INI_FILE;
						die;
				}
				/********************************************************************
				 * 設定
				 ********************************************************************/
				$INI[width]           = trim($ini_array[system][width]);      // 底板寬-桌曆
				$INI[height]          = trim($ini_array[system][height]);     // 底板高-桌曆
				$INI[b_width]         = trim($ini_array[system][b_width]);    // 底板寬-掛曆
				$INI[b_height]        = trim($ini_array[system][b_height]);   // 底板高-掛曆
				$INI[fit_width]       = trim($ini_array[system][fit_width]);  // 底板素材寬-掛曆
				$INI[fit_height]      = trim($ini_array[system][fit_height]); // 底板素材高-掛曆
				$INI[pre_width]       = trim($ini_array[system][pre_width]);  // 預覽圖寬
				$INI[pre_height]      = trim($ini_array[system][pre_height]); // 預覽圖高
				$INI[vertical_width]       = trim($ini_array[system][vertical_width]);  // 直式桌曆底板寬
				$INI[vertical_height]      = trim($ini_array[system][vertical_height]); // 直式桌曆底板高
				$INI[vertical_prewidth]       = trim($ini_array[system][vertical_prewidth]);  // 直式桌曆預覽圖寬
				$INI[vertical_preheight]      = trim($ini_array[system][vertical_preheight]); // 直式桌曆預覽圖高
				
				$INI[A][YEAR_X_GAP]          = trim($ini_array[A][YEAR_X_GAP]);        //模式A  年表月X軸間距(mm)
				$INI[A][YEAR_Y_GAP]          = trim($ini_array[A][YEAR_Y_GAP]);        //模式A  年表月Y軸間距(mm) 
				$INI[A][YEAR_FITW]           = trim($ini_array[A][YEAR_FITW]);         //模式A  年表每個月表格的寬度(mm)
				$INI[A][YEAR_FITH]           = trim($ini_array[A][YEAR_FITH]);
				$INI[A][SINGLE_TABLE_WIDTH]  = trim($ini_array[A][SINGLE_TABLE_WIDTH]);  //模式A  簡約版日期表格寬(mm)
				$INI[A][SINGLE_TABLE_HEIGHT] = trim($ini_array[A][SINGLE_TABLE_HEIGHT]);  //模式A  簡約版日期表格高(mm)
				$INI[A][SINGLE_TABLE_SUB_Y]  = trim($ini_array[A][SINGLE_TABLE_SUB_Y]);  //模式A  簡約版日期表格由下往上(mm)
				
				$INI[A][DOUBLE_TABLE_WIDTH]  = trim($ini_array[A][DOUBLE_TABLE_WIDTH]);  //模式A   豪華版日期表格寬(mm)
				$INI[A][DOUBLE_TABLE_HEIGHT] = trim($ini_array[A][DOUBLE_TABLE_HEIGHT]);  //模式A  豪華版日期表格高(mm)
				$INI[A][DOUBLE_TABLE_START_X]  = trim($ini_array[A][DOUBLE_TABLE_START_X]);  //模式A  豪華版月份表格起始 X (px)
				$INI[A][DOUBLE_TABLE_START_Y]  = trim($ini_array[A][DOUBLE_TABLE_START_Y]);  //模式A  豪華版月份表格起始 Y (px)
			
		
				
				$INI[B][YEAR_FITW]           = trim($ini_array[B][YEAR_FITW]);         //模式A  年表表格的寬度(mm)
				$INI[B][YEAR_FITH]           = trim($ini_array[B][YEAR_FITH]);         //模式A  年表表格的高度(mm)
				$INI[B][SINGLE_TABLE_WIDTH]  = trim($ini_array[B][SINGLE_TABLE_WIDTH]);  //模式A  簡約版日期表格寬(mm)
				$INI[B][SINGLE_TABLE_HEIGHT] = trim($ini_array[B][SINGLE_TABLE_HEIGHT]);  //模式A  簡約版日期表格高(mm)
				$INI[B][SINGLE_TABLE_SUB_Y]  = trim($ini_array[B][SINGLE_TABLE_SUB_Y]);  //模式A  簡約版日期表格由下往上(mm)
				
				
				$INI[B][DOUBLE_TABLE_WIDTH]  = trim($ini_array[B][DOUBLE_TABLE_WIDTH]);  //模式B   豪華版日期表格寬(mm)
				$INI[B][DOUBLE_TABLE_HEIGHT] = trim($ini_array[B][DOUBLE_TABLE_HEIGHT]);  //模式B  豪華版日期表格高(mm)
				$INI[B][DOUBLE_TABLE_START_X]  = trim($ini_array[B][DOUBLE_TABLE_START_X]);  //模式B  豪華版月份表格起始 X (px)
				$INI[B][DOUBLE_TABLE_START_Y]  = trim($ini_array[B][DOUBLE_TABLE_START_Y]);  //模式B  豪華版月份表格起始 Y (px)
				
				$INI[C][YEAR_X_GAP]          = trim($ini_array[C][YEAR_X_GAP]);        //模式c  年表月X軸間距(mm)
				$INI[C][YEAR_Y_GAP]          = trim($ini_array[C][YEAR_Y_GAP]);        //模式c  年表月Y軸間距(mm) 
				$INI[C][YEAR_FITW]           = trim($ini_array[C][YEAR_FITW]);         //模式c  年表每個月表格的寬度(mm)
				$INI[C][YEAR_FITH]           = trim($ini_array[C][YEAR_FITH]);         //模式c  年表表格的高度(mm)
				$INI[C][SINGLE_TABLE_WIDTH]  = trim($ini_array[C][SINGLE_TABLE_WIDTH]);  //模式c  簡約版日期表格寬(mm)
				$INI[C][SINGLE_TABLE_HEIGHT] = trim($ini_array[C][SINGLE_TABLE_HEIGHT]);  //模式c  簡約版日期表格高(mm)
				$INI[C][SINGLE_TABLE_SUB_Y]  = trim($ini_array[C][SINGLE_TABLE_SUB_Y]);  //模式c  簡約版日期表格由下往上(mm)
				
				$INI[C][DOUBLE_TABLE_WIDTH]  = trim($ini_array[C][DOUBLE_TABLE_WIDTH]);  //模式c   豪華版日期表格寬(mm)
				$INI[C][DOUBLE_TABLE_HEIGHT] = trim($ini_array[C][DOUBLE_TABLE_HEIGHT]);  //模式c  豪華版日期表格高(mm)
				$INI[C][DOUBLE_TABLE_START_X]  = trim($ini_array[C][DOUBLE_TABLE_START_X]);  //模式c  豪華版月份表格起始 X (px)
				$INI[C][DOUBLE_TABLE_START_Y]  = trim($ini_array[C][DOUBLE_TABLE_START_Y]);  //模式c  豪華版月份表格起始 Y (px)
				
				$INI[D][YEAR_X_GAP]          = trim($ini_array[D][YEAR_X_GAP]);        //模式c  年表月X軸間距(mm)
				$INI[D][YEAR_Y_GAP]          = trim($ini_array[D][YEAR_Y_GAP]);        //模式c  年表月Y軸間距(mm) 
				$INI[D][YEAR_FITW]           = trim($ini_array[D][YEAR_FITW]);         //模式c  年表每個月表格的寬度(mm)
				$INI[D][YEAR_FITH]           = trim($ini_array[D][YEAR_FITH]);         //模式c  年表表格的高度(mm)
				$INI[D][SINGLE_TABLE_WIDTH]  = trim($ini_array[D][SINGLE_TABLE_WIDTH]);  //模式c  簡約版日期表格寬(mm)
				$INI[D][SINGLE_TABLE_HEIGHT] = trim($ini_array[D][SINGLE_TABLE_HEIGHT]);  //模式c  簡約版日期表格高(mm)
				$INI[D][SINGLE_TABLE_SUB_Y]  = trim($ini_array[D][SINGLE_TABLE_SUB_Y]);  //模式c  簡約版日期表格由下往上(mm)
				
				$INI[D][DOUBLE_TABLE_WIDTH]  = trim($ini_array[D][DOUBLE_TABLE_WIDTH]);  //模式c   豪華版日期表格寬(mm)
				$INI[D][DOUBLE_TABLE_HEIGHT] = trim($ini_array[D][DOUBLE_TABLE_HEIGHT]);  //模式c  豪華版日期表格高(mm)
				$INI[D][DOUBLE_TABLE_START_X]  = trim($ini_array[D][DOUBLE_TABLE_START_X]);  //模式c  豪華版月份表格起始 X (px)
				$INI[D][DOUBLE_TABLE_START_Y]  = trim($ini_array[D][DOUBLE_TABLE_START_Y]);  //模式c  豪華版月份表格起始 Y (px)
				
				$INI[E][YEAR_X_GAP]          = trim($ini_array[E][YEAR_X_GAP]);        //模式c  年表月X軸間距(mm)
				$INI[E][YEAR_Y_GAP]          = trim($ini_array[E][YEAR_Y_GAP]);        //模式c  年表月Y軸間距(mm) 
				$INI[E][YEAR_FITW]           = trim($ini_array[E][YEAR_FITW]);         //模式c  年表每個月表格的寬度(mm)
				$INI[E][YEAR_FITH]           = trim($ini_array[E][YEAR_FITH]);         //模式c  年表表格的高度(mm)
				$INI[E][SINGLE_TABLE_WIDTH]  = trim($ini_array[E][SINGLE_TABLE_WIDTH]);  //模式c  簡約版日期表格寬(mm)
				$INI[E][SINGLE_TABLE_HEIGHT] = trim($ini_array[E][SINGLE_TABLE_HEIGHT]);  //模式c  簡約版日期表格高(mm)
				$INI[E][SINGLE_TABLE_SUB_Y]  = trim($ini_array[E][SINGLE_TABLE_SUB_Y]);  //模式c  簡約版日期表格由下往上(mm)
				
				$INI[E][DOUBLE_TABLE_WIDTH]  = trim($ini_array[E][DOUBLE_TABLE_WIDTH]);  //模式c   豪華版日期表格寬(mm)
				$INI[E][DOUBLE_TABLE_HEIGHT] = trim($ini_array[E][DOUBLE_TABLE_HEIGHT]);  //模式c  豪華版日期表格高(mm)
				$INI[E][DOUBLE_TABLE_START_X]  = trim($ini_array[E][DOUBLE_TABLE_START_X]);  //模式c  豪華版月份表格起始 X (px)
				$INI[E][DOUBLE_TABLE_START_Y]  = trim($ini_array[E][DOUBLE_TABLE_START_Y]);  //模式c  豪華版月份表格起始 Y (px)
				
				
				$INI[F][YEAR_X_GAP]          = trim($ini_array[F][YEAR_X_GAP]);        //模式c  年表月X軸間距(mm)
				$INI[F][YEAR_Y_GAP]          = trim($ini_array[F][YEAR_Y_GAP]);        //模式c  年表月Y軸間距(mm) 
				$INI[F][YEAR_FITW]           = trim($ini_array[F][YEAR_FITW]);         //模式c  年表每個月表格的寬度(mm)
				$INI[F][YEAR_FITH]           = trim($ini_array[F][YEAR_FITH]);         //模式c  年表表格的高度(mm)
				$INI[F][SINGLE_TABLE_WIDTH]  = trim($ini_array[F][SINGLE_TABLE_WIDTH]);  //模式c  簡約版日期表格寬(mm)
				$INI[F][SINGLE_TABLE_HEIGHT] = trim($ini_array[F][SINGLE_TABLE_HEIGHT]);  //模式c  簡約版日期表格高(mm)
				$INI[F][SINGLE_TABLE_START_X]  = trim($ini_array[F][SINGLE_TABLE_START_X]);  //模式c  簡約版日期表格由下往上(mm)
				$INI[F][SINGLE_TABLE_START_Y]  = trim($ini_array[F][SINGLE_TABLE_START_Y]);  //模式c  簡約版日期表格由下往上(mm)
				
				$INI[F][DOUBLE_TABLE_WIDTH]  = trim($ini_array[F][DOUBLE_TABLE_WIDTH]);  //模式   豪華版日期表格寬(mm)
				$INI[F][DOUBLE_TABLE_HEIGHT] = trim($ini_array[F][DOUBLE_TABLE_HEIGHT]);  //模式c  豪華版日期表格高(mm)
				$INI[F][DOUBLE_TABLE_START_X]  = trim($ini_array[F][DOUBLE_TABLE_START_X]);  //模式c  豪華版月份表格起始 X (px)
				$INI[F][DOUBLE_TABLE_START_Y]  = trim($ini_array[F][DOUBLE_TABLE_START_Y]);  //模式c  豪華版月份表格起始 Y (px)
				
				
				
				
				return $INI;
		}
}
