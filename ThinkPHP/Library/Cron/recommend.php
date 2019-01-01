<?php 
 use Think\Log;
$message="欢迎光临本站，现在时间是:".date('Y-m-d H:i:s',time()."--定时任务demo");
 Log::write($message);
