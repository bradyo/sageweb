<?php
/**
 * NP-Gravatar
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is
 * bundled with this package in the file LICENSE.txt.
 */

require_once 'NP/Service/Gravatar/Profiles/ResponseFormat/Abstract.php';

/**
 * QR Code response format.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */
class NP_Service_Gravatar_Profiles_ResponseFormat_QRCode extends NP_Service_Gravatar_Profiles_ResponseFormat_Abstract
{
    protected $_id = 'qr';
}