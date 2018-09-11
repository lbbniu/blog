<?php
class ZywxappHTTPRequest
{
    /**
     * 通过内置域名向平台发送请求
     *
     * @param array $params the parameters to send
     * @param $path
     * @param string $method the connection method
     * @param array $headers the custom headers to send with the request
     *
     * @return array as follows:
     * array(
     *     'headers'=>an array of response headers, such as "x-powered-by" => "PHP/5.2.1",
     *     'body'=>the response string sent by the server, as you would see it with you web browser
     *     'response'=>an array of HTTP response codes. Typically, you'll want to have array('code'=>200, 'message'=>'OK'),
     *     'cookies'=>an array of cookie information
     * )
     */
    public function api($params, $path, $method = 'POST', $headers = array())
    {
        global $wp_version;
        
        if(!class_exists('WP_Http', false)) {
            include_once ABSPATH . WPINC . '/http.php';
        }
		// 获取平台api地址
        $http_host = ZywxappConfig::getInstance()->api_server;
		//向平台发送请求和回调地址
        $api_url = "http://{$http_host}{$path}";
        ZywxappLog::getInstance()->write('DEBUG', "Contacting zywxapp server: {$api_url}","http_requests.zywxapp_http_request");
		//获取发送header 头部信息
        $headers = array_merge(ZywxappConfig::getInstance()->getCommonApiHeaders(), $headers);
        $headers = array_filter($headers);

        ZywxappLog::getInstance()->write('DEBUG', "Contacting zywxapp server with headers: " . print_r($headers, TRUE) . " and params: " . print_r($params, TRUE),"http_requests.zywxapp_http_request");

        if ( $method == 'POST' && empty($params) ){
            $params = array(
                'ts' => time(),
            );
        }

        $request = new WP_Http();
        $params = array(
            'method'    => $method,
            'body'      => $params,
            'timeout'   => 180,
            'blocking'  => TRUE,
            'sslverify' => FALSE, // Avoid issues with self signed certificate
            'headers'   => $headers,
        );
        $result = $request->request($api_url, $params);
        ZywxappLog::getInstance()->write('info', "The result was: " . print_r($result, TRUE), "http_requests.zywxapp_http_request");
        return $result;
    }

    /**
    * 通过指定的url向平台发送请求
    *
    * @param array $params the parameters to send
    * @param string $url the url to access
    * @param string $method the connection method
    * @param array $headers the custom headers to send with the request
    *
    * @return array as follows:
    * array(
    *   'headers'=>an array of response headers, such as "x-powered-by" => "PHP/5.2.1",
    *   'body'=>the response string sent by the server, as you would see it with you web browser
    *   'response'=>an array of HTTP response codes. Typically, you'll want to have array('code'=>200, 'message'=>'OK'),
    *   'cookies'=>an array of cookie information
    * )
    */
    public function external($params, $url, $method = 'POST', $headers = array())
    {
        global $wp_version;
        if(!class_exists('WP_Http', false)){
            include_once ABSPATH . WPINC . '/http.php';
        }
		ZywxappLog::getInstance()->write('DEBUG', "Contacting zywxapp server: {$url}","http_requests.zywxapp_general_http_request");
        // Now, the HTTP request:
        $body = $params;

        $headers = array_filter($headers);

        ZywxappLog::getInstance()->write('debug', "Contacting zywxapp server with headers: " . print_r($headers, TRUE) . " and params: " . print_r($params, TRUE),"http_requests.zywxapp_general_http_request");

        $request = new WP_Http();
        $result = $request->request($url, array(
            'method'    => $method,
            'timeout'   => 60,
            'blocking'  => TRUE,
            'body'      => $body,
            'sslverify' => FALSE, // Avoid issues with self signed certificate
            'headers'   => $headers)
        );
        ZywxappLog::getInstance()->write('info', "The result was: " . print_r($result, TRUE), "http_requests.zywxapp_general_http_request");
        return $result;
    }
}