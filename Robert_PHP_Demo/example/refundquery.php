<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>AlphaPay支付样例-查退款单</title>
</head>
<?php
ini_set('date.timezone', 'America/Vancouver');
error_reporting(E_ERROR); 
require_once "../lib/AlphaPay.Api.php";
require_once 'Log.php';

//初始化日志
$logHandler = new CLogFileHandler("../logs/" . date('Y-m-d') . '.log');
$log = Log::Init($logHandler, 15);


function printf_info($data)
{
    foreach ($data as $key => $value) {
        echo "<font color='#f00;'>$key</font> : $value <br/>";
    }
}

if (isset($_REQUEST["order_id"]) && $_REQUEST["order_id"] != "" && isset($_REQUEST["refund_id"]) && $_REQUEST["refund_id"] != "") {
    $input = new AlphaPayQueryRefund();
    $input->setOrderId($_REQUEST["order_id"]);
    $input->setRefundId($_REQUEST["refund_id"]);
    printf_info(AlphaPayApi::refundQuery($input));
    exit();
}

?>
<body>
<form action="#" method="post">
    <div style="margin-left:2%;">商户订单号：</div>
    <br/>
    <input type="text" style="width:96%;height:35px;margin-left:2%;" name="order_id"/><br/><br/>
    <div style="margin-left:2%;">商户退款单号：</div>
    <br/>
    <input type="text" style="width:96%;height:35px;margin-left:2%;" name="refund_id"/><br/><br/>
    <div align="center">
        <input type="submit" value="查询"
               style="width:210px; height:50px; border-radius: 15px;background-color:#e52b3d; border:0px #e52b3d solid; cursor: pointer;  color:white;  font-size:16px;"
               type="button"/>
    </div>
</form>
</body>
</html>