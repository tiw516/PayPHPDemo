<?php
require_once "AlphaPay.Exception.php";
require_once "AlphaPay.Config.php";
require_once "AlphaPay.Data.php";
 
/**
 *
 * 接口访问类，包含所有AlphaPay支付API列表的封装，类中方法为static方法，
 * 每个接口有默认超时时间（除提交扫码支付为15s，其他均为10s）
 * @author Robert
 *
 */
class AlphaPayApi
{
    /**
     *
     * 汇率查询，nonce_str、time不需要填入
     * @param AlphaPayExchangeRate $inputObj
     * @param int $timeOut
     * @throws AlphaPayException
     * @return $result 成功时返回，其他抛异常
     */
    public static function exchangeRate($inputObj, $timeOut = 10)
    {
        $partnerCode = AlphaPayConfig::PARTNER_CODE;
        $url = "https://pay.alphapay.ca//api/v1.0/gateway/partners/$partnerCode/exchange_rate";
        $inputObj->setTime(self::getMillisecond());//时间戳
        $inputObj->setNonceStr(self::getNonceStr());//随机字符串
        $inputObj->setSign();
        $response = self::getJsonCurl($url, $inputObj, $timeOut);
        $result = AlphaPayResults::init($response);
        return $result;
    }

    /**
     *
     * QR下单，nonce_str、time不需要填入
     * @param AlphaPayUnifiedOrder $inputObj
     * @param int $timeOut
     * @throws AlphaPayException
     * @return $result 成功时返回，其他抛异常
     */
    public static function qrOrder($inputObj, $timeOut = 15)
    {
        $partnerCode = AlphaPayConfig::PARTNER_CODE;
        $orderId = $inputObj->getOrderId();
        $url = "https://pay.alphapay.ca/api/v1.0/gateway/partners/$partnerCode/orders/$orderId";
        $inputObj->setTime(self::getMillisecond());//时间戳
        $inputObj->setNonceStr(self::getNonceStr());//随机字符串
        $inputObj->setSign();
        $response = self::putJsonCurl($url, $inputObj, $timeOut);
        $result = AlphaPayResults::init($response);
        return $result;
    }

    /**
     *
     * JsApi下单，nonce_str、time不需要填入
     * @param AlphaPayUnifiedOrder $inputObj
     * @param int $timeOut
     * @throws AlphaPayException
     * @return $result 成功时返回，其他抛异常
     */
    public static function jsApiOrder($inputObj, $timeOut = 10)
    {
        $partnerCode = AlphaPayConfig::PARTNER_CODE;
        $orderId = $inputObj->getOrderId();
        $url = "https://pay.alphapay.ca/api/v1.0/jsapi_gateway/partners/$partnerCode/orders/$orderId";
        $inputObj->setTime(self::getMillisecond());//时间戳
        $inputObj->setNonceStr(self::getNonceStr());//随机字符串
        $inputObj->setSign();
        $response = self::putJsonCurl($url, $inputObj, $timeOut);
        $result = AlphaPayResults::init($response);
        return $result;
    }


        /**
     *
     * 支付宝H5下单，nonce_str、time不需要填入
     * @param AlphaPayUnifiedOrder $inputObj
     * @param int $timeOut
     * @throws AlphaPayException
     * @return $result 成功时返回，其他抛异常
     */
    public static function h5($inputObj, $timeOut = 15)
    {
        $partnerCode = AlphaPayConfig::PARTNER_CODE;
        $orderId = $inputObj->getOrderId();
        $url = "https://pay.alphapay.ca/api/v1.0/h5_payment/partners/$partnerCode/orders/$orderId";
        $inputObj->setTime(self::getMillisecond());//时间戳
        $inputObj->setNonceStr(self::getNonceStr());//随机字符串
        $inputObj->setSign();
        $response = self::putJsonCurl($url, $inputObj, $timeOut);
        $result = AlphaPayResults::init($response);
        return $result;
    }

    /**
     *
     * QR支付跳转，nonce_str、time不需要填入
     * @param string $pay_url
     * @param AlphaPayRedirect $inputObj
     * @throws AlphaPayException
     * @return $pay_url 成功时返回，其他抛异常
     */
    public static function getQRRedirectUrl($pay_url, $inputObj)
    {
        $inputObj->setTime(self::getMillisecond());//时间戳
        $inputObj->setNonceStr(self::getNonceStr());//随机字符串
        $inputObj->setSign();
        $pay_url .= '?' . $inputObj->toQueryParams();
        return $pay_url;
    }

