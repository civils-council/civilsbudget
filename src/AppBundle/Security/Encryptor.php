<?php

namespace AppBundle\Security;

class Encryptor
{
    /**
     * @var resource
     */
    protected $privateKey;

    /**
     * @var resource
     */
    protected $publicKey;

    public function setPrivateKey($key)
    {
        $content = $this->getFileContent($key);
        $this->privateKey = openssl_get_privatekey($content);

        if (false === $this->privateKey)
        {
            throw new \Exception('Can\'t create private key resource');
        }
    }

    public function setPublicKey($key)
    {
        $this->publicKey = openssl_pkey_get_public(file_get_contents($key));

        if (false === $this->publicKey)
        {
            throw new \Exception('Can\'t create public key resource');
        }
    }

    public function decrypt($value)
    {
        if (!$this->privateKey) {
            throw new \Exception("You need to set private key for decrypt");
        }

        openssl_private_decrypt($value, $result, $this->privateKey);

        return $result;
    }

    public function encrypt($value)
    {
        if (!$this->publicKey) {
            throw new \Exception("You need to set public key for encrypt");
        }

        openssl_public_encrypt($value, $result, $this->publicKey);

        return $result;
    }

    /**
     * @param string $file Filename
     * @return string
     * @throws \Exception
     */
    private function getFileContent($file)
    {
        try {
            $fp = fopen($file, "r");
            $content = fread($fp,8192);
            fclose($fp);
        } catch (\Exception $e) {
            throw new \Exception('Can\'t read file '.$file);
        }

        return $content;
    }
}
