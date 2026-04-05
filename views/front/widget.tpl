{if isset($brands_items) && $brands_items|count > 0}
    <section class="magix-homebrands-section py-5 bg-light">
        <div class="container">

            <div id="magix-homebrands-slider" class="splide magix-homebrands" aria-label="Nos partenaires">
                <div class="splide__track">
                    <ul class="splide__list align-items-center">

                        {foreach $brands_items as $index => $brand}
                            {* Les 6 premiers sont visibles au chargement sur Desktop *}
                            {if $index < 6}
                                {$is_lazy = false}
                                {$prio = 'high'}
                            {else}
                                {$is_lazy = true}
                                {$prio = ''}
                            {/if}

                            <li class="splide__slide text-center px-3">
                                {if !empty($brand.url_brand)}
                                    <a href="{$brand.url_brand}" class="brand-logo-link d-block"
                                       {if $brand.blank_brand}target="_blank" rel="noopener noreferrer"{/if}
                                       {if !empty($brand.desc_brand)}
                                           data-bs-toggle="tooltip" data-bs-title="{$brand.desc_brand|escape}"
                                       {elseif !empty($brand.title_brand)}
                                           title="{$brand.title_brand|escape}"
                                       {/if}>
                                {else}
                                    <div class="brand-logo-link d-block"
                                         {if !empty($brand.desc_brand)}
                                             data-bs-toggle="tooltip" data-bs-title="{$brand.desc_brand|escape}"
                                         {elseif !empty($brand.title_brand)}
                                             title="{$brand.title_brand|escape}"
                                         {/if}>
                                {/if}
                                    {include file="components/img.tpl"
                                        img=$brand.img
                                        size='medium'
                                        responsiveC=true
                                        lazy=$is_lazy
                                        fetchpriority=$prio
                                        alt=$brand.title_brand|default:'Partenaire'
                                    }
                                {if !empty($brand.url_brand)}
                                    </a>
                                {else}
                                    </div>
                                {/if}

                            </li>
                        {/foreach}
                    </ul>
                </div>
            </div>
        </div>
    </section>
{/if}