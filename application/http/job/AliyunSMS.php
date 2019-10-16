<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/11
 * Time: 15:20
 */

namespace app\http\job;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use think\queue\Job;

class AliyunSMS
{
    const BLACK_KEY_CONTROL_LIMIT='isv.BLACK_KEY_CONTROL_LIMIT';
    const MOBILE_NUMBER_ILLEGAL='isv.MOBILE_NUMBER_ILLEGAL';
    const VALVEM_MC='VALVE:M_MC';



    private $signName = '';
    private $templateCode = '';
    private $AccessKeyId = '';
    private $AccessSecret='';
// Download：https://github.com/aliyun/openapi-sdk-php
// Usage：https://github.com/aliyun/openapi-sdk-php/blob/master/README.md
    public function __construct()
    {
        $this->templateCode = config('aliyunSMS.templateCode');
        $this->signName = config('aliyunSMS.signName');
        $this->AccessKeyId = config('aliyunSMS.accessKey');
        $this->AccessSecret=config('aliyunSMS.accessSecret');
    }

    public function fire($phone,$code)
    {
        try {
            AlibabaCloud::accessKeyClient($this->AccessKeyId, $this->AccessSecret)
                ->regionId('cn-hangzhou')->asDefaultClient();
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => "cn-hangzhou",
//                        'AccessKeyId'=>$this->AccessKeyId,
                        'PhoneNumbers' => $phone,
                        'SignName' => $this->signName,
                        'TemplateCode' => $this->templateCode,
                        'TemplateParam' => $code,
                    ],
                ])->request();
            return $result->toArray();
        } catch (ClientException $e) {
            return $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            return $e->getErrorMessage() . PHP_EOL;
        }
    }

    public function message($output){

    }

}