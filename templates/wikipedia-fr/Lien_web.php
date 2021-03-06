<?php
setlocale(LC_TIME, 'fr_FR.UTF-8');

class LienWebTemplate extends Template {
    public $author;
    public $coauthors;
    public $url;
    public $title;
    public $dd;
    public $mm;
    public $yyyy;
    public $site;
    public $pageDate = null;
    public $accessdate;
    public $antiAdBlocker;
    public $paywall;

    /**
     * @var bool Indicates if we've to remove jour/mois/année parameters
     */
    public $skipYMD = false;

    /**
     * @var bool Indicates if we've to remove jour/mois parameters but maybe keep année
     */
    public $skipMD = false;

    /**
     * @var bool Indicates if we've to remove auteur and coauteurs parameters
     */
    public $skipAuthor = false;

    function __construct () {
        $this->name = "Lien web";
        $this->accessdate = trim(strftime(LONG_DATE_FORMAT));
    }

    static function loadFromPage ($page) {
        $template = new LienWebTemplate();

        $template->author = $page->author;
        $template->skipAuthor = $page->skipAuthor;
        $template->coauthors = $page->coauthors;
        $template->url = $page->url;
        $template->title = $page->title;
        $template->dd = $page->dd;
        $template->mm = $page->mm;
        $template->yyyy = $page->yyyy;
        $template->site = $page->site;
        $template->pageDate = $page->date;
        $template->skipYMD = $page->skipYMD;
        $template->skipMD = $page->skipMD;
        $template->antiAdBlocker = $page->antiAdBlocker;
        $template->paywall = $page->paywall;

        return $template;
    }

    function computeDate () {
        //Legacy code issue
        if ($this->pageDate !== "" && $this->pageDate !== null) {
            echo '<div data-alert class="alert-box info radius">';
            echo "<p>The Page metadata contains the following date information:<br />$this->pageDate</p><p>{{Lien web}} should now use jour, mois, année instead of a date parameter to provide richer machine data.</p>";
            echo ' <a href="#" class="close">&times;</a></div>';
        }
    }

    /**
     * Gets the month, as a string in current locale
     *
     * @return string the month name
     */
    function getMonth () {
        if (!$this->mm) {
            return "";
        }

        if (is_numeric($this->mm)) {
            return strftime('%B', mktime(0, 0, 0, (int)$this->mm));
        }

        return $this->mm;
    }

    function __toString () {
        if (!$this->skipAuthor) {
            $this->params['auteur'] = $this->author;

            if ($this->coauthors) {
                $this->params['coauteurs'] = implode(', ', $this->coauthors);
            }
        }
        $this->params['titre'] = $this->title;
        $this->computeDate();
        if (!$this->skipYMD && !$this->skipMD) {
            $this->params['jour'] = $this->dd;
            $this->params['mois'] = $this->getMonth();
        }
        if (!$this->skipYMD) {
            $this->params['année'] = $this->yyyy;
        }
        $this->params['url'] = $this->url;
        $this->params['site'] = $this->site;
        $this->params['consulté le'] = $this->accessdate;

        $template = parent::__toString();

        if ($this->antiAdBlocker) {
            $template .= " {{Publicité forcée}}";
        }

        if ($this->paywall) {
            $template .= " {{inscription nécessaire}}";
        }

        return $template;
    }
}
