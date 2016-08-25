<?php

namespace Mongopper\Auth;

use Doctrine\ODM\MongoDB\DocumentManager;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Support\Str;

/**
 * Created by PhpStorm.
 * User: kz
 * Date: 8/23/16
 * Time: 2:57 PM
 */
class MongoUserProvider implements UserProvider
{
    /**
     * @var DocumentManager
     */
    private $dm;
    /**
     * @var HasherContract
     */
    private $hasher;
    /**
     * @var
     */
    private $model;

    /**
     * MongoUserProvider constructor.
     *
     * @param DocumentManager $dm
     * @param HasherContract  $hasher
     * @param                 $model
     */
    public function __construct(DocumentManager $dm, HasherContract $hasher, $model)
    {
        $this->dm = $dm;
        $this->hasher = $hasher;
        $this->model = $model;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed $identifier
     *
     * @return Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        return $this->dm->getRepository($this->model)->find($identifier);
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed  $identifier
     * @param  string $token
     *
     * @return Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        return $this->dm->getRepository($this->model)->findOneBy(
            [
                'id'             => $identifier,
                'remember_token' => $token,
            ]
        );
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  Authenticatable $user
     * @param  string          $token
     *
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        $this->dm->createQueryBuilder($this->model)
            ->findAndUpdate()
            ->field($user->getAuthIdentifierName())->equals($user->getAuthIdentifier())
            ->field($user->getRememberTokenName())->set($token)
            ->getQuery()
            ->execute();
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     *
     * @return Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials)) {
            return null;
        }

        $query = $this->dm->getRepository($this->model)->createQueryBuilder();

        foreach ($credentials as $key => $value) {
            if (! Str::contains($key, 'password')) {
                $query->field($key)->equals($value);
            }
        }

        return $query->limit(1)->getQuery()->getSingleResult();
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  Authenticatable $user
     * @param  array           $credentials
     *
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $plain = $credentials['password'];

        return $this->hasher->check($plain, $user->getAuthPassword());
    }
}