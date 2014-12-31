echo --- Killing all processes

sudo killall -9 -q Xvfb
sudo killall -9 -q firefox
sudo killall -9 -q java

echo --- Running Xvfb
Xvfb :99 -ac & > /dev/null 2>&1
sleep 2

echo --- Running Firefox
firefox &
sleep 2

echo --- Running Selenium
java -jar /usr/lib/selenium/selenium-server-standalone-2.44.0.jar
