# Scanner-Bundle: Control Framework

> **Warning**  
> This framework is in an early development state. Expect bugs, errors and undefined behaviour all the time.

A small framework to run open-source tools that inspect and scan any kind of webpages for vulnerabilities.  

Disclaimer: This tool is not intended to be used for any criminal act. It's used to find vulnerabilites on websites whose owners have given me express permission to do so! You are responsible for all your decisions.

## Roadmap
- FIX tool deletion issue in bundle.js ([issue #1](https://github.com/david-prv/scanner-bundle/issues/1))
- FIX tool removal procedure to clear interactions ([issue #2](https://github.com/david-prv/scanner-bundle/issues/2))
- FIX faulty launch issue ([issue #3](https://github.com/david-prv/scanner-bundle/issues/3))
- FIX running alert issue ([issue #4](https://github.com/david-prv/scanner-bundle/issues/4))
- ADD reference removal on tool disintegration ([issue #5](https://github.com/david-prv/scanner-bundle/issues/5))
- ADD anti-virus scan for newly uploaded scanners
- ADD support for more native runners
- ADD support for standard github-master ZIPs as integrables
- ADD snapshot creation tool

## Prerequisites

 - Apache2
 - PHP (>= 8.2.0)
 - Python (>= 3.9.13)
 - Composer (>= 2.1.2)

## Snapshot Structure

```
snapshot.zip/
├── _extra/
│   └── empty
├── _tools/
│   ├── sampleTool
│   │   ├── sampleTool.info
│   │   ├── sampleTool.reference
│   │   ├── sampleTool.schedule
│   │   └── sampleTool.zip
│   └── sampleTool2
│       ├── sampleTool2.info
│       ├── sampleTool2.reference
│       ├── sampleTool2.schedule
│       └── sampleTool2.zip
├── .author
└── .info
```

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
## Firing Up

This application is only meant to be running locally. It's not supposed to be a public accessible application in the web. To run a local instance, you can either use `XAMPP` to run a local web-server, or just launch a PHP development server.
```bash
# in project's root folder...
php -S localhost:8080
```
Now open a web browser and navigate to `http://localhost:8080/`. The scanner-bundle framework should appear.

## Getting Started

Information about getting started will be provided [here](https://wiki.etage-4.de/books/eigenentwicklung/chapter/scanner-bundle). For guests, check out the [wiki](/#).
