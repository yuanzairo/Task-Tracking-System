<?php
echo "MYSQLHOST: "     . getenv('MYSQLHOST')     . "<br>";
echo "MYSQLUSER: "     . getenv('MYSQLUSER')      . "<br>";
echo "MYSQLPASSWORD: " . getenv('MYSQLPASSWORD')  . "<br>";
echo "MYSQLDATABASE: " . getenv('MYSQLDATABASE')  . "<br>";
echo "MYSQLPORT: "     . getenv('MYSQLPORT')      . "<br>";
echo "PORT type: "     . gettype((int)getenv('MYSQLPORT')) . "<br>";
