<?php

/**
 * @category    ScandiPWA
 * @package     ScandiPWA_CustomerDownloadableGraphQl
 * @copyright   Copyright 2019 Adobe. All Rights Reserved.
 * @copyright   Copyright © 2018 Scandiweb, Ltd (https://scandiweb.com)
 * @copyright   Modifications © Selveq. All rights reserved.
 * @license     OSL-3.0 (Open Software License ("OSL") v. 3.0)
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace ScandiPWA\CustomerDownloadableGraphQl\Model\Resolver;

use Magento\Downloadable\Model\Link\Purchased\Item as PurchasedLink;
use Magento\DownloadableGraphQl\Model\ResourceModel\GetPurchasedDownloadableProducts;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\UrlInterface;
use Magento\GraphQl\Model\Query\ContextInterface;

class CustomerDownloadableProducts implements ResolverInterface
{
    /**
     * @param GetPurchasedDownloadableProducts $getPurchasedDownloadableProducts
     * @param UrlInterface $urlBuilder
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        private readonly GetPurchasedDownloadableProducts $getPurchasedDownloadableProducts,
        private readonly UrlInterface $urlBuilder,
        private readonly TimezoneInterface $timezone
    ) {}

    /**
     * {@inheritdoc}
     * @throws GraphQlAuthorizationException
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        /** @var ContextInterface $context */
        if (false === $context->getExtensionAttributes()->getIsCustomer()) {
            throw new GraphQlAuthorizationException(__('The current customer isn\'t authorized.'));
        }

        $purchasedProducts = $this->getPurchasedDownloadableProducts->execute($context->getUserId());
        $productsData = [];

        // the query selects both tables with *, so every key below is a column name and not an API
        foreach ($purchasedProducts as $purchasedProduct) {
            $bought = (int)$purchasedProduct['number_of_downloads_bought'];
            $used = (int)$purchasedProduct['number_of_downloads_used'];
            // a link bought with no download count is unlimited, which core stores as zero
            $isUnlimited = $bought === 0;
            $downloadsRemain = $isUnlimited || $bought - $used > 0;
            $remainingDownloads = $isUnlimited ? __('Unlimited') : $bought - $used;

            $downloadUrl = null;
            if ($downloadsRemain && $purchasedProduct['status'] === PurchasedLink::LINK_STATUS_AVAILABLE) {
                $downloadUrl = $this->urlBuilder->getUrl(
                    'downloadable/download/link',
                    ['id' => $purchasedProduct['link_hash'], '_secure' => true]
                );
            }

            $productsData[] = [
                'order_id' => $purchasedProduct['order_id'],
                'order_increment_id' => $purchasedProduct['order_increment_id'],
                // created_at is stored in UTC, so a purchase after 16:00 reports the previous day on a UTC+8 store
                'date' => $this->timezone->date($purchasedProduct['created_at'])->format('Y-m-d'),
                'status' => $purchasedProduct['status'],
                'title' => $purchasedProduct['product_name'],
                'link_title' => $purchasedProduct['link_title'],
                'download_url' => $downloadUrl,
                'remaining_downloads' => $remainingDownloads
            ];
        }

        return ['items' => $productsData];
    }
}
