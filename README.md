
# scanner-bundle
A small framework to run open-source tools that inspect and scan any kind of webpages for vulnerabilities.  

Disclaimer: This tool is not intended to be used for any criminal act. It's used to find vulnerabilites on websites whose owners have given me express permission to do so! You are responsible for all your decisions.

## Todo
- Support for more native runners
- Automatic risk assessment
- Adjust tool integration to work with standard github-master ZIPs

## Prerequisites

 - Apache2
 - PHP (>= 7.4.19 || >= 8.0)
 - Python (>= 3.9.13)
 - Composer (>= 2.1.2)

## Installation
Clone repository:
```bash
git clone https://github.com/david-prv/scanner-bundle.git
```
Navigate into project's root folder:
```bash
cd scanner-bundle
```
Install dependencies:
```bash
composer install
```
Run compressor:
```bash
php compressor.php
```
## Launch

This application is only meant to be running locally. It's not supposed to be a public accessible application in the web. To run a local instance, you can either use `XAMPP` to run a local web-server, or just launch a PHP development server.
```bash
# in project's root folder...
php -S localhost:8080
```
Now open a web browser and navigate to `http://localhost:8080/`. The scanner-bundle framework should appear.

## Screenshots

![image](https://user-images.githubusercontent.com/66866223/199949463-0151365a-fe01-44c3-9ff9-3f6f09b948eb.png)
![image](https://user-images.githubusercontent.com/66866223/200019984-ef0501df-b3de-4f02-a290-14e4043f5df3.png)
![image](https://user-images.githubusercontent.com/66866223/198300858-0a65e372-af2e-4898-8a75-31af486906d0.png)
