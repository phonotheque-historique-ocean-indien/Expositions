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
        session_start();
        $_SESSION["partie"] = "chaude";
        parent::__construct($po_request, $po_response, $pa_view_paths);

        $this->opo_config = Configuration::load(__CA_APP_DIR__ . '/plugins/Expositions/conf/expositions.conf');
    }

    # -------------------------------------------------------
    # Functions to render views
    # -------------------------------------------------------
    public function Index($type = "")
    {
        session_start();
        $_SESSION["partie"] = "chaude";

        $all_articles = ca_site_pages::getPageList();
        $all_articles = array_reverse($all_articles);
        $articles = [];
        $vt_user = $this->request->getUser();
        $roles = $vt_user->getUserGroups();
        $is_redactor = false;
        foreach($roles as $role) {
            if($role["code"]=="redactor") {
                $is_redactor = true;}
        }

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
            if(!$page->get("access") && !$is_redactor) continue;

            $this->view->setVar("article", $article);
            $this->view->setVar("access", $page->get("access"));
            $this->view->setVar("id", $art["page_id"]);
            $this->view->setVar("is_redactor", $is_redactor);

            $blocks .= $this->render("home_block_html.php", true);
        }
        //$page = new ca_site_pages(1);
        $this->view->setVar("is_redactor", $is_redactor);

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
        $is_redactor = false;
        foreach($this->getRequest()->getUser()->getUserGroups() as $group) {
            if($group["code"] == "redactor") $is_redactor=true;
        }
        $id= $this->request->getParameter("id", pInteger);
        // TODO Redirect if no ID
        $page = new ca_site_pages($id);
        //$page = new ca_site_pages(1);
        $article = $page->get("content");

        $page = new ca_site_pages($id);
        $this->view->setVar("access", $page->get("access"));

        $this->view->setVar("article", $article);
        $this->view->setVar("is_redactor", $is_redactor);
        $this->view->setVar("id", $id);
        $this->render('exposition_html.php');
    }

    public function Publish() {
        $is_redactor = false;
        foreach($this->getRequest()->getUser()->getUserGroups() as $group) {
            if($group["code"] == "redactor") $is_redactor=true;
        }
        $id= $this->request->getParameter("id", pInteger);
        // TODO Redirect if no ID
        $page = new ca_site_pages($id);
        $page->setMode(ACCESS_WRITE);
        $page->set("access", 1);
        $page->update();

        $this->redirect("/index.php/Expositions/Show/Details/id/".$id);
    }

    public function Unpublish() {
        $is_redactor = false;
        foreach($this->getRequest()->getUser()->getUserGroups() as $group) {
            if($group["code"] == "redactor") $is_redactor=true;
        }
        $id= $this->request->getParameter("id", pInteger);
        // TODO Redirect if no ID
        $page = new ca_site_pages($id);
        $page->setMode(ACCESS_WRITE);
        $page->set("access", 0);
        $page->update();

        $this->redirect("/index.php/Expositions/Show/Details/id/".$id);
    }

    public function List() {
        $vt_user = $this->request->getUser();
        $roles = $vt_user->getUserGroups();

        $is_redactor = false;
        foreach($roles as $role) {
            if($role["code"]=="redactor") {
                $is_redactor = true;}
        }

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
            $page = new ca_site_pages($article_info["page_id"]);
            if(!$page->get("access") && !$is_redactor) continue;

            $article = $page->get("ca_site_pages.content");

            if($article["date_from"] && !$is_redactor) {
                $date_from = substr($article["date_from"], 6, 4)."-".substr($article["date_from"], 3, 2)."-".substr($article["date_from"], 0, 2);
                // Ignore if the article is to be published in the future
                if(time() < strtotime($date_from)) continue;
            }
            if($article["date_to"] && !$is_redactor) {
                $date_to = substr($article["date_to"], 6, 4)."-".substr($article["date_to"], 3, 2)."-".substr($article["date_to"], 0, 2);
                // Ignore if the article is to be published in the future
                if(time() > strtotime($date_to)) continue;
            }
            $title = ($article["title"] ? $article["title"] : $article_info["title"])." ".$article["subtitle"].($page->get("access") ? '' : '<span class="tag is-warning" style="margin-top:10px;margin-left:12px;">BROUILLON</span>');
            $result[$key] = ["page_id"=>$article_info["page_id"], "title"=>$title, "content"=>$article];
        }
        $this->view->setVar("articles", $result);
        $this->render('list_html.php');
    }

    public function Delete() {
        $is_redactor = false;
        foreach($this->getRequest()->getUser()->getUserGroups() as $group) {
            if($group["code"] == "redactor") $is_redactor=true;
        }
        if(!$is_redactor) die("This function requires redactor privileges.");
        $id= $this->request->getParameter("id", pInteger);
        // TODO Redirect if no ID
        $page = new ca_site_pages($id);
        $page->setMode(ACCESS_WRITE);
        $page->delete();
        $page->update();

        $this->redirect("/index.php/Expositions/Show/index");
    }

}
?>
