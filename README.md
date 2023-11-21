<h1 align="center">
 Overmind<br/>
 Control Framework
</h1>
<p align="center">
 <a href="https://github.com/david-prv/scanner-bundle" title="Go to GitHub repo"><img src="https://img.shields.io/static/v1?label=david-prv&amp;message=scanner-bundle&amp;color=blue&amp;logo=github" alt="david-prv - scanner-bundle"></a>
 <img src="https://img.shields.io/badge/maintained-yes-blue" alt="maintained - yes">
 <a href="https://github.com/david-prv/scanner-bundle/issues"><img src="https://img.shields.io/github/issues/david-prv/scanner-bundle" alt="issues - scanner-bundle"></a>
 <a href="https://github.com/david-prv/scanner-bundle/actions/workflows/php.yml"><img src="https://github.com/david-prv/scanner-bundle/actions/workflows/php.yml/badge.svg" alt="PHP Composer"></a>
</p>
<p align="center">
 A small framework capable of running open-source vulnerability scanners to inspect and scan any kind of webpage<br>
</p>

![image](https://github.com/david-prv/scanner-bundle/assets/66866223/3de178e7-37d9-4a07-9a83-0e17fb38caad)
![image](https://github.com/david-prv/scanner-bundle/assets/66866223/40231ffe-5ab4-4371-9ffb-f220fc977138)

## Disclaimer

This tool is not intended to be used for any criminal act. It's used to find vulnerabilites on websites whose owners
have given me express permission to do so! You are responsible for all your decisions.

## Roadmap

- FIX final report layout
- ADD sonar mode
- ADD website auto-detection (selects e.g. "wordpress" automatically)
- ADD more dependencies (components) for single pages
- ADD customization option for engines (add own runners with corresponding requirements)
- ADD json-import for engines
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

Now open a web browser and navigate to `http://localhost:8080/`. The Overmind framework should appear.

Information about what to do next will be
provided [here](https://wiki.etage-4.de/books/eigenentwicklung/chapter/scanner-bundle). For guests, check out
the [github wiki](/#).

## License

Released under [GPL](/LICENSE) by [@david-prv](https://github.com/david-prv).  

![image](https://github.com/david-prv/scanner-bundle/assets/66866223/385b8bb1-4dc1-48f9-bfc7-e58be51823f1)
