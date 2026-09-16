# ScandiPWA CustomerDownloadableGraphQl

Fork of [scandipwa/customer-downloadable-graphql](https://github.com/scandipwa/customer-downloadable-graphql) 1.0.5, maintained by Selveq for Magento 2.4.9 and PHP 8.3. Module name and namespace are unchanged, and the package replaces `scandipwa/customer-downloadable-graphql` at every version, so it installs as a drop-in replacement. Selveq is not affiliated with or endorsed by Scandiweb.

## What it does

- Lists a customer's purchased downloadable links through `customerDownloadableProducts`, with the order, the purchase date in the store's timezone, the status, the downloads remaining and a download URL while the link is available and downloads remain.
- Adds the fields the account page reads beyond core's: `title`, `link_title` and `order_id`.
- Serves a purchased file to its owner and refuses guests and other customers, sending every refusal to the account's downloads page.
- Declares the `/downloadable/download/` bypass in frontend scope, so the file answers the request instead of the app shell.

## Install

```sh
composer require selveq/customer-downloadable-graphql
bin/magento setup:upgrade
```

A module adding its own `ignoredURLs` bypass must declare it in `etc/frontend/di.xml`, because a frontend-scope declaration replaces the global list.

## License

[OSL-3.0](LICENSE), the license of the original work. Scandiweb's copyright notices are kept in every file, and each file Selveq changed carries a `Modifications © Selveq` notice.
