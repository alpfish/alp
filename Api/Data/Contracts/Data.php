<?php
namespace Alp\Api\Data\Contracts;

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

interface Data
{
    /* *
     * @return self
     * 在帮助函数中实现：return \Alp\Api\Data\Data::getInstance();
     * */
    public static function getInstance();

    /* *
    * 装填用户响应数据(支持参数为数组或键值对)
    *
    * @param  string $key
    * @param  mixed  $value
    * @return $this
    * */
    public function set($key, $value=null);

    /* *
     * 装填用户响应错误数据(支持参数为数组或键值对)
     *
     * @param  string $key
     * @param  mixed  $value
     * @return $this
     * */
    public function setErr($key, $value=null);

    /* *
     * 设响应头
     *
     * @param  string $key
     * @param  string $value
     * @return $this
     * */
    public function header($key, $value);

    /* *
     * 设状态码
     *
     * @param  int $status
     * @return $this
     * */
    public function status($status);

    /* *
     * 发送响应
     *
     * @param  array $content
     * @param  int|string $status
     * @param  array $header
     * @param  string $format
     * @return Response
     * */
    public function send($content=null, $status=null, $header=null, $format='JSON');

    /* *
     * 用户错误信息响应
     *
     * 支持参数为数组或键值对
     * */
    public function sendErr($key, $value=null, $status=400);

    /* *
     * 前端错误方法响应
     * */
    public function sendBadMethod();

    /* *
     * 前端错误参数响应
     * */
    public function sendBadParam($message);

    /* *
     * 获取数据
     *
     * 不带参数获取响应数据，带参数获取对应$key的$value
     * @param  string $key
     * @return mixed
     * */
    public function get($key = null);
}