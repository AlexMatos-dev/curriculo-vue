<?php

namespace Database\Seeders;

use App\Models\CompanySocialNetworkType;
use App\Models\Translation;
use Illuminate\Database\Seeder;

class CompanySocialNetworkTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'youtube', 'description' => [
                    'en' => 'youTube is a video sharing platform', 
                    'pt' => 'youTube é uma plataforma de compartilhamento de vídeos', 
                    'es' => 'YouTube es una plataforma para compartir vídeos'
                ]
            ],
            ['name' => 'instagram', 'description' => [
                    'en' => 'instagram is a free, online photo-sharing application and social network platform', 
                    'pt' => 'instagram é um aplicativo gratuito de compartilhamento de fotos on-line e uma plataforma de rede social', 
                    'es' => 'instagram es una aplicación gratuita para compartir fotografías y una plataforma de red social en línea.'
                ]
            ],
            ['name' => 'facebook', 'description' => [
                    'en' => 'facebook is a social networking website where users can post comments, share photographs, and post links to news', 
                    'pt' => 'facebook é um site de rede social onde os usuários podem postar comentários, compartilhar fotos e postar links para notícias', 
                    'es' => 'facebook es un sitio web de red social donde los usuarios pueden publicar comentarios, compartir fotografías y publicar enlaces a noticias'
                ]
            ],
            ['name' => 'linkedin', 'description' => [
                    'en' => 'linkedIn is a business and employment-focused social media platform that works across websites and mobile apps', 
                    'pt' => 'linkedIn é uma plataforma de mídia social focada em negócios e emprego que funciona através de sites e aplicativos móveis', 
                    'es' => 'linkedIn es una plataforma de redes sociales centrada en los negocios y el empleo que funciona en sitios web y aplicaciones móviles'
                ]
            ],
            ['name' => 'other', 'description' => [
                    'en' => 'other social network', 
                    'pt' => 'outra rede social', 
                    'es' => 'otra red social'
                ]
            ],
        ];
        $companySocialNetworkType = new CompanySocialNetworkType();
        foreach($data as $type){
            $obj = $companySocialNetworkType::where('name', $type['name'])->first();
            if($obj)
                continue;
            $newNetworkType = CompanySocialNetworkType::create([
                'name' => $type['name'],
                'description' => $type['description']['en']
            ]);
            if(!$newNetworkType)
                continue;
            Translation::create([
                'en' => $type['description']['en'],
                'pt' => $type['description']['pt'],
                'es' => $type['description']['es'],
                'category' => Translation::CATEGORY_COMPANY_SOCIAL_NETWORK_TYPE
            ]);
        }
    }
}