    /**
     *
     * 微信JsApi支付跳转，nonce_str、time不需要填入
     * @param string $pay_url
     * @param AlphaPayJsApiRedirect $inputObj
     * @throws AlphaPayException
     * @return $pay_url 成功时返回，其他抛异常
     */
    public static function getJsApiRedirectUrl($pay_url, $inputObj)
    {
        $directPay = $inputObj->getDirectPay();
        if (empty($directPay)) {
            $inputObj->setDirectPay('false');
        }
        $inputObj->setTime(self::getMillisecond());//时间戳
        $inputObj->setNonceStr(self::getNonceStr());//随机字符串
        $inputObj->setSign();
        $pay_url .= '?' . $inputObj->toQueryParams();
        return $pay_url;
    }

        /**
     *
     * H5支付跳转，nonce_str、time不需要填入
     * @param string $pay_url
     * @param AlphaPayRedirect $inputObj
     * @throws AlphaPayException
     * @return $pay_url 成功时返回，其他抛异常
     */
    public static function getH5RedirectUrl($pay_url, $inputObj)
    {
        $inputObj->setTime(self::getMillisecond());//时间戳
        $inputObj->setNonceStr(self::getNonceStr());//随机字符串
        $inputObj->setSign();
        $pay_url .= '?' . $inputObj->toQueryParams();
        return $pay_url;
    }



    /**
     *
     * 线下支付订单，nonce_str、time不需要填入
     * @param AlphaPayMicropayOrder $inputObj
     * @param int $timeOut
     * @throws AlphaPayException
     * @return $result 成功时返回，其他抛异常
     */
    public static function micropayOrder($inputObj, $timeOut = 10)
    {
        $partnerCode = AlphaPayConfig::PARTNER_CODE;
        $orderId = $inputObj->getOrderId();
        $url = "https://pay.alphapay.ca//api/v1.0/micropay/partners/$partnerCode/orders/$orderId";
        $inputObj->setTime(self::getMillisecond());//时间戳
        $inputObj->setNonceStr(self::getNonceStr());//随机字符串
        $inputObj->setSign();
        $response = self::putJsonCurl($url, $inputObj, $timeOut);
        $result = AlphaPayResults::init($response);
        return $result;
    }

    /**
     *
     * 线下QRCode支付单，nonce_str、time不需要填入
     * @param AlphaPayRetailQRCode $inputObj
     * @param int $timeOut
     * @throws AlphaPayException
     * @return $result 成功时返回，其他抛异常
     */
    public static function retailQRCodeOrder($inputObj, $timeOut = 10)
    {
        $partnerCode = AlphaPayConfig::PARTNER_CODE;
        $orderId = $inputObj->getOrderId();
        $url = "https://pay.alphapay.ca//api/v1.0/retail_qrcode/partners/$partnerCode/orders/$orderId";
        $inputObj->setTime(self::getMillisecond());//时间戳
        $inputObj->setNonceStr(self::getNonceStr());//随机字符串
        $inputObj->setSign();
        $response = self::putJsonCurl($url, $inputObj, $timeOut);
        $result = AlphaPayResults::init($response);
        return $result;
    }

    /**
     *
     * 查询订单，nonce_str、time不需要填入
     * @param AlphaPayOrderQuery $inputObj
     * @param int $timeOut
     * @throws AlphaPayException
     * @return $result 成功时返回，其他抛异常
     */
    public static function orderQuery($inputObj, $timeOut = 10)
    {
        $partnerCode = AlphaPayConfig::PARTNER_CODE;
        $orderId = $inputObj->getOrderId();
        $url = "https://pay.alphapay.ca//api/v1.0/gateway/partners/$partnerCode/orders/$orderId";
        $inputObj->setTime(self::getMillisecond());//时间戳
        $inputObj->setNonceStr(self::getNonceStr());//随机字符串
        $inputObj->setSign();
        $response = self::getJsonCurl($url, $inputObj, $timeOut);
        $result = AlphaPayResults::init($response);
        return $result;
    }

    /**
     *
     * 申请退款，nonce_str、time不需要填入
     * @param AlphaPayApplyRefund $inputObj
     * @param int $timeOut
     * @throws AlphaPayException
     * @return $result 成功时返回，其他抛异常
     */
    public static function refund($inputObj, $timeOut = 10)
    {
        $partnerCode = AlphaPayConfig::PARTNER_CODE;
        $orderId = $inputObj->getOrderId();
        $refundId = $inputObj->getRefundId();
        $url = "https://pay.alphapay.ca//api/v1.0/gateway/partners/$partnerCode/orders/$orderId/refunds/$refundId";
        $inputObj->setTime(self::getMillisecond());//时间戳
        $inputObj->setNonceStr(self::getNonceStr());//随机字符串
        $inputObj->setSign();
        $response = self::putJsonCurl($url, $inputObj, $timeOut);
        $result = AlphaPayResults::init($response);
        return $result;
    }

