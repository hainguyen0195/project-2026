<?php
class PaginationsAjax
{
    public $perpage;

    function __construct()
    {
        $this->perpage = 1;
    }

    function getAllPageLinks($count, $href, $elShow)
    {
        $output = '';

        if (empty($_GET["p"])) $_GET["p"] = 1;

        if ($this->perpage != 0)
            $pages = ceil($count / $this->perpage);

        if ($pages > 1) {
            if ($_GET["p"] == 1)
                $output = $output . '<span class="first disabled">First</span><span class="prev disabled">Prev</span>';
            else
                $output = $output . '<span class="first" onclick="loadPaging(\'' . $href . (1) . '\',\'' . $elShow . '\')" >First</span><span class="prev" onclick="loadPaging(\'' . $href . ($_GET["p"] - 1) . '\',\'' . $elShow . '\')" >Prev</span>';

            if (($_GET["p"] - 3) > 0) {
                if ($_GET["p"] == 1)
                    $output = $output . '<span id=1 class="current">1</span>';
                else
                    $output = $output . '<span onclick="loadPaging(\'' . $href . '1\',\'' . $elShow . '\')" >1</span>';
            }
            if (($_GET["p"] - 3) > 1) {
                $output = $output . '<span class="dot">...</span>';
            }

            for ($i = ($_GET["p"] - 2); $i <= ($_GET["p"] + 2); $i++) {
                if ($i < 1) continue;
                if ($i > $pages) break;
                if ($_GET["p"] == $i)
                    $output = $output . '<span id=' . $i . ' class="current">' . $i . '</span>';
                else
                    $output = $output . '<span onclick="loadPaging(\'' . $href . $i . '\',\'' . $elShow . '\')" >' . $i . '</span>';
            }

            if (($pages - ($_GET["p"] + 2)) > 1) {
                $output = $output . '<span class="dot">...</span>';
            }
            if (($pages - ($_GET["p"] + 2)) > 0) {
                if ($_GET["p"] == $pages)
                    $output = $output . '<span id=' . ($pages) . ' class="current">' . ($pages) . '</span>';
                else
                    $output = $output . '<span onclick="loadPaging(\'' . $href .  ($pages) . '\',\'' . $elShow . '\')" >' . ($pages) . '</span>';
            }

            if ($_GET["p"] < $pages)
                $output = $output . '<span class="next" onclick="loadPaging(\'' . $href . ($_GET["p"] + 1) . '\',\'' . $elShow . '\')" >Next</span><span class="last" onclick="loadPaging(\'' . $href . ($pages) . '\',\'' . $elShow . '\')" >Last</span>';
            else
                $output = $output . '<span class="next disabled">Next</span><span class="last disabled">Last</span>';
        }

        return $output;
    }

    function getPrevNext($count, $href, $elShow)
    {
        $output = '';

        if (empty($_GET["p"])) $_GET["p"] = 1;

        if ($this->perpage != 0)
            $pages  = ceil($count / $this->perpage);

        if ($pages > 1) {
            if ($_GET["p"] == 1)
                $output = $output . '<span class="prev disabled">Prev</span>';
            else
                $output = $output . '<span class="prev" onclick="loadPaging(\'' . $href . ($_GET["p"] - 1) . '\',\'' . $elShow . '\')" >Prev</span>';

            if ($_GET["p"] < $pages)
                $output = $output . '<span class="next" onclick="loadPaging(\'' . $href . ($_GET["p"] + 1) . '\',\'' . $elShow . '\')" >Next</span>';
            else
                $output = $output . '<span class="next disabled">Next</span>';
        }

        return $output;
    }
}
