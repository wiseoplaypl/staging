<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    Klarna Bank AB www.klarna.com
 * @copyright Copyright (c) permanent, Klarna Bank AB
 * @license   ISC
 *
 * @see       /LICENSE
 *
 * International Registered Trademark & Property of Klarna Bank AB
 */

namespace KlarnaPayment\Module\Core\Account\Provider;

use KlarnaPayment\Module\Core\Account\DTO\ExpressCheckoutAddressData;
use KlarnaPayment\Module\Core\Account\Exception\CouldNotProvideExpressCheckoutAddressData;
use KlarnaPayment\Module\Core\Shared\Repository\CountryRepositoryInterface;
use KlarnaPayment\Module\Core\Shared\Repository\StateRepositoryInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ExpressCheckoutAddressDataProvider
{
    /** @var CountryRepositoryInterface */
    private $countryRepository;
    /** @var StateRepositoryInterface */
    private $stateRepository;

    public function __construct(
        CountryRepositoryInterface $countryRepository,
        StateRepositoryInterface $stateRepository
    ) {
        $this->countryRepository = $countryRepository;
        $this->stateRepository = $stateRepository;
    }

    /**
     * @throws CouldNotProvideExpressCheckoutAddressData
     *
     * @return ExpressCheckoutAddressData
     */
    public function run(string $countryIsoCode, string $stateIsoCode): ExpressCheckoutAddressData
    {
        /** @var ?\Country $country */
        $country = $this->countryRepository->findOneBy([
            'iso_code' => $countryIsoCode,
            'active' => 1,
        ]);

        if (!$country) {
            throw CouldNotProvideExpressCheckoutAddressData::failedToFindCountry($countryIsoCode);
        }

        if (empty($stateIsoCode)) {
            return ExpressCheckoutAddressData::create((int) $country->id, 0);
        }

        /** @var ?\State $state */
        $state = $this->stateRepository->findOneBy([
            'id_country' => $country->id,
            'iso_code' => $stateIsoCode,
            'active' => 1,
        ]);

        if (!$country->contains_states || !$state) {
            return ExpressCheckoutAddressData::create((int) $country->id, 0);
        }

        return ExpressCheckoutAddressData::create(
            (int) $country->id,
            (int) $state->id
        );
    }
}
