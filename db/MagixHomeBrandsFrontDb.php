<?php

declare(strict_types=1);

namespace Plugins\MagixHomeBrands\db;

use App\Frontend\Db\BaseDb;
use Magepattern\Component\Database\QueryBuilder;

class MagixHomeBrandsFrontDb extends BaseDb
{
    /**
     * Récupère la liste des marques publiées pour le Frontend
     */
    public function getBrandsList(int $idLang): array
    {
        $qb = new QueryBuilder();
        $qb->select(['b.id_brand', 'b.img_brand', 'bc.title_brand', 'bc.desc_brand', 'bc.url_brand', 'bc.blank_brand'])
            ->from('mc_plug_homebrands', 'b')
            ->leftJoin('mc_plug_homebrands_content', 'bc', 'b.id_brand = bc.id_brand AND bc.id_lang = ' . $idLang)
            ->where('bc.published_brand = 1')
            ->orderBy('b.order_brand', 'ASC');

        return $this->executeAll($qb) ?: [];
    }
}