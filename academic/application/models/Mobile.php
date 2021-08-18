<?php
class Application_Model_Mobile extends Zend_Db_Table_Abstract
{
   

    public function sendMessageOnMobile($message='No Message',$number='0000000000') {
        $username = "DMIPATNA";
        $password = "e-2!j6HS1";
        $sender = "DMIADM"; // This is who the message appears to be from.
        $numbers = $number; // A single number or a comma-seperated list of numbers
        // print_r($_SESSION['public']['userefrence']['ca_pri_mobile']);exit;
       
        $message = urlencode($message);
        //information storing data variable 
        $data = "username=" . $username . "&pass=" . $password . "&senderid=" . $sender . "&dest_mobileno=" . $numbers . "&message=" . $message . '&response=Y';
        $ch = curl_init('https://www.smsjust.com/sms/user/urlsms.php?' . $data);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        if ($result == true) {
          echo 'Message Sent Sucessfully';
        }
        curl_close($ch);
    }
}