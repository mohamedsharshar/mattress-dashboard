<?php

namespace Database\Seeders;

use App\Models\PriceListSkuPrice;
use App\Models\Product;
use App\Models\ProductLine;
use App\Models\ProductSku;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            ProductLine::query()
                ->with([
                    'priceList.brand',
                    'priceList.category',
                    'prices.size',
                ])
                ->orderBy('price_list_id')
                ->orderBy('sort_order')
                ->get()
                ->each(function (ProductLine $line) {
                    $this->migrateProductLine($line);
                });
        });
    }

    private function migrateProductLine(ProductLine $line): void
    {
        $priceList = $line->priceList;

        if (
            !$priceList ||
            !$priceList->brand ||
            !$priceList->category
        ) {
            throw new \RuntimeException(
                "Invalid ProductLine relationships: {$line->id}"
            );
        }

        [
            $productName,
            $variantName,
            $specification
        ] = $this->resolveIdentity($line);

        /*
        |--------------------------------------------------------------------------
        | Product
        |--------------------------------------------------------------------------
        */

        $product = Product::updateOrCreate(
            [
                'brand_id' => $priceList->brand_id,
                'category_id' => $priceList->category_id,
                'name' => $productName,
            ],
            [
                'unit' => 'piece',
                'is_active' => true,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Variant
        |--------------------------------------------------------------------------
        */

        $variant = ProductVariant::updateOrCreate(
            [
                'product_id' => $product->id,
                'name' => $variantName,
            ],
            [
                'thickness_cm' => $line->thickness_cm,
                'specification' => $specification,
                'description' => $line->description,
                'sort_order' => $line->sort_order,
                'is_active' => (bool) $line->is_active,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | SKU + Selling Price
        |--------------------------------------------------------------------------
        */

        $sortOrder = 1;

        foreach ($line->prices as $legacyPrice) {
            foreach (
                $this->expandSize($legacyPrice->size)
                as $physicalSize
            ) {
                $skuCode = $this->makeSku(
                    brandName: $priceList->brand->name_en,
                    productId: $product->id,
                    variantId: $variant->id,
                    sizeLabel: $physicalSize['label'],
                );

                $sku = ProductSku::updateOrCreate(
                    [
                        'product_variant_id' => $variant->id,
                        'size_label' => $physicalSize['label'],
                    ],
                    [
                        'sku' => $skuCode,
                        'barcode' => null,
                        'width_cm' => $physicalSize['width_cm'],
                        'length_cm' => $physicalSize['length_cm'],
                        'sort_order' => $sortOrder++,
                        'is_active' => true,
                    ]
                );

                PriceListSkuPrice::updateOrCreate(
                    [
                        'price_list_id' => $priceList->id,
                        'product_sku_id' => $sku->id,
                    ],
                    [
                        'price' => $legacyPrice->price,
                    ]
                );
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Product / Variant Resolver
    |--------------------------------------------------------------------------
    */

    private function resolveIdentity(ProductLine $line): array
    {
        $originalName = trim($line->name);

        $productName = preg_replace(
            '/\s+/u',
            ' ',
            $originalName
        );

        $specification = null;

        /*
         * Air Bed examples:
         *
         * MEDICAL 25 CM 1S
         * MEDICAL 30 CM 2S
         */

        if (
            preg_match(
                '/\b([12]S)\b/iu',
                $productName,
                $matches
            )
        ) {
            $specification = strtoupper($matches[1]);

            $productName = preg_replace(
                '/\b[12]S\b/iu',
                '',
                $productName
            );
        }

        /*
         * Some seeders store 1S / 2S in description.
         */

        if (
            !$specification &&
            is_string($line->description) &&
            preg_match(
                '/^\s*([12]S)\s*$/i',
                $line->description,
                $matches
            )
        ) {
            $specification = strtoupper($matches[1]);
        }

        $thicknessText = null;

        if ($line->thickness_cm !== null) {
            $thicknessText = $this->formatNumber(
                (float) $line->thickness_cm
            );

            $quotedThickness = preg_quote(
                $thicknessText,
                '/'
            );

            /*
             * English:
             *
             * MEDICAL 30 CM
             * GOLD 25 CM
             */

            $productName = preg_replace(
                "/{$quotedThickness}\s*CM/iu",
                '',
                $productName
            );

            /*
             * Arabic:
             *
             * ارتفاع 5 سم
             * ارتفاع 5سم
             */

            $productName = preg_replace(
                "/ارتفاع\s*{$quotedThickness}\s*سم/iu",
                '',
                $productName
            );

            $productName = preg_replace(
                "/{$quotedThickness}\s*سم/iu",
                '',
                $productName
            );
        }

        $productName = preg_replace(
            '/\s+/u',
            ' ',
            $productName
        );

        $productName = trim(
            $productName,
            " \t\n\r\0\x0B-_/"
        );

        if ($productName === '') {
            $productName = $originalName;
        }

        /*
        |--------------------------------------------------------------------------
        | Variant Name
        |--------------------------------------------------------------------------
        */

        if ($thicknessText !== null) {
            $variantName = "{$thicknessText} CM";

            if ($specification) {
                $variantName .= " {$specification}";
            }
        } elseif ($specification) {
            $variantName = $specification;
        } else {
            $variantName = 'Standard';
        }

        return [
            $productName,
            $variantName,
            $specification,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Size Expander
    |--------------------------------------------------------------------------
    |
    | Legacy example:
    |
    | 90*190/195/2
    |
    | becomes:
    |
    | 90*190
    | 90*195
    | 90*200
    |--------------------------------------------------------------------------
    */

    private function expandSize($size): array
    {
        if (!$size) {
            return [[
                'label' => 'بدون مقاس',
                'width_cm' => null,
                'length_cm' => null,
            ]];
        }

        $label = trim((string) $size->label);

        $label = str_replace(
            ['×', 'X', 'x', ' '],
            ['*', '*', '*', ''],
            $label
        );

        /*
        |--------------------------------------------------------------------------
        | Combined lengths
        |--------------------------------------------------------------------------
        */

        if (
            str_contains($label, '/') &&
            preg_match(
                '/^(\d+(?:\.\d+)?)\*(.+)$/',
                $label,
                $matches
            )
        ) {
            $width = (float) $matches[1];

            $lengthParts = explode(
                '/',
                $matches[2]
            );

            $result = [];

            foreach ($lengthParts as $lengthPart) {
                $lengthPart = trim($lengthPart);

                /*
                 * Legacy shorthand:
                 *
                 * 190/195/2
                 *
                 * 2 = 200
                 */

                if ($lengthPart === '2') {
                    $lengthPart = '200';
                }

                if (!is_numeric($lengthPart)) {
                    continue;
                }

                $length = (float) $lengthPart;

                $result[] = [
                    'label' =>
                        $this->formatNumber($width)
                        . '*'
                        . $this->formatNumber($length),

                    'width_cm' => $width,
                    'length_cm' => $length,
                ];
            }

            if (!empty($result)) {
                return $result;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Normal width x length
        |--------------------------------------------------------------------------
        */

        if (
            preg_match(
                '/^(\d+(?:\.\d+)?)\*(\d+(?:\.\d+)?)$/',
                $label,
                $matches
            )
        ) {
            $width = (float) $matches[1];
            $length = (float) $matches[2];

            return [[
                'label' =>
                    $this->formatNumber($width)
                    . '*'
                    . $this->formatNumber($length),

                'width_cm' => $width,
                'length_cm' => $length,
            ]];
        }

        /*
        |--------------------------------------------------------------------------
        | Width only
        |--------------------------------------------------------------------------
        |
        | Milton / toppers:
        |
        | 90
        | 100
        | 120
        |--------------------------------------------------------------------------
        */

        if (is_numeric($label)) {
            $width = (float) $label;

            return [[
                'label' => $this->formatNumber($width),
                'width_cm' => $width,
                'length_cm' => null,
            ]];
        }

        /*
        |--------------------------------------------------------------------------
        | Fallback to dimensions already stored
        |--------------------------------------------------------------------------
        */

        if (
            $size->width_cm !== null ||
            $size->length_cm !== null
        ) {
            return [[
                'label' => $label,
                'width_cm' =>
                    $size->width_cm !== null
                        ? (float) $size->width_cm
                        : null,
                'length_cm' =>
                    $size->length_cm !== null
                        ? (float) $size->length_cm
                        : null,
            ]];
        }

        /*
        |--------------------------------------------------------------------------
        | Non-dimensional item
        |--------------------------------------------------------------------------
        */

        return [[
            'label' => $label,
            'width_cm' => null,
            'length_cm' => null,
        ]];
    }

    /*
    |--------------------------------------------------------------------------
    | SKU Generator
    |--------------------------------------------------------------------------
    */

    private function makeSku(
        ?string $brandName,
        int $productId,
        int $variantId,
        string $sizeLabel
    ): string {
        $brandCode = match (
            strtolower(trim((string) $brandName))
        ) {
            'bed janssen' => 'BED',
            'englander' => 'ENG',
            'janssen prestige' => 'JPR',
            'air bed' => 'AIR',
            default => 'BR',
        };

        $sizeCode = strtoupper(
            str_replace(
                '*',
                'X',
                $sizeLabel
            )
        );

        $sizeCode = preg_replace(
            '/[^A-Z0-9]+/',
            '',
            $sizeCode
        );

        if ($sizeCode === '') {
            $sizeCode = 'STD';
        }

        return sprintf(
            '%s-P%d-V%d-%s',
            $brandCode,
            $productId,
            $variantId,
            $sizeCode
        );
    }

    private function formatNumber(float $number): string
    {
        if (floor($number) === $number) {
            return (string) (int) $number;
        }

        return rtrim(
            rtrim(
                number_format(
                    $number,
                    2,
                    '.',
                    ''
                ),
                '0'
            ),
            '.'
        );
    }
}