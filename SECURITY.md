# Security Policy

## Supported versions

Security fixes are only released for the latest version of Timecrack. Please upgrade to the most recent
[release](https://github.com/alextselegidis/timecrack/releases) before reporting a problem.

## Reporting a vulnerability

**Please do not report security vulnerabilities through public GitHub issues.**

Report them privately instead, either through
[GitHub Security Advisories](https://github.com/alextselegidis/timecrack/security/advisories/new) or by email
to [alextselegidis@gmail.com](mailto:alextselegidis@gmail.com).

Include as much of the following as you can:

* The type of issue and the affected file, route or endpoint.
* The Timecrack version, along with the PHP and MySQL versions you are running.
* Step by step instructions to reproduce the issue, including any proof of concept.
* The impact of the issue and how an attacker could exploit it.

You can expect an acknowledgement within a few days. Once the issue is confirmed a fix is prepared and
released, and your report is credited in the release notes unless you prefer to stay anonymous. Please give
us a reasonable amount of time to publish the fix before disclosing the issue publicly.

## Self hosted installations

Timecrack is self hosted, so the security of an installation also depends on its environment. Keep PHP, MySQL
and the web server up to date, serve the application over HTTPS, set a unique `APP_KEY`, keep `APP_DEBUG` off
and make sure that only the `public` directory is exposed by the web server.
