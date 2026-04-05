{extends file="layout.tpl"}

{block name='head:title'}Édition du Partenaire{/block}

{block name="article"}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="bi bi-pencil-square me-2"></i> {if $brand.id_brand == 0}Ajouter un Partenaire{else}Modifier le Partenaire #{$brand.id_brand}{/if}
        </h1>
        <a href="index.php?controller=MagixHomeBrands" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Retour à la liste
        </a>
    </div>

    <form action="index.php?controller=MagixHomeBrands&action=saveBrand" method="post" enctype="multipart/form-data" class="validate_form {if $brand.id_brand == 0}add_form{/if}">
        <input type="hidden" name="hashtoken" value="{$hashtoken|default:''}">
        <input type="hidden" name="id_brand" value="{$brand.id_brand|default:0}">

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                <h6 class="m-0 fw-bold text-primary">Image du partenaire</h6>
                {if isset($langs)}{include file="components/dropdown-lang.tpl"}{/if}
            </div>

            <div class="card-body p-4">
                <div class="mb-4 bg-light p-3 rounded border">
                    <label class="form-label fw-bold">Fichier Image {if $brand.id_brand == 0}<span class="text-danger">*</span>{/if}</label>

                    {if !empty($brand.img_brand)}
                        <div class="d-flex align-items-center bg-white p-3 border rounded shadow-sm mb-3">
                            <div class="me-3">
                                <img id="preview-image" src="/upload/magixhomebrands/{$brand.id_brand}/{$brand.img_brand}" alt="Aperçu" class="img-thumbnail" style="max-height: 90px; object-fit: cover;">
                            </div>
                            <div>
                                <span class="d-block fw-bold text-success"><i class="bi bi-check-circle-fill me-1"></i> Image actuellement en ligne</span>
                                <small class="text-muted d-block mb-1">Fichier : <strong>{$brand.img_brand}</strong></small>
                                <small class="text-muted"><em>Chargez un nouveau fichier ci-dessous pour la remplacer.</em></small>
                            </div>
                        </div>
                    {/if}

                    <input type="file" id="img_input" name="img_brand" class="form-control" accept="image/*" {if $brand.id_brand == 0}required{/if}>
                </div>

                <div class="tab-content" id="myTabContent">
                    {if isset($langs)}
                        {foreach $langs as $idLang => $iso}
                            <div class="tab-pane fade {if $iso@first}show active{/if}" id="lang-{$idLang}" role="tabpanel">

                                <div class="row g-3">
                                    <div class="col-md-9">
                                        <label class="form-label fw-medium">Nom de la marque</label>
                                        <input type="text" name="brand_content[{$idLang}][title_brand]" class="form-control" value="{$brand.content[$idLang].title_brand|default:''}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-medium d-block">Statut</label>
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" type="checkbox" role="switch" name="brand_content[{$idLang}][published_brand]" value="1" {if ($brand.content[$idLang].published_brand|default:1) == 1}checked{/if}>
                                            <label class="form-check-label text-muted">Publié</label>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-medium">Description</label>
                                        <textarea name="brand_content[{$idLang}][desc_brand]" class="form-control" rows="3">{$brand.content[$idLang].desc_brand|default:''}</textarea>
                                    </div>

                                    <div class="col-12">
                                        <div class="p-3 bg-light rounded border border-light-subtle">
                                            <h6 class="fw-bold mb-3 small text-uppercase text-muted"><i class="bi bi-link-45deg"></i> Lien (Optionnel)</h6>
                                            <div class="row g-3">
                                                <div class="col-md-8">
                                                    <label class="form-label small">URL de destination</label>
                                                    <input type="text" name="brand_content[{$idLang}][url_brand]" class="form-control form-control-sm" value="{$brand.content[$idLang].url_brand|default:''}">
                                                </div>
                                                <div class="col-md-4 d-flex align-items-end">
                                                    <div class="form-check mb-1">
                                                        <input class="form-check-input" type="checkbox" name="brand_content[{$idLang}][blank_brand]" value="1" {if ($brand.content[$idLang].blank_brand|default:0) == 1}checked{/if}>
                                                        <label class="form-check-label small">Ouvrir dans un nouvel onglet</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        {/foreach}
                    {/if}
                </div>
            </div>

            <div class="card-footer bg-light text-end py-3">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-2"></i> Enregistrer
                </button>
            </div>
        </div>
    </form>
{/block}

{block name="javascripts" append}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Prévisualisation instantanée de l'image
            const imgInput = document.getElementById('img_input');
            const previewImg = document.getElementById('preview-image');

            if (imgInput && previewImg) {
                imgInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        previewImg.src = URL.createObjectURL(this.files[0]);
                    }
                });
            }
        });
    </script>
{/block}