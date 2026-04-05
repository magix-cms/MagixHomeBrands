{extends file="layout.tpl"}

{block name='head:title'}Magix HomeBrands{/block}

{block name="article"}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="bi bi-star me-2"></i> Magix HomeBrands
        </h1>
        <a href="index.php?controller=MagixHomeBrands&action=add" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Ajouter un Partenaire
        </a>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 fw-bold text-primary">Liste des marques et partenaires</h6>
        </div>
        <div class="card-body p-0">
            {include file="components/table-forms.tpl"
            data=$brandsList
            idcolumn='id_brand'
            activation=true
            sortable=true
            controller="MagixHomeBrands"
            }
        </div>
    </div>
{/block}