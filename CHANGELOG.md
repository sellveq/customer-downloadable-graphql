# Changelog

## 2.0.0

Forked from `scandipwa/customer-downloadable-graphql` 1.0.5. Module name and namespace are unchanged, and the package replaces `scandipwa/customer-downloadable-graphql` at every version, so it installs as a drop-in replacement.

- Magento 2.4.9 and PHP 8.3 support.
- Download URLs reach the download controller again: the `/downloadable/download/` bypass is declared where Magento reads it, in frontend scope, where before every download URL was answered by the app shell.
- A purchased file is served only while its link is available, so an order that is unpaid, under fraud review or refunded no longer downloads.
- The purchase date is rendered in the store's timezone instead of the raw UTC date, so a late-evening purchase no longer reports the previous day.
- A refused download lands on the account's downloads page instead of an empty route.
- The owner check compares customer ids as integers, so a customer is never refused their own file over a type difference.
