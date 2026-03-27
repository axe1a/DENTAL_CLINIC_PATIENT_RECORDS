@echo off

:: check if php exists
if not exist php\php.exe (
    echo Downloading PHP - 35mB or 35,000,000 Bytes... This may take a while...
    powershell -Command "Invoke-WebRequest -Uri https://downloads.php.net/~windows/releases/archives/php-8.5.4-nts-Win32-vs17-x64.zip -OutFile php.zip"
    powershell -Command "Expand-Archive php.zip php"
    del php.zip
)

:: Copy custom php.ini to php folder if it does not exist
if not exist php\php.ini (
    copy /Y php.ini php\php.ini
)

:: Check if database exists
if not exist database\database.sqlite (
    echo Database not found. Running setupDB.php...
    php\php.exe -f app\setupDB.php -t app
) else (
    echo Database exists. Skipping setup.
)

start http://localhost:8080
php\php.exe -S localhost:8080 -t app
pause