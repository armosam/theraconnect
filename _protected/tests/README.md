This directory contains various tests for the advanced applications.

Tests in `codeception` directory are developed with [Codeception PHP Testing Framework](http://codeception.com/).

After creating and setting up the advanced application, follow these steps to prepare for the tests:

1. Install Codeception if it's not yet installed:

   ```
   composer global require "codeception/codeception=*" "codeception/specify=*" "codeception/verify=*"
   ```

   If you've never used Composer for global packages run `composer global status`. It should output:

   ```
   Changed current directory to <directory>
   ```

   Then add `<directory>/vendor/bin` to you `PATH` environment variable. Now you're able to use `codecept` from command
   line globally.

2. Install faker extension by running the following from template root directory where `composer.json` is:

   ```
   composer require --dev yiisoft/yii2-faker:*
   ```

3. Create `yii2_advanced_tests` database then update it by applying migrations:

   ```
   codeception/bin/yii migrate
   ```

4. In order to be able to run acceptance tests you need to start a webserver. The simplest way is to use PHP built in
   webserver. In the root directory where `common`, `frontend` etc. are execute the following:

   ```
   php -S localhost:8080
   ```

5. Now you can run the tests with the following commands, assuming you are in the `tests/codeception` directory:

   ```
   # frontend tests
   cd frontend
   codecept build
   codecept run
   
   # backend tests
   
   cd backend
   codecept build
   codecept run
    
   # etc.
   ```

   >#####Note: To run tests with less info and show just dots like PHPUnit tests add `--ext DotReporter` option to the test command
   ```
   codecept run --ext DotReporter
   ```
    
  If you already have run `codecept build` for each application, you can skip that step and run all tests by a single `codecept run`.
  
  >##Acceptance Tests With Real Browser
   For acceptance tests with real browser we need to run Selenium Server with Chrome or Firefox driver 
   To run selenium standalone server we need Java as well as Chrome or Firefox browser installed:

   First we need to install Java 8 in our host and guest environments.
    
   Install Java on centOS:
   ```bash
   sudo yum install java-1.8.0-openjdk
   ```
    
   Prepare your ubuntu system:
   ```bash
    sudo apt-get update
    sudo apt-get install -y unzip xvfb libxi6 libgconf-2-4 
   ```
   
   Install Java on ubuntu:
   ```bash
    sudo add-apt-repository ppa:webupd8team/java
    sudo apt-get update
    sudo apt-get install oracle-java8-installer

    sudo apt-get install oracle-java8-set-default

    java -version
    ...

    cat >> /etc/environment <<EOL
JAVA_HOME=/usr/lib/jvm/java-8-oracle
JRE_HOME=/usr/lib/jvm/java-8-oracle/jre
EOL
   ```
   Next install firefox and driver on your ubuntu to communicate with browser 
   ```bash
    sudo apt-get -y install firefox
   ```
   Next install Google chrome and download latest chromedriver (currently: 76.0.3809.68)
   ```bash
    sudo curl -sS -o - https://dl-ssl.google.com/linux/linux_signing_key.pub | apt-key add
    sudo echo "deb [arch=amd64] http://dl.google.com/linux/chrome/deb/ stable main" >> /etc/apt/sources.list.d/google-chrome.list
    sudo apt-get -y update
    sudo apt-get -y install google-chrome-stable

    cd ~/Downloads/
    wget https://chromedriver.storage.googleapis.com/76.0.3809.68/chromedriver_linux64.zip
    unzip chromedriver_linux64.zip

    sudo mv chromedriver /usr/bin/chromedriver
    sudo chown root:root /usr/bin/chromedriver
    sudo chmod +x /usr/bin/chromedriver
   ``` 
    
   Run command to install selenium server globally on the guest host. It will download selenium `jar` file on the guest so you can use that to run selenium on the guest as well as on the host `ubuntu`   
   ```bash
    composer global require se/selenium-server-standalone
   ``` 
   This will create symlink in the app/_protected/vendor/bin folder of your project.
    
   >For `host selenium server` we can use same instance as we have in the guest hosts. 
   It will be available on host by path ``_protected/vendor/bin``  through shared folders 
    
   Then change configuration to allow acceptance testing by WebDriver 
   (There is a commented configuration) instead of PhpBrowser.
   Run server in `hub` mode on the vagrant box (virtual guest machine).
   
   As you added app/_protected/vendor/bin to your centOS path then run this command:
   ```bash
     selenium-server-standalone -role hub
   ```
    
   >#####Note: When running selenium in hub mode if everything is ok it will show to links one is for client another for nodes connection like:
   
   ```bash
        21:00:51.229 INFO [Hub.start] - Nodes should register to http://192.168.50.5:4444/grid/register/
        21:00:51.229 INFO [Hub.start] - Clients should connect to http://192.168.50.5:4444/wd/hub 
   ```
    
   When we installed selenium on the guest it creates symlink in this directory `_protected/vendor/bin` so we can use it from host computer if linux.
   You can run selenium as a `node` mode by using selenium installed on the guest host which symlink is located `_protected/vendor/bin` In this case you need to go that directory in the host computer and run:
   ```bash
    ./selenium-server-standalone -role node -hub http://192.168.50.5:4444/grid/register
    or
    ./selenium-server-standalone -role node -hub http://192.168.50.5:4444/grid/register/ -Dwebdriver.chrome.driver=/vagrant/www/cgi-bin/tests/chromedriver
    ./selenium-server-standalone -role node -hub http://192.168.50.5:4444/grid/register/ -Dwebdriver.gecko.driver=/vagrant/www/cgi-bin/tests/geckodriver
   ```
   Otherwise you can download selenium jar file, and run it separately as a selenium standalone server in `node` mode
   
   Go to `www/cgi-bin/tests` and you can find there already downloaded selenium and web drivers... 
   If you need you can download newer and compatible versions for your system.
   ```bash
    wget https://selenium-release.storage.googleapis.com/3.13/selenium-server-standalone-3.13.0.jar
   ```
   To run standalone selenium instance on the host you can go to the folder of instance `www/cgi-bin/tests` run following commands:
   ```bash
    chrome
        java -Dwebdriver.chrome.driver=/vagrant/www/cgi-bin/tests/chromedriver -jar selenium-server-standalone-3.141.59.jar -role webdriver -hub http://192.168.50.5:4444/grid/register/ -port 5558 -browser browserName=chrome
    firefox
        java -Dwebdriver.gecko.driver=/vagrant/www/cgi-bin/tests/geckodriver -jar selenium-server-standalone-3.141.59.jar -role webdriver -hub http://192.168.50.5:4444/grid/register/ -port 5556 -browser browserName=firefox
   ``` 
   >For windows host first have java installed, download selenium and webdrivers (chromedriver.exe, geckodriver.exe, IEDriverServer.exe) from `selenium.org` and put in folder then add folder in the path
   Currently it is in the `/www/cgi-bin/tests`.
   
   Then after running hub in the guest, go the folder with selenium and driver using the command prompt `www/cgi-bin/tests` and run following command to register in the hub
   ```apacheconfig
   chrome
    java -Dwebdriver.chrome.driver=chromedriver.exe -jar selenium-server-standalone-3.141.59.jar -role webdriver -hub http://192.168.50.5:4444/grid/register/ -port 5558 -browser browserName=chrome
   firefox
    java -Dwebdriver.gecko.driver=geckodriver.exe -jar selenium-server-standalone-3.141.59.jar -role webdriver -hub http://192.168.50.5:4444/grid/register/ -port 5556 -browser browserName=firefox
   IE
    java -Dwebdriver.ie.driver=IEDriverServer.exe -jar selenium-server-standalone-3.141.59.jar -role webdriver -hub http://192.168.50.5:4444/grid/register/ -port 5558 -browser browserName=iexplorer 
   ```

   >######Note: Now if everything is good it has to show fallowing and transfer all test suit commands from guest to host machine using `hub` to `node` communication through 4444 default port of selenium server
   ```bash
     00:35:44.304 INFO [SelfRegisteringRemote.registerToHub] - Registering the node to the hub: http://192.168.50.5:4444/grid/register
     00:35:44.670 INFO [SelfRegisteringRemote.registerToHub] - Updating the node configuration from the hub
     00:35:44.741 INFO [SelfRegisteringRemote.registerToHub] - The node is registered to the hub and ready to use
   ```

   >######Info: Selenium provides interface for grid management console  http://127.0.0.1:4444/grid/console?config=true&configDebug=true
   
   >### Finally run your acceptance tests in the new terminal tab. It has to open browser in your host computer and load your tests.
    
   
    
   >##Documentation and Help
   Good help here https://github.com/yveshwang/selenium-2step
   Example of vagrantfile that will install necessary dependencies for selenium

   ```Vagrantfile
   Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
       config.vm.define :selenium do |selenium|
           selenium.vm.box = "precise64"
           selenium.vm.box_url = "http://files.vagrantup.com/precise64.box"
           selenium.vm.network "forwarded_port", guest: 4444, host:4444
           $script_selenium = <<SCRIPT
   echo ==== Create a selenium folder ====
   mkdir /usr/local/selenium
   echo ==== Installing dependencies, curl, wget, unzip ====
   apt-get update
   apt-get install wget -y
   apt-get install curl -y
   apt-get install unzip -y
   echo ==== Installing Java ====
   apt-get install openjdk-7-jre -y 
   apt-get install openjdk-7-jdk -y
   apt-get install ant -y
   echo ==== Installing firefox ====
   apt-get install firefox -y
   echo ==== Installing chrome ====
   wget http://chromedriver.storage.googleapis.com/2.10/chromedriver_linux64.zip
   wget https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb
   mv *.zip /usr/local/selenium
   mv *.deb /usr/local/selenium
   unzip /usr/local/selenium/*.zip -d /usr/local/selenium
   dpkg -i /usr/local/selenium/google-chrome*; sudo apt-get -f install -y
   echo ==== Setting up Xvfb ====
   apt-get install xvfb -y
   cp /vagrant/Xvfb /etc/init.d/.
   update-rc.d Xvfb defaults
   service Xvfb start
   echo ==== Setting up selenium ====
   wget http://selenium-release.storage.googleapis.com/2.42/selenium-server-standalone-2.42.1.jar
   mv *.jar /usr/local/selenium/.
   cp /vagrant/selenium-grid /etc/init.d/.
   update-rc.d selenium-grid defaults
   service selenium-grid start
   cp /vagrant/selenium-node /etc/init.d/.
   update-rc.d selenium-node defaults
   service selenium-node start
   SCRIPT
           selenium.vm.provision :shell, :inline => $script_selenium
       end
   end
   ```
   
   >Good tutorial about selenium grid
   ```https://www.lullabot.com/articles/running-behat-tests-in-a-vagrant-box```
   
   >####ATTENTION: 
   - Open port 4444 in the guest and host firewall
   ```
    On CentOS:
    sudo firewall-cmd --zone=public --add-port=4444/tcp --permanent
   ```
   - Add port forwarding in Vagrantfile from guest: 4444 to host: 4444
   ```bash
    config.vm.network "forwarded_port", guest: 4444, host: 4444
   ```
   - Webdriver needs to be configured in your suit configuration
    
    
 
   
   