<?php

namespace PiePHP\Console;

use GuzzleHttp\Client;
use RuntimeException;
use ZipArchive;

class Installer
{
    private $zipFile;
    private $application;

    public function __construct($name)
    {
        if (!$name) {
            throw new RuntimeException('Application name is not specified.');
        }

        $this->zipFile = getcwd().'/piephp_'.md5(time().uniqid()).'.zip';
        $this->application = getcwd().'/'.$name;
    }

    public function download()
    {
        $response = (new Client())->get('https://github.com/LeNiglo/PiePHP-template/raw/master/build/piephp.zip');
        file_put_contents($this->zipFile, $response->getBody());

        return $this;
    }

    public function extract()
    {
        $archive = new ZipArchive();
        $response = $archive->open($this->zipFile, ZipArchive::CHECKCONS);

        if ($response === ZipArchive::ER_NOZIP) {
            throw new RuntimeException('The zip file could not be downloaded.');
        }

        $archive->extractTo($this->application);
        $archive->close();

        return $this;
    }

    public function cleanup()
    {
        chmod($this->zipFile, 0777);
        unlink($this->zipFile);

        return $this;
    }
}
