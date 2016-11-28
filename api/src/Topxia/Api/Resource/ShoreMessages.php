<?php 

namespace Topxia\Api\Resource;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Topxia\Common\CurlToolkit;
use Topxia\Service\CloudPlatform\CloudAPIFactory;

class ShortMessages extends BaseResource
{
    public function post(Application $app, Request $request)
    {
        $data = $request->query->all();

        $token = json_decode($data);

        //登录用户，重置密码
        if (!empty($token['userId'])) {
            return $this->sendVerify($token);
        }

        //同一个ip访问次数超过5次
        if () {
        }

        if (!$this->getUserService()->getUserByVerifiedMobile($token['mobile'])) {
            return $this->error('5001', '手机号未验证');
        }

        return $this->sendVerify($token);
    }

    protected function sendVerify($token)
    {
        $smsCode = $this->generateSmsCode();

        $api    = CloudAPIFactory::create('leaf');
        $result = $api->post("/sms/{$api->getAccessKey()}/sendVerify", array(
            'mobile'      => $token['mobile'], 
            'category'    => $token['category'], 
            'description' => '发送手机验证码', 
            'verify'      => $smsCode
        ));

        return $result;   
    }

    protected function generateSmsCode($length = 6)
    {
        $code = rand(0, 9);

        for ($i = 1; $i < $length; $i++) {
            $code = $code.rand(0, 9);
        }

        return $code;
    }

    protected function getUserService()
    {
        return $this->getServiceKernel()->createService('User.UserService');
    }
}