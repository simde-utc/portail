<?php
/**
 * Indicates that a model can create notifications.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Interfaces\Model;

interface CanNotify
{
    /**
     * @return string|null
     */
    public function getName(): ?string;
}
