# Scanner-Bundle: Control Framework
[![david-prv - scanner-bundle](https://img.shields.io/static/v1?label=david-prv&message=scanner-bundle&color=blue&logo=github)](https://github.com/david-prv/scanner-bundle "Go to GitHub repo")
![maintained - yes](https://img.shields.io/badge/maintained-yes-blue)
[![issues - scanner-bundle](https://img.shields.io/github/issues/david-prv/scanner-bundle)](https://github.com/david-prv/scanner-bundle/issues)
[![PHP Composer](https://github.com/david-prv/scanner-bundle/actions/workflows/php.yml/badge.svg)](https://github.com/david-prv/scanner-bundle/actions/workflows/php.yml)

A small framework to run open-source tools that inspect and scan any kind of webpages for vulnerabilities.  

**Disclaimer**: This tool is not intended to be used for any criminal act. It's used to find vulnerabilites on websites whose owners have given me express permission to do so! You are responsible for all your decisions.

## 🧪 Roadmap
- FIX tool deletion issue in bundle.js ([issue #1](https://github.com/david-prv/scanner-bundle/issues/1))
- FIX faulty launch issue ([issue #3](https://github.com/david-prv/scanner-bundle/issues/3))
- FIX integration bot namespace issue ([issue #7](https://github.com/david-prv/scanner-bundle/issues/7))
- ADD anti-virus scan for newly uploaded scanners
- ADD support for more native runners
- ADD support for standard github-master ZIPs as integrables
- ADD report removal after successful finished process (mitigates weird results)

## 📋 Requirements

 - Apache2
 - PHP (>= 8.2.0)
 - Python (>= 3.9.13)
 - Composer (>= 2.1.2)

## 📦 Snapshot Structure

```
snapshot.zip/
├── _extra/
│   └── empty
├── _tools/
│   ├── sample
│   │   ├── sample.info
│   │   ├── sample.reference
│   │   ├── sample.schedule
│   │   └── sample.zip
│   └── sample2
│       ├── sample2.info
│       ├── sample2.reference
│       ├── sample2.schedule
│       └── sample2.zip
├── .author
└── .info
```

## 📥 Installation
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
## 🔥 Firing Up & Getting Started

This application is only meant to be running locally. It's not supposed to be a public accessible application in the web. To run a local instance, you can either use `XAMPP` to run a local web-server, or just launch a PHP development server.
```bash
# in project's root folder...
php -S localhost:8080
```
Now open a web browser and navigate to `http://localhost:8080/`. The scanner-bundle framework should appear.

Information about what to do next will be provided [here](https://wiki.etage-4.de/books/eigenentwicklung/chapter/scanner-bundle). For guests, check out the [github wiki](/#).

## 📝 License

Released under [MIT](/LICENSE) by [@david-prv](https://github.com/david-prv).
