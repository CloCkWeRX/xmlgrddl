@echo off
set PHPBIN="G:\php\.\php.exe"
"G:\php\.\php.exe" -d safe_mode=Off "process-grddl.php" %*
