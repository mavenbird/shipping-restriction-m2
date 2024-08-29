<?php
/**
 * Mavenbird Technologies Private Limited
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://mavenbird.com/Mavenbird-Module-License.txt
 *
 * =================================================================
 *
 * @category   Mavenbird
 * @package    Mavenbird_Shiprestriction
 * @author     Mavenbird Team
 * @copyright  Copyright (c) 2018-2024 Mavenbird Technologies Private Limited ( http://mavenbird.com )
 * @license    http://mavenbird.com/Mavenbird-Module-License.txt
 */

declare(strict_types=1);

namespace Mavenbird\Shiprestriction\Model;

class LinkValidator
{
    public const ALLOWED_DOMAINS = [
        'mavenbird.com',
        'marketplace.magento.com'
    ];

    /**
     * Validate
     *
     * @param string $link
     *
     * @return bool
     */
    public function validate(string $link): bool
    {
        if (! (string) $link) { // fix for xml object
            return true;
        }

        foreach (static::ALLOWED_DOMAINS as $allowedDomain) {
            if (preg_match('/^http[s]?:\/\/' . $allowedDomain . '\/.*$/', $link) === 1) {
                return true;
            }
        }

        return false;
    }
}
