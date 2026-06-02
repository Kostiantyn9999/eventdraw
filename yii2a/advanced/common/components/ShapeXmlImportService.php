<?php

namespace common\components;

use common\models\MomentusShape;
use Yii;
use yii\db\Query;

/**
 * Parses draw.io / EventDraw stencil libraries (<mxlibrary> JSON) and imports shape rows.
 */
class ShapeXmlImportService
{
    /**
     * @return array{category:string,items:array<int,array{shape_name:string,shape_type:?string,library_title:string}>,errors:array<int,string>}
     */
    public function parseLibraryFile($filePath, $categoryOverride = null, $originalFileName = null)
    {
        $errors = [];
        $contents = @file_get_contents($filePath);
        if ($contents === false) {
            return ['category' => '', 'items' => [], 'errors' => ['Could not read the uploaded file.']];
        }

        $sourceFileName = trim((string) $originalFileName);
        if ($sourceFileName === '') {
            $sourceFileName = basename((string) $filePath);
        }

        return $this->parseLibraryXml($contents, $categoryOverride, $sourceFileName);
    }

    /**
     * @return array{category:string,items:array<int,array{shape_name:string,shape_type:?string,library_title:string}>,errors:array<int,string>}
     */
    public function parseLibraryXml($xmlContents, $categoryOverride = null, $sourceFileName = null)
    {
        $errors = [];
        $category = trim((string) $categoryOverride);
        if ($category === '' && $sourceFileName !== null) {
            $category = pathinfo($sourceFileName, PATHINFO_FILENAME);
        }

        libxml_use_internal_errors(true);
        $doc = simplexml_load_string($xmlContents);
        if ($doc === false) {
            return ['category' => $category, 'items' => [], 'errors' => ['Invalid XML file.']];
        }

        $json = trim((string) $doc);
        if ($json === '' && isset($doc->mxlibrary)) {
            $json = trim((string) $doc->mxlibrary);
        }

        if ($json === '') {
            return ['category' => $category, 'items' => [], 'errors' => ['No <mxlibrary> content found.']];
        }

        $library = json_decode($json, true);
        if (!is_array($library)) {
            return ['category' => $category, 'items' => [], 'errors' => ['mxlibrary JSON is invalid.']];
        }

        $items = [];
        foreach ($library as $index => $entry) {
            if (!is_array($entry)) {
                $errors[] = 'Skipped library entry #' . ($index + 1) . ' (not an object).';
                continue;
            }

            $libraryTitle = isset($entry['title']) ? trim((string) $entry['title']) : '';
            $attrs = $this->decodeShapeXmlAttributes(isset($entry['xml']) ? (string) $entry['xml'] : '');

            $shapeName = '';
            if (!empty($attrs['ShapeName'])) {
                $shapeName = trim((string) $attrs['ShapeName']);
            } elseif ($libraryTitle !== '') {
                $shapeName = $libraryTitle;
            }

            if ($shapeName === '') {
                $errors[] = 'Skipped library entry #' . ($index + 1) . ' (no title or ShapeName).';
                continue;
            }

            $shapeType = !empty($attrs['ShapeType']) ? trim((string) $attrs['ShapeType']) : null;

            $items[] = [
                'shape_name' => $shapeName,
                'shape_type' => $shapeType,
                'library_title' => $libraryTitle,
            ];
        }

        return ['category' => $category, 'items' => $items, 'errors' => $errors];
    }

    /**
     * @param array<int,array{shape_name:string,shape_type:?string,library_title:string}> $items
     * @return array{created:int,skipped:int,updated:int,errors:array<int,string>}
     */
    public function importItems(array $items, $category, $clientId = null, $updateExisting = false)
    {
        $result = ['created' => 0, 'skipped' => 0, 'updated' => 0, 'errors' => []];
        $category = trim((string) $category);

        foreach ($items as $item) {
            $shapeName = trim((string) $item['shape_name']);
            if ($shapeName === '') {
                continue;
            }

            $query = MomentusShape::find()->where(['shapeType' => $shapeName]);
            if (MomentusShape::hasClientIdColumn() && $clientId !== null) {
                $query->andWhere(['clientid' => $clientId]);
            }
            $existing = $query->one();

            if ($existing !== null) {
                if ($updateExisting) {
                    $existing->ed_shapes_category = $category !== '' ? $category : $existing->ed_shapes_category;
                    if (!empty($item['shape_type'])) {
                        $existing->shapetypes = (string) $item['shape_type'];
                    }
                    if ($existing->description === '' || $existing->description === $existing->shapeType) {
                        $existing->description = $shapeName;
                    }
                    if ($existing->save(false)) {
                        $result['updated']++;
                    } else {
                        $result['errors'][] = 'Failed to update "' . $shapeName . '".';
                    }
                } else {
                    $result['skipped']++;
                }
                continue;
            }

            $maxSourceId = (int) (new Query())->from('{{%shapes}}')->max('source_id', Yii::$app->db);

            $shape = new MomentusShape();
            $shape->source_id = $maxSourceId + 1;
            $shape->shapeType = $shapeName;
            $shape->description = $shapeName;
            $shape->shapetypes = !empty($item['shape_type']) ? (string) $item['shape_type'] : null;
            $shape->ed_shapes_category = $category !== '' ? $category : null;
            $shape->category = 0;
            $shape->elevate = 0;
            $shape->height = 0;
            $shape->model = 'standard';

            if (MomentusShape::hasClientIdColumn() && $clientId !== null) {
                $shape->clientid = $clientId;
            }

            if ($shape->save(false)) {
                $result['created']++;
            } else {
                $result['errors'][] = 'Failed to create "' . $shapeName . '".';
            }
        }

        return $result;
    }

    /**
     * @return array<string,string>
     */
    private function decodeShapeXmlAttributes($encodedXml)
    {
        if ($encodedXml === '') {
            return [];
        }

        $decoded = $this->decodeMxLibraryXmlPayload($encodedXml);
        if ($decoded === '') {
            return [];
        }

        if (preg_match('/<object\\b([^>]*?)>/i', $decoded, $matches)) {
            return $this->parseXmlAttributes($matches[1]);
        }

        return [];
    }

    private function decodeMxLibraryXmlPayload($encodedXml)
    {
        $raw = base64_decode($encodedXml, true);
        if ($raw === false) {
            return '';
        }

        $inflated = @gzinflate($raw);
        if ($inflated === false) {
            $inflated = @gzuncompress($raw);
        }
        if ($inflated === false && function_exists('zlib_decode')) {
            $inflated = @zlib_decode($raw);
        }
        if ($inflated === false) {
            return '';
        }

        return rawurldecode($inflated);
    }

    /**
     * @return array<string,string>
     */
    private function parseXmlAttributes($attributeString)
    {
        $attrs = [];
        if (preg_match_all('/(\\w+)="([^"]*)"/', $attributeString, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $attrs[$match[1]] = html_entity_decode($match[2], ENT_QUOTES | ENT_XML1, 'UTF-8');
            }
        }

        return $attrs;
    }
}
