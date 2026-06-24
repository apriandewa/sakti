<?php
/**
 * Main Master Configuration
 *
 * @package Main Master Configuration
 * @version 1.0.0
 * @license MIT
 */
return [
    'app'=>[
        'profile'=>[
            'name'=>'Diskominfotik Kabupaten Indragiri Hulu', // Your application name
            'short_name'=>'Diskominfotik Inhu', // Short name for your application
            'description'=>'Website Resmi Diskominfotik Kabupaten Indragiri Hulu', // Description of your application
            'keywords'=>'Diskominfotik, Indragiri Hulu, Website Resmi, Informasi Publik', // Keywords for your application
            'author'=>'@arwahyupradana', // Your name or company
            'version'=>'1.0.1', // major.minor.patch
            'laravel'=>'12', // Laravel version
        ],
        'root'=>[
            'backend'=>'App/Http/Controllers/Backend', // path to backend controller
            'frontend'=>'App/Http/Controllers/Frontend', // path to frontend controller
            'model'=>'App/Models', // path to model
            'view'=>'views/backend' // path to backend view
        ],
        'url'=>[
            'backend'=>'admin', // url for backend
            'frontend'=>'web', // url for frontend
        ],
        'view'=>[
            'backend'=>'backend', // path to backend view
            'frontend'=>'frontend', // path to frontend view
        ],
        'web'=>[
            'template'=>'eduadmin', // template for frontend view (default: eduadmin)
            'icon'=>'',
            'logo_light'=>'/images/logodiskominfotik.png',
            'logo_dark'=>'/images/logodiskominfotik.png',
            'favicon'=>'/images/logoppidinhu.jpg',
            'background'=>'/images/auth-bg/bg-1.jpg',
        ],
        'level'=>[
            'read', 'create', 'update', 'delete' // level of access for user role and permission module
        ]
    ],
    'content'=>[
        'announcement'=>[
            'status'=>[
                'sangat_penting'=>'Sangat Penting',
                'penting'=>'Penting',
                'biasa'=>'Biasa',
            ],
            'color'=>[
                'sangat_penting'=>'danger',
                'penting'=>'warning',
                'biasa'=>'info',
            ],
        ]
    ]
];
