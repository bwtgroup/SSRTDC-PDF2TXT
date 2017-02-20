<?php


class Dataseter
{
    private $pfolder;
    private $dfolder;
    private $order;
    private $incrsize;
    private $fMap;
    /**
     * @var string
     */
    private $saveType;

    /**
     * Dataseter constructor.
     * @param $pfolder
     * @param $dfolder
     * @param $order
     * @param $incrsize
     * @param string $saveType
     */
    public function __construct($pfolder, $dfolder, $order, $incrsize, $saveType = 'file')
    {

        $this->pfolder = $pfolder;
        $this->dfolder = $dfolder;
        $this->order = $order;
        $this->incrsize = $incrsize;
        $this->saveType = $saveType;
    }

    /**
     *
     */
    public function run()
    {
        $this->getFilesMap();
        $this->order();
        $this->save();
    }

    /**
     *
     */
    public function getFilesMap()
    {
        $fMap = [];

        $links = scandir($this->pfolder);

        foreach ($links as $link) {
            preg_match_all('/\.txt$/i', $link, $matches);
            if (count($matches[0]) > 0) {
                $fName = $link;
                preg_match_all('/\w-(\d{4})-\d*\((\d*)\)-/', $link, $matches2);

                $fMap[$fName] = ['year' => (int)$matches2[1][0], 'jurnalNumber' => (int)$matches2[2][0]];
            }
        }

        $this->fMap = $fMap;
    }

    /**
     *
     */
    public function order()
    {
        switch ($this->order) {
            case "chrono":
                uasort($this->fMap, function ($a, $b) {
                    if ($a['year'] == $b['year']) {
                        return $a['jurnalNumber'] - $b['jurnalNumber'];
                    }

                    return strcmp($a['year'], $b['year']);
                });
                break;
            case "rev-chrono":
                uasort($this->fMap, function ($a, $b) {
                    if ($a['year'] == $b['year']) {
                        return $b['jurnalNumber'] - $a['jurnalNumber'];
                    }

                    return strcmp($b['year'], $a['year']);
                });
                break;
            case "bi-dir":
                uasort($this->fMap, function ($a, $b) {
                    if ($a['year'] == $b['year']) {
                        return $a['jurnalNumber'] - $b['jurnalNumber'];
                    }

                    return strcmp($a['year'], $b['year']);
                });
                $this->fMap = $this->sortBiDir($this->fMap);
                break;
            case "random":
                $this->shuffleAssoc($this->fMap);
                break;
        }
    }

    /**
     * @param $array
     * @return array
     */
    public function sortBiDir($array)
    {
        $result = [];
        $arr = $array;
        while (count($arr) > 0) {
            $result[] = array_shift($arr);
            if (count($arr) > 0) {
                $result[] = array_pop($arr);
            }
        }

        return $result;
    }

    /**
     *
     */
    public function saveToFiles()
    {
        $contentAll = '';
        $contentCurr = '';
        $fMap = $this->fMap;
        $i = 0;
        $j = 0;
        while (count($fMap) > 0) {

            $fName = array_keys($fMap)[0];
            $item = array_shift($fMap);
            $j++;
            $fContent = file_get_contents($this->pfolder . '/' . $fName);
            $fContent = $this->refactorText($fContent);
            $contentCurr .= $fContent . PHP_EOL . PHP_EOL;
            if ($j == $this->incrsize) {
                $i++;
                $j = 0;
                $contentAll = $contentCurr;
                $contentCurr = '';
                if($i>1) {
                    copy($this->dfolder . '/D' . ($i - 1) . '.txt', $this->dfolder . '/D' . $i . '.txt');
                }
                file_put_contents($this->dfolder . '/D' . $i . '.txt', print_r($contentAll, true), FILE_APPEND);
            }
        }

        if ($contentCurr != '') {
            $i++;
            $j = 0;
            $contentAll = $contentCurr;
            $contentCurr = '';
            if($i>1) {
                copy($this->dfolder . '/D' . ($i - 1) . '.txt', $this->dfolder . '/D' . $i . '.txt');
            }
            file_put_contents($this->dfolder . '/D' . $i . '.txt', print_r($contentAll, true), FILE_APPEND);
        }
    }

    public function save()
    {
        switch ($this->saveType) {
            case 'file':
                $this->saveToFiles();
                break;
            case 'dir':
                $this->saveToDirectories();
                break;
            default:
                $this->saveToFiles();
                break;
        }
    }
    public function saveToDirectories()
    {
        $fMap = $this->fMap;
        $i = 1;
        @mkdir($this->dfolder . '/D' . $i);
        $j = 0;
        $total = count($fMap);
        while (count($fMap) > 0) {
            $fName = array_keys($fMap)[0];
            $fItem = array_shift($fMap);
            copy($this->pfolder . '/' . $fName, $this->dfolder . '/D' . $i . '/' . $fName);
            $j++;
            if ($j == $this->incrsize) {
                $i++;
                $j = 0;
                if($i>1) {
                    $this->recurse_copy($this->dfolder . '/D' . ($i - 1), $this->dfolder . '/D' . $i );
                }
                echo ($total - count($fMap)) . '/' . $total . PHP_EOL;
            }
        }
        echo ($total - count($fMap)) . '/' . $total . PHP_EOL;
    }

    public function recurse_copy($src,$dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($src . '/' . $file) ) {
                    $this->recurse_copy($src . '/' . $file,$dst . '/' . $file);
                }
                else {
                    copy($src . '/' . $file,$dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    /**
     * @param $array
     * @return bool
     */
    public function shuffleAssoc(&$array)
    {
        $keys = array_keys($array);

        shuffle($keys);
        foreach ($keys as $key) {
            $new[$key] = $array[$key];
        }

        $array = $new;

        return true;
    }

    public function refactorText($content)
    {
        $content = preg_replace('/\r\n/', ' ', $content);
        $content = preg_replace('/\n/', ' ', $content);
        $content = preg_replace('/\. /', '.'.PHP_EOL, $content);


        return $content;
    }
}