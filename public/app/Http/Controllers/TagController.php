<?php

namespace App\Http\Controllers;

use App\Helpers\ModelUtils;
use App\Helpers\Validator;
use App\Models\Tag;

class TagController extends Controller
{
    /**
     * Searches for Tags accordingly to sent word and language
     * @param String term
     * @param Int limit - default = 5
     * @return @return \Illuminate\Http\JsonResponse
     */
    public function search()
    {
        Validator::validateParameters($this->request, [
            'limit' => 'integer'
        ]);
        $limit = request('limit', 5);
        if($limit > 50)
            $limit = 50;
        $results = (new Tag())->getTagByNameAndLanguage((string)request('term', ''), $limit);
        if(count($results) > 0)
            $results = ModelUtils::parseAsArrayWithAllLanguagesIsosAndTranslations($results, ['tags_id']);
        returnResponse(['data' => $results]);
    }
}
