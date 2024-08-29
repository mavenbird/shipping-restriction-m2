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


namespace Mavenbird\Shiprestriction\Model\Import;

class ImportCounter
{
    /**
     * @var int
     */
    private $created = 0;

    /**
     * @var int
     */
    private $updated = 0;

    /**
     * @var int
     */
    private $deleted = 0;

    /**
     * Increment the count of created items.
     *
     * @param int $incrementOn Number to increment by (default: 1).
     */
    public function incrementCreated($incrementOn = 1)
    {
        $this->created += (int)$incrementOn;
    }

    /**
     * Increment the count of updated items.
     *
     * @param int $incrementOn Number to increment by (default: 1).
     */
    public function incrementUpdated($incrementOn = 1)
    {
        $this->updated += (int)$incrementOn;
    }
    
    /**
     * Increment the count of deleted items.
     *
     * @param int $incrementOn Number to increment by (default: 1).
     */
    public function incrementDeleted($incrementOn = 1)
    {
        $this->deleted += (int)$incrementOn;
    }

    /**
     * Get Created Count
     *
     * @return int
     */
    public function getCreatedCount()
    {
        return $this->created;
    }

    /**
     * Get Update count
     *
     * @return int
     */
    public function getUpdatedCount()
    {
        return $this->updated;
    }

    /**
     * Get delete count
     *
     * @return int
     */
    public function getDeletedCount()
    {
        return $this->updated;
    }
}
