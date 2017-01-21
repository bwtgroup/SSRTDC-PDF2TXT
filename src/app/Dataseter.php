<?php


class Dataseter
{
    private $pfolder;
    private $dfolder;
    private $order;
    private $incrsize;
    private $fMap;

    /**
     * Dataseter constructor.
     * @param $pfolder
     * @param $dfolder
     * @param $order
     * @param $incrsize
     */
    public function __construct($pfolder, $dfolder, $order, $incrsize)
    {

        $this->pfolder = $pfolder;
        $this->dfolder = $dfolder;
        $this->order = $order;
        $this->incrsize = $incrsize;
    }

    /**
     *
     */
    public function run()
    {
        $this->getFilesMap();
        $this->order();
        $this->saveToFiles();
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
            $contentCurr .= $fContent . PHP_EOL . PHP_EOL;
            if ($j == $this->incrsize) {
                $i++;
                $j = 0;
                $contentAll .= $contentCurr;
                $contentCurr = '';
                file_put_contents($this->dfolder . '/D' . $i . '.txt', print_r($contentAll, true));
            }
        }

        if ($contentCurr != '') {
            $i++;
            $j = 0;
            $contentAll .= $contentCurr;
            $contentCurr = '';
            file_put_contents($this->dfolder . '/D' . $i . '.txt', print_r($contentAll, true));
        }
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
}