    /**
     *
     * 查询退款状态，nonce_str、time不需要填入
     * @param AlphaPayQueryRefund $inputObj
     * @param int $timeOut
     * @throws AlphaPayException
     * @return $result 成功时返回，其他抛异常
     */
    public static function refundQuery($inputObj, $timeOut = 10)
    {
        $partnerCode = AlphaPayConfig::PARTNER_CODE;
        $orderId = $inputObj->getOrderId();
        $refundId = $inputObj->getRefundId();
        $url = "https://pay.alphapay.ca//api/v1.0/gateway/partners/$partnerCode/orders/$orderId/refunds/$refundId";
        $inputObj->setTime(self::getMillisecond());//时间戳
        $inputObj->setNonceStr(self::getNonceStr());//随机字符串
        $inputObj->setSign();
        $response = self::getJsonCurl($url, $inputObj, $timeOut);
        $result = AlphaPayResults::init($response);
        return $result;
    }

    /**
     *
     * 查看账单，nonce_str、time不需要填入
     * @param AlphaPayQueryOrders $inputObj
     * @param int $timeOut
     * @throws AlphaPayException
     * @return $result 成功时返回，其他抛异常
     */
    public static function orders($inputObj, $timeOut = 10)
    {
        $partnerCode = AlphaPayConfig::PARTNER_CODE;
        $url = "https://pay.alphapay.ca//api/v1.0/gateway/partners/$partnerCode/orders";
        $inputObj->setTime(self::getMillisecond());//时间戳
        $inputObj->setNonceStr(self::getNonceStr());//随机字符串
        $inputObj->setSign();
        $response = self::getJsonCurl($url, $inputObj, $timeOut);
        $result = AlphaPayResults::init($response);
        return $result;
    }

    /**
     *
     * 产生随机字符串，不长于30位
     * @param int $length
     * @return $str 产生的随机字符串
     */
    public static function getNonceStr($length = 30)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 以get方式提交json到对应的接口url
     *
     * @param string $url
     * @param object $inputObj
     * @param int $second url执行超时时间，默认30s
     * @throws AlphaPayException
     */
    private static function getJsonCurl($url, $inputObj, $second = 30)
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        //如果有配置代理这里就设置代理
        if (AlphaPayConfig::CURL_PROXY_HOST != "0.0.0.0"
            && AlphaPayConfig::CURL_PROXY_PORT != 0
        ) {
            curl_setopt($ch, CURLOPT_PROXY, AlphaPayConfig::CURL_PROXY_HOST);
            curl_setopt($ch, CURLOPT_PROXYPORT, AlphaPayConfig::CURL_PROXY_PORT);
        }
        $url .= '?' . $inputObj->toQueryParams();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);//严格校验
        //设置header
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        //GET提交方式
        curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            throw new AlphaPayException("curl出错，错误码:$error");
        }
    }

    /**
     * 以put方式提交json到对应的接口url
     *
     * @param string $url
     * @param object $inputObj
     * @param int $second url执行超时时间，默认30s
     * @throws AlphaPayException
     */
    private static function putJsonCurl($url, $inputObj, $second = 30)
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);

        //如果有配置代理这里就设置代理
        if (AlphaPayConfig::CURL_PROXY_HOST != "0.0.0.0"
            && AlphaPayConfig::CURL_PROXY_PORT != 0
        ) {
            curl_setopt($ch, CURLOPT_PROXY, AlphaPayConfig::CURL_PROXY_HOST);
            curl_setopt($ch, CURLOPT_PROXYPORT, AlphaPayConfig::CURL_PROXY_PORT);
        }
        $url .= '?' . $inputObj->toQueryParams();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);//严格校验
        //设置header
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json'));
        //PUT提交方式
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $inputObj->toBodyParams());
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            throw new AlphaPayException("curl出错，错误码:$error");
        }
    }

    /**
     * 获取毫秒级别的时间戳
     */
    private static function getMillisecond()
    {
        //获取毫秒的时间戳
        $time = explode(" ", microtime());
		$millisecond = "000".($time[0] * 1000);
		$millisecond2 = explode(".", $millisecond);
		$millisecond = substr($millisecond2[0],-3);
        $time = $time[1] . $millisecond;
        return $time;
    }
}

