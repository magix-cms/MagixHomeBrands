<?php

declare(strict_types=1);

namespace Plugins\MagixHomeBrands\db;

use App\Backend\Db\BaseDb;
use Magepattern\Component\Database\QueryBuilder;

class MagixHomeBrandsDb extends BaseDb
{
    // ==========================================
    // GESTION DES MARQUES / PARTENAIRES
    // ==========================================

    /**
     * Récupère la liste des marques pour le tableau de bord
     */
    public function getBrandsList(int $idLang): array
    {
        $qb = new QueryBuilder();
        $qb->select(['b.id_brand', 'b.img_brand', 'b.order_brand', 'b.date_register', 'bc.title_brand', 'bc.published_brand'])
            ->from('mc_plug_homebrands', 'b')
            ->leftJoin('mc_plug_homebrands_content', 'bc', 'b.id_brand = bc.id_brand AND bc.id_lang = ' . $idLang)
            ->orderBy('b.order_brand', 'ASC');

        return $this->executeAll($qb) ?: [];
    }

    /**
     * Supprime une marque
     */
    public function deleteBrand(int $idBrand): bool
    {
        $qb = new QueryBuilder();
        $qb->delete('mc_plug_homebrands')->where('id_brand = :id', ['id' => $idBrand]);
        return $this->executeDelete($qb);
    }

    /**
     * Met à jour uniquement le nom de l'image d'une marque après l'upload
     */
    public function updateBrandImage(int $idBrand, string $imageName): bool
    {
        $qb = new QueryBuilder();
        $qb->update('mc_plug_homebrands', ['img_brand' => $imageName])
            ->where('id_brand = :id', ['id' => $idBrand]);

        return $this->executeUpdate($qb);
    }

    /**
     * Sauvegarde ou insère une marque et ses traductions
     */
    public function saveBrand(int $idBrand, array $mainData, array $contentData): int
    {
        // 1. Mise à jour ou insertion de la base de la marque
        $qbMain = new QueryBuilder();

        if ($idBrand > 0) {
            if (!empty($mainData)) {
                $qbMain->update('mc_plug_homebrands', $mainData)->where('id_brand = :id', ['id' => $idBrand]);
                $this->executeUpdate($qbMain);
            }
        } else {
            // INSERTION : On calcule le prochain ordre disponible
            $qbOrder = new QueryBuilder();
            $qbOrder->select('MAX(order_brand) as max_order')->from('mc_plug_homebrands');
            $res = $this->executeRow($qbOrder);
            $order = $res ? (int)$res['max_order'] + 1 : 1;

            $mainData['order_brand'] = $order;
            if (!isset($mainData['img_brand'])) {
                $mainData['img_brand'] = '';
            }

            $qbMain->insert('mc_plug_homebrands', $mainData);
            if ($this->executeInsert($qbMain)) {
                $idBrand = $this->getLastInsertId();
            } else {
                return 0; // Échec
            }
        }

        // 2. Gestion des traductions
        foreach ($contentData as $idLang => $data) {
            $qbCheck = new QueryBuilder();
            $qbCheck->select('id_brand_content')->from('mc_plug_homebrands_content')
                ->where('id_brand = :id AND id_lang = :lang', ['id' => $idBrand, 'lang' => $idLang]);

            if ($this->executeRow($qbCheck)) {
                $qbUp = new QueryBuilder();
                $qbUp->update('mc_plug_homebrands_content', $data)
                    ->where('id_brand = :id AND id_lang = :lang', ['id' => $idBrand, 'lang' => $idLang]);
                $this->executeUpdate($qbUp);
            } else {
                $data['id_brand'] = $idBrand;
                $data['id_lang']  = $idLang;
                $qbIn = new QueryBuilder();
                $qbIn->insert('mc_plug_homebrands_content', $data);
                $this->executeInsert($qbIn);
            }
        }

        return $idBrand;
    }

    /**
     * Récupère une marque complète avec toutes ses traductions (pour l'édition AJAX)
     */
    public function getBrandFull(int $idBrand): array
    {
        // 1. Infos principales
        $qbMain = new QueryBuilder();
        $qbMain->select('*')->from('mc_plug_homebrands')->where('id_brand = :id', ['id' => $idBrand]);
        $brand = $this->executeRow($qbMain);

        if (!$brand) {
            return [];
        }

        // 2. Traductions
        $qbLang = new QueryBuilder();
        $qbLang->select('*')->from('mc_plug_homebrands_content')->where('id_brand = :id', ['id' => $idBrand]);
        $langs = $this->executeAll($qbLang);

        $brand['content'] = [];
        if ($langs) {
            foreach ($langs as $l) {
                $brand['content'][$l['id_lang']] = $l;
            }
        }

        return $brand;
    }

    /**
     * Met à jour l'ordre d'une marque
     */
    public function updateBrandOrder(int $idBrand, int $position): bool
    {
        $qb = new QueryBuilder();
        $qb->update('mc_plug_homebrands', ['order_brand' => $position])
            ->where('id_brand = :id', ['id' => $idBrand]);

        return $this->executeUpdate($qb);
    }
}