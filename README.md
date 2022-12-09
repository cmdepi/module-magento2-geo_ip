[![Bina Commerce](https://binacommerce.com/media/repo/banner-readme.png)](https://binacommerce.com/)

# About the project

A [Magento 2](https://business.adobe.com/products/magento/magento-commerce.html) implementation for my [Bina Commerce](https://binacommerce.com/) brand.

This little development allows the redirection of the customers to the respective version of your store considering its geolocalization.

> **Warning**
> To be able to use this implementation, it is necessary that your store is integrated with [Cloudflare](https://www.cloudflare.com/) using the [IP geolocation feature](https://support.cloudflare.com/hc/en-us/articles/200168236-Configuring-IP-geolocation).

## Installation

To proceed with the installation of your module, you need to go to the [document root of your Magento installation](https://experienceleague.adobe.com/docs/commerce-operations/installation-guide/tutorials/docroot.html) and run the following commands:

```
composer require bina/module-popup
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy
bin/magento cache:clean
bin/magento cache:flush
```
> **Note**
> To execute `bin/magento setup:static-content:deploy` you need to list all the languages of your store/s using the ISO-639 format. Also, if you are using a platform mode different than production, you need to execute the command using the `-f` flag: `bin/magento setup:static-content:deploy -f` (https://devdocs.magento.com/guides/v2.4/config-guide/cli/config-cli-subcommands-static-view.html).

> **Note**
> The command `bin/magento cache:flush` is optional (https://devdocs.magento.com/guides/v2.4/config-guide/cli/config-cli-subcommands-cache.html#config-cli-subcommands-cache-clean).

## Usage

In order to find documentation on the use and related issues, visit the [official page](https://binacommerce.com/popup.html) of this development.

## Author

- [LinkedIn](https://www.linkedin.com/in/cristian-marcelo-de-picciotto/) 
