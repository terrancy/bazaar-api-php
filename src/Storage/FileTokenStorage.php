<?php
namespace Nikapps\BazaarApi\Storage;

use Nikapps\BazaarApi\Models\Token;

class FileTokenStorage implements TokenStorageInterface
{
    /**
     * @var string
     */
    private $path;

    /**
     * FileTokenStorage constructor.
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }


    public function retrieve()
    {
        if (!is_null($data = $this->read())) {
            return $data['token'];
        }

        return null;

    }

    public function expired()
    {
        if (!is_null($data = $this->read())) {
            return $data['expireTime'] < time();
        }

        return true;
    }

    protected function read()
    {
        $data = json_decode(file_get_contents($this->path), true);

        if (empty($data) || !isset($data['token'], $data['expireTime'])) {
            return null;
        }

        return $data;
    }

    /**
     * @param Token $token
     */
    public function save(Token $token)
    {
        $expireTime = time() + $token->lifetime();

        $data = json_encode([
            'expireTime' => $expireTime,
            'token' => $token->accessToken()
        ]);

        file_put_contents($this->path, $data);
    }
}