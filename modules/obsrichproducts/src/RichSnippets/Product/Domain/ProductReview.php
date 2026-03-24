<?php
/**
 * 2011-2025 OBSOLUTIONS WD S.L. All Rights Reserved.
 *
 * NOTICE:  All information contained herein is, and remains
 * the property of OBSOLUTIONS WD S.L. and its suppliers,
 * if any.  The intellectual and technical concepts contained
 * herein are proprietary to OBSOLUTIONS WD S.L.
 * and its suppliers and are protected by trade secret or copyright law.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from OBSOLUTIONS WD S.L.
 *
 *  @author    OBSOLUTIONS WD S.L. <http://addons.prestashop.com/en/65_obs-solutions>
 *  @copyright 2011-2025 OBSOLUTIONS WD S.L.
 *  @license   OBSOLUTIONS WD S.L. All Rights Reserved
 *  International Registered Trademark & Property of OBSOLUTIONS WD S.L.
 */

namespace OBSolutions\RichSnippets\Product\Domain;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ProductReview
{
    private $content;
    private $dateAdd;
    private $title;
    private $rating;
    private $author;

    public function __construct($content, $dateAdd, $title, $rating, $author)
    {
        // REMOVE addslashes
        $content = preg_replace("/\\\\'/", "'", $content);
        // REMOVE ALL HTML TAGS
        $content = strip_tags($content);

        // DECODE HTML CHARACTERS
        $content = html_entity_decode($content);

        // REMOVE QUOTES
        $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');

        // REMOVE EXTRA SPACES
        $this->content = trim($content);
        $this->dateAdd = $dateAdd;
        $this->title = $title;
        $this->rating = $rating;
        $this->author = $author;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getDateAdd()
    {
        return $this->dateAdd;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getRating()
    {
        return $this->rating;
    }

    public function getAuthor()
    {
        return $this->author;
    }
}
