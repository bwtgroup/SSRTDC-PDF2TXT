<?php

class PdfDirectoryParser
{
    /**
     * @var null
     */
    private $dir;
    /**
     * @var null
     */
    private $txtDir;
    /**
     * @var null
     */
    private $listPDFs;
    /**
     * PdfDirectoryParser constructor.
     * @param null $dir
     */
    public function __construct($dir = null)
    {
        if(!is_null($dir) && $this->validateDir($dir)) {
            $this->dir = $dir;
        } else {
            $this->dir = null;
        }
    }

    /**
     * Set directory path.
     *
     * @param $dir
     */
    public function setDir($dir)
    {
        $this->dir = $dir;
    }

    /**
     * Create directory for txt files.
     */
    public function createTxtDir()
    {
        if(!is_dir($this->dir . '/txt')) {
            mkdir($this->dir . '/txt');
        }
        $this->txtDir = $this->dir . '/txt';
    }

    /**
     * Check is dir exist.
     *
     * @param $dir
     * @return bool
     */
    public function validateDir($dir)
    {
        return is_dir($dir);
    }

    /**
     *  Get list pdf files from directory.
     */
    public function getListFiles()
    {
        $links = scandir($this->dir);
        $this->listPDFs = [];
        foreach ($links as $link) {
            preg_match_all('/\.pdf/i', $link, $matches);
            if(count($matches[0]) > 0) {
                $this->listPDFs[] = $link;
            }
        }
    }

    /**
     * Run parsing.
     */
    public function run()
    {
        $this->createTxtDir();
        $this->getListFiles();

    }

    /**
     * Parse single pdf file;
     *
     * @param $link
     */
    public function parseFile($link)
    {

    }
}