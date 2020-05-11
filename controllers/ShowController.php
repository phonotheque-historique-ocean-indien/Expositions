<?php
/* ----------------------------------------------------------------------
 * simpleListEditor
 * ----------------------------------------------------------------------
 * List & list values editor plugin for Providence - CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Plugin by idéesculture (www.ideesculture.com)
 * This plugin is published under GPL v.3. Please do not remove this header
 * and add your credits thereafter.
 *
 * File modified by :
 * ----------------------------------------------------------------------
 */
ini_set("display_errors", 1);
error_reporting(E_ERROR);
require_once(__CA_MODELS_DIR__.'/ca_site_pages.php');

class ShowController extends ActionController
{
    # -------------------------------------------------------
    protected $opo_config;        // plugin configuration file
    protected $opa_list_of_lists; // list of lists
    protected $opa_listIdsFromIdno; // list of lists
    protected $opa_locale; // locale id
    private $opo_list;
    # -------------------------------------------------------
    # Constructor
    # -------------------------------------------------------

    public function __construct(&$po_request, &$po_response, $pa_view_paths = null)
    {
        parent::__construct($po_request, $po_response, $pa_view_paths);

        $this->opo_config = Configuration::load(__CA_APP_DIR__ . '/plugins/Expositions/conf/expositions.conf');

    }

    # -------------------------------------------------------
    # Functions to render views
    # -------------------------------------------------------
    public function Index($type = "")
    {
        $all_articles = ca_site_pages::getPageList();
        $all_articles = array_reverse($all_articles);
        $articles = [];
        foreach ($all_articles as $testarticle) {
            if ($testarticle["template_title"]=="exposition") {
                $articles[] = $testarticle;
            }
        }
        $articles = array_splice($articles,0, 6);
        $blocks = "";
        foreach ($articles as $art) {
            $page = new ca_site_pages($art["page_id"]);
            $article = $page->get("content");
            $this->view->setVar("article", $article);
            $this->view->setVar("id", $art["page_id"]);
            $blocks .= $this->render("home_block_html.php", true);
        }
        //$page = new ca_site_pages(1);
        $this->view->setVar("blocks", $blocks);
        $this->render('index_html.php');
    }

    public function All($type = "")
    {
        $all_articles = ca_site_pages::getPageList();
        $all_articles = array_reverse($all_articles);
        $articles = [];
        foreach ($all_articles as $testarticle) {
            if ($testarticle["template_title"]=="exposition") {
                $articles[] = $testarticle;
            }
        }
        $blocks = "";
        foreach ($articles as $art) {
//            var_dump($art);die();
            $page = new ca_site_pages($art["page_id"]);
            $article = $page->get("content");
            $this->view->setVar("article", $article);
            $this->view->setVar("id", $art["page_id"]);
            $blocks .= $this->render("home_block_html.php", true);
        }
        //$page = new ca_site_pages(1);
        $this->view->setVar("blocks", $blocks);
        $this->render('all_expositions_html.php');
    }

    public function Wall() {
        $this->render('index_html.php');
    }

    public function Details() {
        $id= $this->request->getParameter("id", pInteger);
        // TODO Redirect if no ID
        $page = new ca_site_pages($id);
        $article = $page->get("content");
        $this->view->setVar("article", $article);

        $this->render('exposition_html.php');
    }

    public function List() {
        $all_articles = ca_site_pages::getPageList();
        $all_articles = array_reverse($all_articles);
        $articles = [];
        foreach ($all_articles as $testarticle) {
            if ($testarticle["template_title"]=="exposition") {
//	            $articles = $testarticle;
//	            array_push($articles, $testarticle);
                $articles[] = $testarticle;
            }
        }
        $result=[];
        foreach($articles as $key=>$article_info) {
            $article = new ca_site_pages($article_info["page_id"]);
            $content = $article->get("ca_site_pages.content");
            $result[$key] = ["page_id"=>$article_info["page_id"], "title"=>$article_info["title"], "content"=>$content];
        }
        $this->view->setVar("articles", $result);
        $this->render('list_html.php');
    }
}
?>
