<?php

/**
 * This file is part of the authbucket/oauth2-php package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AuthBucket\OAuth2\Symfony\Component\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Regex;

/**
 * @Annotation
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ClientSecret extends Regex
{
    public function __construct($options = null)
    {
        return parent::__construct(array_merge([
            'message' => 'This is not a valid client_secret.',
            'pattern' => '/^([\x20-\x7E]*)$/',
        ], (array) $options));
    }
}
