<?php
 
/**
 *
 * AlphaPay支付API异常类
 * @author Robert
 *
 */
class AlphaPayException extends Exception
{
    public function errorMessage()
    {
        return $this->getMessage();
    }
}
