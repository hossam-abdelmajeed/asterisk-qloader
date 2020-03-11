# asterisk-qloader
	Loading the Asterisk queue_log file into MySQL database

1. What is qloader?

	qloader is a small PHP script that fetchs data from Asterisk queue_log file into a MySQL database.
	
2. Settingup qloader:

	2.1. Update config.php file with your server information:
		
		2.1.1. MEM_LIMIT is PHP memory_limit, by default I set it to 1 Gigabyte, to get raide of 
				` Fatal error: Allowed memory size of x bytes exhausted (tried to allocate x bytes) in /path/of/script/file `
				It's all yours to in/decrease it.
				
		2.1.2. **ERR_LOG** is the file will store all errors if it occurred.
		
		2.1.3. **LOOKUP** is the file will hold the starting point for every queue_log read (line number the script will start reading from).
		
		2.1.4. **QUEUE_LOG** is the full path to the queue_log file it has to parse.
		
		2.1.5. **DLMTR** is the pre-defined delimiter in queue_log file, which is by default a pipe "|", preferred not change that unless you know what you are doing.
		
		2.1.6. **NEEDED_EVENETS** is an array holds needed events that will be considered into parsing and inserted into database.
		
			You can use any of the below:
			
				```
				+----------------+
				| event          |
				+----------------+
				| QUEUESTART     |
				| CONFIGRELOAD   |
				| DID            |
				| ENTERQUEUE     |
				| ABANDON        |
				| AGENTLOGIN     |
				| UNPAUSEALL     |
				| UNPAUSE        |
				| SYSCOMPAT      |
				| PAUSEALL       |
				| PAUSE          |
				| AGENTLOGOFF    |
				| CONNECT        |
				| COMPLETECALLER |
				| COMPLETEAGENT  |
				| ADDMEMBER      |
				| REMOVEMEMBER   |
				| HEARTBEAT      |
				+----------------+
				```
		
		2.1.7. **SRV** the MySQL hostname to connect to.
		
		2.1.8. **USR** the MySQL username to use.
		
		2.1.9. **PWD** the MySQL password to use.
		
		2.1.10. **DB** the MySQL database to connect to.
		
		2.1.11. **TB** the MySQL table to parse data into.
		
		2.1.12. **FETCH_EVERY** is the fetching sequence per milliseconds.
		
		
	2.2. You have to prepare your Table, using these commands:
	
		```
			DROP TABLE IF EXISTS `queue_log`;
			CREATE TABLE `queue_log` (`id` int(11) NOT NULL,`fetched_at` timestamp NOT NULL DEFAULT current_timestamp,`timestamp` varchar(100) NOT NULL,`uniqueid` varchar(100) NOT NULL,`queue` varchar(50) NOT NULL,`agent` varchar(10) NOT NULL,`event` varchar(25) NOT NULL,`arg1` varchar(15) DEFAULT NULL,`arg2` varchar(15) DEFAULT NULL,`arg3` varchar(15) DEFAULT NULL,`arg4` varchar(15) DEFAULT NULL,`arg5` varchar(15) DEFAULT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;
			ALTER TABLE `queue_log` ADD PRIMARY KEY (`id`), ADD KEY `uniqueid` (`uniqueid`);
			ALTER TABLE `queue_log` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
		```
		
	2.3. Starting qloader.php as a service.
	
That's it.
