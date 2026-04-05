<?php

declare(strict_types=1);

namespace Plugins\MagixHomeBrands\src;

use App\Backend\Controller\BaseController;
use Plugins\MagixHomeBrands\db\MagixHomeBrandsDb;
use Magepattern\Component\HTTP\Request;
use Magepattern\Component\Tool\FormTool;
use Magepattern\Component\Tool\SmartyTool;
use App\Component\File\UploadTool;
use App\Component\File\ImageTool;
use Magepattern\Component\File\FileTool;
use Magepattern\Component\HTTP\Url;

class BackendController extends BaseController
{
    public function run(): void
    {
        SmartyTool::addTemplateDir('admin', ROOT_DIR . 'plugins' . DS . 'MagixHomeBrands' . DS . 'views' . DS . 'admin');

        $action = $_GET['action'] ?? null;

        // INTERCEPTION : table-forms.tpl utilise '?edit=ID' pour les boutons de modification
        if (isset($_GET['edit'])) {
            $action = 'edit';
        }

        if ($action && $action !== 'run' && method_exists($this, $action)) {
            $this->$action();
            return;
        }

        $this->index();
    }

    public function index(): void
    {
        $db = new MagixHomeBrandsDb();
        $idLangue = (int)($this->defaultLang['id_lang'] ?? 1);
        $langs = $db->fetchLanguages();

        // 1. Schéma pour table-forms
        $targetColumns = ['id_brand', 'order_brand', 'title_brand', 'published_brand', 'date_register'];

        $rawScheme = array_merge(
            $db->getTableScheme('mc_plug_homebrands'),
            $db->getTableScheme('mc_plug_homebrands_content')
        );

        $associations = [
            'id_brand'        => ['title' => 'id', 'type' => 'text', 'class' => 'text-center text-muted small px-2'],
            'order_brand'     => ['title' => 'ordre', 'type' => 'text', 'class' => 'text-muted fw-bold'],
            'title_brand'     => ['title' => 'nom', 'type' => 'text', 'class' => 'w-50 fw-bold'],
            'published_brand' => ['title' => 'published', 'type' => 'bin', 'class' => 'text-center px-3', 'enum' => 'published_'],
            'date_register'   => ['title' => 'date', 'type' => 'date', 'class' => 'text-center text-nowrap text-muted small']
        ];

        $this->getScheme($rawScheme, $targetColumns, $associations);

        // 2. Récupération et formatage des images
        $rawBrandsList = $db->getBrandsList($idLangue);
        $imageTool = new ImageTool();
        $brandsListForImageTool = [];

        foreach ($rawBrandsList as $brand) {
            $brand['name_img'] = $brand['img_brand'];
            $brandsListForImageTool[] = $brand;
        }

        $formattedBrandsList = [];
        foreach ($brandsListForImageTool as $brand) {
            $customBaseDir = '/upload/magixhomebrands/' . $brand['id_brand'] . '/';
            $processed = $imageTool->setModuleImages('magixhomebrands', 'magixhomebrands', [$brand], 0, $customBaseDir);
            $formattedBrandsList[] = $processed[0];
        }

        $this->getItems('brandsList', $formattedBrandsList, true);

        // 3. Variables Smarty
        $token = $this->session->getToken();
        $this->view->assign([
            'langs'       => $langs,
            'langIdsJson' => json_encode(array_keys($langs)),
            'brandsList'  => $formattedBrandsList,
            'defaultLang' => $this->defaultLang,
            'idcolumn'    => 'id_brand',
            'hashtoken'   => $token,
            'url_token'   => urlencode($token),
            'sortable'    => true,
            'checkbox'    => true,
            'edit'        => true,
            'dlt'         => true
        ]);

        $this->view->display('index.tpl');
    }

    public function add(): void
    {
        $db = new MagixHomeBrandsDb();

        $this->view->assign([
            'langs'     => $db->fetchLanguages(),
            'hashtoken' => $this->session->getToken(),
            'brand'     => ['id_brand' => 0, 'content' => []]
        ]);

        $this->view->display('form.tpl');
    }

    public function edit(): void
    {
        $idBrand = (int)($_GET['edit'] ?? 0);
        $db = new MagixHomeBrandsDb();

        $data = $db->getBrandFull($idBrand);

        if (empty($data)) {
            header('Location: index.php?controller=MagixHomeBrands');
            exit;
        }

        // Traitement de l'image pour avoir la miniature dans le formulaire
        if (!empty($data['img_brand'])) {
            $imageTool = new ImageTool();
            $data['name_img'] = $data['img_brand'];
            $customBaseDir = '/upload/magixhomebrands/' . $data['id_brand'] . '/';

            $processed = $imageTool->setModuleImages('magixhomebrands', 'magixhomebrands', [$data], 0, $customBaseDir);

            if (!empty($processed[0])) {
                $data = $processed[0];
            }
        }

        $this->view->assign([
            'langs'     => $db->fetchLanguages(),
            'hashtoken' => $this->session->getToken(),
            'brand'     => $data
        ]);

        $this->view->display('form.tpl');
    }

