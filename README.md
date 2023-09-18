<h1 align="center">
 Scanner-Bundle<br/>
 Control Framework
</h1>
<p align="center">
 <a href="https://github.com/david-prv/scanner-bundle" title="Go to GitHub repo"><img src="https://img.shields.io/static/v1?label=david-prv&amp;message=scanner-bundle&amp;color=blue&amp;logo=github" alt="david-prv - scanner-bundle"></a>
 <img src="https://img.shields.io/badge/maintained-yes-blue" alt="maintained - yes">
 <a href="https://github.com/david-prv/scanner-bundle/issues"><img src="https://img.shields.io/github/issues/david-prv/scanner-bundle" alt="issues - scanner-bundle"></a>
 <a href="https://github.com/david-prv/scanner-bundle/actions/workflows/php.yml"><img src="https://github.com/david-prv/scanner-bundle/actions/workflows/php.yml/badge.svg" alt="PHP Composer"></a>
</p>
<p align="center">
 A small framework to run open-source tools that inspect and scan any kind of webpages for vulnerabilities.<br>
</p>

![image](https://github.com/david-prv/scanner-bundle/assets/66866223/9625b036-4691-431d-9568-1cfb5ef8189b)

## Disclaimer

This tool is not intended to be used for any criminal act. It's used to find vulnerabilites on websites whose owners
have given me express permission to do so! You are responsible for all your decisions.

## Roadmap

- ADD automated requirement installation to integration bot *(wip...)*
- ADD support for more native runners *(wip...)*
- ADD advanced search params (e.g. `not:wordpress`, `engine:php`)
- ADD antivirus scan for newly uploaded scanners
- ADD AI-assistant for report analysis

*Don't forget issues, that should be
re-opened: [label:willreopen](https://github.com/david-prv/scanner-bundle/issues?q=is%3Aissue+label%3Awillreopen)*

## Requirements

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

## Firing Up & Getting Started

This application is only meant to be running locally. It's not supposed to be a public accessible application in the
web. To run a local instance, you can either use `XAMPP` to run a local web-server, or just launch a PHP development
server.

```bash
# in project's root folder...
php -S localhost:8080
```

Now open a web browser and navigate to `http://localhost:8080/`. The scanner-bundle framework should appear.

Information about what to do next will be
provided [here](https://wiki.etage-4.de/books/eigenentwicklung/chapter/scanner-bundle). For guests, check out
the [github wiki](/#).

## License

Released under [MIT](/LICENSE) by [@david-prv](https://github.com/david-prv).
