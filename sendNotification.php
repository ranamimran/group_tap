<?php
include_once 'ApnsPHP/Autoload.php';
class sendNotification
{
    function sendNotificationByApns($deviceToken, $msg,$type, $userId)
    {
        $push = new ApnsPHP_Push(
                                ApnsPHP_Abstract::ENVIRONMENT_PRODUCTION, 'certificate/aps_dist_certi.pem'
                        );
        $push->setRootCertificationAuthority('certificate/aps_dist_certi.pem');
        $push->connect();
        $message = new ApnsPHP_Message($deviceToken);
//        $message = new ApnsPHP_Message("407fa2549e4cb290c4f13059340a36e69679d9e1ba34b40751663819d98281ab");
        $message->setCustomProperty('type',$type);
        $message->setCustomProperty('userId',$userId);
//        $message->setCustomProperty('channelName',$channelName);
        $message->setText($msg);
        $message->setBadge(1);
        $message->setSound();

        $push->add($message);
        $res = $push->send();
        $push->disconnect();
//        return true;
//            echo "sese";
//            exit;
    }
}

?>