<?php

use Smalot\PdfParser\Parser;

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
     * @var null
     */
    private $smalotPdfParser;
    /**
     * @var null
     */
    private $log;
    /**
     * @var null
     */
    private $logIterator;
    /**
     * PdfDirectoryParser constructor.
     * @param null $dir
     * @param bool $log
     */
    public function __construct($dir = null, $log = false)
    {
        ini_set('memory_limit', '-1');

        if(!is_null($dir) && $this->validateDir($dir)) {
            $this->dir = preg_replace('/\/$/i', '', $dir);
        } else {
            $this->dir = null;
        }
        $this->smalotPdfParser = new Parser();
        $this->log = $log;
        $this->logIterator = 0;
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
     * Create directory for txt files.
     */
    public function createLogDir()
    {
        if(!is_dir($this->dir . '/logs')) {
            mkdir($this->dir . '/logs');
        }
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
            preg_match_all('/\.pdf$/i', $link, $matches);
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
        if($this->log) {
            $this->createLogDir();

        }
        $this->getListFiles();

        foreach ($this->listPDFs as $pdfFile) {
            $result = 'OK';
            try {
                $this->parseFile($pdfFile);
            } catch (\Exception $e) {
                $result = 'FAIL';
            }

            if($this->log) {
                echo ++$this->logIterator . ') ' . $pdfFile . '  ' .$result. PHP_EOL;
                if($result == 'OK') {
                    file_put_contents($this->dir . '/logs/logs.txt', print_r(++$this->logIterator . ') ' . $pdfFile, true), FILE_APPEND);
                } else {
                    file_put_contents($this->dir . '/logs/errors.txt', print_r(++$this->logIterator . ') ' . $pdfFile, true), FILE_APPEND);
                }
            }
        }

        return $this->logIterator;
    }

    /**
     * Parse single pdf file;
     *
     * @param $link
     */
    public function parseFile($link)
    {
        $fullPdfLink = $this->dir . '/' . $link;
        $newFileName = $this->dir . '/txt/' . preg_replace('/\.pdf$/i','.txt', $link);
        $content = $this->smalotPdfParser->parseFile($fullPdfLink)->getText();

        $content = mb_convert_encoding($content, "ASCII", "auto");
        $content = $this->deleteLineWrapping($content);
        file_put_contents($newFileName, $content);
    }

    /**
     * @param $text
     * @return mixed
     */
    public function deleteLineWrapping($text)
    {
        $text = preg_replace('/(\w)-\n/m', '$1', $text);

        return $text;
    }
}