    public function saveBrand(): void
    {
        if (!Request::isMethod('POST')) return;

        $token = $_POST['hashtoken'] ?? '';
        if (!$this->session->validateToken($token)) {
            $this->jsonResponse(false, 'Session expirée.');
        }

        $db = new MagixHomeBrandsDb();
        $idBrand = (int)($_POST['id_brand'] ?? 0);
        $isNew = ($idBrand === 0);

        // 1. Sauvegarde du contenu textuel
        $contentData = [];
        if (isset($_POST['brand_content']) && is_array($_POST['brand_content'])) {
            foreach ($_POST['brand_content'] as $idLang => $c) {
                $contentData[$idLang] = [
                    'title_brand'     => FormTool::simpleClean($c['title_brand'] ?? ''),
                    'desc_brand'      => $c['desc_brand'] ?? '',
                    'url_brand'       => FormTool::simpleClean($c['url_brand'] ?? ''),
                    'blank_brand'     => isset($c['blank_brand']) ? 1 : 0,
                    'published_brand' => isset($c['published_brand']) ? 1 : 0
                ];
            }
        }

        $idBrand = $db->saveBrand($idBrand, [], $contentData);

        if ($idBrand === 0) {
            $this->jsonResponse(false, 'Erreur lors de la sauvegarde du partenaire.');
        }

        // 2. Gestion de l'Upload d'image
        if (isset($_FILES['img_brand']) && $_FILES['img_brand']['error'] === UPLOAD_ERR_OK) {

            if (!$isNew) {
                $brandDir = ROOT_DIR . 'upload' . DS . 'magixhomebrands' . DS . $idBrand;
                if (is_dir($brandDir)) {
                    FileTool::removeRecursiveFile($brandDir);
                }
            }

            $uploadTool = new UploadTool();
            $idLangDefault = (int)($this->defaultLang['id_lang'] ?? 1);

            $brandTitle = $contentData[$idLangDefault]['title_brand'] ?? '';
            $seoName = !empty($brandTitle) ? Url::clean($brandTitle) : 'brand-' . $idBrand;

            $options = [
                'postKey' => 'img_brand',
                'name'    => $seoName
            ];

            $result = $uploadTool->singleImageUpload(
                'magixhomebrands',
                'magixhomebrands',
                'upload',
                ['magixhomebrands', (string)$idBrand],
                $options
            );

            if ($result['status'] === true) {
                $db->updateBrandImage($idBrand, $result['file']);
            } else {
                $this->jsonResponse(false, 'Données sauvées, mais erreur d\'image : ' . $result['msg']);
            }
        } elseif ($isNew) {
            $this->jsonResponse(false, 'L\'image est obligatoire pour créer un nouveau partenaire.');
        }

        // 3. Retour JSON
        $msg = $isNew ? 'Le partenaire a été ajouté avec succès.' : 'Le partenaire a été mis à jour.';
        $reload = (!$isNew && isset($_FILES['img_brand']) && $_FILES['img_brand']['error'] === UPLOAD_ERR_OK);

        if ($reload) {
            $this->jsonResponse(true, $msg, [
                'type' => 'update',
                'redirect' => 'index.php?controller=MagixHomeBrands&edit=' . $idBrand
            ]);
        } else {
            $this->jsonResponse(true, $msg, ['type' => $isNew ? 'add' : 'update']);
        }
    }

    public function delete(): void
    {
        if (ob_get_length()) ob_clean();

        $token = $_GET['hashtoken'] ?? '';
        if (!$this->session->validateToken(str_replace(' ', '+', $token))) {
            $this->jsonResponse(false, 'Token invalide.');
        }

        $ids = $_POST['ids'] ?? [$_POST['id'] ?? null];
        $cleanIds = array_filter(array_map('intval', (array)$ids));

        if (!empty($cleanIds)) {
            $db = new MagixHomeBrandsDb();
            $successCount = 0;

            foreach ($cleanIds as $idBrand) {
                if ($db->deleteBrand($idBrand)) {
                    $successCount++;
                    $brandDir = ROOT_DIR . 'upload' . DS . 'magixhomebrands' . DS . $idBrand;
                    FileTool::remove($brandDir);
                }
            }

            if ($successCount > 0) {
                $msg = $successCount > 1 ? 'Les partenaires ont été supprimés.' : 'Le partenaire a été supprimé.';
                echo $this->json->encode(['success' => true, 'message' => $msg, 'ids' => $cleanIds]);
                exit;
            }
        }

        echo $this->json->encode(['success' => false, 'message' => 'Aucune sélection.']);
        exit;
    }

    public function reorder(): void
    {
        if (ob_get_length()) ob_clean();

        $rawToken = $_GET['hashtoken'] ?? '';
        $token = str_replace(' ', '+', $rawToken);

        if (!$this->session->validateToken($token)) {
            echo $this->json->encode(['success' => false, 'message' => 'Token invalide']);
            exit;
        }

        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (isset($data['order']) && is_array($data['order'])) {
            $db = new MagixHomeBrandsDb();
            try {
                $position = 1;
                foreach ($data['order'] as $id) {
                    $db->updateBrandOrder((int)$id, $position);
                    $position++;
                }
                echo $this->json->encode(['success' => true, 'message' => 'Ordre mis à jour avec succès.']);
                exit;
            } catch (\Exception $e) {
                echo $this->json->encode(['success' => false, 'message' => $e->getMessage()]);
                exit;
            }
        }

        echo $this->json->encode(['success' => false, 'message' => 'Données invalides.']);
        exit;
    }
}