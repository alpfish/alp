<?php
namespace Alp\Api\Data;

use Alp\Api\Data\Contracts\Data as DataContract;

/*--------------------------------------------------------------------------------------------
 *    简单数据封装 和 基本响应
 *--------------------------------------------------------------------------------------------
 *
 * 1. 全局使用data()帮助函数，使用前须创建
 * 2. 支持方法链(sent()须放最后)
 * 3. 错误数据类型：
 *    (1)后端编写错误
 *    (2)前端调用错误
 *    (3)用户错误信息
 * 4. 响应数据固定格式：
 *    {
 *      "error": '',
 *      "message": '',
 *      "help": '',
 *
 *      "data":[
 *          "errors": [
 * 
 *          ]
 *      ]
 *    }
 * 5. 格式约定
 *    后端正确 && 前端正确则返回"data"区数据， 否则返回相关前后端错误信息
 *    (1)后端错误："data"外，系统响应，格式不固定
 *    (2)前端错误："data"外，后端指定，格式不固定，sendBadFoo()装填，建议有"help"相关帮助文档链接
 *    (3)用户错误："data"内，后端指定，格式严格固定，setErr($k,$v)装填或sendErr($k, $v)装填加响应，$k为错误对象(名词),$v为错误信息
 *    (4)正常数据："data"内，set()装填，
 * 6. 用户错误信息键为"errors"复数，数组
 * 7. "data"区数据为数组形式，所有数据必须有$key,$value键值对
 * 8. 默认响应格式为JSON，请求中携带$format参数指定响应格式，支持JSON和XML
 *
 * */

class Data implements DataContract
{
    // 全局数据仓库
    protected $data;
    
    // 响应数据
    protected $responseData;

    // 默认响应状态码
    protected $status = 200;

    // 响应头
    protected $header;

    public function __construct() {
        
        // 初始化data集合
        $this->data = collect([]);
        
        // 响应头
        $this->header = collect([]);
        
        // 封装响应数据
        $this->responseData = collect(['data' => $this->data]);
    }

    /* *
     * 装填用户响应数据(支持参数为数组或键值对)
     *
     * @param  string $key
     * @param  mixed  $value
     * @return $this
     * */
    public function set($key, $value=null) {
        // 数组
        if (is_array($key)) {
            foreach ($key as $k=>$v)
                $this->data->put(trim($k), $v);
            return $this;
        }
        // 键值对
        if (! is_object($key)) {
                $this->data->put(trim($key), $value);
            return $this;
        }
        // 对象
        $this->data->put('Object Key to Value '.mt_rand(0,99), $key);

        return $this;
    }

    /* *
     * 装填用户响应错误数据(支持参数为数组或键值对)
     *
     * @param  string $key
     * @param  mixed  $value
     * @return $this
     * */
    public function setErr($key, $value=null) {
        // 初始化
        isset($this->data['errors']) ?: $this->data['errors'] = collect([]);
        // 数组
        if (is_array($key)) {
            foreach ($key as $k=>$v)
                $this->data['errors']->put(trim($k), $v);
            return $this;
        }
        // 键值对
        if (! is_object($key)) {
            $this->data['errors']->put(trim($key), $value);
            return $this;
        }
        // 对象
        $this->data['errors']->put('Object Key to Value '.mt_rand(0,99), $key);

        return $this;
    }

    /* *
     * 装填"没有权限"数据
     * */
    public function setNotHasPower() {
        // TODO
    }

    /* *
     * 装填"没有记录"数据
     * */
    public function setNotFoundRecord() {
        // TODO
    }

    /* *
     * 设响应头
     *
     * @param  string $key
     * @param  string $value
     * @return $this
     * */
    public function header($key, $value){

        if (!empty($key) && !empty($value))
            $this->header->put($key, $value);

        return $this;
    }

    /* *
     * 设状态码
     *
     * @param  int $status
     * @return $this
     * */
    public function status($status){

        if (is_int($status) && strlen($status)==3)
            $this->status = $status;

        return $this;
    }

    /* *
     * 发送响应
     *
     * @param  array $content
     * @param  int|string $status
     * @param  array $header
     * @param  string $format
     * @return Response
     * */
    public function send($content=null, $status=null, $header=null, $format='JSON') {
        // 追加数据
        if (isset($content))
            $this->set($content);

        // 状态码
        is_int($status) && strlen($status)==3 ?: $status = $this->status;

        // 响应头
        if (isset($header) && is_array($header)) {
            foreach ($header as $key => $value)
                $this->header($key, $value);
        }

        // XML响应
        empty(request('format')) ?: $format=request('format');
        if (strtoupper($format) == 'XML') {
            $content = $this->xmlEncode($this->responseData->toArray());
            $this->header('Content-Type', 'text/xml');
            return response($content, $status, $this->header->toArray());
        }

        // JSON响应
        return response()->json($this->responseData->toArray(), $status, $this->header->toArray());
    }

    /* *
     * 用户错误信息响应
     *
     * 支持参数为数组或键值对
     * */
    public function sendErr($key, $value=null) {

        $this->setErr($key, $value);

        return $this->send();
    }

    /* *
     * 前端错误方法响应
     * */
    public function sendBadMethod() {
        $this->status = 405;
        $this->responseData = collect([
            'status ' => $this->status,
            'error  ' => 'METHOD_NOT_ALLOWED',
            'message' => '请求的API方法不存在：method='.request('method'),
            'helpDoc' => request()->url(),
        ]);

        return $this->send();
    }

    /* *
     * 前端错误参数响应
     * */
    public function sendBadParam($message) {
        $this->status = 400;
        $this->responseData = collect([
            'status ' => $this->status,
            'error  ' => 'HTTP_BAD_REQUEST',
            'message' => "请求的API参数错误:{$message}",
            'helpDoc' => request()->url() . '?help=' . request('method'),
        ]);

        return $this->send();
    }

    /* *
     * 前端请求头错误响应
     * */
    public function sendBadHeader() {
        // TODO
    }

    /* *
     * 获取数据
     *
     * 不带参数获取响应数据，带参数获取对应$key的$value
     * @param  string $key
     * @return mixed
     * */
    public function get($key = null) {
        if ($key) {
            if (isset($this->data[$key]))
                return $this->data[$key];

            return null;
        }
        return $this->responseData;
    }

    /* *
     * xml编码
     *
     * @param array $data 数据
     * return string
     * */
    private function xmlEncode($data = array()) {
        $xml = "<?xml version='1.0' encoding='UTF-8'?>\n";
        $xml .= "<root>\n";
        $xml .= $this->xmlToEncode($data);
        $xml .= "</root>";
        return $xml;
    }

    private function xmlToEncode($data) {
        $xml = "";
        foreach($data as $key => $value) {
            $attr = "";
            if(is_numeric($key)) {
                $attr = " id='{$key}'";
                $key = "item";
            }
            $xml .= "<{$key}{$attr}>";
            $xml .= is_array($value) ? self::xmlToEncode($value) : $value;
            $xml .= "</{$key}>\n";
        }
        return $xml;
    }
}