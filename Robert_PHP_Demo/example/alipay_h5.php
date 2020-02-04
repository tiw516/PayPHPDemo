<?php
ini_set('date.timezone', 'America/Vancouver');
require_once "../lib/AlphaPay.Api.php";
header("Content-Type:text/html;charset=utf-8");
/**
 * 流程：
 * 1、创建QRCode支付单，取得code_url，生成二维码
 * 2、用户扫描二维码，进行支付
 * 3、支付完成之后，AlphaPay服务器会通知支付成功
 * 4、在支付成功通知中需要查单确认是否真正支付成功（见：notify.php）
 */
//获取扫码
$input = new AlphaPayUnifiedOrder();
$input->setOrderId(AlphaPayConfig::PARTNER_CODE . date("YmdHis"));
$input->setDescription("test");
$input->setPrice("10");
$input->setCurrency("CAD");
$input->setChannel("Alipay");
$input->setNotifyUrl("https://www.your_domain.com/notify");
$input->setOperator("alipay h5");
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

$result = AlphaPayApi::h5($input);
 
//跳转
$inputObj = new AlphaPayRedirect();
$inputObj->setRedirect(urlencode('https://www.your_domain.com/success.php?order_id=' . strval($input->getOrderId())));
?>

<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>AlphaPay支付样例- 支付宝 H5</title>
    <script>
        function redirect(url) {
            window.location.href = url;
        }
    </script>
</head>
<body>
    <h1>AlphaPay支付样例- 支付宝 H5</h1>
<br/>
<br/>
<font color="#9ACD32"><b>该笔订单支付金额为<span style="color:#f00;font-size:50px">0.01</span>加币</b></font><br/><br/>
<div align="center">
    <button
        style="width:210px; height:50px; border-radius: 15px;background-color:#e52b3d; border:0px #e52b3d solid; cursor: pointer;  color:white;  font-size:16px;"
        type="button"
        onclick="redirect('<?php echo AlphaPayApi::getH5RedirectUrl($result['pay_url'], $inputObj); ?>')">
        立即支付
    </button>
</div>
</body>
</html>