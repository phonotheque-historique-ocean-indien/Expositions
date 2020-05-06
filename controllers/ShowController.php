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

        $this->opo_config = Configuration::load(__CA_APP_DIR__ . '/plugins/Articles/conf/expositions.conf');

    }

    # -------------------------------------------------------
    # Functions to render views
    # -------------------------------------------------------
    public function Index($type = "")
    {
        $blocks = "";
        for($id=1;$id<7;$id++) {
            $page = new ca_site_pages($id);
            $article = $page->get("content");
            $this->view->setVar("article", $article);
            $this->view->setVar("id", $id);
            $blocks .= $this->render("home_block_html.php", true);
        }
        //$page = new ca_site_pages(1);
        $this->view->setVar("blocks", $blocks);
        $this->render('index_html.php');
    }

    public function Wall() {
        $this->render('index_html.php');
    }

    public function Details() {
        $id= $this->request->getParameter("id", pInteger);
        // TODO Redirect if no ID
        $page = new ca_site_pages($id);
        //$page = new ca_site_pages(1);
        $article = $page->get("content");
        $this->view->setVar("article", $article);

        $this->render('article_html.php');
    }

    public function List() {
        $listing = ca_site_pages::getPageList();
        $listing = array_slice($listing, 0, 10);
        $articles = [];
        foreach($listing as $key=>$article_info) {
            $article = new ca_site_pages($article_info["page_id"]);
            $content = $article->get("ca_site_pages.content");
            $articles[$key] = ["page_id"=>$article_info["page_id"], "title"=>$article_info["title"], "content"=>$content];
        }
        $this->view->setVar("articles", $articles);
        $this->render('list_html.php');
    }
}
?>
