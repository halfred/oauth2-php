<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Extension;

use Pantarei\OAuth2\Exception\InvalidRequestException;
use Pantarei\OAuth2\Exception\UnsupportedGrantTypeException;
use Pantarei\OAuth2\OAuth2TypeInterface;
use Pantarei\OAuth2\Util\CredentialUtils;
use Pantarei\OAuth2\Util\ParameterUtils;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Defines the abstract class for grant type.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
abstract class GrantType implements OAuth2TypeInterface
{
  protected $client_id = '';

  protected $username = '';

  protected $scope = array();

  public function setClientId($client_id)
  {
    $this->client_id = $client_id;
    return $this;
  }

  public function getClientId()
  {
    return $this->client_id;
  }

  public function setUsername($username)
  {
    $this->username = $username;
    return $this;
  }

  public function getUsername()
  {
    return $this->username;
  }

  public function setScope($scope)
  {
    $this->scope = $scope;
    return $this;
  }

  public function getScope()
  {
    return $this->scope;
  }

  public static function getType(Request $request, Application $app)
  {
    $get = $request->query->all();
    $post = $request->request->all();

    $filtered_get = ParameterUtils::filter($get);
    $filtered_post = ParameterUtils::filter($post);

    // Shouldn't provide data from both GET and POST.
    if (!empty($filtered_get) && !empty($filtered_post)) {
      throw new InvalidRequestException();
    }

    // If input format invalid we should stop here.
    if (empty($filtered_post) || $filtered_post != $post) {
      throw new InvalidRequestException();
    }

    // grant_type is required.
    if (!isset($filtered_post['grant_type'])) {
      throw new InvalidRequestException();
    }
    $grant_type = $filtered_post['grant_type'];

    // Check if grant_type is supported.
    if (!isset($app['oauth2.token.options']['grant_type'][$grant_type])) {
      throw new UnsupportedGrantTypeException();
    }

    // Validate client_id.
    CredentialUtils::check($request, $app);

    // Create and return the token type.
    $namespace = $app['oauth2.token.options']['grant_type'][$grant_type];
    return $namespace::create($request, $app);
  }
}
