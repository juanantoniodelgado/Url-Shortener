<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Tuupola\Base62;

/**
 * Class StoredUrl
 * @package AppBundle\Entity
 * @ORM\Entity(repositoryClass="App\Repository\StoredUrlRepository")
 * @ORM\Table(name="stored_url", uniqueConstraints={@UniqueConstraint(name="unique_token", columns={"token"})})
 */
class StoredUrl
{
    /**
     * @var integer $id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Id()
     */
    private $id;

    /**
     * @var string $origin
     * @ORM\Column(name="origin", type="string", length=255)
     */
    private $origin;

    /**
     * @var string $token
     * @ORM\Column(name="token", type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @var boolean $valid
     * @ORM\Column(name="valid", type="boolean")
     */
    private $valid;

    public function __construct(string $origin = null)
    {
        if ($origin !== null) {

            $this->setOrigin($origin);
        }

        $this->setValid(true);
    }

    /**
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * @param string $origin
     */
    public function setOrigin(string $origin)
    {
        $this->origin = $origin;
    }

    /**
     * @return string
     */
    public function getToken() : string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token)
    {
        $this->token = $token;
    }

    /**
     * @return bool
     */
    public function isValid() : bool
    {
        return $this->valid;
    }

    /**
     * @param boolean $valid
     */
    public function setValid(bool $valid)
    {
        $this->valid = $valid;
    }

    /**
     * Generates a new random token from an encryptation of the current timestamp
     */
    public function generateToken()
    {
        $base62 = new Base62();
        list($a, $b) = explode(" ", microtime());

        $this->setToken($base62->encode((float) $a * (float) $b));
    }
}