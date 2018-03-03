<html>
<head>
    <meta charset="utf-8"/>
    <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
</head>
<body>
<?php
//echo iconv("GB2312","UTF-8","中文");
//echo exec ('ipconfig');
//echo shell_exec ('cordova');
//passthru('cordova');
//echo system ('cordova');

//exec('cordova -v 2>&1', $output);
//exec('ipconfig 2>&1', $output);
//print_r($output);
try {
    $responsecode = '';
    //生成工程
    //$command = 'cordova create cordova/'.iconv("UTF-8","GBK",'测试').' com.example.hello '.iconv("UTF-8","GBK",'测试项目');
    //exec($command,$responsecode);

    //echo $command;
    if(chdir( 'cordova/'.iconv("UTF-8","GBK",'测试'))){
        set_time_limit (300);

        //安卓平台
        $command ='cordova platform add android';
        //exec($command,$responsecode);

        //插件支持
        $command ='cordova plugin add org.apache.cordova.device';
        //exec($command,$responsecode);

        $command ='cordova plugin add org.apache.cordova.network-information';
        //exec($command,$responsecode);

        $command ='cordova plugin add org.apache.cordova.battery-status';
        //exec($command,$responsecode);

        $command ='cordova plugin add org.apache.cordova.device-motion';
        //exec($command,$responsecode);

        $command ='cordova plugin add org.apache.cordova.device-orientation';
        //exec($command,$responsecode);

        $command ='cordova plugin add org.apache.cordova.geolocation';
        //exec($command,$responsecode);

        $command ='cordova plugin add org.apache.cordova.camera';
        //exec($command,$responsecode);

        $command ='cordova plugin add org.apache.cordova.media-capture';
        //exec($command,$responsecode);

        $command ='cordova plugin add org.apache.cordova.media';
        //exec($command,$responsecode);

        $command ='cordova plugin add org.apache.cordova.file';
        //exec($command,$responsecode);

        $command ='cordova plugin add org.apache.cordova.file-transfer';
        //exec($command,$responsecode);

        $command ='cordova plugin add org.apache.cordova.dialogs';
        //exec($command,$responsecode);

        $command ='cordova plugin add org.apache.cordova.vibration';
        //exec($command,$responsecode);

        $command ='cordova plugin add org.apache.cordova.contacts';
        //exec($command,$responsecode);

        $command ='cordova plugin add org.apache.cordova.globalization';
        //exec($command,$responsecode);

        $command ='cordova plugin add org.apache.cordova.splashscreen';
        //exec($command,$responsecode);

        $command ='cordova plugin add org.apache.cordova.splashscreen';
        //exec($command,$responsecode);

        //config.xml
        $configxml = simplexml_load_file('config.xml');
        $configxml->description='测试项目';
        $configxml->author['email']='email@kmdx.cn';
        $configxml->author['href']='http://www.kmdx.cn';
        $configxml->author='昆明道讯';
        $configxml->content['src']='http://ylst.kmdx.cn/index.php?g=Wap&m=Index&a=index&token=hrxznz1420117686&rget=1';
        $configxml->asXML('config.xml');

        //编译
        $command ='cordova build android';
        //$command ='cordova build android -release';
        exec($command,$responsecode);
    }
    print_r($responsecode);
    //exec("cd hello && dir");
}
catch(Exception $ex) {
    echo $ex->getMessage();
}
?>
</body>
</html>

