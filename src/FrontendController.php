<?php

declare(strict_types=1);

namespace Plugins\MagixHomeBrands\src;

use Plugins\MagixHomeBrands\db\MagixHomeBrandsFrontDb;
use App\Component\File\ImageTool;
use Magepattern\Component\Tool\SmartyTool;

class FrontendController
{
    public static function renderWidget(array $params = []): string
    {
        // 1. SÉCURITÉ / AIGUILLAGE
        $hookName = $params['name'] ?? '';
        if (!str_starts_with($hookName, 'displayHome')) {
            return '';
        }

        // 2. TRAITEMENT NORMAL
        $currentLang = $params['current_lang'] ?? ['id_lang' => 1, 'iso_lang' => 'fr'];
        $idLang = (int)$currentLang['id_lang'];

        $db = new MagixHomeBrandsFrontDb();
        $activeBrands = $db->getBrandsList($idLang);

        if (empty($activeBrands)) {
            return '';
        }

        $imageTool = new ImageTool();
        $formattedBrands = [];

        foreach ($activeBrands as $brand) {
            if (empty($brand['img_brand'])) continue;

            $brand['name_img'] = $brand['img_brand'];
            $customBaseDir = '/upload/magixhomebrands/' . $brand['id_brand'] . '/';

            $processed = $imageTool->setModuleImages('magixhomebrands', 'magixhomebrands', [$brand], 0, $customBaseDir);

            if (!empty($processed[0])) {
                $formattedBrands[] = $processed[0];
            }
        }

        if (empty($formattedBrands)) {
            return '';
        }

        $view = SmartyTool::getInstance('front');
        $view->assign([
            'brands_items' => $formattedBrands
        ]);

        return $view->fetch(ROOT_DIR . 'plugins/MagixHomeBrands/views/front/widget.tpl');
    }
}