<?php

function search($text = "温哥华知道吧crab")
{
    $url = 'http://www.xunsearch.com/scws/api.php';
    
    $context = stream_context_create(array(
        'http' => array(
            'method' => 'POST',
            'header' => 'Content-type: application/x-www-form-urlencoded',
            'content' => http_build_query(
                array(
                    'data' => $text,
                    'multi' => '0',
                    'ignore' => 'yes',
                    'respond' => 'json'
                )
            ),
            'timeout' => 60
        )
    ));
    $weight=20;
    $condition=[];
    $condition['where'] = "title LIKE '%".$text."%' or content LIKE '%".$text."%'";
    $condition['order'] = "(CASE WHEN title LIKE '%".$text."%' or content LIKE '%".$text."%' THEN $weight ELSE 0 END)";  //设置全文匹配最大权重
    $words_result = json_decode(file_get_contents($url, FALSE, $context),true);
    $cnt = 0;
    if ($words_result['status'] == "ok"){
        foreach($words_result['words'] as $word_arr){
            $condition['where'] .= " OR title LIKE '%".$word_arr['word']."%' or content LIKE '%".$word_arr['word']."%' ";
            $condition['order'] .= " + (CASE WHEN title LIKE '%".$word_arr['word']."%' or content LIKE '%".$word_arr['word']."%'  THEN ".$word_arr['idf']." ELSE 0 END)"; 
            $condition['keywords'][$cnt++] = $word_arr['word'];
        }
    }

       

     
    return $condition;  
}
function convert_city_id_to_name($city_id){
    $cities = db('cities')->where('id','in',$city_id)->select();
    $cities = array_map(function($city){
        return $city['city_name'];
    },$cities);
    return join(',',$cities);
}
function I($name, $default = '', $filter = null, $datas = null) {
	if (strpos ( $name, '.' )) { // 指定参数来源
		list ( $method, $name ) = explode ( '.', $name, 2 );
	} else { // 默认为自动判断
		$method = 'param';
	}
	switch (strtolower ( $method )) {
		case 'get' :
			$input = & $_GET;
			break;
		case 'post' :
			$input = & $_POST;
			break;
		case 'put' :
			parse_str ( file_get_contents ( 'php://input' ), $input );
			break;
		case 'param' :
			switch ($_SERVER ['REQUEST_METHOD']) {
				case 'POST' :
					$input = $_POST;
					break;
				case 'PUT' :
					parse_str ( file_get_contents ( 'php://input' ), $input );
					break;
				default :
					$input = $_GET;
			}
			break;
		case 'path' :
			$input = array ();
			if (! empty ( $_SERVER ['PATH_INFO'] )) {
				$depr = C ( 'URL_PATHINFO_DEPR' );
				$input = explode ( $depr, trim ( $_SERVER ['PATH_INFO'], $depr ) );
			}
			break;
		case 'request' :
			$input = & $_REQUEST;
			break;
		case 'session' :
			$input = & $_SESSION;
			break;
		case 'cookie' :
			$input = & $_COOKIE;
			break;
		case 'server' :
			$input = & $_SERVER;
			break;
		case 'globals' :
			$input = & $GLOBALS;
			break;
		case 'data' :
			$input = & $datas;
			break;
		default :
			return NULL;
	}
	if ('' == $name) { // 获取全部变量
		$data = $input;
		array_walk_recursive ( $data, 'filter_exp' );
		$filters = isset ( $filter ) ? $filter : '';
		if ($filters) {
			if (is_string ( $filters )) {
				$filters = explode ( ',', $filters );
			}
			foreach ( $filters as $filter ) {
				$data = array_map_recursive ( $filter, $data ); // 参数过滤
			}
		}
	} elseif (isset ( $input [$name] )) { // 取值操作
		$data = $input [$name];
		is_array ( $data ) && array_walk_recursive ( $data, 'filter_exp' );
		$filters = isset ( $filter ) ? $filter : '';
		if ($filters) {
			if (is_string ( $filters )) {
				$filters = explode ( ',', $filters );
			} elseif (is_int ( $filters )) {
				$filters = array (
						$filters 
				);
			}
			
			foreach ( $filters as $filter ) {
				if (function_exists ( $filter )) {
					$data = is_array ( $data ) ? array_map_recursive ( $filter, $data ) : $filter ( $data ); // 参数过滤
				} else {
					$data = filter_var ( $data, is_int ( $filter ) ? $filter : filter_id ( $filter ) );
					if (false === $data) {
						return isset ( $default ) ? $default : NULL;
					}
				}
			}
		}
	} else { // 变量默认值
		$data = isset ( $default ) ? $default : NULL;
	}
	return $data;
}