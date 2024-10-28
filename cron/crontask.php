<?php
require_once dirname(__DIR__) . '/classes/CronTask.php';

$cron = new CronTasks();
$cron->clearPastBlockedDates();
