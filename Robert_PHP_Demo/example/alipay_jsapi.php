<?php
ini_set('date.timezone', 'America/Vancouver');
error_reporting(E_ERROR);
require_once "../lib/AlphaPay.Api.php";
require_once 'Log.php';
header("Content-Type:text/html;charset=utf-8");

//初始化日志 
$logHandler = new CLogFileHandler("../logs/" . date('Y-m-d') . '.log');
$log = Log::Init($logHandler, 15);

$input = new AlphaPayUnifiedOrder();
$input->setOrderId(AlphaPayConfig::PARTNER_CODE . date("YmdHis"));
$input->setDescription("test");
$input->setPrice("1");
$input->setCurrency("CAD");
$input->setChannel("Alipay");
$input->setNotifyUrl("https://www.your_domain.com/notify");
$input->setOperator("alipay jaspi");
$currency = $input->getCurrency();
if (!empty($currency) && $currency == 'CNY') {
    //建议缓存汇率,每天更新一次,遇节假日或其他无汇率更新情况,可取最近一个工作日的汇率
    $inputRate = new AlphaPayExchangeRate();
    $rate = AlphaPayApi::exchangeRate($inputRate);
    if ($rate['return_code'] == 'SUCCESS') {
        $real_pay_amt = $input->getPrice() / $rate['rate'] / 100;
        if ($real_pay_amt < 0.01) {
            echo '人民币转换加币后必须大于0.01加币';
            exit();
        }
    }
}
//支付下单
$result = AlphaPayApi::jsApiOrder($input);



//跳转
$inputObj = new AlphaPayJsApiRedirect();
$inputObj->setDirectPay('true');
$inputObj->setRedirect(urlencode('https://www.your_domain.com/success.php?order_id=' . strval($input->getOrderId())));

$order_id = AlphaPayConfig::PARTNER_CODE . date("YmdHis");
$partner_code = AlphaPayConfig::PARTNER_CODE;
$url = "https://pay.alphapay.ca//api/v1.0/gateway/alipay/partners/".$partner_code."/orders/".$order_id."/app_pay"

?>

<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>AlphaPay支付样例-支付宝jsApi支付</title>
    <script>
        function redirect(url) {
            window.location.href = url;
        }
    </script>
</head>
<body>
<h1>AlphaPay支付样例-支付宝jsApi支付</h1>
<br/>
<font color="#9ACD32"><b>该笔订单支付金额为<span style="color:#f00;font-size:50px">0.01</span>加币</b></font><br/><br/>
<div align="center">
    <button
        style="width:210px; height:50px; border-radius: 15px;background-color:#e52b3d; border:0px #e52b3d solid; cursor: pointer;  color:white;  font-size:16px;"
        type="button"
        onclick="redirect('<?php echo AlphaPayApi::getJsApiRedirectUrl($url, $inputObj); ?>')">
        立即支付
    </button>
</div>
</body>
</html>