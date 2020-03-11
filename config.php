<?php
define('MEM_LIMIT','1G');

define('ERR_LOG',__DIR__ . '/'. 'error_log');
define('LOOKUP',__DIR__ . '/'. 'lookup');

define('QUEUE_LOG','/var/log/asterisk/queue_log');
define('DLMTR','|');

define('NEEDED_EVENETS', serialize(array('COMPLETECALLER','COMPLETEAGENT','ABANDON')));
define('SRV','localhost');
define('USR','root');
define('PWD','');
define('DB','call_center');	// preferred call_center database to JOIN tables on uniqueid
define('TB','queue_log');

define('FETCH_EVERY',1000); // per milliseconds

/////////////////////////////////////
//         TABLE CREATION         //
////////////////////////////////////
/*
DROP TABLE IF EXISTS `queue_log`;
CREATE TABLE `queue_log` (`id` int(11) NOT NULL,`fetched_at` timestamp NOT NULL DEFAULT current_timestamp,`timestamp` varchar(100) NOT NULL,`uniqueid` varchar(100) NOT NULL,`queue` varchar(50) NOT NULL,`agent` varchar(10) NOT NULL,`event` varchar(25) NOT NULL,`arg1` varchar(15) DEFAULT NULL,`arg2` varchar(15) DEFAULT NULL,`arg3` varchar(15) DEFAULT NULL,`arg4` varchar(15) DEFAULT NULL,`arg5` varchar(15) DEFAULT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `queue_log` ADD PRIMARY KEY (`id`), ADD KEY `uniqueid` (`uniqueid`);
ALTER TABLE `queue_log` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
*/
?>
